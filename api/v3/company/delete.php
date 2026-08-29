<?php
/**
 * V3 Company Soft Delete / Status API
 * Endpoint: /api/v3/company/delete.php
 * Method: POST
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;
if ($siteId <= 0 && isset($_SESSION['site_id'])) {
    $siteId = (int)$_SESSION['site_id'];
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: $_POST;

$companyId = (int)($data['id'] ?? ($data['company_id'] ?? 0));
if ($companyId <= 0 && !empty($data['encoded_id'])) {
    $companyId = (int)base64_decode($data['encoded_id']);
}

if ($companyId <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Valid company ID is required.'
    ]);
    exit;
}

// Soft delete: update status to 0
$query = "UPDATE {$tblPrefix}company SET company_status = 0 WHERE company_id = {$companyId} AND company_site_id = {$siteId} LIMIT 1";
if (mysqli_query($connection, $query)) {
    echo json_encode([
        'status' => 'success',
        'msg'    => 'Company deleted successfully.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Could not delete company. Please try again.'
    ]);
}
