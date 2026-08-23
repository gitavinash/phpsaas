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

$rawId = $_GET['packageId'] ?? ($_GET['id'] ?? 0);
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// 1. Fetch Package Record
$query = "
    SELECT 
        tp.tour_package_id,
        tp.tour_package_title,
        tp.tour_package_type,
        tp.tour_package_duration,
        tp.tour_package_theme,
        tp.tour_package_city,
        tp.tour_package_country,
        tp.tour_package_hotel,
        tp.tour_package_outstation_vehicle,
        tp.tour_package_localsightseen_vehicle,
        tp.tour_package_overview,
        tp.tour_package_itinerary,
        tp.tour_package_inclusions,
        tp.tour_package_exclusions,
        tp.tour_package_status,
        l.link_url
    FROM {$tblPrefix}tour_package AS tp
    LEFT JOIN {$tblPrefix}link AS l
        ON l.link_relation_id = tp.tour_package_id
        AND l.link_type = 'tour_package'
        AND l.link_site_id = {$siteId}
    WHERE tp.tour_package_id = '{$packageId}'
    AND tp.tour_package_site_id = {$siteId}
    LIMIT 1
";

$res = mysqli_query($connection, $query);
if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Tour package not found or access denied.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$row = mysqli_fetch_assoc($res);

// 2. Fetch Vehicles List
$vehiclesList = [];
$vehiclesRes = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM {$tblPrefix}vehicles ORDER BY vehicle_title ASC");
if ($vehiclesRes) {
    while ($vRow = mysqli_fetch_assoc($vehiclesRes)) {
        $vehiclesList[] = [
            'title' => $vRow['vehicle_title'],
            'type'  => $vRow['vehicle_type']
        ];
    }
}

// 3. Supported Travel Styles
$travelStyles = [
    'cultural'    => 'Cultural Travel',
    'adventure'   => 'Adventure Travel',
    'luxury'      => 'Luxury Travel',
    'solo'        => 'Solo Travel',
    'group'       => 'Group Travel',
    'eco'         => 'Eco/Responsible Travel',
    'honeymoon'   => 'Honeymoon Travel',
    'couple'      => 'Couple Travel',
    'family'      => 'Family Travel',
    'wellness'    => 'Wellness/Retreat Travel',
    'budget'      => 'Budget Travel',
    'roadtrip'    => 'Road Trip',
    'cruise'      => 'Cruise Travel',
    'backpacking' => 'Backpacking Travel',
    'religious'   => 'Religious Travel',
    'heritage'    => 'Heritage Travel'
];

// 4. Supported Hotel Types
$hotelTypes = [
    '3_star_hotel'   => '3 Star Hotel (Comfort)',
    '4_star_hotel'   => '4 Star Hotel (First Class)',
    '5_star_hotel'   => '5 Star Hotel (Luxury)',
    '2_star_hotel'   => '2 Star Hotel (Standard)',
    '1_star_hotel'   => '1 Star Hotel (Tourist)',
    'resorts'        => 'Resorts',
    'home_stay'      => 'Home stay',
    'apartments'     => 'Apartments',
    'capsule_hotel'  => 'Capsule hotel',
    'guest_house'    => 'Guest house',
    'inn'            => 'Inn',
    'hostel'         => 'Hostel',
    'house'          => 'House',
    'motel'          => 'Motel',
    'lodge'          => 'Lodge',
    'tent'           => 'Tent',
    'farm_stay'      => 'Farm stay'
];

// Decode Itinerary
$rawItinerary = json_decode($row['tour_package_itinerary'] ?? '[]', true);
if (!is_array($rawItinerary)) {
    $rawItinerary = [];
}

$duration = (int)($row['tour_package_duration'] ?? 0);
$nights = $duration;
$days = $duration + 1;

$response = [
    'status'        => 'success',
    'package'       => [
        'id'                     => (int)$row['tour_package_id'],
        'encoded_id'             => base64_encode($row['tour_package_id']),
        'title'                  => $row['tour_package_title'] ?? '',
        'type'                   => $row['tour_package_type'] ?? 'domestic',
        'duration'               => $duration,
        'nights'                 => $nights,
        'days'                   => $days,
        'theme'                  => $row['tour_package_theme'] ?? '',
        'city'                   => $row['tour_package_city'] ?? '',
        'country'                => $row['tour_package_country'] ?? '',
        'hotel'                  => $row['tour_package_hotel'] ?? '',
        'outstation_vehicle'     => $row['tour_package_outstation_vehicle'] ?? '',
        'localsightseen_vehicle' => $row['tour_package_localsightseen_vehicle'] ?? '',
        'overview'               => $row['tour_package_overview'] ?? '',
        'itinerary'              => $rawItinerary,
        'inclusions'             => $row['tour_package_inclusions'] ?? '',
        'exclusions'             => $row['tour_package_exclusions'] ?? '',
        'status'                 => (int)($row['tour_package_status'] ?? 1),
        'link_url'               => $row['link_url'] ?? ''
    ],
    'vehicles'      => $vehiclesList,
    'travel_styles' => $travelStyles,
    'hotel_types'   => $hotelTypes
];

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
