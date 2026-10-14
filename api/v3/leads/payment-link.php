<?php

header("Content-type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!function_exists('longToShortUrl')) {
    if (defined('UTIL_FUNCTIONS') && file_exists(UTIL_FUNCTIONS)) {
        require_once UTIL_FUNCTIONS;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;

$rawLeadId = isset($_POST['leadId']) ? $_POST['leadId'] : (isset($_POST['lead_id']) ? $_POST['lead_id'] : '');
if (empty($rawLeadId)) {
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
    exit();
}

$decoded = base64_decode(trim($rawLeadId), true);
$leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;

if ($leadId <= 0 || !isset($connection) || !$connection) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid lead request.']);
    exit();
}

$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
if ($amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid payment amount greater than zero.']);
    exit();
}

$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
$sendWhatsApp = !isset($_POST['send_whatsapp']) || $_POST['send_whatsapp'] === '1' || $_POST['send_whatsapp'] === 'true' || $_POST['send_whatsapp'] === true;

// 1. Fetch Lead Details
$siteFilter = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$leadSql = "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteFilter} LIMIT 1";
$leadRes = mysqli_query($connection, $leadSql);

if (!$leadRes || mysqli_num_rows($leadRes) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Lead details could not be found.']);
    exit();
}

$lead = mysqli_fetch_assoc($leadRes);
$customerName = trim(($lead['enquiry_first_name'] ?? '') . ' ' . ($lead['enquiry_last_name'] ?? ''));
if (empty($customerName)) {
    $customerName = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Customer';
}
$customerMobile = preg_replace('/[^0-9]/', '', (string)($lead['enquiry_mobile'] ?? ''));
$customerEmail  = trim((string)($lead['enquiry_email'] ?? ''));
$effectiveSiteId = (int)($lead['enquiry_site_id'] ?? $siteId);
if ($effectiveSiteId <= 0) $effectiveSiteId = 1;

// 2. Fetch Latest Active Quotation for Lead
$quotationId = isset($_POST['quotation_id']) ? (int)$_POST['quotation_id'] : 0;
if ($quotationId <= 0) {
    $qSql = "SELECT quotation_id, quotation_price, quotation_description, quotation_type_id 
             FROM {$tblPrefix}quotation 
             WHERE quotation_ref_id = '{$leadId}' AND quotation_ref_type = 'enquiry' 
             ORDER BY quotation_id DESC LIMIT 1";
    $qRes = mysqli_query($connection, $qSql);
    if ($qRes && $qRow = mysqli_fetch_assoc($qRes)) {
        $quotationId = (int)$qRow['quotation_id'];
    }
}

// 3. Resolve or Create User Profile for Order Tracking
$userId = 0;
if (!empty($customerMobile)) {
    $uCheckSql = "SELECT profile_user_id FROM {$tblPrefix}profile WHERE profile_mobile = '{$customerMobile}' LIMIT 1";
    $uCheckRes = mysqli_query($connection, $uCheckSql);
    if ($uCheckRes && $uRow = mysqli_fetch_assoc($uCheckRes)) {
        $userId = (int)$uRow['profile_user_id'];
    }
}

if ($userId <= 0 && defined('MODULES') && file_exists(MODULES . 'users.php')) {
    require_once MODULES . 'users.php';
    $usersModule = new Users();
    $uResp = $usersModule->createNewUser($connection, [
        'name'   => $customerName,
        'mobile' => $customerMobile,
        'email'  => !empty($customerEmail) ? $customerEmail : "lead{$leadId}@travelcrm.local"
    ]);
    if (!empty($uResp['userId'])) {
        $userId = (int)$uResp['userId'];
    }
}

// 4. Generate Real Payment Link
$realPaymentLink = '';
if ($userId > 0 && defined('MODULES') && file_exists(MODULES . 'order.php')) {
    require_once MODULES . 'order.php';
    $orderMod = new orderModule();
    $realPaymentLink = $orderMod->generatePaymentLinkOrder($connection, $userId, 'INR', $amount, '');
}

// Fallback to order gateway link if order module unavailable
if (empty($realPaymentLink)) {
    $encQId = base64_encode((string)$quotationId);
    $realPaymentLink = SITE_URL . '/payment/generate/?id=' . $encQId . '&amount=' . $amount . '&pg_type=order';
}

// Shorten payment link if shortener is enabled
$displayLink = $realPaymentLink;
if (function_exists('longToShortUrl')) {
    $short = longToShortUrl($connection, $realPaymentLink);
    if (!empty($short)) {
        $displayLink = $short;
    }
}

// 5. Record Payment Link in Enquiry Notes (Timeline)
$userName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : 'Agent';
$payLinkNoteData = [
    'amount'        => $amount,
    'payment_link'  => $displayLink,
    'full_url'      => $realPaymentLink,
    'quotation_id'  => $quotationId,
    'notes'         => $notes,
    'generated_by'  => $userName,
    'date'          => date('d-m-Y h:i A')
];

$encodedNoteData = mysqli_real_escape_string($connection, json_encode($payLinkNoteData));
$descMsg = "Generated payment link for ₹ " . number_format($amount, 2);
if (!empty($notes)) {
    $descMsg .= " (" . $notes . ")";
}
$descMsgEsc = mysqli_real_escape_string($connection, $descMsg);

mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
    enquiry_notes_ref_id, 
    enquiry_notes_user_id, 
    enquiry_notes_meta_type, 
    enquiry_notes_meta_value
) VALUES (
    '{$leadId}', 
    '{$currentUserId}', 
    'payment_link', 
    '{$encodedNoteData}'
)");

// Update lead modification timestamp
mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_update = NOW() WHERE enquiry_id = {$leadId}");

// 6. Optional Outbound WhatsApp Notification
$whatsappSent = false;
$whatsappMsg = "Dear {$customerName}, kindly find your advance payment link for ₹ " . number_format($amount) . ":\n{$displayLink}";
if (!empty($notes)) {
    $whatsappMsg .= "\nNote: {$notes}";
}

if ($sendWhatsApp && !empty($customerMobile)) {
    // A: Check if WhatsApp Gateway / Microservice is active
    $gwUrl = defined('WHATSAPP_GATEWAY_URL') ? WHATSAPP_GATEWAY_URL : 'http://localhost:3000';
    $gwEndpoint = rtrim($gwUrl, '/') . '/send-message';
    
    $payload = json_encode([
        'phone' => (strlen($customerMobile) === 10 ? '91' . $customerMobile : $customerMobile),
        'message' => $whatsappMsg
    ]);

    $ch = curl_init($gwEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $gwResponse = @curl_exec($ch);
    $curlHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlHttpCode === 200) {
        $whatsappSent = true;
    } elseif (defined('ENABLE_WHATSAPP_BUSINESS_API') && ENABLE_WHATSAPP_BUSINESS_API === 'yes') {
        // B: Official Meta / Business API Template
        if (defined('WHATSAPP_TEMPLATE_VISITOR') && file_exists(WHATSAPP_TEMPLATE_VISITOR . 'payment_request.php')) {
            $profile_display_name = $customerName;
            $mobile = $customerMobile;
            $tour_package_title = $lead['enquiry_subject'] ?? 'Tour Itinerary';
            $paymentLink = $displayLink;
            @require_once WHATSAPP_TEMPLATE_VISITOR . 'payment_request.php';
            $whatsappSent = true;
        }
    }
}

echo json_encode([
    'status'        => 'success',
    'message'       => 'Payment link generated successfully.',
    'payment_link'  => $displayLink,
    'full_url'      => $realPaymentLink,
    'amount'        => $amount,
    'quotation_id'  => $quotationId,
    'currency'      => '₹',
    'whatsapp_sent' => $whatsappSent,
    'customer_name' => $customerName,
    'customer_phone'=> $customerMobile,
    'whatsapp_msg'  => $whatsappMsg
]);
exit();
