<?php

/**
 * V3 Itinerary Search API
 * Endpoint: /api/v3/itinerary/list.php
 * Method: GET / POST
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

require_once LOGIN_CHECK;

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

$reqData = $_GET;
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $reqData = array_merge($reqData, $decoded);
    }
}
$reqData = array_merge($_POST, $_REQUEST, $reqData);

$destination = trim($reqData['destination'] ?? '');
$theme       = trim($reqData['theme'] ?? '');
$search      = trim($reqData['search'] ?? '');
$limit       = isset($reqData['limit']) ? max(1, min(100, (int)$reqData['limit'])) : 50;

$siteFilter = ($siteId > 0) ? "tour_package_site_id = {$siteId}" : "1=1";
$themeFilter = ($theme !== '' && strtolower($theme) !== 'select' && strtolower($theme) !== 'all')
    ? " AND tour_package_theme = '" . mysqli_real_escape_string($connection, $theme) . "'"
    : "";

$whereClauses = [];
$whereClauses[] = $siteFilter;
$whereClauses[] = "tour_package_status = 1";

if ($destination !== '') {
    $destEscaped = mysqli_real_escape_string($connection, $destination);
    $placeParts = array_map('trim', explode(',', $destination));
    $partCount = count($placeParts);

    if ($partCount === 1) {
        $part = mysqli_real_escape_string($connection, $placeParts[0]);
        $whereClauses[] = "(
            TRIM(tour_package_country) LIKE '%{$part}%' 
            OR TRIM(tour_package_state) LIKE '%{$part}%' 
            OR TRIM(tour_package_city) LIKE '%{$part}%' 
            OR TRIM(tour_package_title) LIKE '%{$part}%'
        )";
    } elseif ($partCount === 2) {
        $statePart = mysqli_real_escape_string($connection, $placeParts[0]);
        $countryPart = mysqli_real_escape_string($connection, $placeParts[1]);
        $whereClauses[] = "(
            (TRIM(tour_package_state) LIKE '%{$statePart}%' OR TRIM(tour_package_city) LIKE '%{$statePart}%')
            AND TRIM(tour_package_country) LIKE '%{$countryPart}%'
        )";
    } else {
        $cityPart = mysqli_real_escape_string($connection, $placeParts[0]);
        $whereClauses[] = "(
            TRIM(tour_package_city) LIKE '%{$cityPart}%' 
            OR TRIM(tour_package_title) LIKE '%{$cityPart}%'
        )";
    }
}

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($connection, $search);
    $whereClauses[] = "(
        tour_package_title LIKE '%{$searchEscaped}%' 
        OR tour_package_city LIKE '%{$searchEscaped}%' 
        OR tour_package_state LIKE '%{$searchEscaped}%'
    )";
}

$whereSql = implode(' AND ', $whereClauses) . $themeFilter;

$query = "
    WITH ranked_prices AS (
      SELECT 
        tp.tour_package_id,
        tp.tour_package_title,
        tp.tour_package_theme,
        tp.tour_package_template,
        tp.tour_package_duration,
        tp.tour_package_city,
        tp.tour_package_state,
        tp.tour_package_country,
        tp.tour_package_overview,
        tp.tour_package_itinerary,
        tp.tour_package_inclusions,
        tp.tour_package_exclusions,
        tp.tour_package_outstation_vehicle,
        tp.tour_package_localsightseen_vehicle,
        tpp.tour_package_price_id,
        tpp.tour_package_price_type,
        tpp.tour_package_price_mrp_adult,
        tpp.tour_package_price_mrp_child,
        tpp.tour_package_price_selling_adult,
        tpp.tour_package_price_selling_child,
        tpp.tour_package_price_net_adult,
        tpp.tour_package_price_net_child,
        tpp.tour_package_price_agent_adult,
        tpp.tour_package_price_agent_child,
        COALESCE(tpp.tour_package_price_selling_adult, tpp.tour_package_price_mrp_adult, 0) AS tour_package_price_adult,
        COALESCE(tpp.tour_package_price_selling_child, tpp.tour_package_price_mrp_child, 0) AS tour_package_price_child,
        0 AS tour_package_price_infant,
        ROW_NUMBER() OVER (
          PARTITION BY tp.tour_package_id
          ORDER BY 
            CASE TRIM(tpp.tour_package_price_type)
              WHEN 'double' THEN 1
              WHEN 'single' THEN 2
              ELSE 3
            END
        ) AS rn
      FROM {$tblPrefix}tour_package tp
      LEFT JOIN {$tblPrefix}tour_package_price tpp
        ON tpp.tour_package_price_parent_id = tp.tour_package_id
      WHERE {$whereSql}
    )
    SELECT * FROM ranked_prices WHERE rn = 1
    ORDER BY tour_package_id DESC
    LIMIT {$limit}
";

$itineraryList = [];
$res = mysqli_query($connection, $query);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        // Decode itinerary JSON if stored as JSON string
        if (!empty($row['tour_package_itinerary'])) {
            $decodedItin = json_decode($row['tour_package_itinerary'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $row['parsed_itinerary'] = $decodedItin;
            }
        }
        $row['tour_package_overview'] = stripslashes($row['tour_package_overview'] ?? '');
        $row['overview'] = $row['tour_package_overview'];
        // Ensure template identifier is present
        $row['tour_package_template'] = !empty($row['tour_package_template']) ? $row['tour_package_template'] : 'mit';
        $itineraryList[] = $row;
    }

    $count = count($itineraryList);
    if ($count > 0) {
        http_response_code(200);
        echo json_encode([
            'status'        => 'success',
            'httpStatus'    => 200,
            'msg'           => "Found {$count} matching tour packages.",
            'count'         => $count,
            'itineraryList' => $itineraryList
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'status'        => 'warning',
            'httpStatus'    => 404,
            'msg'           => "No tour packages found for '{$destination}'.",
            'count'         => 0,
            'itineraryList' => []
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        'status'     => 'error',
        'httpStatus' => 500,
        'msg'        => (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? mysqli_error($connection) : 'Failed to search tour packages.',
        'itineraryList' => []
    ]);
}
