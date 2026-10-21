<?php
/**
 * V3 Vehicles Delete API
 * Path: /api/v3/vehicles/delete.php
 * Handles deleting a vehicle model safely
 */

header("Content-type: application/json; charset=utf-8");

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
}
if (!isset($connection)) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
if ($siteId <= 0 && !empty($_SERVER['HTTP_HOST'])) {
    $host = mysqli_real_escape_string($connection, strtolower(trim($_SERVER['HTTP_HOST'])));
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE LOWER(site_domain) = '{$host}' OR LOWER(site_url) = '{$host}' LIMIT 1");
    if ($siteQuery && ($siteRow = mysqli_fetch_assoc($siteQuery))) {
        $siteId = (int) $siteRow['site_id'];
    }
}

$input = file_get_contents('php://input');
$json  = json_decode($input, true);
$data  = is_array($json) ? array_merge($_POST, $json) : $_POST;

$rawId = $data['vehicle_id'] ?? ($data['id'] ?? ($_GET['id'] ?? ($_GET['vehicle_id'] ?? 0)));
$vehicleId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $vehicleId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $vehicleId = (int)$decoded;
    }
}

if ($vehicleId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid or missing vehicle ID.']);
    exit;
}

// Check if vehicle exists and belongs to this site
$checkSql = "SELECT * FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId} AND vehicle_site_id = {$siteId} LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Vehicle record not found in your fleet.']);
    exit;
}

$vehicle = mysqli_fetch_assoc($checkRes);
if (isset($vehicle['vehicle_is_deletable']) && (int)$vehicle['vehicle_is_deletable'] === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'This vehicle cannot be deleted.']);
    exit;
}

$delSql = "DELETE FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId} AND vehicle_site_id = {$siteId}";
if (mysqli_query($connection, $delSql)) {
    echo json_encode([
        'status'     => 'success',
        'msg'        => 'Vehicle deleted successfully.',
        'vehicle_id' => $vehicleId
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to delete vehicle record.',
        'debug'  => mysqli_error($connection)
    ]);
}
