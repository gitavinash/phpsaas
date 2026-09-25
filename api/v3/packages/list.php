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
        tp.tour_package_state,
        tp.tour_package_country,
        tp.tour_package_start_from,
        tp.tour_package_end_to,
        tp.tour_package_theme, 
        tp.tour_package_template,
        tp.tour_package_duration, 
        tp.tour_package_hotel, 
        tp.tour_package_outstation_vehicle,
        tp.tour_package_localsightseen_vehicle,
        tp.tour_package_status, 
        tp.tour_package_created_at,
        tp.tour_package_updated_at,
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

$rawRows = [];
$pkgIds = [];
$listRes = mysqli_query($connection, $listQuery);
if ($listRes) {
    while ($row = mysqli_fetch_assoc($listRes)) {
        $rawRows[] = $row;
        $pkgIds[] = (int)$row['tour_package_id'];
    }
}

// 6. Batch Fetch Pricing Details for Current Page Packages
$pricesMap = [];
if (!empty($pkgIds)) {
    $idsInSql = implode(',', $pkgIds);
    $priceBatchSql = "
        SELECT 
            tour_package_price_parent_id,
            COUNT(*) as total_slabs,
            SUM(CASE WHEN tour_package_price_status = 1 THEN 1 ELSE 0 END) as active_slabs,
            MIN(CASE WHEN tour_package_price_selling_adult > 0 THEN tour_package_price_selling_adult ELSE NULL END) as min_selling,
            MAX(CASE WHEN tour_package_price_selling_adult > 0 THEN tour_package_price_selling_adult ELSE NULL END) as max_selling,
            MIN(CASE WHEN tour_package_price_mrp_adult > 0 THEN tour_package_price_mrp_adult ELSE NULL END) as min_mrp,
            MIN(CASE WHEN tour_package_price_net_adult > 0 THEN tour_package_price_net_adult ELSE NULL END) as min_net,
            MIN(CASE WHEN tour_package_price_agent_adult > 0 THEN tour_package_price_agent_adult ELSE NULL END) as min_agent,
            GROUP_CONCAT(DISTINCT tour_package_price_type ORDER BY tour_package_price_type SEPARATOR ', ') as price_types,
            MIN(tour_package_price_date_start) as date_start,
            MAX(tour_package_price_date_end) as date_end
        FROM {$tblPrefix}tour_package_price
        WHERE tour_package_price_status != 3
          AND tour_package_price_parent_id IN ({$idsInSql})
          AND tour_package_price_site_id = {$siteId}
        GROUP BY tour_package_price_parent_id
    ";

    $priceBatchRes = mysqli_query($connection, $priceBatchSql);
    if ($priceBatchRes) {
        while ($pRow = mysqli_fetch_assoc($priceBatchRes)) {
            $pricesMap[(int)$pRow['tour_package_price_parent_id']] = $pRow;
        }
    }
}

// 7. Batch Fetch Assigned Hotels Count
$hotelsMap = [];
if (!empty($pkgIds)) {
    $idsInSql = implode(',', $pkgIds);
    $hotelBatchSql = "
        SELECT 
            tph_package_id,
            COUNT(DISTINCT tph_hotel_id) as hotel_count
        FROM {$tblPrefix}tour_package_hotel
        WHERE tph_package_id IN ({$idsInSql})
          AND tph_site_id = {$siteId}
        GROUP BY tph_package_id
    ";
    $hotelBatchRes = mysqli_query($connection, $hotelBatchSql);
    if ($hotelBatchRes) {
        while ($hRow = mysqli_fetch_assoc($hotelBatchRes)) {
            $hotelsMap[(int)$hRow['tph_package_id']] = (int)$hRow['hotel_count'];
        }
    }
}

$packages = [];
foreach ($rawRows as $row) {
    $pkgId = (int)$row['tour_package_id'];
    $encodedId = base64_encode($pkgId);
    $title = stripslashes($row['tour_package_title'] ?? 'Untitled Package');
    $pkgType = strtolower($row['tour_package_type'] ?? 'domestic');
    $city = stripslashes($row['tour_package_city'] ?? '');
    $state = stripslashes($row['tour_package_state'] ?? '');
    $country = stripslashes($row['tour_package_country'] ?? '');
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

    // Pricing parsing
    $pData = $pricesMap[$pkgId] ?? null;
    $hasPricing = ($pData !== null && (int)($pData['total_slabs'] ?? 0) > 0);
    $minSelling = $hasPricing && $pData['min_selling'] !== null ? (float)$pData['min_selling'] : 0;
    $maxSelling = $hasPricing && $pData['max_selling'] !== null ? (float)$pData['max_selling'] : 0;
    $minMrp     = $hasPricing && $pData['min_mrp'] !== null ? (float)$pData['min_mrp'] : 0;
    $minNet     = $hasPricing && $pData['min_net'] !== null ? (float)$pData['min_net'] : 0;
    $minAgent   = $hasPricing && $pData['min_agent'] !== null ? (float)$pData['min_agent'] : 0;

    $sellingDisplay = '';
    if ($minSelling > 0) {
        if ($maxSelling > $minSelling) {
            $sellingDisplay = '₹' . number_format($minSelling) . ' - ₹' . number_format($maxSelling);
        } else {
            $sellingDisplay = '₹' . number_format($minSelling);
        }
    }

    $mrpDisplay = ($minMrp > 0) ? '₹' . number_format($minMrp) : '';
    $netDisplay = ($minNet > 0) ? '₹' . number_format($minNet) : '';
    $agentDisplay = ($minAgent > 0) ? '₹' . number_format($minAgent) : '';

    $pricing = [
        'hasPricing'     => $hasPricing,
        'totalSlabs'     => $hasPricing ? (int)$pData['total_slabs'] : 0,
        'activeSlabs'    => $hasPricing ? (int)$pData['active_slabs'] : 0,
        'minSelling'     => $minSelling,
        'maxSelling'     => $maxSelling,
        'sellingDisplay' => $sellingDisplay,
        'minMrp'         => $minMrp,
        'mrpDisplay'     => $mrpDisplay,
        'minNet'         => $minNet,
        'netDisplay'     => $netDisplay,
        'minAgent'       => $minAgent,
        'agentDisplay'   => $agentDisplay,
        'types'          => $hasPricing && !empty($pData['price_types']) ? ucwords($pData['price_types']) : '',
        'dateStart'      => $pData['date_start'] ?? '',
        'dateEnd'        => $pData['date_end'] ?? ''
    ];

    // Route & Vehicles
    $startFrom = stripslashes(trim($row['tour_package_start_from'] ?? ''));
    $endTo = stripslashes(trim($row['tour_package_end_to'] ?? ''));
    $routeDisplay = '';
    if (!empty($startFrom) || !empty($endTo)) {
        if ($startFrom === $endTo) {
            $routeDisplay = $startFrom;
        } elseif (!empty($startFrom) && !empty($endTo)) {
            $routeDisplay = $startFrom . ' → ' . $endTo;
        } else {
            $routeDisplay = $startFrom ?: $endTo;
        }
    }

    $outstationVeh = stripslashes(trim($row['tour_package_outstation_vehicle'] ?? ''));
    $localVeh = stripslashes(trim($row['tour_package_localsightseen_vehicle'] ?? ''));
    $vehiclesList = [];
    if (!empty($outstationVeh) && $outstationVeh !== 'select') $vehiclesList[] = ucwords($outstationVeh);
    if (!empty($localVeh) && $localVeh !== 'select' && strtolower($localVeh) !== strtolower($outstationVeh)) $vehiclesList[] = ucwords($localVeh);
    $vehicleDisplay = implode(' + ', $vehiclesList);

    $createdAt = $row['tour_package_created_at'] ?? '';
    $updatedAt = $row['tour_package_updated_at'] ?? '';
    $displayDate = (!empty($updatedAt) && $updatedAt !== '0000-00-00 00:00:00') ? $updatedAt : $createdAt;
    $updatedAtHuman = !empty($displayDate) ? date('d M Y', strtotime($displayDate)) : '';

    $packages[] = [
        'id'             => $pkgId,
        'encodedId'      => $encodedId,
        'title'          => $title,
        'type'           => $pkgType,
        'city'           => $city,
        'state'          => $state,
        'country'        => $country,
        'location'       => $locDisplay,
        'theme'          => $theme,
        'themeClass'     => $themeClass,
        'themeLabel'     => $themeLabel,
        'durationN'      => $durationN,
        'durationD'      => $durationD,
        'hotel'          => $hotelRaw,
        'hotelLabel'     => $hotelLabel,
        'hotelCount'     => $hotelsMap[$pkgId] ?? 0,
        'route'          => $routeDisplay,
        'vehicle'        => $vehicleDisplay,
        'template'       => $row['tour_package_template'] ?? 'mit',
        'status'         => (int)$row['tour_package_status'],
        'statusClass'    => $statusClass,
        'statusLabel'    => $statusLabel,
        'createdAt'      => $createdAt,
        'updatedAt'      => $updatedAt,
        'updatedAtHuman' => $updatedAtHuman,
        'linkUrl'        => $row['link_url'] ?? '',
        'pricing'        => $pricing,
        'price'          => $minSelling,
        'priceDisplay'   => $sellingDisplay
    ];
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
