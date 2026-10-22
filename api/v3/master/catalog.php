<?php

/**
 * V3 Master Data Catalog API
 * Path: /api/v3/master/catalog.php
 * Returns available master datasets, counts, destination breakdowns, and current site metrics.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

/** @var mysqli $connection */
if (!($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection is unavailable.']);
    exit;
}

if (!defined('LOGIN_CHECK')) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/lib/modules/isLogin.php')) {
        define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
    } elseif (file_exists($docRoot . '/lib/session/index.php')) {
        define('LOGIN_CHECK', $docRoot . '/lib/session/index.php');
    }
}
if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    @require_once LOGIN_CHECK;
}

$userRole = strtolower(trim(isset($site_access_type) ? (string)$site_access_type : (defined('USER_TYPE') ? (string)USER_TYPE : '')));
$isAdmin = (isset($site_access_type) && in_array($site_access_type, ['administrator', 'admin'], true)) || in_array($userRole, ['administrator', 'admin', 'superadmin'], true);

if (php_sapi_name() !== 'cli' && !$isAdmin) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Access denied. Administrator privileges required.']);
    exit;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID') ? (int)SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID') ? (int)USER_ID : (int)($_SESSION['user_id'] ?? 0);

require_once __DIR__ . '/schema.php';
ensureMasterDataTables($connection, $tblPrefix, false);

// 1. Get Master Data Summary Counts
$masterCounts = [
    'tour_packages'       => 0,
    'destinations'        => 0,
    'vehicles'            => 0,
    'hotels'              => 0,
    'transporters'        => 0
];

$qPkg = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_tour_package` WHERE tour_package_status = 1 OR tour_package_status IS NULL");
if ($qPkg && $row = mysqli_fetch_assoc($qPkg)) $masterCounts['tour_packages'] = (int)$row['c'];

$qDest = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_travel_destinations`");
if ($qDest && $row = mysqli_fetch_assoc($qDest)) $masterCounts['destinations'] = (int)$row['c'];

$qVeh = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_vehicles`");
if ($qVeh && $row = mysqli_fetch_assoc($qVeh)) $masterCounts['vehicles'] = (int)$row['c'];

$qHotel = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_hotels` WHERE property_status = 1 OR property_status IS NULL");
if ($qHotel && $row = mysqli_fetch_assoc($qHotel)) $masterCounts['hotels'] = (int)$row['c'];

$qTrans = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_transporters`");
if ($qTrans && $row = mysqli_fetch_assoc($qTrans)) $masterCounts['transporters'] = (int)$row['c'];

// 2. Get Current Site Data Summary Counts (to display what the user already has)
$siteCounts = [
    'tour_packages' => 0,
    'hotels'        => 0,
    'transporters'  => 0,
    'vehicles'      => 0,
    'destinations'  => $masterCounts['destinations']
];

if ($siteId > 0) {
    $qSitePkg = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}tour_package` WHERE tour_package_site_id = {$siteId}");
    if ($qSitePkg && $row = mysqli_fetch_assoc($qSitePkg)) $siteCounts['tour_packages'] = (int)$row['c'];

    $qSiteHotel = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}property` WHERE property_site_id = {$siteId}");
    if ($qSiteHotel && $row = mysqli_fetch_assoc($qSiteHotel)) $siteCounts['hotels'] = (int)$row['c'];

    $qSiteTrans = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}transporter` WHERE transporter_site_id = {$siteId}");
    if ($qSiteTrans && $row = mysqli_fetch_assoc($qSiteTrans)) $siteCounts['transporters'] = (int)$row['c'];

    $qSiteVeh = mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}vehicles` WHERE vehicle_site_id = {$siteId}");
    if ($qSiteVeh && $row = mysqli_fetch_assoc($qSiteVeh)) $siteCounts['vehicles'] = (int)$row['c'];
}

// 3. Destination Breakdown from Master Tour Packages
$destinationsMap = [];
$destQuery = mysqli_query($connection, "
    SELECT tour_package_city, tour_package_state, tour_package_country, tour_package_type, COUNT(*) as pkg_cnt
    FROM `{$tblPrefix}master_tour_package`
    WHERE (tour_package_status = 1 OR tour_package_status IS NULL)
    GROUP BY tour_package_city, tour_package_state, tour_package_country, tour_package_type
    ORDER BY pkg_cnt DESC
");
if ($destQuery) {
    while ($dRow = mysqli_fetch_assoc($destQuery)) {
        $cityName = trim($dRow['tour_package_city'] ?? '');
        $stateName = trim($dRow['tour_package_state'] ?? '');
        $name = !empty($cityName) ? $cityName : (!empty($stateName) ? $stateName : 'General');
        $pkgCount = (int)$dRow['pkg_cnt'];
        $type = strtolower($dRow['tour_package_type'] ?? 'domestic') === 'international' ? 'international' : 'domestic';

        $destKey = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        if (!isset($destinationsMap[$destKey])) {
            $destinationsMap[$destKey] = [
                'key'            => $destKey,
                'name'           => $name,
                'state'          => $stateName,
                'type'           => $type,
                'packages_count' => 0
            ];
        }
        $destinationsMap[$destKey]['packages_count'] += $pkgCount;
    }
}

// 4. Sample Vehicles list
$vehiclesList = [];
$vehQ = mysqli_query($connection, "SELECT vehicle_id, vehicle_title, vehicle_type, vehicle_seating, vehicle_per_km_rate_with_tax FROM `{$tblPrefix}master_vehicles` ORDER BY vehicle_id ASC");
if ($vehQ) {
    while ($v = mysqli_fetch_assoc($vehQ)) {
        $vehiclesList[] = [
            'id'      => (int)$v['vehicle_id'],
            'title'   => $v['vehicle_title'],
            'type'    => $v['vehicle_type'],
            'seating' => (int)$v['vehicle_seating'],
            'rate'    => (float)$v['vehicle_per_km_rate_with_tax']
        ];
    }
}

// 5. Sample Hotels distribution by City
$hotelCities = [];
$hcQ = mysqli_query($connection, "
    SELECT property_city, COUNT(*) as hotel_cnt
    FROM `{$tblPrefix}master_hotels`
    WHERE property_city IS NOT NULL AND property_city != ''
    GROUP BY property_city
    ORDER BY hotel_cnt DESC
    LIMIT 12
");
if ($hcQ) {
    while ($hc = mysqli_fetch_assoc($hcQ)) {
        $hotelCities[] = [
            'city'  => $hc['property_city'],
            'count' => (int)$hc['hotel_cnt']
        ];
    }
}

echo json_encode([
    'status'        => 'success',
    'master_counts' => $masterCounts,
    'site_counts'   => $siteCounts,
    'destinations'  => array_values($destinationsMap),
    'vehicles'      => $vehiclesList,
    'hotel_cities'  => $hotelCities,
    'site_id'       => $siteId
]);
