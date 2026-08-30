<?php

header("Content-Type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$searchParam = trim($_GET['search'] ?? ($_GET['q'] ?? ($_GET['term'] ?? '')));
$escapedSearch = mysqli_real_escape_string($connection, $searchParam);

$searchCondition = "";
if (!empty($escapedSearch)) {
    $searchCondition = " AND (tour_package_title LIKE '%{$escapedSearch}%' 
                         OR tour_package_city LIKE '%{$escapedSearch}%' 
                         OR tour_package_country LIKE '%{$escapedSearch}%' 
                         OR tour_package_theme LIKE '%{$escapedSearch}%')";
}

$siteCondition = ($siteId > 0) ? " AND tour_package_site_id = {$siteId}" : "";

$query = "SELECT tour_package_id, tour_package_title, tour_package_city, tour_package_country, 
                 tour_package_duration, tour_package_type, tour_package_theme, tour_package_status
          FROM {$tblPrefix}tour_package
          WHERE tour_package_status != 3 {$siteCondition}{$searchCondition}
          ORDER BY tour_package_type ASC, tour_package_title ASC";

$domestic = [];
$international = [];
$flatList = [];

if (isset($connection) && $connection && ($res = mysqli_query($connection, $query))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $pId       = (int)$row['tour_package_id'];
        $pTitle    = trim(stripslashes($row['tour_package_title'] ?? ''));
        $pCity     = trim(stripslashes($row['tour_package_city'] ?? ''));
        $pCountry  = trim(stripslashes($row['tour_package_country'] ?? ''));
        $pDuration = (int)($row['tour_package_duration'] ?? 0);
        $pDays     = $pDuration > 0 ? ($pDuration + 1) : 1;
        $pType     = strtolower(trim($row['tour_package_type'] ?? 'domestic'));
        $pTheme    = ucfirst(str_replace('_', ' ', $row['tour_package_theme'] ?? 'Standard'));
        $loc       = $pCity ?: $pCountry;

        $item = [
            'id'                 => $pTitle,
            'pkg_id'             => $pId,
            'text'               => $pTitle,
            'title'              => $pTitle,
            'city'               => $loc,
            'country'            => $pCountry,
            'duration'           => $pDuration,
            'days'               => $pDays,
            'formatted_duration' => "{$pDuration}N / {$pDays}D",
            'type'               => $pType,
            'theme'              => $pTheme,
            'status'             => (int)$row['tour_package_status']
        ];

        $flatList[] = $item;

        if ($pType === 'international') {
            $international[] = $item;
        } else {
            $domestic[] = $item;
        }
    }
}

// Select2 Grouped Output Structure
$select2Results = [];
if (!empty($domestic)) {
    $select2Results[] = [
        'text'     => '🏔️ Domestic Tour Packages (' . count($domestic) . ')',
        'children' => $domestic
    ];
}
if (!empty($international)) {
    $select2Results[] = [
        'text'     => '✈️ International Packages (' . count($international) . ')',
        'children' => $international
    ];
}

echo json_encode([
    'status'        => 200,
    'total'         => count($flatList),
    'results'       => $select2Results,
    'data'          => [
        'domestic'      => $domestic,
        'international' => $international,
        'all'           => $flatList
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
