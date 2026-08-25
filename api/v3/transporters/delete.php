<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
    if (file_exists($docRoot . '/lib/modules/siteStatus.php')) {
        require_once $docRoot . '/lib/modules/siteStatus.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Method Not Allowed. Use POST.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$input = file_get_contents('php://input');
$data  = json_decode($input, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$rawId = $data['transporter_id'] ?? ($data['id'] ?? 0);
if (is_numeric($rawId) && (int)$rawId > 0) {
    $transporterId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $transporterId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

if ($transporterId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Invalid transporter ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Delete relations first
mysqli_query($connection, "
    DELETE FROM {$tblPrefix}tour_package_transporter 
    WHERE tpt_transporter_id = {$transporterId} AND tpt_site_id = {$siteId}
");

// Delete transporter
$delSql = "
    DELETE FROM {$tblPrefix}transporter 
    WHERE transporter_id = {$transporterId} AND transporter_site_id = {$siteId}
";

if (mysqli_query($connection, $delSql)) {
    echo json_encode([
        'status' => 'success',
        'msg'    => 'Transporter deleted successfully.'
    ], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to delete transporter.'
    ], JSON_UNESCAPED_SLASHES);
}
