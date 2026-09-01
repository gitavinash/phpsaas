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

// 1. Extract & Sanitize Package ID
$rawId = $_POST['tourPackageId'] ?? ($_POST['id'] ?? 0);
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

// 2. Read Form Payload
$packageName            = isset($_POST['packageName']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['packageName']))) : '';
$travelStyle            = isset($_POST['travelStyle']) && $_POST['travelStyle'] !== 'select' ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['travelStyle']))) : 'standard';
$template               = isset($_POST['tourPackageTemplate']) && $_POST['tourPackageTemplate'] !== 'select' ? mysqli_real_escape_string($connection, trim((string)$_POST['tourPackageTemplate'])) : 'mit';
$packageType            = isset($_POST['packageType']) ? mysqli_real_escape_string($connection, trim((string)$_POST['packageType'])) : 'domestic';
$hotel                  = isset($_POST['hotel']) && $_POST['hotel'] !== 'select' ? mysqli_real_escape_string($connection, trim((string)$_POST['hotel'])) : '';
$outstationVehicle      = isset($_POST['outstationVehicle']) && $_POST['outstationVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(stripslashes(trim((string)$_POST['outstationVehicle'])))) : '';
$localSighseeignVehicle = isset($_POST['localSighseeignVehicle']) && $_POST['localSighseeignVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(stripslashes(trim((string)$_POST['localSighseeignVehicle'])))) : '';
$overview               = isset($_POST['overview']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['overview']))) : '';
$inclusion              = isset($_POST['inclusion']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['inclusion']))) : '';
$exclusion              = isset($_POST['exclusion']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['exclusion']))) : '';

if (empty($packageName)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Package title cannot be empty.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// 3. Process Day-Wise Itinerary Items
$dayTitles       = isset($_POST['dayTitle']) && is_array($_POST['dayTitle']) ? $_POST['dayTitle'] : [];
$dayContents     = isset($_POST['dayContent']) && is_array($_POST['dayContent']) ? $_POST['dayContent'] : [];
$dayStays        = isset($_POST['dayStay']) && is_array($_POST['dayStay']) ? $_POST['dayStay'] : [];
$dayHotels       = isset($_POST['dayHotel']) && is_array($_POST['dayHotel']) ? $_POST['dayHotel'] : [];
$dayRoomTypes    = isset($_POST['dayRoomType']) && is_array($_POST['dayRoomType']) ? $_POST['dayRoomType'] : [];
$dayFeatureImgs  = isset($_POST['dayFeatureImage']) && is_array($_POST['dayFeatureImage']) ? $_POST['dayFeatureImage'] : [];
$dayGalleries    = isset($_POST['dayGallery']) && is_array($_POST['dayGallery']) ? $_POST['dayGallery'] : [];

// Fetch existing row to preserve itinerary and duration if not posted
$existingPkgRes = mysqli_query($connection, "SELECT tour_package_itinerary, tour_package_duration FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1");
$existingRow = $existingPkgRes ? mysqli_fetch_assoc($existingPkgRes) : [];

if (!empty($dayTitles)) {
    $itineraryList = [];
    $totalDays = count($dayTitles);

    // If day hotels are supplied, prepare to sync tour_package_hotel
    if (!empty($dayHotels)) {
        mysqli_query($connection, "DELETE FROM {$tblPrefix}tour_package_hotel WHERE tph_package_id = '{$packageId}' AND tph_site_id = {$siteId}");
    }

    for ($i = 0; $i < $totalDays; $i++) {
        $dayNum     = $i + 1;
        $title      = stripslashes(trim($dayTitles[$i] ?? "Day {$dayNum}"));
        $overview   = stripslashes(trim($dayContents[$i] ?? ""));
        $stay       = stripslashes(trim($dayStays[$i] ?? ""));
        $featureImg = trim($dayFeatureImgs[$i] ?? "");

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
        
        $rawGal = $dayGalleries[$i] ?? [];
        if (is_string($rawGal)) {
            $parsedGal = json_decode($rawGal, true);
            $gallery = is_array($parsedGal) ? $parsedGal : array_filter(array_map('trim', explode(',', $rawGal)));
        } else {
            $gallery = is_array($rawGal) ? $rawGal : [];
        }

        $itineraryList[] = [
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

        if ($hotelId > 0 && !empty($dayHotels)) {
            $escRoomType = mysqli_real_escape_string($connection, $roomTypeName);
            $insertTph = "INSERT INTO {$tblPrefix}tour_package_hotel (tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) 
                          VALUES ('{$hotelId}', '{$packageId}', '{$roomTypeId}', '{$escRoomType}', '{$dayNum}', 1, {$siteId})";
            mysqli_query($connection, $insertTph);
        }
    }

    $encodedItinerary = mysqli_real_escape_string($connection, json_encode($itineraryList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    $calculatedDuration = max(0, $totalDays - 1);
} else {
    $encodedItinerary = mysqli_real_escape_string($connection, $existingRow['tour_package_itinerary'] ?? '[]');
    $calculatedDuration = (int)($existingRow['tour_package_duration'] ?? 0);
    $parsedExisting = json_decode($existingRow['tour_package_itinerary'] ?? '[]', true) ?: [];
    $totalDays = count($parsedExisting);
}

// 4. Update Database
$updateQuery = "
    UPDATE {$tblPrefix}tour_package 
    SET 
        tour_package_title                  = '{$packageName}',
        tour_package_theme                  = '{$travelStyle}',
        tour_package_template               = '{$template}',
        tour_package_type                   = '{$packageType}',
        tour_package_duration               = {$calculatedDuration},
        tour_package_hotel                  = '{$hotel}',
        tour_package_outstation_vehicle     = '{$outstationVehicle}',
        tour_package_localsightseen_vehicle = '{$localSighseeignVehicle}',
        tour_package_overview               = '{$overview}',
        tour_package_inclusions             = '{$inclusion}',
        tour_package_exclusions             = '{$exclusion}',
        tour_package_itinerary              = '{$encodedItinerary}'
    WHERE tour_package_id = '{$packageId}'
      AND tour_package_site_id = {$siteId}
    LIMIT 1
";

if (mysqli_query($connection, $updateQuery)) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Tour itinerary updated successfully.',
        'packageId'=> $packageId,
        'encodedId'=> base64_encode($packageId),
        'duration' => $calculatedDuration,
        'days'     => $totalDays
    ], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE ? mysqli_error($connection) : 'Database update failed. Please try again.'
    ], JSON_UNESCAPED_SLASHES);
}
