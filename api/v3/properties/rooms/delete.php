<?php
/**
 * V3 Hotel Property Rooms Delete API
 * Path: /api/v3/properties/rooms/delete.php
 * POST / GET: Delete a room from a property
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4);
    if (file_exists($docRoot . '/config.php'))                  require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}

/** @var mysqli $connection */
if (!isset($connection) || !($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection unavailable.']);
    exit();
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);

$rawRoomId = $_POST['room_id'] ?? ($_GET['room_id'] ?? ($_GET['id'] ?? 0));
$roomId    = 0;
if (is_numeric($rawRoomId) && (int)$rawRoomId > 0) {
    $roomId = (int)$rawRoomId;
} elseif (!empty($rawRoomId)) {
    $dec = base64_decode($rawRoomId, true);
    if ($dec !== false && is_numeric($dec) && (int)$dec > 0) $roomId = (int)$dec;
}

if ($roomId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Valid room ID is required.']);
    exit();
}

$sql = "DELETE FROM {$prefix}room WHERE room_id = {$roomId}";
if ($siteId > 0) {
    $sql .= " AND room_site_id = {$siteId}";
}

if (mysqli_query($connection, $sql)) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'msg' => 'Room deleted successfully.']);
    exit();
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Failed to delete room.', 'debug' => mysqli_error($connection)]);
    exit();
}
