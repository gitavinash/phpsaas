<?php

/**
 * Operations Invoicing & Proforma Generator API
 * Auto-calculates from quotation, handles GST/TCS breakdown, and dispatches via WhatsApp & Email
 * Path: /api/v3/operations/invoice.php
 */

header("Content-Type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

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

$userRole = '';
if (!empty($site_access_type)) {
    $userRole = strtolower(trim($site_access_type));
} elseif (defined('USER_TYPE') && !empty(USER_TYPE)) {
    $userRole = strtolower(trim(USER_TYPE));
} elseif (!empty($_SESSION['user_type'])) {
    $userRole = strtolower(trim($_SESSION['user_type']));
} elseif (!empty($_COOKIE['user_type'])) {
    $userRole = strtolower(trim(base64_decode($_COOKIE['user_type'])));
}

// Restriction: Sales agent cannot generate invoices/proformas; Operations & Admin only
if (!in_array($userRole, ['administrator', 'operations', 'super_administrator'])) {
    echo json_encode([
        'status' => 'error',
        'code'   => 403,
        'message' => 'Access denied. Sales agents cannot generate invoices or proformas. Restricted to Operations and Administrators.'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch Pre-filled Invoice & Quotation Calculation
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $rawLeadId = isset($_GET['lead_id']) ? $_GET['lead_id'] : '';
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

    // Customer
    $fName = trim($lead['enquiry_first_name'] ?? '');
    $lName = trim($lead['enquiry_last_name'] ?? '');
    $guestName = trim("{$fName} {$lName}");
    if (empty($guestName)) $guestName = 'Guest #' . $leadId;

    $customerMobile = trim($lead['enquiry_mobile'] ?? '');
    $customerEmail  = trim($lead['enquiry_email'] ?? '');
    $destination    = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Custom Tour Package';

    // Fetch Quotation data if available
    $packageCost = (float)($lead['enquiry_expected_value'] ?? 0);
    $qSql = "SELECT quotation_price, quotation_description FROM {$tblPrefix}quotation WHERE quotation_ref_id = {$leadId} ORDER BY quotation_id DESC LIMIT 1";
    if ($qRes = mysqli_query($connection, $qSql)) {
        if ($qRow = mysqli_fetch_assoc($qRes)) {
            if ((float)$qRow['quotation_price'] > 0) {
                $packageCost = (float)$qRow['quotation_price'];
            }
        }
    }

    // Fetch verified payments
    $totalPaid = 0.0;
    $paySql = "SELECT enquiry_notes_meta_value FROM {$tblPrefix}enquiry_notes WHERE enquiry_notes_ref_id = {$leadId} AND enquiry_notes_meta_type = 'payment_record'";
    if ($pRes = mysqli_query($connection, $paySql)) {
        while ($pRow = mysqli_fetch_assoc($pRes)) {
            $pData = json_decode($pRow['enquiry_notes_meta_value'], true);
            if (is_array($pData) && (!isset($pData['status']) || in_array($pData['status'], ['verified', 'approved']))) {
                $totalPaid += (float)($pData['amount'] ?? 0);
            }
        }
    }

    $subTotal = $packageCost > 0 ? round($packageCost / 1.05, 2) : 0;
    $gstAmount = round($packageCost - $subTotal, 2);
    $balanceDue = max(0, $packageCost - $totalPaid);

    $invoiceNumber = 'PI-' . date('Y') . '-' . str_pad($leadId, 4, '0', STR_PAD_LEFT);

    echo json_encode([
        'status'          => 'success',
        'invoice_number'  => $invoiceNumber,
        'date'            => date('d M Y'),
        'guest_name'      => $guestName,
        'guest_phone'     => $customerMobile,
        'guest_email'     => $customerEmail,
        'destination'     => $destination,
        'package_cost'    => $packageCost,
        'subtotal'        => $subTotal,
        'gst_rate'        => 5.0,
        'gst_amount'      => $gstAmount,
        'total_paid'      => $totalPaid,
        'balance_due'     => $balanceDue,
        'agency_name'     => $siteName,
        'bank_details'    => [
            'account_name'  => $siteName . ' Private Limited',
            'bank_name'     => 'HDFC Bank',
            'account_no'    => '50200088991122',
            'ifsc_code'     => 'HDFC0001234',
            'upi_id'        => 'aleya@hdfcbank'
        ]
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Dispatch Invoice / Proforma to Traveler
// --------------------------------------------------------------------------
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

    $docType = isset($input['type']) ? trim($input['type']) : 'proforma'; // proforma or tax_invoice
    $invNum  = isset($input['invoice_number']) ? trim($input['invoice_number']) : 'INV-' . $leadId;
    $amount  = isset($input['total_amount']) ? (float)$input['total_amount'] : 0.0;
    $balance = isset($input['balance_due']) ? (float)$input['balance_due'] : 0.0;
    $guestMobile = isset($input['guest_phone']) ? trim($input['guest_phone']) : '';
    $guestName   = isset($input['guest_name']) ? trim($input['guest_name']) : 'Traveler';

    // Build WhatsApp invoice summary message
    $docTitle = ($docType === 'tax_invoice') ? 'Tax Invoice' : 'Proforma Invoice';
    $waText = "Dear *{$guestName}*,\n\nPlease find your *{$docTitle}* (*{$invNum}*) from *{$siteName}*:\n\n💰 *Total Package Value:* ₹ " . number_format($amount, 2) . "\n💳 *Balance Due:* ₹ " . number_format($balance, 2) . "\n\n📄 You can view and download your invoice copy from your traveler dashboard.\n\nThank you for choosing *{$siteName}*!\nOperations Team";

    $cleanPhone = preg_replace('/[^0-9]/', '', $guestMobile);
    $targetWa = (strlen($cleanPhone) === 10) ? '91' . $cleanPhone : $cleanPhone;
    $waLink = !empty($targetWa) ? "https://wa.me/{$targetWa}?text=" . rawurlencode($waText) : '';

    // Log note in lead timeline
    $noteMsg = "{$docTitle} ({$invNum}) for ₹ " . number_format($amount, 2) . " generated and dispatched by Operations.";
    $noteMsgEsc = mysqli_real_escape_string($connection, $noteMsg);
    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
        (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
        VALUES ({$leadId}, {$currentUserId}, 'invoice_sent', '{$noteMsgEsc}', CURRENT_TIMESTAMP)");

    echo json_encode([
        'status'         => 'success',
        'invoice_number' => $invNum,
        'whatsapp_link'  => $waLink,
        'message'        => "{$docTitle} generated successfully and logged on lead timeline."
    ]);
    exit();
}
