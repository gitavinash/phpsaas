<?php

/**
 * Operations Confirmation Voucher Generator & Ref ID API
 * Supports modular service vouchers (Hotel, Cab, Activity) and combined Master Trip Vouchers
 * Path: /api/v3/operations/voucher.php
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

if (!in_array($userRole, ['administrator', 'operations', 'super_administrator'])) {
    echo json_encode([
        'status' => 'error',
        'code'   => 403,
        'message' => 'Access denied. Only Operations and Administrators can generate confirmation vouchers.'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch existing bookings and voucher readiness for a lead
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

    // Verify payment check: check if payment has been received (statuses 8, 12, 1) or verified payments exist
    $hasPayment = in_array((int)$lead['enquiry_status'], [8, 12, 1, 11, 13, 14]);
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
    if ($totalPaid > 0) {
        $hasPayment = true;
    }

    // Fetch bookings associated with this lead
    $bookings = [];
    $bRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}booking WHERE booking_enquiry_id = {$leadId} ORDER BY booking_id ASC");
    if ($bRes) {
        while ($bRow = mysqli_fetch_assoc($bRes)) {
            $details = !empty($bRow['booking_details']) ? json_decode($bRow['booking_details'], true) : [];
            $bookings[] = [
                'id'               => (int)$bRow['booking_id'],
                'service'          => $bRow['booking_service'],
                'status'           => (int)$bRow['booking_status'],
                'vendor_name'      => $details['vendor_name'] ?? 'Vendor',
                'confirmation_ref' => $details['confirmation_ref'] ?? '',
                'driver_name'      => $details['driver_name'] ?? '',
                'driver_phone'     => $details['driver_phone'] ?? '',
                'vehicle_no'       => $details['vehicle_no'] ?? '',
                'check_in'         => $details['check_in'] ?? '',
                'check_out'        => $details['check_out'] ?? '',
                'room_category'    => $details['room_category'] ?? '',
                'meal_plan'        => $details['meal_plan'] ?? '',
                'emergency_contact'=> $details['emergency_contact'] ?? '',
                'special_notes'    => $details['special_notes'] ?? ''
            ];
        }
    }

    $fName = trim($lead['enquiry_first_name'] ?? '');
    $lName = trim($lead['enquiry_last_name'] ?? '');
    $guestName = trim("{$fName} {$lName}");
    if (empty($guestName)) $guestName = 'Guest #' . $leadId;

    echo json_encode([
        'status'         => 'success',
        'has_payment'    => $hasPayment,
        'total_paid'     => $totalPaid,
        'guest_name'     => $guestName,
        'guest_phone'    => $lead['enquiry_mobile'] ?? '',
        'guest_email'    => $lead['enquiry_email'] ?? '',
        'destination'    => $lead['enquiry_subject'] ?? 'Tour Package',
        'current_status' => (int)$lead['enquiry_status'],
        'bookings'       => $bookings
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Update Confirmation Ref IDs & Issue Vouchers
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

    $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
    $lRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteClause} LIMIT 1");
    if (!$lRes || mysqli_num_rows($lRes) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Lead not found.']);
        exit();
    }
    $lead = mysqli_fetch_assoc($lRes);

    // Payment validation rule: No voucher without advance payment (status 8, 12, 1) or verified payments
    $isPaidOrAdvance = in_array((int)$lead['enquiry_status'], [8, 12, 1, 11, 13, 14]);
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
    if ($totalPaid > 0) {
        $isPaidOrAdvance = true;
    }

    if (!$isPaidOrAdvance) {
        echo json_encode([
            'status'  => 'error',
            'code'    => 'PAYMENT_REQUIRED',
            'message' => 'Business Rule Enforcement: Travel Vouchers cannot be issued until an advance payment is verified (Status 8 Upay, 12 Payment Received, or 1 Success).'
        ]);
        exit();
    }

    // Process voucher items
    $bookingsData = isset($input['bookings']) && is_array($input['bookings']) ? $input['bookings'] : [];
    $updatedCount = 0;

    foreach ($bookingsData as $bItem) {
        $bId = isset($bItem['id']) ? (int)$bItem['id'] : 0;
        if ($bId <= 0) continue;

        // Fetch current booking record
        $bRes = mysqli_query($connection, "SELECT booking_details FROM {$tblPrefix}booking WHERE booking_id = {$bId} AND booking_enquiry_id = {$leadId} LIMIT 1");
        if ($bRes && $bRow = mysqli_fetch_assoc($bRes)) {
            $details = !empty($bRow['booking_details']) ? json_decode($bRow['booking_details'], true) : [];
            
            // Update reference IDs and driver info
            if (isset($bItem['confirmation_ref']))  $details['confirmation_ref'] = trim($bItem['confirmation_ref']);
            if (isset($bItem['driver_name']))       $details['driver_name'] = trim($bItem['driver_name']);
            if (isset($bItem['driver_phone']))      $details['driver_phone'] = trim($bItem['driver_phone']);
            if (isset($bItem['vehicle_no']))        $details['vehicle_no'] = trim($bItem['vehicle_no']);
            if (isset($bItem['emergency_contact'])) $details['emergency_contact'] = trim($bItem['emergency_contact']);
            if (isset($bItem['special_notes']))     $details['special_notes'] = trim($bItem['special_notes']);

            $newDetailsJson = mysqli_real_escape_string($connection, json_encode($details));
            // Status 2 = Voucher Issued
            mysqli_query($connection, "UPDATE {$tblPrefix}booking SET booking_details = '{$newDetailsJson}', booking_status = 2, booking_update = CURRENT_TIMESTAMP WHERE booking_id = {$bId}");
            $updatedCount++;
        }
    }

    // Update lead status to 11 (Voucher Issued) if currently < 11
    $curStatus = (int)$lead['enquiry_status'];
    if ($curStatus < 11) {
        mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_status = 11, enquiry_update = CURRENT_TIMESTAMP WHERE enquiry_id = {$leadId}");
        
        // Log status note
        $noteMsg = "Travel vouchers issued with vendor confirmation reference numbers.";
        $noteMsgEsc = mysqli_real_escape_string($connection, $noteMsg);
        mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
            (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
            VALUES ({$leadId}, {$currentUserId}, 'voucher_issued', '{$noteMsgEsc}', CURRENT_TIMESTAMP)");
    }

    $encLead = base64_encode((string)$leadId);
    $viewUrl = "/view-voucher/{$encLead}/";
    $downloadUrl = "/download-voucher/{$encLead}/";

    $customerMobile = trim($lead['enquiry_mobile'] ?? '');
    $cleanMobile = preg_replace('/[^0-9]/', '', $customerMobile);
    $targetWa = (strlen($cleanMobile) === 10) ? '91' . $cleanMobile : $cleanMobile;

    $fName = trim($lead['enquiry_first_name'] ?? '');
    $lName = trim($lead['enquiry_last_name'] ?? '');
    $gName = trim("{$fName} {$lName}") ?: 'Valued Traveler';

    $waShareMsg = "Dear {$gName}, your official travel confirmation voucher from {$siteName} has been generated:\n{$siteDomain}{$viewUrl}\n\nWishing you a delightful journey!\nOperations Team";
    $waShareLink = !empty($targetWa) ? "https://wa.me/{$targetWa}?text=" . rawurlencode($waShareMsg) : '';

    echo json_encode([
        'status'         => 'success',
        'updated_count'  => $updatedCount,
        'lead_status'    => 11,
        'view_url'       => $viewUrl,
        'download_url'   => $downloadUrl,
        'whatsapp_link'  => $waShareLink,
        'message'        => 'Confirmation vouchers generated and reference IDs saved successfully.'
    ]);
    exit();
}
