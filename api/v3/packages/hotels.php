<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// POST: Assign / Unassign Hotel to Night
// ==========================================
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = !empty($rawInput) ? json_decode($rawInput, true) : $_POST;
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $rawPkgId = $payload['package_id'] ?? ($payload['packageId'] ?? ($payload['tour_id'] ?? ($payload['id'] ?? 0)));
    $packageId = 0;
    if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
        $packageId = (int)$rawPkgId;
    } else if (!empty($rawPkgId)) {
        $decoded = base64_decode((string)$rawPkgId, true);
        if ($decoded !== false && is_numeric($decoded)) {
            $packageId = (int)$decoded;
        }
    }

    $propertyId = isset($payload['property_id']) ? (int)$payload['property_id'] : (int)($payload['hotel_id'] ?? 0);
    $night      = isset($payload['night']) ? (int)$payload['night'] : 0;
    $roomType   = isset($payload['roomType']) ? trim($payload['roomType']) : (trim($payload['room_type'] ?? ''));

    if ($packageId <= 0 || $propertyId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Valid Package ID and Hotel Property ID are required.'
        ], JSON_UNESCAPED_SLASHES);
        exit();
    }

    // Verify Property & Package exist
    $checkProp = mysqli_query($connection, "SELECT property_id, property_title FROM {$tblPrefix}property WHERE property_id = '{$propertyId}' AND property_site_id = {$siteId} LIMIT 1");
    $checkPkg  = mysqli_query($connection, "SELECT tour_package_id, tour_package_duration FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1");

    if (!$checkProp || mysqli_num_rows($checkProp) === 0 || !$checkPkg || mysqli_num_rows($checkPkg) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Hotel Property or Tour Package not found.'
        ], JSON_UNESCAPED_SLASHES);
        exit();
    }

    $propData = mysqli_fetch_assoc($checkProp);
    $propTitle = $propData['property_title'] ?? 'Hotel';

    if ($night === 0) {
        // Unassign hotel from this package
        $deleteSql = "DELETE FROM {$tblPrefix}tour_package_hotel WHERE tph_package_id = '{$packageId}' AND tph_hotel_id = '{$propertyId}' AND tph_site_id = {$siteId}";
        if (mysqli_query($connection, $deleteSql)) {
            echo json_encode([
                'status'  => 'success',
                'msg'     => "{$propTitle} unassigned from package.",
                'night'   => 0,
                'hotel_id'=> $propertyId
            ], JSON_UNESCAPED_SLASHES);
            exit();
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Database error unassigning hotel.'
            ], JSON_UNESCAPED_SLASHES);
            exit();
        }
    } else {
        $roomId = 0;
        $roomTypeName = '';

        if (!empty($roomType) && $roomType !== '#') {
            $roomParts = explode(':', $roomType);
            $roomId = (int)($roomParts[0] ?? 0);
            $roomTypeName = trim($roomParts[1] ?? '');
        }

        // Clear existing assignment for this night OR for this hotel in this package (enforcing 1 hotel per night)
        $clearSql = "DELETE FROM {$tblPrefix}tour_package_hotel WHERE tph_package_id = '{$packageId}' AND (tph_night = '{$night}' OR tph_hotel_id = '{$propertyId}') AND tph_site_id = {$siteId}";
        mysqli_query($connection, $clearSql);

        $escapedRoomType = mysqli_real_escape_string($connection, $roomTypeName);
        $insertSql = "INSERT INTO {$tblPrefix}tour_package_hotel (tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) VALUES ('{$propertyId}', '{$packageId}', '{$roomId}', '{$escapedRoomType}', '{$night}', 1, {$siteId})";

        if (mysqli_query($connection, $insertSql)) {
            echo json_encode([
                'status'     => 'success',
                'msg'        => "{$propTitle} assigned to Night {$night}.",
                'night'      => $night,
                'hotel_id'   => $propertyId,
                'room_type'  => $roomTypeName
            ], JSON_UNESCAPED_SLASHES);
            exit();
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Database error assigning hotel: ' . mysqli_error($connection)
            ], JSON_UNESCAPED_SLASHES);
            exit();
        }
    }
}

// ==========================================
// GET: Fetch Package, KPIs & Matching Hotels
// ==========================================
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

$requestedCity = trim($_GET['city'] ?? '');

if ($packageId <= 0 && empty($requestedCity)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Valid Package ID or City is required.'
    ], JSON_UNESCAPED_SLASHES);
    exit();
}

$pkgTitle = 'Tour Package';
$pkgTheme = 'Standard';
$pkgCity  = '';
$pkgState = '';
$pkgCountry = '';
$pkgDuration = 1;
$pkgDays = 2;
$pkgItinerary = '';
$pkgCategory = 'domestic';
$linkUrl = '';

// 1. Fetch Package Info (if packageId > 0)
if ($packageId > 0) {
    $pkgSql = "
        SELECT 
            p.tour_package_id,
            p.tour_package_title, 
            p.tour_package_theme,
            p.tour_package_city, 
            p.tour_package_state, 
            p.tour_package_country, 
            p.tour_package_duration, 
            p.tour_package_hotel, 
            p.tour_package_itinerary,
            p.tour_package_type,
            l.link_url
        FROM {$tblPrefix}tour_package p
        LEFT JOIN {$tblPrefix}link l 
          ON l.link_relation_id = p.tour_package_id 
         AND l.link_type = 'tour_package'
         AND l.link_site_id = {$siteId}
        WHERE p.tour_package_id = '{$packageId}' AND p.tour_package_site_id = {$siteId}
        LIMIT 1
    ";

    $pkgRes = mysqli_query($connection, $pkgSql);
    if ($pkgRes && mysqli_num_rows($pkgRes) > 0) {
        $pkgRow = mysqli_fetch_assoc($pkgRes);
        $pkgTitle = $pkgRow['tour_package_title'] ?? 'Tour Package';
        $pkgTheme = trim($pkgRow['tour_package_theme'] ?? 'Standard');
        $pkgCity  = trim($pkgRow['tour_package_city'] ?? '');
        $pkgState = trim($pkgRow['tour_package_state'] ?? '');
        $pkgCountry = trim($pkgRow['tour_package_country'] ?? '');
        $pkgDuration = max(1, (int)($pkgRow['tour_package_duration'] ?? 1));
        $pkgDays = $pkgDuration + 1;
        $pkgItinerary = trim($pkgRow['tour_package_itinerary'] ?? '');
        $pkgCategory = strtolower(trim($pkgRow['tour_package_type'] ?? 'domestic'));
        $linkUrl = $pkgRow['link_url'] ?? '';
    }
}

// 2. Fetch Existing Hotel Night Assignments for this Package
$assignedNightMap = [];
$hotelAssignmentMap = [];
if ($packageId > 0) {
    $assignedQuery = "
        SELECT tph_id, tph_hotel_id, tph_room_type_id, tph_room_type, tph_night 
        FROM {$tblPrefix}tour_package_hotel 
        WHERE tph_package_id = '{$packageId}' AND tph_site_id = {$siteId}
    ";
    if ($assignedRes = mysqli_query($connection, $assignedQuery)) {
        while ($aRow = mysqli_fetch_assoc($assignedRes)) {
            $n = (int)$aRow['tph_night'];
            $hId = (int)$aRow['tph_hotel_id'];
            $assignedNightMap[$n] = $aRow;
            $hotelAssignmentMap[$hId] = $aRow;
        }
    }
}

// 3. Smart City Detection from Inventory & Itinerary
$dbCityStateQuery = "
    SELECT DISTINCT TRIM(property_city) AS city, TRIM(property_state) AS state 
    FROM {$tblPrefix}property 
    WHERE property_site_id = {$siteId} AND property_status = 1 AND property_city <> ''
";
$allDbCities = [];
if ($cRes = mysqli_query($connection, $dbCityStateQuery)) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        if (!empty($cRow['city'])) {
            $allDbCities[] = $cRow['city'];
        }
    }
}

$mentionedCities = [];
$itinerarySearchText = strtolower($pkgTitle . ' ' . $pkgCity . ' ' . $pkgState . ' ' . $pkgItinerary);
foreach ($allDbCities as $dbCity) {
    $dbCityLower = strtolower(trim($dbCity));
    if (empty($dbCityLower)) continue;
    if (stripos($itinerarySearchText, $dbCityLower) !== false) {
        $mentionedCities[] = $dbCity;
    }
}
if (!empty($pkgCity) && !in_array($pkgCity, $mentionedCities, true)) {
    $mentionedCities[] = $pkgCity;
}

// 4. Build Where & Priority Clause for Matching Hotels
$whereConditions = [
    "p.property_site_id = {$siteId}",
    "p.property_status = 1"
];

$assignedIds = array_keys($hotelAssignmentMap);
$assignedIdsStr = !empty($assignedIds) ? implode(',', $assignedIds) : '0';

$escapedReqCity = !empty($requestedCity) ? mysqli_real_escape_string($connection, strtoupper(trim($requestedCity))) : '';
$escapedCities = array_map(function ($c) use ($connection) {
    return "'" . mysqli_real_escape_string($connection, strtoupper(trim($c))) . "'";
}, $mentionedCities);
$citiesInStr = !empty($escapedCities) ? implode(',', $escapedCities) : "''";
$escapedState = !empty($pkgState) ? mysqli_real_escape_string($connection, strtoupper(trim($pkgState))) : '';

if (!empty($escapedReqCity)) {
    // If specific city requested, match that city or any assigned hotel for this package
    if (!empty($assignedIds)) {
        $whereConditions[] = "(UPPER(TRIM(p.property_city)) = '{$escapedReqCity}' OR UPPER(TRIM(p.property_city)) LIKE '%{$escapedReqCity}%' OR p.property_id IN ({$assignedIdsStr}))";
    } else {
        $whereConditions[] = "(UPPER(TRIM(p.property_city)) = '{$escapedReqCity}' OR UPPER(TRIM(p.property_city)) LIKE '%{$escapedReqCity}%')";
    }
} else {
    if (!empty($mentionedCities) || !empty($pkgState) || !empty($assignedIds)) {
        $locationOrs = [];
        if (!empty($assignedIds)) {
            $locationOrs[] = "p.property_id IN ({$assignedIdsStr})";
        }
        if (!empty($mentionedCities) && $citiesInStr !== "''") {
            $locationOrs[] = "UPPER(TRIM(p.property_city)) IN ({$citiesInStr})";
        }
        if (!empty($pkgState)) {
            $locationOrs[] = "UPPER(TRIM(p.property_state)) = '{$escapedState}'";
        }
        if (!empty($locationOrs)) {
            $whereConditions[] = "(" . implode(" OR ", $locationOrs) . ")";
        }
    }
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

$priorityCases = [];
$priorityCases[] = "WHEN p.property_id IN ({$assignedIdsStr}) THEN 1";
if (!empty($escapedReqCity)) {
    $priorityCases[] = "WHEN UPPER(TRIM(p.property_city)) = '{$escapedReqCity}' THEN 2";
}
if ($citiesInStr !== "''") {
    $priorityCases[] = "WHEN UPPER(TRIM(p.property_city)) IN ({$citiesInStr}) THEN 3";
}
if (!empty($escapedState)) {
    $priorityCases[] = "WHEN UPPER(TRIM(p.property_state)) = '{$escapedState}' THEN 4";
}
$priorityCasesStr = implode("\n          ", $priorityCases);

$query = "
    SELECT 
        p.property_id,
        p.property_title, 
        p.property_thumbnail, 
        p.property_address, 
        TRIM(p.property_city)     AS property_city, 
        TRIM(p.property_state)    AS property_state, 
        TRIM(p.property_country)  AS property_country, 
        p.property_type,
        CASE 
          {$priorityCasesStr}
          ELSE 99
        END AS match_priority,
        GROUP_CONCAT(
          DISTINCT CONCAT(r.room_id, ':', r.room_type) 
          ORDER BY r.room_id SEPARATOR ','
        ) AS rooms
    FROM {$tblPrefix}property p
    LEFT JOIN {$tblPrefix}room r
      ON r.room_property_id = p.property_id
     AND r.room_site_id     = {$siteId}
    {$whereClause}
    GROUP BY p.property_id
    ORDER BY match_priority ASC, p.property_title ASC
";

$hotelList = [];
$cityCountsMap = [];

if ($hotelData = mysqli_query($connection, $query)) {
    while ($row = mysqli_fetch_assoc($hotelData)) {
        $hId = (int)$row['property_id'];
        $cName = ucwords(strtolower(trim($row['property_city'] ?? '')));
        if (!empty($cName)) {
            $cityCountsMap[$cName] = ($cityCountsMap[$cName] ?? 0) + 1;
        }

        $assignedNight = 0;
        $assignedRoomId = 0;
        $assignedRoomType = '';

        if (isset($hotelAssignmentMap[$hId])) {
            $assignedNight = (int)$hotelAssignmentMap[$hId]['tph_night'];
            $assignedRoomId = (int)$hotelAssignmentMap[$hId]['tph_room_type_id'];
            $assignedRoomType = $hotelAssignmentMap[$hId]['tph_room_type'];
        }

        $parsedRooms = [];
        $roomsRaw = $row['rooms'] ?? '';
        if (!empty($roomsRaw)) {
            $roomItems = explode(',', $roomsRaw);
            foreach ($roomItems as $rItem) {
                $rParts = explode(':', $rItem);
                $rId = (int)($rParts[0] ?? 0);
                $rType = $rParts[1] ?? '';
                if (!empty($rType)) {
                    $parsedRooms[] = [
                        'id'   => $rId,
                        'name' => ucwords(str_replace('_', ' ', $rType)),
                        'val'  => $rId . ':' . $rType
                    ];
                }
            }
        }
        if (empty($parsedRooms)) {
            $parsedRooms[] = [
                'id'   => 0,
                'name' => 'Standard Room',
                'val'  => '0:Standard Room'
            ];
        }

        $hotelList[] = [
            'id'                 => $hId,
            'title'              => $row['property_title'],
            'thumbnail'          => !empty($row['property_thumbnail']) ? $row['property_thumbnail'] : '/app/dashboard/v3/default/desktop/assets/images/default-picture.png',
            'address'            => $row['property_address'] ?? '',
            'city'               => $cName,
            'state'              => $row['property_state'] ?? '',
            'country'            => $row['property_country'] ?? '',
            'type'               => $row['property_type'] ?? 'hotel',
            'type_label'         => ucwords(str_replace('_', ' ', $row['property_type'] ?? 'Hotel')),
            'assigned_night'     => $assignedNight,
            'assigned_room_id'   => $assignedRoomId,
            'assigned_room_type' => $assignedRoomType,
            'rooms'              => $parsedRooms,
            'match_priority'     => (int)$row['match_priority']
        ];
    }
}

// Occupied Map by Night
$occupiedNightHotelNameMap = [];
foreach ($assignedNightMap as $n => $info) {
    $hId = (int)$info['tph_hotel_id'];
    foreach ($hotelList as $h) {
        if ((int)$h['id'] === $hId) {
            $occupiedNightHotelNameMap[$n] = $h['title'];
            break;
        }
    }
}

// Sort: Assigned nights first (Night 1, 2, 3...), then match priority
usort($hotelList, function ($a, $b) {
    $nightA = (int)$a['assigned_night'] > 0 ? (int)$a['assigned_night'] : 999999;
    $nightB = (int)$b['assigned_night'] > 0 ? (int)$b['assigned_night'] : 999999;

    if ($nightA !== $nightB) {
        return $nightA - $nightB;
    }
    if ($a['match_priority'] !== $b['match_priority']) {
        return $a['match_priority'] - $b['match_priority'];
    }
    return strcmp($a['title'], $b['title']);
});

$assignedHotelsCount = 0;
$unassignedHotelsCount = 0;
foreach ($hotelList as $h) {
    if ((int)$h['assigned_night'] > 0) {
        $assignedHotelsCount++;
    } else {
        $unassignedHotelsCount++;
    }
}

$nightsCoveredCount = count($assignedNightMap);
$nightsMissingCount = max(0, $pkgDuration - $nightsCoveredCount);

echo json_encode([
    'status'  => 'success',
    'package' => [
        'id'             => $packageId,
        'encoded_id'     => base64_encode((string)$packageId),
        'title'          => $pkgTitle,
        'theme'          => $pkgTheme,
        'city'           => $pkgCity,
        'state'          => $pkgState,
        'country'        => $pkgCountry,
        'duration'       => $pkgDuration,
        'days'           => $pkgDays,
        'location_label' => $pkgCity ?: ($pkgState ?: 'Domestic'),
        'category'       => $pkgCategory,
        'link_url'       => $pkgRow['link_url'] ?? '',
        'linkUrl'        => $pkgRow['link_url'] ?? ''
    ],
    'metrics' => [
        'total_hotels'           => count($hotelList),
        'nights_covered'         => $nightsCoveredCount,
        'nights_missing'         => $nightsMissingCount,
        'assigned_hotels_count'  => $assignedHotelsCount,
        'unassigned_hotels_count'=> $unassignedHotelsCount,
        'mentioned_cities'       => $mentionedCities,
        'occupied_map'           => (object)$occupiedNightHotelNameMap,
        'city_counts'            => (object)$cityCountsMap
    ],
    'hotels'  => $hotelList
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
