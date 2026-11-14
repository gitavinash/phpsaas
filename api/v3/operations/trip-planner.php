<?php

/**
 * Operations Daily Trip Planner & Logistics Board API
 * Categorizes active travelers into Departing Today, Active On-Tour, Returning Today, and Completed
 * Path: /api/v3/operations/trip-planner.php
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
$siteClause = ($siteId > 0) ? " AND e.enquiry_site_id = {$siteId}" : "";

// --------------------------------------------------------------------------
// 1. GET: Fetch Trips Board
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $targetDate = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $todayStr = date('Y-m-d', strtotime($targetDate));

    // Fetch all confirmed/in-progress/completed leads
    $sql = "SELECT 
            e.*,
            ep.date_of_travel as profile_travel_date,
            ep.number_of_days as profile_duration_days,
            p.profile_display_name as agent_name,
            p.profile_mobile as agent_phone
        FROM {$tblPrefix}enquiry e
        LEFT JOIN {$tblPrefix}enquiry_profile ep ON ep.enquiry_profile_ref_id = e.enquiry_id
        LEFT JOIN {$tblPrefix}user u ON e.enquiry_agent = u.user_id
        LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
        WHERE e.enquiry_status IN (10, 6, 8, 12, 1, 11, 13, 14) {$siteClause}
        ORDER BY e.enquiry_id DESC LIMIT 300";

    $departingToday = [];
    $activeOnTour   = [];
    $returningToday = [];
    $completedTrips = [];

    if ($res = mysqli_query($connection, $sql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $lId = (int)$row['enquiry_id'];
            $st  = (int)$row['enquiry_status'];

            $fName = trim($row['enquiry_first_name'] ?? '');
            $lName = trim($row['enquiry_last_name'] ?? '');
            $guestName = trim("{$fName} {$lName}");
            if (empty($guestName)) $guestName = 'Guest #' . $lId;

            $mobile = trim($row['enquiry_mobile'] ?? '');
            $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
            $waMobile = (strlen($cleanMobile) === 10) ? '91' . $cleanMobile : $cleanMobile;

            // Parse travel dates from message
            $extra = [];
            if (!empty($row['enquiry_message'])) {
                $p = json_decode($row['enquiry_message'], true);
                if (is_array($p)) $extra = $p;
            }

            $rawCheckIn = (!empty($row['profile_travel_date']) && $row['profile_travel_date'] !== '0000-00-00')
                ? $row['profile_travel_date']
                : (!empty($extra['travel_date']) ? $extra['travel_date'] : (!empty($extra['departure_date']) ? $extra['departure_date'] : ''));

            $durationDays = !empty($row['profile_duration_days']) ? (int)$row['profile_duration_days'] : (!empty($extra['duration_days']) ? (int)$extra['duration_days'] : 3);
            if ($durationDays < 1) $durationDays = 3;

            $startDate = (!empty($rawCheckIn) && strtotime($rawCheckIn) && date('Y', strtotime($rawCheckIn)) > 1970) ? date('Y-m-d', strtotime($rawCheckIn)) : '';
            $endDate   = (!empty($rawCheckOut) && strtotime($rawCheckOut) && date('Y', strtotime($rawCheckOut)) > 1970) ? date('Y-m-d', strtotime($rawCheckOut)) : '';

            if (empty($startDate)) {
                $startDate = date('Y-m-d', strtotime($row['enquiry_time']));
            }
            if (empty($endDate)) {
                $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $durationDays . ' days'));
            }

            $item = [
                'id'            => $lId,
                'encoded_id'    => base64_encode((string)$lId),
                'guest_name'    => $guestName,
                'mobile'        => $mobile,
                'wa_mobile'     => $waMobile,
                'destination'   => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : ($extra['destination'] ?? 'Holiday Tour'),
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'status'        => $st,
                'agent_name'    => !empty($row['agent_name']) ? $row['agent_name'] : 'Operations',
                'pax_adults'    => isset($extra['adults']) ? (int)$extra['adults'] : 2,
                'pax_children'  => isset($extra['children']) ? (int)$extra['children'] : 0,
                'has_voucher'   => ($st === 11 || $st === 13 || $st === 14),
                'driver_name'   => '',
                'driver_phone'  => '',
                'vehicle_no'    => '',
                'pickup_time'   => ''
            ];

            // Check if driver assigned in bookings
            $trRes = mysqli_query($connection, "SELECT booking_details FROM {$tblPrefix}booking WHERE booking_enquiry_id = {$lId} AND booking_service = 'transport' LIMIT 1");
            if ($trRes && $trRow = mysqli_fetch_assoc($trRes)) {
                $trDetails = json_decode($trRow['booking_details'], true) ?: [];
                $item['driver_name']  = $trDetails['driver_name'] ?? '';
                $item['driver_phone'] = $trDetails['driver_phone'] ?? '';
                $item['vehicle_no']   = $trDetails['vehicle_no'] ?? '';
                $item['pickup_time']  = $trDetails['pickup_time'] ?? '';
            }

            if ($st === 14) {
                $completedTrips[] = $item;
            } elseif ($startDate === $todayStr) {
                $departingToday[] = $item;
            } elseif ($endDate === $todayStr) {
                $returningToday[] = $item;
            } elseif ($startDate < $todayStr && $endDate > $todayStr) {
                $activeOnTour[] = $item;
            } elseif ($st === 13) {
                // Marked as trip in progress
                $activeOnTour[] = $item;
            } elseif ($startDate > $todayStr && ($st === 11 || $st === 12 || $st === 10)) {
                // Upcoming departure
                $departingToday[] = $item;
            } else {
                $activeOnTour[] = $item;
            }
        }
    }

    echo json_encode([
        'status'          => 'success',
        'target_date'     => $todayStr,
        'departing_today' => array_values($departingToday),
        'active_on_tour'  => array_values($activeOnTour),
        'returning_today' => array_values($returningToday),
        'completed_trips' => array_values($completedTrips)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Update Trip Milestone State (Depart / Complete)
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $rawLeadId = isset($input['lead_id']) ? $input['lead_id'] : '';
    $milestone = isset($input['milestone']) ? trim($input['milestone']) : '';

    if (empty($rawLeadId) || empty($milestone)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead ID and milestone action are required.']);
        exit();
    }

    $decodedLead = base64_decode(trim($rawLeadId), true);
    $leadId = ($decodedLead !== false && is_numeric($decodedLead)) ? (int)$decodedLead : (int)$rawLeadId;

    $targetStatus = 0;
    $logNote = '';

    if ($milestone === 'dispatch_driver') {
        $driverName  = trim($input['driver_name'] ?? '');
        $driverPhone = trim($input['driver_phone'] ?? '');
        $vehicleNo   = trim($input['vehicle_no'] ?? '');
        $vehicleType = trim($input['vehicle_type'] ?? 'Sedan / SUV');
        $pickupTime  = trim($input['pickup_time'] ?? '08:00 AM');
        $pickupLoc   = trim($input['pickup_location'] ?? 'Hotel / Airport');

        if (empty($driverName) || empty($driverPhone)) {
            echo json_encode(['status' => 'error', 'message' => 'Driver name and phone are required.']);
            exit();
        }

        // Fetch lead customer info
        $lRes = mysqli_query($connection, "SELECT enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_subject FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId} LIMIT 1");
        $lData = $lRes ? mysqli_fetch_assoc($lRes) : [];
        $gName = trim(($lData['enquiry_first_name'] ?? '') . ' ' . ($lData['enquiry_last_name'] ?? '')) ?: 'Valued Guest';
        $dest  = $lData['enquiry_subject'] ?? 'Tour';

        // Update transport booking if exists
        $trRes = mysqli_query($connection, "SELECT booking_id, booking_details FROM {$tblPrefix}booking WHERE booking_enquiry_id = {$leadId} AND booking_service = 'transport' LIMIT 1");
        if ($trRes && $trRow = mysqli_fetch_assoc($trRes)) {
            $bId = (int)$trRow['booking_id'];
            $bDetails = json_decode($trRow['booking_details'], true) ?: [];
            $bDetails['driver_name']     = $driverName;
            $bDetails['driver_phone']    = $driverPhone;
            $bDetails['vehicle_no']      = $vehicleNo;
            $bDetails['vehicle_type']    = $vehicleType;
            $bDetails['pickup_time']     = $pickupTime;
            $bDetails['pickup_location'] = $pickupLoc;
            $bDetailsJson = mysqli_real_escape_string($connection, json_encode($bDetails));
            mysqli_query($connection, "UPDATE {$tblPrefix}booking SET booking_details = '{$bDetailsJson}', booking_update = CURRENT_TIMESTAMP WHERE booking_id = {$bId}");
        }

        // WhatsApp message dispatch
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya Holidays';
        $msg = "Namaste *{$gName}*! 🚗\n\nYour travel arrangements for *{$dest}* are confirmed! Kindly find your driver details below:\n\n👤 *Chauffeur:* {$driverName}\n📱 *Contact:* {$driverPhone}\n🚘 *Vehicle:* {$vehicleType} ({$vehicleNo})\n⏰ *Pickup Time:* {$pickupTime}\n📍 *Pickup Point:* {$pickupLoc}\n\nOur operations team is available 24/7 if you need any assistance.\n\nWishing you a delightful journey!\n*{$siteName}* Operations Desk";

        $cleanMobile = preg_replace('/[^0-9]/', '', $lData['enquiry_mobile'] ?? '');
        $targetWa = (strlen($cleanMobile) === 10) ? '91' . $cleanMobile : $cleanMobile;
        $waLink = !empty($targetWa) ? "https://wa.me/{$targetWa}?text=" . rawurlencode($msg) : '';

        // Log note
        $logNote = "Driver details dispatched to traveler via WhatsApp: {$driverName} ({$driverPhone}), Vehicle: {$vehicleNo}";
        $logNoteEsc = mysqli_real_escape_string($connection, $logNote);
        mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
            (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
            VALUES ({$leadId}, {$currentUserId}, 'driver_dispatch', '{$logNoteEsc}', CURRENT_TIMESTAMP)");

        echo json_encode([
            'status'        => 'success',
            'whatsapp_link' => $waLink,
            'message'       => 'Driver details assigned and WhatsApp dispatch message generated.'
        ]);
        exit();
    }

    if ($milestone === 'start_trip') {
        $targetStatus = 13; // Trip in Progress
        $logNote = "Lead marked as Trip in Progress by Operations.";
    } elseif ($milestone === 'complete_trip') {
        $targetStatus = 14; // Trip Completed
        $logNote = "Trip marked as safely completed by Operations.";
    } elseif ($milestone === 'close_lead') {
        $targetStatus = 15; // Closed
        $logNote = "Lead closed and archived following post-trip review.";
    }

    if ($targetStatus > 0) {
        mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_status = {$targetStatus}, enquiry_update = CURRENT_TIMESTAMP WHERE enquiry_id = {$leadId}{$siteClause}");
        
        $logNoteEsc = mysqli_real_escape_string($connection, $logNote);
        mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes 
            (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update)
            VALUES ({$leadId}, {$currentUserId}, 'trip_milestone', '{$logNoteEsc}', CURRENT_TIMESTAMP)");

        echo json_encode([
            'status'        => 'success',
            'new_status'    => $targetStatus,
            'message'       => $logNote
        ]);
        exit();
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid milestone transition.']);
    exit();
}
