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

$dayNumber = isset($_POST['dayNumber']) ? (int)$_POST['dayNumber'] : (isset($_POST['day']) ? (int)$_POST['day'] : 0);

if ($packageId <= 0 || $dayNumber <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing package ID or day number.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// 1. Fetch Existing Itinerary
$query = "
    SELECT tour_package_id, tour_package_itinerary, tour_package_duration
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
$itinerary = json_decode($row['tour_package_itinerary'] ?? '[]', true) ?: [];

if (count($itinerary) <= 1) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Cannot delete the only day. A package must have at least one day.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$targetIndex = $dayNumber - 1;
if ($targetIndex >= 0 && $targetIndex < count($itinerary)) {
    array_splice($itinerary, $targetIndex, 1);
    $totalDays = count($itinerary);
    $calculatedDuration = max(0, $totalDays - 1);
    $encodedItinerary = mysqli_real_escape_string($connection, json_encode($itinerary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // 2. Update Itinerary and tour_package_duration
    $updateQuery = "
        UPDATE {$tblPrefix}tour_package
        SET 
            tour_package_itinerary = '{$encodedItinerary}',
            tour_package_duration  = {$calculatedDuration}
        WHERE tour_package_id = '{$packageId}'
          AND tour_package_site_id = {$siteId}
        LIMIT 1
    ";

    if (mysqli_query($connection, $updateQuery)) {
        echo json_encode([
            'status'        => 'success',
            'message'       => "Day {$dayNumber} has been successfully deleted from the itinerary.",
            'deletedDay'    => $dayNumber,
            'totalDays'     => $totalDays,
            'duration'      => $calculatedDuration
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Database error while deleting day: ' . mysqli_error($connection)
        ], JSON_UNESCAPED_SLASHES);
    }
} else {
    // Day wasn't in DB yet (unsaved day card), return success
    $totalDays = count($itinerary);
    $calculatedDuration = max(0, $totalDays - 1);
    echo json_encode([
        'status'        => 'success',
        'message'       => "Day {$dayNumber} removed from the itinerary.",
        'deletedDay'    => $dayNumber,
        'totalDays'     => $totalDays,
        'duration'      => $calculatedDuration
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
