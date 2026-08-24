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

$packageIdRaw = $_POST['packageId'] ?? ($_POST['tour_id'] ?? ($_POST['id'] ?? 0));
if (is_numeric($packageIdRaw) && (int)$packageIdRaw > 0) {
    $packageId = (int)$packageIdRaw;
} else {
    $decoded = base64_decode((string)$packageIdRaw, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

$dayNumber = isset($_POST['day']) ? (int)$_POST['day'] : (isset($_POST['night']) ? (int)$_POST['night'] : (isset($_POST['dayNumber']) ? (int)$_POST['dayNumber'] : 1));
$stayText   = isset($_POST['stay']) ? trim($_POST['stay']) : (isset($_POST['dayStay']) ? trim($_POST['dayStay']) : '');

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// 1. Fetch Existing Itinerary
$pkgQuery = "SELECT tour_package_itinerary FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1";
$pkgRes = mysqli_query($connection, $pkgQuery);
if (!$pkgRes || mysqli_num_rows($pkgRes) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Tour package not found.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$pkgRow = mysqli_fetch_assoc($pkgRes);
$itinerary = json_decode($pkgRow['tour_package_itinerary'] ?? '[]', true) ?: [];

// 2. Update Itinerary Array Stay Field
$targetIndex = $dayNumber - 1;
if ($targetIndex >= 0) {
    while (count($itinerary) <= $targetIndex) {
        $itinerary[] = [
            'day'           => "Day " . (count($itinerary) + 1),
            'overview'      => '',
            'stay'          => '',
            'feature_image' => '',
            'gallery'       => []
        ];
    }
    $itinerary[$targetIndex]['stay'] = $stayText;

    $encodedItinerary = mysqli_real_escape_string($connection, json_encode($itinerary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    $updateQuery = "UPDATE {$tblPrefix}tour_package SET tour_package_itinerary = '{$encodedItinerary}' WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1";
    mysqli_query($connection, $updateQuery);
}

echo json_encode([
    'status'     => 'success',
    'message'    => "Stay city assigned to Day {$dayNumber} successfully.",
    'packageId'  => $packageId,
    'day'        => $dayNumber,
    'stay'       => $stayText
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
