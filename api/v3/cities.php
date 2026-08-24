<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    if (!defined('ACTIVE_URL_NON_QUERY')) {
        $siteStatusPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/siteStatus.php';
        if (file_exists($siteStatusPath)) {
            require_once $siteStatusPath;
        }
    }
    if (!isset($deviceType)) {
        $devicePath = $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/device.php';
        if (file_exists($devicePath)) {
            require_once $devicePath;
            $deviceType = new Device();
        }
    }
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$rawPkgId = $_GET['package_id'] ?? ($_GET['id'] ?? 0);
$packageId = 0;
if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
    $packageId = (int)$rawPkgId;
} else if (!empty($rawPkgId)) {
    $decoded = base64_decode((string)$rawPkgId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $packageId = (int)$decoded;
    }
}

$stateParam   = trim($_GET['state'] ?? '');
$countryParam = trim($_GET['country'] ?? '');
$searchParam  = trim($_GET['search'] ?? ($_GET['q'] ?? ''));

// Determine Package State if package_id is provided and no state was explicitly passed
$pkgState = $stateParam;
if ($packageId > 0 && empty($pkgState)) {
    $pkgSql = "SELECT tour_package_state FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1";
    if ($pkgRes = mysqli_query($connection, $pkgSql)) {
        if ($pkgRow = mysqli_fetch_assoc($pkgRes)) {
            $pkgState = trim($pkgRow['tour_package_state'] ?? '');
        }
    }
}

$escapedSearch  = mysqli_real_escape_string($connection, $searchParam);
$escapedState   = mysqli_real_escape_string($connection, $pkgState);
$escapedCountry = mysqli_real_escape_string($connection, $countryParam);

// Helper: Title case formatting
function formatCityTitle($str) {
    if (empty($str)) return '';
    return mb_convert_case(trim($str), MB_CASE_TITLE, "UTF-8");
}

// Build Query on isw_city joined with isw_state and isw_country
$whereClauses = ["c.city_name IS NOT NULL", "c.city_name <> ''"];
if (!empty($escapedSearch)) {
    $whereClauses[] = "(c.city_name LIKE '%{$escapedSearch}%' OR s.state_name LIKE '%{$escapedSearch}%' OR co.country_name LIKE '%{$escapedSearch}%')";
}
if (!empty($escapedCountry)) {
    $whereClauses[] = "(co.country_name = '{$escapedCountry}' OR co.country_id = '{$escapedCountry}')";
}

$whereSql = implode(' AND ', $whereClauses);

// Priority Ordering: 1. Current State first, 2. Name match position, 3. Alphabetical
$stateOrderSql = !empty($escapedState) ? "CASE WHEN UPPER(TRIM(s.state_name)) = UPPER('{$escapedState}') THEN 0 ELSE 1 END," : "";
$matchOrderSql = !empty($escapedSearch) ? "CASE WHEN c.city_name LIKE '{$escapedSearch}%' THEN 0 ELSE 1 END," : "";

$query = "
    SELECT 
        c.city_id,
        TRIM(c.city_name) AS city_name,
        TRIM(s.state_name) AS state_name,
        TRIM(co.country_name) AS country_name
    FROM isw_city c
    LEFT JOIN isw_state s ON c.city_state_id = s.state_id
    LEFT JOIN isw_country co ON s.state_country_id = co.country_id
    WHERE {$whereSql}
    ORDER BY {$stateOrderSql} {$matchOrderSql} c.city_name ASC
    LIMIT 50
";

$results = [];
$stateCities = [];
$otherCities = [];
$seen = [];

if ($res = mysqli_query($connection, $query)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cName = formatCityTitle($row['city_name']);
        $sName = formatCityTitle($row['state_name'] ?? '');
        $coName = formatCityTitle($row['country_name'] ?? 'India');
        if (empty($cName)) continue;

        $cKey = strtolower($cName . '_' . $sName . '_' . $coName);
        if (in_array($cKey, $seen, true)) {
            continue;
        }
        $seen[] = $cKey;

        // Label: "Manali (Himachal Pradesh, India)" or "Jaipur (Rajasthan, India)"
        $metaParts = array_filter([$sName, $coName]);
        $labelText = $cName . (!empty($metaParts) ? ' (' . implode(', ', $metaParts) . ')' : '');

        $cityItem = [
            'id'      => $cName,
            'text'    => $labelText,
            'city'    => $cName,
            'state'   => $sName,
            'country' => $coName
        ];

        $results[] = $cityItem;

        if (!empty($pkgState) && strtoupper(trim($sName)) === strtoupper(trim($pkgState))) {
            $stateCities[] = $cName;
        } else {
            $otherCities[] = $cName;
        }
    }
}

echo json_encode([
    'status'       => 'success',
    'state'        => $pkgState,
    'query'        => $searchParam,
    'total'        => count($results),
    'results'      => $results,
    'state_cities' => array_values(array_unique($stateCities)),
    'other_cities' => array_values(array_unique($otherCities)),
    'cities'       => array_values(array_unique(array_map(function($i){ return $i['city']; }, $results)))
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
