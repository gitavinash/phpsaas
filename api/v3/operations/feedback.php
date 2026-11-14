<?php

/**
 * Operations & Sales Customer Feedback Request API
 * Generates smart feedback review link & dispatches WhatsApp review prompt
 * Path: /api/v3/operations/feedback.php
 */

header("Content-Type: application/json; charset=utf-8");

$inputRaw = file_get_contents('php://input');
$inputData = json_decode($inputRaw, true) ?: $_POST;
$isPublicSubmit = (isset($_GET['action']) && $_GET['action'] === 'submit_review') || (isset($inputData['action']) && $inputData['action'] === 'submit_review');

if (!$isPublicSubmit && defined('LOGIN_CHECK')) {
    require_once LOGIN_CHECK;
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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID') ? (int) USER_ID : 0;
$siteDomain = SITE_URL;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya Holidays';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. PUBLIC ACTION: Traveler Submits Review Form
// --------------------------------------------------------------------------
if ($isPublicSubmit && $method === 'POST') {
    $rawLeadId = isset($inputData['lead_id']) ? $inputData['lead_id'] : '';
    if (empty($rawLeadId)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
        exit();
    }

    $decodedLead = base64_decode(trim($rawLeadId), true);
    $leadId = ($decodedLead !== false && is_numeric($decodedLead)) ? (int)$decodedLead : (int)$rawLeadId;

    $ratingHotel     = max(1, min(5, (int)($inputData['rating_hotel'] ?? 5)));
    $ratingDriver    = max(1, min(5, (int)($inputData['rating_driver'] ?? 5)));
    $ratingItinerary = max(1, min(5, (int)($inputData['rating_itinerary'] ?? 5)));
    $ratingOverall   = max(1, min(5, (int)($inputData['rating_overall'] ?? 5)));
    $comments        = trim($inputData['comments'] ?? '');

    $reviewMeta = [
        'rating_hotel'     => $ratingHotel,
        'rating_driver'    => $ratingDriver,
        'rating_itinerary' => $ratingItinerary,
        'rating_overall'   => $ratingOverall,
        'comments'         => $comments,
        'submitted_at'     => date('Y-m-d H:i:s')
    ];

    $metaJsonEsc = mysqli_real_escape_string($connection, json_encode($reviewMeta));
    $noteMsg = "Traveler submitted review: {$ratingOverall}/5 Stars. " . (!empty($comments) ? "Remarks: \"{$comments}\"" : "");
    $noteMsgEsc = mysqli_real_escape_string($connection, $noteMsg);

    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
        (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
        VALUES ({$leadId}, 0, 'review_received', '{$metaJsonEsc}', CURRENT_TIMESTAMP)");

    // Advance lead status to 15 (Closed)
    mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_status = 15, enquiry_update = CURRENT_TIMESTAMP WHERE enquiry_id = {$leadId}");

    echo json_encode([
        'status'         => 'success',
        'rating_overall' => $ratingOverall,
        'message'        => 'Thank you for your valuable review!'
    ]);
    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $rawLeadId = isset($input['lead_id']) ? $input['lead_id'] : '';
    if (empty($rawLeadId)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
        exit();
    }

    $decodedLead = base64_decode(trim($rawLeadId), true);
    $leadId = ($decodedLead !== false && is_numeric($decodedLead)) ? (int)$decodedLead : (int)$rawLeadId;

    $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
    $lRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteClause} LIMIT 1");
    if (!$lRes || mysqli_num_rows($lRes) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Lead not found.']);
        exit();
    }
    $lead = mysqli_fetch_assoc($lRes);

    $fName = trim($lead['enquiry_first_name'] ?? '');
    $lName = trim($lead['enquiry_last_name'] ?? '');
    $guestName = trim("{$fName} {$lName}");
    if (empty($guestName)) $guestName = 'Traveler';

    $mobile = trim($lead['enquiry_mobile'] ?? '');
    $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
    $targetWa = (strlen($cleanMobile) === 10) ? '91' . $cleanMobile : $cleanMobile;

    $destination = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Holiday Tour';

    // Build smart feedback WhatsApp text
    $reviewUrl = $siteDomain . '/review/' . base64_encode((string)$leadId) . '/';
    $waText = "Namaste *{$guestName}*! 🙏\n\nHope you had a wonderful and memorable trip to *{$destination}* with *{$siteName}*! 🌟\n\nYour feedback means the world to our team. Could you please take 30 seconds to rate your experience (Hotels, Driver, Itinerary flow)?\n\n👉 *Leave Your Review Here:*\n{$reviewUrl}\n\nThank you for traveling with us, and we look forward to planning your next adventure!\n\nWarm regards,\n*{siteName}* Team";

    $waLink = !empty($targetWa) ? "https://wa.me/{$targetWa}?text=" . rawurlencode($waText) : '';

    // Log note in lead timeline
    $noteMsg = "Post-trip review & feedback request dispatched to {$guestName} via WhatsApp.";
    $noteMsgEsc = mysqli_real_escape_string($connection, $noteMsg);
    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
        (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
        VALUES ({$leadId}, {$currentUserId}, 'feedback_request', '{$noteMsgEsc}', CURRENT_TIMESTAMP)");

    echo json_encode([
        'status'        => 'success',
        'whatsapp_link' => $waLink,
        'review_url'    => $reviewUrl,
        'message'       => 'Feedback request generated and logged on lead record.'
    ]);
    exit();
}
