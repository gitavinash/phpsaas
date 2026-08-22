<?php

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'domestic';
if (!in_array($type, ['domestic', 'international', 'all'])) {
    $type = 'domestic';
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

$typeFilter = ($type === 'all') ? "1=1" : "tp.tour_package_type = '{$type}'";
$locationCol = ($type === 'international') ? 'tour_package_country' : 'tour_package_city';

// 1. KPI Statistics Query (Independent of active search/filters)
$kpiQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN tour_package_status = 1 THEN 1 ELSE 0 END) as active_cnt,
        SUM(CASE WHEN tour_package_status = 2 THEN 1 ELSE 0 END) as draft_cnt,
        COUNT(DISTINCT NULLIF({$locationCol}, '')) as destinations_cnt
    FROM {$tblPrefix}tour_package AS tp
    WHERE tp.tour_package_status != 3
    AND {$typeFilter}
    AND tp.tour_package_site_id = {$siteId}
";

$total = 0;
$active = 0;
$draft = 0;
$destCount = 0;

$kpiRes = mysqli_query($connection, $kpiQuery);
if ($kpiRes && $kpiRow = mysqli_fetch_assoc($kpiRes)) {
    $total = (int)($kpiRow['total'] ?? 0);
    $active = (int)($kpiRow['active_cnt'] ?? 0);
    $draft = (int)($kpiRow['draft_cnt'] ?? 0);
    $destCount = (int)($kpiRow['destinations_cnt'] ?? 0);
}

// 2. Distinct Destinations / Countries Query
$locQuery = "
    SELECT DISTINCT NULLIF({$locationCol}, '') AS loc_name
    FROM {$tblPrefix}tour_package AS tp
    WHERE tp.tour_package_status != 3
    AND {$typeFilter}
    AND tp.tour_package_site_id = {$siteId}
    AND {$locationCol} != ''
    ORDER BY loc_name ASC
";

$destinations = [];
$locRes = mysqli_query($connection, $locQuery);
if ($locRes) {
    while ($locRow = mysqli_fetch_assoc($locRes)) {
        if (!empty($locRow['loc_name'])) {
            $destinations[] = $locRow['loc_name'];
        }
    }
}

// 3. Dynamic Filter Criteria Construction
$whereConditions = [
    "tp.tour_package_status != 3",
    $typeFilter,
    "tp.tour_package_site_id = {$siteId}"
];

// Search filter
if (!empty($_GET['search'])) {
    $searchEsc = mysqli_real_escape_string($connection, trim($_GET['search']));
    $whereConditions[] = "(
        tp.tour_package_title LIKE '%{$searchEsc}%' OR 
        tp.tour_package_city LIKE '%{$searchEsc}%' OR 
        tp.tour_package_country LIKE '%{$searchEsc}%' OR 
        tp.tour_package_hotel LIKE '%{$searchEsc}%' OR
        tp.tour_package_theme LIKE '%{$searchEsc}%'
    )";
}

// Status filter
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $statusVal = strtolower(trim($_GET['status']));
    if ($statusVal === 'active') {
        $whereConditions[] = "tp.tour_package_status = 1";
    } elseif ($statusVal === 'draft') {
        $whereConditions[] = "tp.tour_package_status = 2";
    } elseif ($statusVal === 'pending') {
        $whereConditions[] = "tp.tour_package_status NOT IN (1, 2, 3)";
    } elseif (is_numeric($statusVal)) {
        $stNum = (int)$statusVal;
        $whereConditions[] = "tp.tour_package_status = {$stNum}";
    }
}

// Theme filter
if (!empty($_GET['theme'])) {
    $themeEsc = mysqli_real_escape_string($connection, strtolower(trim($_GET['theme'])));
    $whereConditions[] = "LOWER(tp.tour_package_theme) = '{$themeEsc}'";
}

// Duration filter
if (!empty($_GET['duration'])) {
    $durVal = strtolower(trim($_GET['duration']));
    if ($durVal === 'short') {
        $whereConditions[] = "tp.tour_package_duration BETWEEN 1 AND 3";
    } elseif ($durVal === 'medium') {
        $whereConditions[] = "tp.tour_package_duration BETWEEN 4 AND 6";
    } elseif ($durVal === 'long') {
        $whereConditions[] = "tp.tour_package_duration >= 7";
    }
}

// Hotel filter
if (!empty($_GET['hotel'])) {
    $hotelEsc = mysqli_real_escape_string($connection, strtolower(trim($_GET['hotel'])));
    $whereConditions[] = "LOWER(REPLACE(tp.tour_package_hotel, '_', ' ')) LIKE '%{$hotelEsc}%'";
}

// Destination filter
if (!empty($_GET['destination'])) {
    $destEsc = mysqli_real_escape_string($connection, strtolower(trim($_GET['destination'])));
    $whereConditions[] = "(LOWER(tp.tour_package_city) = '{$destEsc}' OR LOWER(tp.tour_package_country) = '{$destEsc}')";
}

$whereSql = implode(' AND ', $whereConditions);

// 4. Count Total Filtered Records
$countQuery = "SELECT COUNT(*) as filtered_total FROM {$tblPrefix}tour_package AS tp WHERE {$whereSql}";
$countRes = mysqli_query($connection, $countQuery);
$totalRecords = 0;
if ($countRes && $countRow = mysqli_fetch_assoc($countRes)) {
    $totalRecords = (int)$countRow['filtered_total'];
}

$totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;
if ($page > $totalPages && $totalRecords > 0) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

// 5. Paged Tour Packages Query
$listQuery = "
    SELECT 
        tp.tour_package_id, 
        tp.tour_package_title, 
        tp.tour_package_type, 
        tp.tour_package_city, 
        tp.tour_package_country,
        tp.tour_package_theme, 
        tp.tour_package_duration, 
        tp.tour_package_hotel, 
        tp.tour_package_status, 
        tp.tour_package_created_at,
        l.link_url 
    FROM {$tblPrefix}tour_package AS tp
    LEFT JOIN {$tblPrefix}link AS l 
        ON l.link_relation_id = tp.tour_package_id 
        AND l.link_status = 200 
        AND l.link_site_id = {$siteId} 
        AND l.link_type = 'tour_package'
    WHERE {$whereSql}
    ORDER BY tp.tour_package_created_at DESC
    LIMIT {$limit} OFFSET {$offset}
";

$packages = [];
$listRes = mysqli_query($connection, $listQuery);
if ($listRes) {
    while ($row = mysqli_fetch_assoc($listRes)) {
        $pkgId = (int)$row['tour_package_id'];
        $encodedId = base64_encode($pkgId);
        $title = $row['tour_package_title'] ?? 'Untitled Package';
        $pkgType = strtolower($row['tour_package_type'] ?? 'domestic');
        $city = $row['tour_package_city'] ?? '';
        $country = $row['tour_package_country'] ?? '';
        $locDisplay = ($pkgType === 'international') ? ($country ?: $city ?: 'International') : ($city ?: 'Multiple Destinations');
        $theme = strtolower(trim($row['tour_package_theme'] ?? 'standard'));
        $themeClass = 'pkg-theme-' . preg_replace('/[^a-z0-9_-]/', '_', $theme);
        $themeLabel = ucwords(str_replace('_', ' ', $theme));
        $durationN = (int)($row['tour_package_duration'] ?? 0);
        $durationD = $durationN + 1;
        $hotelRaw = str_replace('_', ' ', $row['tour_package_hotel'] ?: 'Standard');
        $hotelLabel = ucwords($hotelRaw);

        switch ((int)$row['tour_package_status']) {
            case 1:
                $statusClass = 'status-active';
                $statusLabel = 'Active';
                break;
            case 2:
                $statusClass = 'status-draft';
                $statusLabel = 'Draft';
                break;
            default:
                $statusClass = 'status-pending';
                $statusLabel = 'Pending';
                break;
        }

        $packages[] = [
            'id'           => $pkgId,
            'encodedId'    => $encodedId,
            'title'        => $title,
            'type'         => $pkgType,
            'city'         => $city,
            'country'      => $country,
            'location'     => $locDisplay,
            'theme'        => $theme,
            'themeClass'   => $themeClass,
            'themeLabel'   => $themeLabel,
            'durationN'    => $durationN,
            'durationD'    => $durationD,
            'hotel'        => $hotelRaw,
            'hotelLabel'   => $hotelLabel,
            'status'       => (int)$row['tour_package_status'],
            'statusClass'  => $statusClass,
            'statusLabel'  => $statusLabel,
            'createdAt'    => $row['tour_package_created_at'] ?? '',
            'linkUrl'      => $row['link_url'] ?? ''
        ];
    }
}

echo json_encode([
    'status'       => 'success',
    'type'         => $type,
    'kpis'         => [
        'total'        => $total,
        'active'       => $active,
        'draft'        => $draft,
        'destinations' => $destCount
    ],
    'destinations' => $destinations,
    'pagination'   => [
        'page'         => $page,
        'limit'        => $limit,
        'totalRecords' => $totalRecords,
        'totalPages'   => $totalPages,
        'hasNextPage'  => $page < $totalPages,
        'hasPrevPage'  => $page > 1
    ],
    'packages'     => $packages
], JSON_UNESCAPED_SLASHES);
