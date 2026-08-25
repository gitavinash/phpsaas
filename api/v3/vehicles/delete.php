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

// Check if vehicle exists and is deletable
$checkSql = "SELECT * FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId} LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Vehicle record not found.']);
    exit;
}

$vehicle = mysqli_fetch_assoc($checkRes);
if (isset($vehicle['vehicle_is_deletable']) && (int)$vehicle['vehicle_is_deletable'] === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'This default system vehicle cannot be deleted.']);
    exit;
}

$delSql = "DELETE FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId}";
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
