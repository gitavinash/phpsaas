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

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$inclusion = isset($_POST['inclusion']) ? mysqli_real_escape_string($connection, trim($_POST['inclusion'])) : '';
$exclusion = isset($_POST['exclusion']) ? mysqli_real_escape_string($connection, trim($_POST['exclusion'])) : '';

$updateQuery = "
    UPDATE {$tblPrefix}tour_package
    SET 
        tour_package_inclusions = '{$inclusion}',
        tour_package_exclusions = '{$exclusion}'
    WHERE tour_package_id = '{$packageId}'
      AND tour_package_site_id = {$siteId}
    LIMIT 1
";

if (mysqli_query($connection, $updateQuery)) {
    echo json_encode([
        'status'    => 'success',
        'message'   => 'Inclusions & Exclusions updated successfully.',
        'packageId' => $packageId
    ], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while updating policies: ' . mysqli_error($connection)
    ], JSON_UNESCAPED_SLASHES);
}
