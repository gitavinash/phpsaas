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
$packageName            = isset($_POST['packageName']) ? mysqli_real_escape_string($connection, trim($_POST['packageName'])) : '';
$travelStyle            = isset($_POST['travelStyle']) && $_POST['travelStyle'] !== 'select' ? mysqli_real_escape_string($connection, trim($_POST['travelStyle'])) : 'standard';
$packageType            = isset($_POST['packageType']) ? mysqli_real_escape_string($connection, trim($_POST['packageType'])) : 'domestic';
$hotel                  = isset($_POST['hotel']) && $_POST['hotel'] !== 'select' ? mysqli_real_escape_string($connection, trim($_POST['hotel'])) : '';
$outstationVehicle      = isset($_POST['outstationVehicle']) && $_POST['outstationVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(trim($_POST['outstationVehicle']))) : '';
$localSighseeignVehicle = isset($_POST['localSighseeignVehicle']) && $_POST['localSighseeignVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(trim($_POST['localSighseeignVehicle']))) : '';
$overview               = isset($_POST['overview']) ? mysqli_real_escape_string($connection, trim($_POST['overview'])) : '';
$inclusion              = isset($_POST['inclusion']) ? mysqli_real_escape_string($connection, trim($_POST['inclusion'])) : '';
$exclusion              = isset($_POST['exclusion']) ? mysqli_real_escape_string($connection, trim($_POST['exclusion'])) : '';

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
$dayFeatureImgs  = isset($_POST['dayFeatureImage']) && is_array($_POST['dayFeatureImage']) ? $_POST['dayFeatureImage'] : [];
$dayGalleries    = isset($_POST['dayGallery']) && is_array($_POST['dayGallery']) ? $_POST['dayGallery'] : [];

// Fetch existing row to preserve itinerary and duration if not posted
$existingPkgRes = mysqli_query($connection, "SELECT tour_package_itinerary, tour_package_duration FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1");
$existingRow = $existingPkgRes ? mysqli_fetch_assoc($existingPkgRes) : [];

if (!empty($dayTitles)) {
    $itineraryList = [];
    $totalDays = count($dayTitles);

    for ($i = 0; $i < $totalDays; $i++) {
        $dayNum     = $i + 1;
        $title      = trim($dayTitles[$i] ?? "Day {$dayNum}");
        $overview   = trim($dayContents[$i] ?? "");
        $stay       = trim($dayStays[$i] ?? "");
        $featureImg = trim($dayFeatureImgs[$i] ?? "");
        
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
            'feature_image' => $featureImg,
            'gallery'       => array_values($gallery)
        ];
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
