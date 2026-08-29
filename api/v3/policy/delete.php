<?php
/**
 * V3 Delete Policy API
 * Endpoint: /api/v3/policy/delete.php
 * Method: POST or GET
 * Params: id (encoded or numeric page_id)
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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
if ($siteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $siteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($siteId <= 0 && isset($connection)) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if (!empty($host)) {
        $escapedHost = mysqli_real_escape_string($connection, $host);
        $sQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
        if ($sQuery && $sRow = mysqli_fetch_assoc($sQuery)) {
            $siteId = (int)$sRow['site_id'];
        }
    }
}

$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?: [];

$rawId = $_POST['id'] ?? ($_REQUEST['id'] ?? ($jsonData['id'] ?? ''));
$pageId = 0;

if (is_numeric($rawId) && (int)$rawId > 0) {
    $pageId = (int)$rawId;
} elseif (!empty($rawId)) {
    $decoded = base64_decode($rawId, true);
    if ($decoded && is_numeric($decoded)) {
        $pageId = (int)$decoded;
    }
}

if ($pageId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'A valid policy ID is required to delete.']);
    exit;
}

$siteFilter = $siteId > 0 ? "AND l.link_site_id = {$siteId}" : "";

// Check policy existence
$checkQuery = mysqli_query($connection, "
    SELECT p.page_id, l.link_id 
    FROM {$tblPrefix}page p
    INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
    WHERE p.page_id = {$pageId} AND p.page_type = 'policy' AND l.link_type = 'page' {$siteFilter}
    LIMIT 1
");

if (!$checkQuery || mysqli_num_rows($checkQuery) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Policy not found or already deleted.']);
    exit;
}

$row = mysqli_fetch_assoc($checkQuery);
$linkId = (int)$row['link_id'];

mysqli_begin_transaction($connection);

try {
    mysqli_query($connection, "DELETE FROM {$tblPrefix}page WHERE page_id = {$pageId}");
    mysqli_query($connection, "DELETE FROM {$tblPrefix}link WHERE link_id = {$linkId} AND link_type = 'page'");
    mysqli_query($connection, "DELETE FROM {$tblPrefix}meta WHERE meta_relation_id = {$linkId} AND meta_type = 'page'");

    mysqli_commit($connection);

    echo json_encode([
        'status' => 'success',
        'msg' => 'Policy deleted successfully.'
    ]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Error deleting policy: ' . $e->getMessage()
    ]);
}
