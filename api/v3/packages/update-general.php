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

$packageName            = isset($_POST['packageName']) ? mysqli_real_escape_string($connection, trim($_POST['packageName'])) : '';
$travelStyle            = isset($_POST['travelStyle']) && $_POST['travelStyle'] !== 'select' ? mysqli_real_escape_string($connection, trim($_POST['travelStyle'])) : 'standard';
$packageType            = isset($_POST['packageType']) ? mysqli_real_escape_string($connection, trim($_POST['packageType'])) : 'domestic';
$hotel                  = isset($_POST['hotel']) && $_POST['hotel'] !== 'select' ? mysqli_real_escape_string($connection, trim($_POST['hotel'])) : '';
$outstationVehicle      = isset($_POST['outstationVehicle']) && $_POST['outstationVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(trim($_POST['outstationVehicle']))) : '';
$localSighseeignVehicle = isset($_POST['localSighseeignVehicle']) && $_POST['localSighseeignVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(trim($_POST['localSighseeignVehicle']))) : '';
$overview               = isset($_POST['overview']) ? mysqli_real_escape_string($connection, trim($_POST['overview'])) : '';

if (empty($packageName)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Package title cannot be empty.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$updateQuery = "
    UPDATE {$tblPrefix}tour_package
    SET 
        tour_package_title                  = '{$packageName}',
        tour_package_theme                  = '{$travelStyle}',
        tour_package_type                   = '{$packageType}',
        tour_package_hotel                  = '{$hotel}',
        tour_package_outstation_vehicle     = '{$outstationVehicle}',
        tour_package_localsightseen_vehicle = '{$localSighseeignVehicle}',
        tour_package_overview               = '{$overview}'
    WHERE tour_package_id = '{$packageId}'
      AND tour_package_site_id = {$siteId}
    LIMIT 1
";

if (mysqli_query($connection, $updateQuery)) {
    echo json_encode([
        'status'      => 'success',
        'message'     => 'Itinerary updated successfully.',
        'packageId'   => $packageId,
        'packageName' => stripslashes($packageName)
    ], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while updating itinerary: ' . mysqli_error($connection)
    ], JSON_UNESCAPED_SLASHES);
}
