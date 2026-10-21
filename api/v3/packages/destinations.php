<?php

/**
 * V3 Tour Package API - Available Master Destinations & Package Catalog
 * Path: /api/v3/packages/destinations.php
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

require_once __DIR__ . '/destination-helper.php';

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);
if ($siteId <= 0 && !empty($_SERVER['HTTP_HOST'])) {
    $host = mysqli_real_escape_string($connection, strtolower(trim($_SERVER['HTTP_HOST'])));
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE LOWER(site_domain) = '{$host}' OR LOWER(site_url) = '{$host}' LIMIT 1");
    if ($siteQuery && ($siteRow = mysqli_fetch_assoc($siteQuery))) {
        $siteId = (int) $siteRow['site_id'];
    }
}

$destQuery = mysqli_query($connection, "
    SELECT tour_package_id, tour_package_title, tour_package_type, tour_package_country, tour_package_state, tour_package_city
    FROM {$tblPrefix}tour_package
    WHERE tour_package_site_id = {$siteId}
      AND (tour_package_status = 1 OR tour_package_status IS NULL)
    ORDER BY tour_package_id ASC
");

$destinations = [
    'domestic'      => [],
    'international' => []
];

$seenTitles = [];
$totalUniquePackages = 0;

if ($destQuery) {
    while ($pkg = mysqli_fetch_assoc($destQuery)) {
        $cleanTitle = strtolower(trim($pkg['tour_package_title'] ?? ''));
        if (empty($cleanTitle) || isset($seenTitles[$cleanTitle])) {
            continue;
        }
        $seenTitles[$cleanTitle] = true;
        $totalUniquePackages++;

        $info = classifyPackageDestination($pkg);
        $type = $info['type'];
        $key  = $info['key'];

        if (!isset($destinations[$type][$key])) {
            $destinations[$type][$key] = [
                'key'            => $key,
                'name'           => $info['name'],
                'type'           => $type,
                'icon'           => $info['icon'],
                'highlights'     => $info['highlights'],
                'default'        => $info['default'],
                'packages_count' => 0
            ];
        }
        $destinations[$type][$key]['packages_count']++;
    }
}

// Convert associative arrays to indexed lists sorted by package count descending
$domesticList = array_values($destinations['domestic']);
usort($domesticList, function($a, $b) {
    return $b['packages_count'] <=> $a['packages_count'];
});

$internationalList = array_values($destinations['international']);
usort($internationalList, function($a, $b) {
    return $b['packages_count'] <=> $a['packages_count'];
});

echo json_encode([
    'status' => 'success',
    'data'   => [
        'domestic'               => $domesticList,
        'international'          => $internationalList,
        'total_domestic_count'   => count($domesticList),
        'total_intl_count'       => count($internationalList),
        'total_unique_packages'  => $totalUniquePackages
    ]
], JSON_UNESCAPED_UNICODE);
exit;
