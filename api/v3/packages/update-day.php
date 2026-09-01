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
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

$packageIdRaw = $_POST['packageId'] ?? ($_POST['tourPackageId'] ?? ($_POST['id'] ?? 0));
if (is_numeric($packageIdRaw) && (int)$packageIdRaw > 0) {
    $packageId = (int)$packageIdRaw;
} else {
    $decoded = base64_decode((string)$packageIdRaw, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

$dayNumber = isset($_POST['dayNumber']) ? (int)$_POST['dayNumber'] : 1;
if ($dayNumber <= 0) {
    $dayNumber = 1;
}

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// 1. Fetch Existing Package Itinerary
$query = "
    SELECT tour_package_id, tour_package_itinerary
    FROM {$tblPrefix}tour_package
    WHERE tour_package_id = '{$packageId}'
      AND tour_package_site_id = {$siteId}
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
$existingItinerary = json_decode($row['tour_package_itinerary'] ?? '[]', true) ?: [];

// 2. Read Day Inputs (Only for this specific day)
$dayTitle    = isset($_POST['dayTitle']) ? trim($_POST['dayTitle']) : "Day {$dayNumber}";
$dayOverview = isset($_POST['dayContent']) ? trim($_POST['dayContent']) : (isset($_POST['dayOverview']) ? trim($_POST['dayOverview']) : '');
$dayStay     = isset($_POST['dayStay']) ? trim($_POST['dayStay']) : '';
$dayHotelRaw = isset($_POST['dayHotel']) ? trim($_POST['dayHotel']) : (isset($_POST['hotel']) ? trim($_POST['hotel']) : '');
$dayRoomTypeRaw = isset($_POST['dayRoomType']) ? trim($_POST['dayRoomType']) : (isset($_POST['roomType']) ? trim($_POST['roomType']) : '');

$hotelId = 0;
$hotelTitle = '';
if (is_numeric($dayHotelRaw) && (int)$dayHotelRaw > 0) {
    $hotelId = (int)$dayHotelRaw;
    $hQuery = mysqli_query($connection, "SELECT property_title FROM {$tblPrefix}property WHERE property_id = '{$hotelId}' AND property_site_id = {$siteId} LIMIT 1");
    if ($hQuery && ($hRow = mysqli_fetch_assoc($hQuery))) {
        $hotelTitle = stripslashes($hRow['property_title']);
    }
} elseif (!empty($dayHotelRaw)) {
    if (strpos($dayHotelRaw, ':') !== false) {
        $parts = explode(':', $dayHotelRaw, 2);
        if (is_numeric($parts[0])) {
            $hotelId = (int)$parts[0];
            $hotelTitle = trim($parts[1]);
        } else {
            $hotelTitle = $dayHotelRaw;
        }
    } else {
        $hotelTitle = $dayHotelRaw;
    }
}

$roomTypeId = 0;
$roomTypeName = '';
if (!empty($dayRoomTypeRaw)) {
    if (strpos($dayRoomTypeRaw, ':') !== false) {
        $rParts = explode(':', $dayRoomTypeRaw, 2);
        if (is_numeric($rParts[0])) {
            $roomTypeId = (int)$rParts[0];
            $roomTypeName = trim($rParts[1]);
        } else {
            $roomTypeName = $dayRoomTypeRaw;
        }
    } else {
        $roomTypeName = $dayRoomTypeRaw;
    }
}

// 3. Update ONLY the target day in the itinerary array
$targetIndex = $dayNumber - 1;
while (count($existingItinerary) <= $targetIndex) {
    $existingItinerary[] = [
        'day'           => "Day " . (count($existingItinerary) + 1),
        'overview'      => '',
        'stay'          => '',
        'city'          => '',
        'hotel_id'      => 0,
        'hotel_title'   => '',
        'room_type_id'  => 0,
        'room_type'     => '',
        'feature_image' => '',
        'gallery'       => []
    ];
}

// Preserve existing feature_image and gallery for this day if present
$prevFeature = $existingItinerary[$targetIndex]['feature_image'] ?? '';
$prevGallery = $existingItinerary[$targetIndex]['gallery'] ?? [];

$existingItinerary[$targetIndex] = [
    'day'           => $dayTitle,
    'overview'      => $dayOverview,
    'stay'          => $dayStay,
    'city'          => $dayStay,
    'hotel_id'      => $hotelId,
    'hotel_title'   => $hotelTitle,
    'room_type_id'  => $roomTypeId,
    'room_type'     => $roomTypeName,
    'feature_image' => $prevFeature,
    'gallery'       => $prevGallery
];

// Synchronize tour_package_hotel for this night
$clearTph = "DELETE FROM {$tblPrefix}tour_package_hotel WHERE tph_package_id = '{$packageId}' AND tph_night = '{$dayNumber}' AND tph_site_id = {$siteId}";
mysqli_query($connection, $clearTph);

if ($hotelId > 0) {
    $escRoomType = mysqli_real_escape_string($connection, $roomTypeName);
    $insertTph = "INSERT INTO {$tblPrefix}tour_package_hotel (tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) 
                  VALUES ('{$hotelId}', '{$packageId}', '{$roomTypeId}', '{$escRoomType}', '{$dayNumber}', 1, {$siteId})";
    mysqli_query($connection, $insertTph);
}

$encodedItinerary = mysqli_real_escape_string($connection, json_encode($existingItinerary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$totalDays = count($existingItinerary);
$calculatedDuration = max(0, $totalDays - 1);

// 4. Update tour_package_itinerary and tour_package_duration in Database
$updatePkgQuery = "
    UPDATE {$tblPrefix}tour_package
    SET 
        tour_package_itinerary = '{$encodedItinerary}',
        tour_package_duration  = {$calculatedDuration}
    WHERE tour_package_id = '{$packageId}'
      AND tour_package_site_id = {$siteId}
    LIMIT 1
";

if (mysqli_query($connection, $updatePkgQuery)) {
    echo json_encode([
        'status'     => 'success',
        'message'    => "Day {$dayNumber} details updated successfully.",
        'dayNumber'  => $dayNumber,
        'duration'   => $calculatedDuration,
        'totalDays'  => $totalDays,
        'day'        => $existingItinerary[$targetIndex]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while updating day details: ' . mysqli_error($connection)
    ], JSON_UNESCAPED_SLASHES);
}
