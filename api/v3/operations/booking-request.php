<?php

/**
 * Operations Vendor Booking Request & Dispatch API
 * Handles pre-filling, live preview merge tags, in-line edit overrides, and dual WhatsApp/Email dispatch
 * Path: /api/v3/operations/booking-request.php
 */

header("Content-Type: application/json; charset=utf-8");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        'message' => 'Access denied. Restricted to Operations and Administrators.'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Dispatched Bookings Ledger OR Pre-populate Single Lead Form
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $action = isset($_GET['action']) ? trim($_GET['action']) : '';
    $rawLeadId = isset($_GET['lead_id']) ? $_GET['lead_id'] : '';

    // 1A. All Dispatched Bookings Ledger
    if ($action === 'ledger' || (empty($rawLeadId) && !isset($_GET['lead_id']))) {
        $filterVendorType = isset($_GET['vendor_type']) ? trim($_GET['vendor_type']) : '';
        $filterStatus = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : -1;
        $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
        $offset = ($page - 1) * $limit;

        $where = ["1=1"];
        if ($siteId > 0) {
            $where[] = "b.booking_site_id = {$siteId}";
        }
        if (!empty($filterVendorType) && $filterVendorType !== 'all') {
            $where[] = "b.booking_service = '" . mysqli_real_escape_string($connection, $filterVendorType) . "'";
        }
        if ($filterStatus >= 0) {
            $where[] = "b.booking_status = {$filterStatus}";
        }
        if (!empty($search)) {
            $where[] = "(b.booking_enquiry_id = '{$search}' 
                OR e.enquiry_first_name LIKE '%{$search}%' 
                OR e.enquiry_last_name LIKE '%{$search}%' 
                OR b.booking_details LIKE '%{$search}%')";
        }

        $whereSql = implode(' AND ', $where);

        $cntRes = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}booking b LEFT JOIN {$tblPrefix}enquiry e ON b.booking_enquiry_id = e.enquiry_id WHERE {$whereSql}");
        $totalCount = ($cntRes && $cRow = mysqli_fetch_assoc($cntRes)) ? (int)$cRow['total'] : 0;

        $sql = "SELECT 
                b.*,
                e.enquiry_first_name,
                e.enquiry_last_name,
                e.enquiry_mobile,
                e.enquiry_subject,
                p.profile_display_name as ops_agent_name
            FROM {$tblPrefix}booking b
            LEFT JOIN {$tblPrefix}enquiry e ON b.booking_enquiry_id = e.enquiry_id
            LEFT JOIN {$tblPrefix}user u ON b.booking_user_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
            WHERE {$whereSql}
            ORDER BY b.booking_id DESC
            LIMIT {$offset}, {$limit}";

        $requests = [];
        if ($res = mysqli_query($connection, $sql)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $details = !empty($row['booking_details']) ? json_decode($row['booking_details'], true) : [];
                $fName = trim($row['enquiry_first_name'] ?? '');
                $lName = trim($row['enquiry_last_name'] ?? '');
                $gName = trim("{$fName} {$lName}") ?: 'Guest #' . $row['booking_enquiry_id'];

                $st = (int)$row['booking_status'];
                $stLabel = 'Pending Response';
                $stBadge = 'badge-payment-init';
                if ($st === 1) { $stLabel = 'Confirmed'; $stBadge = 'badge-paid'; }
                elseif ($st === 2) { $stLabel = 'Voucher Issued'; $stBadge = 'badge-voucher'; }
                elseif ($st === 3) { $stLabel = 'Declined'; $stBadge = 'badge-cancelled'; }

                $requests[] = [
                    'id'               => (int)$row['booking_id'],
                    'lead_id'          => (int)$row['booking_enquiry_id'],
                    'encoded_lead_id'  => base64_encode((string)$row['booking_enquiry_id']),
                    'guest_name'       => $gName,
                    'guest_mobile'     => $row['enquiry_mobile'] ?? '',
                    'destination'      => $row['enquiry_subject'] ?? '',
                    'service'          => $row['booking_service'],
                    'vendor_name'      => $details['vendor_name'] ?? 'Vendor',
                    'vendor_phone'     => $details['vendor_phone'] ?? '',
                    'vendor_email'     => $details['vendor_email'] ?? '',
                    'check_in'         => $details['check_in'] ?? '',
                    'check_out'        => $details['check_out'] ?? '',
                    'room_category'    => $details['room_category'] ?? ($details['vehicle_type'] ?? ''),
                    'meal_plan'        => $details['meal_plan'] ?? '',
                    'confirmation_ref' => $details['confirmation_ref'] ?? '',
                    'dispatched_via'   => $details['dispatched_via'] ?? 'whatsapp',
                    'requested_at'     => $details['requested_at'] ?? $row['booking_update'],
                    'status'           => $st,
                    'status_label'     => $stLabel,
                    'status_badge'     => $stBadge,
                    'ops_agent'        => $row['ops_agent_name'] ?? 'Operations'
                ];
            }
        }

        echo json_encode([
            'status'      => 'success',
            'total'       => $totalCount,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => ceil($totalCount / $limit),
            'requests'    => $requests
        ]);
        exit();
    }

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

    // Customer info
    $fName = trim($lead['enquiry_first_name'] ?? '');
    $lName = trim($lead['enquiry_last_name'] ?? '');
    $guestName = trim("{$fName} {$lName}");
    if (empty($guestName)) $guestName = 'Guest #' . $leadId;

    $guestPhone = trim($lead['enquiry_mobile'] ?? '');

    // Parse extra message details
    $extra = [];
    if (!empty($lead['enquiry_message'])) {
        $p = json_decode($lead['enquiry_message'], true);
        if (is_array($p)) $extra = $p;
    }

    $paxAdults = isset($extra['adults']) ? (int)$extra['adults'] : (isset($extra['pax']) ? (int)$extra['pax'] : 2);
    $paxChildren = isset($extra['children']) ? (int)$extra['children'] : 0;
    $duration = isset($extra['duration_days']) ? (int)$extra['duration_days'] : 3;

    $epRes = mysqli_query($connection, "SELECT date_of_travel, number_of_days, number_of_adults, number_of_childrens FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = {$leadId} LIMIT 1");
    if ($epRes && $epRow = mysqli_fetch_assoc($epRes)) {
        if (!empty($epRow['date_of_travel']) && $epRow['date_of_travel'] !== '0000-00-00') {
            $extra['travel_date'] = date('d M Y', strtotime($epRow['date_of_travel']));
        }
        if (!empty($epRow['number_of_days'])) {
            $duration = (int)$epRow['number_of_days'];
        }
        if (!empty($epRow['number_of_adults'])) {
            $paxAdults = (int)$epRow['number_of_adults'];
        }
        if (isset($epRow['number_of_childrens'])) {
            $paxChildren = (int)$epRow['number_of_childrens'];
        }
    }

    $checkIn  = !empty($extra['travel_date']) ? $extra['travel_date'] : (!empty($extra['departure_date']) ? $extra['departure_date'] : date('d M Y', strtotime('+3 days')));
    $checkOut = !empty($extra['return_date']) ? $extra['return_date'] : date('d M Y', strtotime($checkIn . " + {$duration} days"));
    $calcSecs = strtotime($checkOut) - strtotime($checkIn);
    $nights   = ($calcSecs > 0) ? max(1, (int)round($calcSecs / 86400)) : 1;

    // Fetch Hotels / Properties for dropdown
    $properties = [];
    $pRes = mysqli_query($connection, "SELECT property_id, property_title, property_mobile, property_email, property_city FROM {$tblPrefix}property WHERE property_status = 1 " . ($siteId > 0 ? "AND property_site_id = {$siteId}" : "") . " ORDER BY property_title ASC LIMIT 100");
    if ($pRes) {
        while ($pRow = mysqli_fetch_assoc($pRes)) {
            $properties[] = [
                'id'    => (int)$pRow['property_id'],
                'name'  => $pRow['property_title'],
                'phone' => $pRow['property_mobile'] ?? '',
                'email' => $pRow['property_email'] ?? '',
                'city'  => $pRow['property_city'] ?? ''
            ];
        }
    }

    // Fetch Transporters for dropdown
    $transporters = [];
    $tRes = mysqli_query($connection, "SELECT transporter_id, transporter_title, transporter_mobile, transporter_email, transporter_city FROM {$tblPrefix}transporter WHERE 1 " . ($siteId > 0 ? "AND transporter_site_id = {$siteId}" : "") . " ORDER BY transporter_title ASC LIMIT 100");
    if ($tRes) {
        while ($tRow = mysqli_fetch_assoc($tRes)) {
            $transporters[] = [
                'id'    => (int)$tRow['transporter_id'],
                'name'  => $tRow['transporter_title'],
                'phone' => $tRow['transporter_mobile'] ?? '',
                'email' => $tRow['transporter_email'] ?? '',
                'city'  => $tRow['transporter_city'] ?? ''
            ];
        }
    }

    // Operations Manager Info
    $opsName = 'Operations Desk';
    $opsPhone = '+91 98765 43210';
    $uRes = mysqli_query($connection, "SELECT p.profile_display_name, p.profile_mobile, u.user_email FROM {$tblPrefix}user u LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id WHERE u.user_id = {$currentUserId} LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        if (!empty($uRow['profile_display_name'])) $opsName = trim($uRow['profile_display_name']);
        if (!empty($uRow['profile_mobile'])) $opsPhone = trim($uRow['profile_mobile']);
    }

    // Fetch default templates
    $templates = [];
    $tplRes = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}operations_template` WHERE template_site_id = {$siteId} AND template_status = 1 ORDER BY template_is_default DESC, template_id ASC");
    if ($tplRes) {
        while ($tpl = mysqli_fetch_assoc($tplRes)) {
            $templates[] = [
                'id'          => (int)$tpl['template_id'],
                'channel'     => $tpl['template_channel'],
                'vendor_type' => $tpl['template_vendor_type'],
                'title'       => $tpl['template_title'],
                'subject'     => $tpl['template_subject'],
                'body'        => $tpl['template_body'],
                'is_default'  => (int)$tpl['template_is_default'] === 1
            ];
        }
    }

    // Existing bookings for this lead
    $existingBookings = [];
    $bRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}booking WHERE booking_enquiry_id = {$leadId} ORDER BY booking_id DESC");
    if ($bRes) {
        while ($bRow = mysqli_fetch_assoc($bRes)) {
            $details = !empty($bRow['booking_details']) ? json_decode($bRow['booking_details'], true) : [];
            $existingBookings[] = [
                'id'         => (int)$bRow['booking_id'],
                'service'    => $bRow['booking_service'],
                'status'     => (int)$bRow['booking_status'],
                'details'    => $details,
                'created_at' => $bRow['booking_update']
            ];
        }
    }

    echo json_encode([
        'status'       => 'success',
        'lead'         => [
            'id'           => $leadId,
            'encoded_id'   => base64_encode((string)$leadId),
            'guest_name'   => $guestName,
            'guest_phone'  => $guestPhone,
            'check_in'     => $checkIn,
            'check_out'    => $checkOut,
            'nights'       => $nights,
            'pax_adults'   => $paxAdults,
            'pax_children' => $paxChildren,
            'destination'  => !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Holiday Package'
        ],
        'agency'       => [
            'name'         => $siteName,
            'ops_name'     => $opsName,
            'ops_phone'    => $opsPhone
        ],
        'properties'   => $properties,
        'transporters' => $transporters,
        'templates'    => $templates,
        'existing_bookings' => $existingBookings
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Dispatch Booking Request OR Update Existing Booking Status
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $subAction = isset($input['action']) ? trim($input['action']) : 'dispatch';

    // 2A. Update Booking Response Status / Ref ID
    if ($subAction === 'update_status') {
        $bookingId = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;
        $newStatus = isset($input['status']) ? (int)$input['status'] : 1;
        $confRef   = isset($input['confirmation_ref']) ? trim($input['confirmation_ref']) : '';

        if ($bookingId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Booking ID is required.']);
            exit();
        }

        $bRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}booking WHERE booking_id = {$bookingId} LIMIT 1");
        if ($bRes && $bRow = mysqli_fetch_assoc($bRes)) {
            $details = !empty($bRow['booking_details']) ? json_decode($bRow['booking_details'], true) : [];
            if (!empty($confRef)) {
                $details['confirmation_ref'] = $confRef;
            }
            $details['status_updated_at'] = date('Y-m-d H:i:s');

            $newDetailsJson = mysqli_real_escape_string($connection, json_encode($details));
            mysqli_query($connection, "UPDATE {$tblPrefix}booking SET booking_details = '{$newDetailsJson}', booking_status = {$newStatus}, booking_update = CURRENT_TIMESTAMP WHERE booking_id = {$bookingId}");

            // Log note
            $lId = (int)$bRow['booking_enquiry_id'];
            $stText = ($newStatus === 1) ? 'Confirmed' : (($newStatus === 3) ? 'Declined' : 'Updated');
            $vendorName = $details['vendor_name'] ?? 'Vendor';
            $logMsg = "Booking #{$bookingId} with {$vendorName} marked as {$stText}" . (!empty($confRef) ? " (Ref: {$confRef})" : "");
            $logMsgEsc = mysqli_real_escape_string($connection, $logMsg);
            mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
                (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
                VALUES ({$lId}, {$currentUserId}, 'booking_update', '{$logMsgEsc}', CURRENT_TIMESTAMP)");

            echo json_encode(['status' => 'success', 'message' => "Booking status updated to {$stText}."]);
            exit();
        }

        echo json_encode(['status' => 'error', 'message' => 'Booking record not found.']);
        exit();
    }

    $rawLeadId = isset($input['lead_id']) ? $input['lead_id'] : '';
    if (empty($rawLeadId)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
        exit();
    }

    $decodedLead = base64_decode(trim($rawLeadId), true);
    $leadId = ($decodedLead !== false && is_numeric($decodedLead)) ? (int)$decodedLead : (int)$rawLeadId;

    $vendorType = isset($input['vendor_type']) ? strtolower(trim($input['vendor_type'])) : 'hotel';
    $vendorName = isset($input['vendor_name']) ? trim($input['vendor_name']) : '';
    $vendorPhone = isset($input['vendor_phone']) ? trim($input['vendor_phone']) : '';
    $vendorEmail = isset($input['vendor_email']) ? trim($input['vendor_email']) : '';
    $vendorId = isset($input['vendor_id']) ? (int)$input['vendor_id'] : 0;

    $channel = isset($input['channel']) ? strtolower(trim($input['channel'])) : 'both';
    $waContent = isset($input['whatsapp_content']) ? trim($input['whatsapp_content']) : '';
    $emailSubject = isset($input['email_subject']) ? trim($input['email_subject']) : 'Booking Request';
    $emailContent = isset($input['email_content']) ? trim($input['email_content']) : '';

    $checkIn  = isset($input['check_in']) ? trim($input['check_in']) : date('Y-m-d');
    $checkOut = isset($input['check_out']) ? trim($input['check_out']) : date('Y-m-d', strtotime('+3 days'));
    $roomCategory = isset($input['room_category']) ? trim($input['room_category']) : 'Standard';
    $roomCount = isset($input['room_count']) ? (int)$input['room_count'] : 1;
    $mealPlan = isset($input['meal_plan']) ? trim($input['meal_plan']) : 'CP';
    $vehicleType = isset($input['vehicle_type']) ? trim($input['vehicle_type']) : 'Sedan';
    $specialNotes = isset($input['special_notes']) ? trim($input['special_notes']) : '';

    if (empty($vendorName)) {
        echo json_encode(['status' => 'error', 'message' => 'Vendor or Property Name is required.']);
        exit();
    }

    // 1. Insert record into isw_booking
    $bookingDetails = [
        'vendor_id'         => $vendorId,
        'vendor_name'       => $vendorName,
        'vendor_phone'      => $vendorPhone,
        'vendor_email'      => $vendorEmail,
        'vendor_type'       => $vendorType,
        'check_in'          => $checkIn,
        'check_out'         => $checkOut,
        'room_category'     => $roomCategory,
        'room_count'        => $roomCount,
        'meal_plan'         => $mealPlan,
        'vehicle_type'      => $vehicleType,
        'special_notes'     => $specialNotes,
        'confirmation_ref'  => '',
        'requested_at'      => date('Y-m-d H:i:s'),
        'dispatched_via'    => $channel
    ];

    $detailsJsonEsc = mysqli_real_escape_string($connection, json_encode($bookingDetails));
    $servEsc = mysqli_real_escape_string($connection, $vendorType);
    $checkInTs = !empty($checkIn) ? (int)strtotime($checkIn) : time();
    $checkOutTs = !empty($checkOut) ? (int)strtotime($checkOut) : time() + (3 * 86400);
    $bkgSql = "INSERT INTO {$tblPrefix}booking 
        (booking_site_id, booking_user_id, booking_enquiry_id, booking_date, booking_time, booking_service, booking_details, booking_check_in, booking_check_out, booking_status) 
        VALUES ({$siteId}, {$currentUserId}, {$leadId}, CURRENT_DATE, '" . date('H:i:s') . "', '{$servEsc}', '{$detailsJsonEsc}', {$checkInTs}, {$checkOutTs}, 0)";
    
    $bookingId = 0;
    if (mysqli_query($connection, $bkgSql)) {
        $bookingId = mysqli_insert_id($connection);
    }

    // 2. Dispatch WhatsApp Message
    $waSent = false;
    $waLink = '';
    $cleanVendorPhone = preg_replace('/[^0-9]/', '', $vendorPhone);
    if (strlen($cleanVendorPhone) === 10) {
        $targetWa = '91' . $cleanVendorPhone;
    } elseif (strlen($cleanVendorPhone) >= 11) {
        $targetWa = $cleanVendorPhone;
    } else {
        $targetWa = '';
    }

    if (!empty($targetWa) && !empty($waContent)) {
        $waLink = "https://wa.me/{$targetWa}?text=" . rawurlencode($waContent);
        // Attempt automated WhatsApp direct dispatch if helper exists
        $waHelper = dirname(__DIR__) . '/whatsapp/device_helper.php';
        if (file_exists($waHelper)) {
            require_once $waHelper;
            if (function_exists('sendWhatsAppDirectMessage')) {
                $waRes = sendWhatsAppDirectMessage($connection, $siteId, $currentUserId, $targetWa, $waContent);
                if (is_array($waRes) && (!empty($waRes['success']) || !empty($waRes['messageId']))) {
                    $waSent = true;
                }
            }
        }
    }

    // 3. Dispatch Email
    $emailSent = false;
    if (!empty($vendorEmail) && !empty($emailContent) && ($channel === 'email' || $channel === 'both')) {
        $phpMailerDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)) . '/lib/configuration/mail/';
        if (file_exists($phpMailerDir . 'php_mailer.php')) {
            require_once $phpMailerDir . 'exception.php';
            require_once $phpMailerDir . 'php_mailer.php';
            require_once $phpMailerDir . 'smtp.php';

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.zeptomail.in';
                $mail->SMTPAuth = true;
                $mail->Username = 'emailapikey';
                $mail->Password = 'PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('noreply@aleyaholidays.com', $siteName . ' Operations');
                $mail->addAddress($vendorEmail, $vendorName);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $emailSubject;
                $mail->Body    = $emailContent;
                $mail->AltBody = strip_tags($emailContent);
                $mail->Timeout = 6;
                if ($mail->send()) {
                    $emailSent = true;
                }
            } catch (\Throwable $e) {
                // Email delivery attempt logged
            }
        }
    }

    // 4. Log interaction note in lead timeline
    $logMsg = "Booking request dispatched to {$vendorName} ({$vendorType}) via " . strtoupper($channel);
    $metaJson = json_encode([
        'booking_id'    => $bookingId,
        'vendor_name'   => $vendorName,
        'vendor_type'   => $vendorType,
        'channel'       => $channel,
        'wa_sent'       => $waSent,
        'email_sent'    => $emailSent
    ]);
    $logMsgEsc = mysqli_real_escape_string($connection, $logMsg);
    $metaJsonEsc = mysqli_real_escape_string($connection, $metaJson);
    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
        (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
        VALUES ({$leadId}, {$currentUserId}, 'booking_request', '{$metaJsonEsc}', CURRENT_TIMESTAMP)");

    echo json_encode([
        'status'         => 'success',
        'booking_id'     => $bookingId,
        'whatsapp_sent'  => $waSent,
        'whatsapp_link'  => $waLink,
        'email_sent'     => $emailSent,
        'message'        => "Booking request successfully recorded and dispatched to {$vendorName}."
    ]);
    exit();
}
