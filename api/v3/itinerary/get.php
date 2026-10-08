<?php
/**
 * V3 Itinerary Get API
 * Endpoint: /api/v3/itinerary/get.php
 * Method: GET
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$rawId = isset($_GET['id']) ? trim((string)$_GET['id']) : (isset($_GET['quotation_id']) ? trim((string)$_GET['quotation_id']) : '');
if (empty($rawId) && isset($_GET['encoded_id'])) {
    $decoded = base64_decode(trim((string)$_GET['encoded_id']), true);
    if ($decoded !== false && is_numeric($decoded)) {
        $rawId = $decoded;
    }
}
$id = is_numeric($rawId) ? (int)$rawId : 0;
$leadId = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;

if ($id <= 0 && $leadId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Quotation ID or Lead ID is required.'
    ]);
    exit;
}

$whereClause = ($id > 0)
    ? "q.quotation_id = {$id}"
    : "q.quotation_ref_id = '{$leadId}' AND q.quotation_ref_type = 'enquiry'";

$siteClause = ($siteId > 0) ? "AND (q.quotation_site_id = {$siteId} OR q.quotation_site_id = 0)" : "";

$query = "
    SELECT 
        q.*,
        tp.tour_package_title,
        tp.tour_package_city,
        tp.tour_package_state,
        tp.tour_package_country
    FROM {$tblPrefix}quotation q
    LEFT JOIN {$tblPrefix}tour_package tp
      ON tp.tour_package_id = q.quotation_type_id
    WHERE {$whereClause}
      {$siteClause}
    ORDER BY q.quotation_id DESC
    LIMIT 1
";

$res = mysqli_query($connection, $query);

if ($res && mysqli_num_rows($res) > 0) {
    $quotation = mysqli_fetch_assoc($res);
    $descData = [];
    if (!empty($quotation['quotation_description'])) {
        $descData = json_decode($quotation['quotation_description'], true) ?: [];
    }
    if (!isset($descData['package_id']) && !empty($quotation['quotation_type_id'])) {
        $descData['package_id'] = (int)$quotation['quotation_type_id'];
    }
    $quotation['parsed_content'] = $descData;
    $quotation['content']        = $descData;

    $encodedId = base64_encode((string)$quotation['quotation_id']);
    $quotation['encoded_id'] = $encodedId;
    $quotation['view_url'] = (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/view-itinerary/{$encodedId}/";
    $quotation['pdf_url'] = (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/download-itinerary/{$encodedId}/";

    http_response_code(200);
    echo json_encode([
        'status'    => 'success',
        'quotation' => $quotation
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Quotation not found.'
    ]);
}
