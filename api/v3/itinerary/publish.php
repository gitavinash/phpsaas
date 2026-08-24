<?php
/**
 * V3 Itinerary Publish API
 * Endpoint: /api/v3/itinerary/publish.php
 * Method: POST
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $_SERVER['DOCUMENT_ROOT'] . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Method Not Allowed. Use POST.'
    ]);
    exit;
}

// Get raw POST JSON or form data
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

$packageId = isset($data['id']) ? (int)$data['id'] : 0;
$refId     = isset($data['refId']) ? (int)$data['refId'] : 0;
$refType   = isset($data['refType']) ? mysqli_real_escape_string($connection, trim($data['refType'])) : 'enquiry';
$mrp       = isset($data['mrp']) ? (float)$data['mrp'] : 0;
$content   = isset($data['content']) && is_array($data['content']) ? $data['content'] : [];

if ($packageId <= 0 || $refId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Missing package ID or Lead reference ID.'
    ]);
    exit;
}

// Extract and sanitize pricing
preg_match('/\d+/', (string)($content['adult_per_person'] ?? ''), $mAdult);
$adultPrice = isset($mAdult[0]) ? (int)$mAdult[0] : 0;

preg_match('/\d+/', (string)($content['child_per_person'] ?? ''), $mChild);
$childPrice = isset($mChild[0]) ? (int)$mChild[0] : 0;

preg_match('/\d+/', (string)($content['infant_per_person'] ?? ''), $mInfant);
$infantPrice = isset($mInfant[0]) ? (int)$mInfant[0] : 0;

preg_match('/\d+/', (string)($content['total_price'] ?? ''), $mTotal);
$totalPrice = isset($mTotal[0]) ? (int)$mTotal[0] : ($adultPrice > 0 ? $adultPrice : 0);

$itineraryJson = mysqli_real_escape_string($connection, json_encode($content, JSON_UNESCAPED_UNICODE));

// Check if previous quotation exists to establish parent relationship
$parentCheckQuery = "
    SELECT quotation_id AS itineraryParentId 
    FROM {$tblPrefix}quotation 
    WHERE quotation_ref_id = '{$refId}' 
      AND quotation_ref_type = '{$refType}' 
      AND quotation_type_id = {$packageId} 
      AND quotation_type = 'tour_package' 
      AND quotation_site_id = {$siteId} 
    ORDER BY quotation_id ASC 
    LIMIT 1
";

$itineraryParentId = 0;
$checkParentRes = mysqli_query($connection, $parentCheckQuery);
if ($checkParentRes && mysqli_num_rows($checkParentRes) > 0) {
    $pRow = mysqli_fetch_assoc($checkParentRes);
    $itineraryParentId = (int)($pRow['itineraryParentId'] ?? 0);
}

// Insert new quotation record
$insertQuery = "
    INSERT INTO {$tblPrefix}quotation (
        quotation_parent_id,
        quotation_ref_id,
        quotation_ref_type,
        quotation_type_id,
        quotation_type,
        quotation_site_id,
        quotation_sent_by,
        quotation_mrp,
        quotation_per_adult,
        quotation_per_child,
        quotation_per_infant,
        quotation_price,
        quotation_description,
        quotation_status,
        quotation_time
    ) VALUES (
        {$itineraryParentId},
        '{$refId}',
        '{$refType}',
        {$packageId},
        'tour_package',
        {$siteId},
        {$userId},
        {$mrp},
        {$adultPrice},
        {$childPrice},
        {$infantPrice},
        {$totalPrice},
        '{$itineraryJson}',
        0,
        NOW()
    )
";

if (mysqli_query($connection, $insertQuery)) {
    $quotationId = mysqli_insert_id($connection);
    $encodedId = base64_encode((string)$quotationId);

    // Log in enquiry_notes
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}enquiry_notes (
            enquiry_notes_ref_id, 
            enquiry_notes_user_id, 
            enquiry_notes_meta_type, 
            enquiry_notes_meta_value
        ) VALUES (
            {$refId}, 
            {$userId}, 
            'itinerary', 
            '{$quotationId}'
        )
    ");

    // Fetch customer details for optional dispatch
    $customerQuery = mysqli_query($connection, "
        SELECT * FROM {$tblPrefix}enquiry 
        WHERE enquiry_id = {$refId} 
          AND enquiry_site_id = {$siteId}
        LIMIT 1
    ");

    if ($customerQuery && mysqli_num_rows($customerQuery) > 0) {
        $customerData = mysqli_fetch_assoc($customerQuery);
        $clientName   = trim(($customerData['enquiry_user_name'] ?? '') ?: 'Valued Customer');
        $clientEmail  = trim($customerData['enquiry_user_email'] ?? '');
        $clientMobile = trim($customerData['enquiry_user_phone'] ?? '');
        
        $viewUrl = (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/view-itinerary/{$encodedId}/";
        $pdfUrl  = (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/download-itinerary/{$encodedId}/";

        // Dispatch Email if template exists
        if (defined('USER_EMAIL_TEMPLATE') && file_exists(USER_EMAIL_TEMPLATE . 'send-itinerary.php')) {
            @require_once USER_EMAIL_TEMPLATE . 'send-itinerary.php';
        }

        // Dispatch WhatsApp if template exists
        if (defined('WHATSAPP_TEMPLATE_USER') && file_exists(WHATSAPP_TEMPLATE_USER . 'send-itinerary.php')) {
            @require_once WHATSAPP_TEMPLATE_USER . 'send-itinerary.php';
        }
    }

    http_response_code(200);
    echo json_encode([
        'status'       => 'success',
        'httpStatus'   => 200,
        'msg'          => 'Itinerary published successfully.',
        'itineraryId'  => $quotationId,
        'encodedId'    => $encodedId,
        'viewUrl'      => $viewUrl ?? '',
        'pdfUrl'       => $pdfUrl ?? ''
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status'     => 'error',
        'httpStatus' => 500,
        'msg'        => (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? mysqli_error($connection) : 'Failed to publish itinerary.'
    ]);
}
