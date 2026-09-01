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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method Not Allowed. Use POST.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$packageIdRaw = $_POST['packageId'] ?? ($_POST['tourPackageId'] ?? ($_POST['id'] ?? 0));
if (is_numeric($packageIdRaw) && (int)$packageIdRaw > 0) {
    $packageId = (int)$packageIdRaw;
} else {
    $decoded = base64_decode((string)$packageIdRaw, true);
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

// 2. Read Posted Arrays
$dayTitles       = isset($_POST['dayTitle']) && is_array($_POST['dayTitle']) ? $_POST['dayTitle'] : [];
$dayContents     = isset($_POST['dayContent']) && is_array($_POST['dayContent']) ? $_POST['dayContent'] : [];
$dayStays        = isset($_POST['dayStay']) && is_array($_POST['dayStay']) ? $_POST['dayStay'] : [];
$dayHotels       = isset($_POST['dayHotel']) && is_array($_POST['dayHotel']) ? $_POST['dayHotel'] : [];
$dayRoomTypes    = isset($_POST['dayRoomType']) && is_array($_POST['dayRoomType']) ? $_POST['dayRoomType'] : [];
$dayFeatureImgs  = isset($_POST['dayFeatureImage']) && is_array($_POST['dayFeatureImage']) ? $_POST['dayFeatureImage'] : [];
$dayGalleries    = isset($_POST['dayGallery']) && is_array($_POST['dayGallery']) ? $_POST['dayGallery'] : [];

$totalDays = count($dayTitles);
if ($totalDays === 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'No itinerary days provided to update.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Clear all existing hotel assignments for this package to re-sync
mysqli_query($connection, "DELETE FROM {$tblPrefix}tour_package_hotel WHERE tph_package_id = '{$packageId}' AND tph_site_id = {$siteId}");

$newItinerary = [];

for ($i = 0; $i < $totalDays; $i++) {
    $dayNum     = $i + 1;
    $title      = stripslashes(trim($dayTitles[$i] ?? "Day {$dayNum}"));
    $overview   = stripslashes(trim($dayContents[$i] ?? ""));
    $stay       = stripslashes(trim($dayStays[$i] ?? ""));
    $featureImg = trim($dayFeatureImgs[$i] ?? ($existingItinerary[$i]['feature_image'] ?? ""));

    $hotelRaw    = trim($dayHotels[$i] ?? "");
    $roomTypeRaw = trim($dayRoomTypes[$i] ?? "");

    $hotelId = 0;
    $hotelTitle = '';
    if (is_numeric($hotelRaw) && (int)$hotelRaw > 0) {
        $hotelId = (int)$hotelRaw;
        $hQ = mysqli_query($connection, "SELECT property_title FROM {$tblPrefix}property WHERE property_id = '{$hotelId}' AND property_site_id = {$siteId} LIMIT 1");
        if ($hQ && ($hRow = mysqli_fetch_assoc($hQ))) {
            $hotelTitle = stripslashes($hRow['property_title']);
        }
    } elseif (!empty($hotelRaw)) {
        if (strpos($hotelRaw, ':') !== false) {
            $parts = explode(':', $hotelRaw, 2);
            if (is_numeric($parts[0])) {
                $hotelId = (int)$parts[0];
                $hotelTitle = trim($parts[1]);
            } else {
                $hotelTitle = $hotelRaw;
            }
        } else {
            $hotelTitle = $hotelRaw;
        }
    }

    $roomTypeId = 0;
    $roomTypeName = '';
    if (!empty($roomTypeRaw)) {
        if (strpos($roomTypeRaw, ':') !== false) {
            $rParts = explode(':', $roomTypeRaw, 2);
            if (is_numeric($rParts[0])) {
                $roomTypeId = (int)$rParts[0];
                $roomTypeName = trim($rParts[1]);
            } else {
                $roomTypeName = $roomTypeRaw;
            }
        } else {
            $roomTypeName = $roomTypeRaw;
        }
    }

    // Gallery
    $rawGal = $dayGalleries[$i] ?? ($existingItinerary[$i]['gallery'] ?? []);
    if (is_string($rawGal)) {
        $parsedGal = json_decode($rawGal, true);
        $gallery = is_array($parsedGal) ? $parsedGal : array_filter(array_map('trim', explode(',', $rawGal)));
    } else {
        $gallery = is_array($rawGal) ? $rawGal : [];
    }

    // Sync tour_package_hotel if hotel chosen
    if ($hotelId > 0) {
        $escRoomType = mysqli_real_escape_string($connection, $roomTypeName);
        $insertTph = "INSERT INTO {$tblPrefix}tour_package_hotel (tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) 
                      VALUES ('{$hotelId}', '{$packageId}', '{$roomTypeId}', '{$escRoomType}', '{$dayNum}', 1, {$siteId})";
        mysqli_query($connection, $insertTph);
    }

    $newItinerary[] = [
        'day'           => $title,
        'overview'      => $overview,
        'stay'          => $stay,
        'city'          => $stay,
        'hotel_id'      => $hotelId,
        'hotel_title'   => $hotelTitle,
        'room_type_id'  => $roomTypeId,
        'room_type'     => $roomTypeName,
        'feature_image' => $featureImg,
        'gallery'       => array_values($gallery)
    ];
}

$encodedItinerary = mysqli_real_escape_string($connection, json_encode($newItinerary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$calculatedDuration = max(0, $totalDays - 1);

// 3. Update Database
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
        'message'    => "All {$totalDays} days itinerary updated successfully.",
        'totalDays'  => $totalDays,
        'duration'   => $calculatedDuration,
        'itinerary'  => $newItinerary
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while updating all days itinerary: ' . mysqli_error($connection)
    ], JSON_UNESCAPED_SLASHES);
}
