<?php
header("Content-type: application/json; charset=utf-8");
$input = file_get_contents('php://input');
$data = json_decode($input, true) ?? $_POST;
$rawTransporterId = $data['transporter_id'] ?? 0;
$transporterId = is_numeric($rawTransporterId) ? (int)$rawTransporterId : 0;
if ($transporterId <= 0 && !empty($rawTransporterId)) {
    $decodedTransporterId = base64_decode((string)$rawTransporterId, true);
    if ($decodedTransporterId !== false && is_numeric($decodedTransporterId)) {
        $transporterId = (int)$decodedTransporterId;
    }
}
$fleetTypes = $data['fleet_types'] ?? [];
if ($transporterId <= 0 || !is_array($fleetTypes)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Transporter and fleet selections are required.']);
    exit;
}
if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}
if (!isset($connection) || !$connection) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection is unavailable.']);
    exit;
}
$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$fleetTypes = array_values(array_filter(array_map('trim', $fleetTypes)));
$description = mysqli_query($connection, "SELECT transporter_description FROM {$tblPrefix}transporter WHERE transporter_id = {$transporterId} LIMIT 1");
$row = $description ? mysqli_fetch_assoc($description) : null;
if (!$row) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Transporter not found.']);
    exit;
}
$raw = preg_replace('/\s*Fleet Types:\s*[^|\]]*(?:\s*\|\s*)?/i', '', $row['transporter_description'] ?? '');
$raw = preg_replace('/\[\s*\]/', '', $raw);
$meta = $fleetTypes ? ' [Fleet Types: ' . implode(', ', $fleetTypes) . ']' : '';
$safe = mysqli_real_escape_string($connection, trim($raw) . $meta);
if (!mysqli_query($connection, "UPDATE {$tblPrefix}transporter SET transporter_description = '{$safe}' WHERE transporter_id = {$transporterId}")) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Fleet assignments could not be saved.']);
    exit;
}
echo json_encode(['status' => 'success', 'msg' => 'Fleet assignments updated.']);
