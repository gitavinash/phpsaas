<?php

/**
 * Operations Confirmed Leads & Operational Metrics API
 * Path: /api/v3/operations/leads.php
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

// Verify user role: only administrator or operations allowed
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
        'message' => 'Access denied. The Operations Hub is restricted to Operations and Administrator roles.'
    ]);
    exit();
}

$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';
$siteFilter = ($siteId > 0) ? " AND e.enquiry_site_id = {$siteId}" : "";

// Status definition dictionary
$statusDictionary = [
    0  => ['label' => 'Initiate (Unassigned)', 'badge' => 'badge-initiate', 'color' => '#64748b'],
    1  => ['label' => 'Success (Paid)', 'badge' => 'badge-success', 'color' => '#10b981'],
    2  => ['label' => 'Pending (Assigned)', 'badge' => 'badge-pending', 'color' => '#f59e0b'],
    3  => ['label' => 'Follow-up', 'badge' => 'badge-followup', 'color' => '#3b82f6'],
    6  => ['label' => 'Payment Initiated', 'badge' => 'badge-payment-init', 'color' => '#8b5cf6'],
    7  => ['label' => 'Sales Request', 'badge' => 'badge-sales-req', 'color' => '#ec4899'],
    8  => ['label' => 'Upay Done', 'badge' => 'badge-upay', 'color' => '#14b8a6'],
    9  => ['label' => 'Itinerary Sent', 'badge' => 'badge-itinerary', 'color' => '#06b6d4'],
    10 => ['label' => 'Quotation Approved', 'badge' => 'badge-approved', 'color' => '#10b981'],
    11 => ['label' => 'Voucher Issued', 'badge' => 'badge-voucher', 'color' => '#059669'],
    12 => ['label' => 'Payment Received', 'badge' => 'badge-paid', 'color' => '#16a34a'],
    13 => ['label' => 'Trip in Progress', 'badge' => 'badge-in-progress', 'color' => '#2563eb'],
    14 => ['label' => 'Trip Completed', 'badge' => 'badge-completed', 'color' => '#475569'],
    15 => ['label' => 'Closed', 'badge' => 'badge-closed', 'color' => '#64748b'],
    16 => ['label' => 'Cancelled', 'badge' => 'badge-cancelled', 'color' => '#ef4444'],
    17 => ['label' => 'Lost', 'badge' => 'badge-lost', 'color' => '#94a3b8'],
    18 => ['label' => 'Payment Refund', 'badge' => 'badge-refund', 'color' => '#f97316']
];

// --------------------------------------------------------------------------
// 1. METRICS & COUNTS
// --------------------------------------------------------------------------
if ($action === 'metrics') {
    $metrics = [
        'confirmed_total'   => 0,
        'quotation_approved'=> 0,
        'payments_recorded' => 0,
        'vouchers_issued'   => 0,
        'trips_in_progress' => 0,
        'trips_completed'   => 0,
        'unassigned_count'  => 0,
        'departing_today'   => 0
    ];

    // Count by status
    $cntSql = "SELECT enquiry_status, COUNT(*) as cnt 
               FROM {$tblPrefix}enquiry e 
               WHERE 1=1 {$siteFilter} 
               GROUP BY enquiry_status";
    if ($res = mysqli_query($connection, $cntSql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $st = (int)$row['enquiry_status'];
            $c  = (int)$row['cnt'];

            if (in_array($st, [10, 6, 8, 12, 1, 11, 13])) {
                $metrics['confirmed_total'] += $c;
            }
            if ($st === 10) $metrics['quotation_approved'] += $c;
            if (in_array($st, [8, 12, 1])) $metrics['payments_recorded'] += $c;
            if ($st === 11) $metrics['vouchers_issued'] += $c;
            if ($st === 13) $metrics['trips_in_progress'] += $c;
            if ($st === 14) $metrics['trips_completed'] += $c;
            if ($st === 0)  $metrics['unassigned_count'] += $c;
        }
    }

    echo json_encode([
        'status'  => 'success',
        'metrics' => $metrics
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. LIST LEADS (Confirmed Leads or Unassigned Pool)
// --------------------------------------------------------------------------
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'confirmed';
$statusCode = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
$agentId = isset($_GET['agent_id']) ? (int)$_GET['agent_id'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 20;
$offset = ($page - 1) * $limit;

$whereClauses = ["1=1"];
if ($siteId > 0) {
    $whereClauses[] = "e.enquiry_site_id = {$siteId}";
}

if ($type === 'unassigned') {
    // Unassigned pool: status 0 or agent unassigned
    $whereClauses[] = "(e.enquiry_status = 0 OR e.enquiry_agent = 0 OR e.enquiry_agent IS NULL)";
} else {
    // Default: Confirmed Operations Queue (Quotation Approved onwards)
    if ($statusCode !== '' && is_numeric($statusCode)) {
        $stNum = (int)$statusCode;
        $whereClauses[] = "e.enquiry_status = {$stNum}";
    } else {
        // All active operational leads
        $whereClauses[] = "e.enquiry_status IN (10, 6, 8, 12, 1, 11, 13, 14)";
    }
}

if ($agentId > 0) {
    $whereClauses[] = "e.enquiry_agent = {$agentId}";
}

if (!empty($search)) {
    $whereClauses[] = "(e.enquiry_id = '{$search}' 
        OR e.enquiry_first_name LIKE '%{$search}%' 
        OR e.enquiry_last_name LIKE '%{$search}%' 
        OR e.enquiry_mobile LIKE '%{$search}%' 
        OR e.enquiry_email LIKE '%{$search}%' 
        OR e.enquiry_subject LIKE '%{$search}%')";
}

$whereSql = implode(" AND ", $whereClauses);

// Count total matching
$totalCount = 0;
$cntSql = "SELECT COUNT(*) as total FROM {$tblPrefix}enquiry e WHERE {$whereSql}";
if ($cntRes = mysqli_query($connection, $cntSql)) {
    if ($cntRow = mysqli_fetch_assoc($cntRes)) {
        $totalCount = (int)$cntRow['total'];
    }
}

// Fetch leads with agent name and latest quotation info
$leadsSql = "SELECT 
        e.*,
        ep.date_of_travel as profile_travel_date,
        ep.number_of_adults as profile_adults,
        ep.number_of_childrens as profile_children,
        p.profile_display_name as agent_name,
        p.profile_mobile as agent_phone,
        u.user_email as agent_email
    FROM {$tblPrefix}enquiry e
    LEFT JOIN {$tblPrefix}enquiry_profile ep ON ep.enquiry_profile_ref_id = e.enquiry_id
    LEFT JOIN {$tblPrefix}user u ON e.enquiry_agent = u.user_id
    LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
    WHERE {$whereSql}
    ORDER BY e.enquiry_id DESC
    LIMIT {$offset}, {$limit}";

$leads = [];
$leadIds = [];

if ($res = mysqli_query($connection, $leadsSql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $lId = (int)$row['enquiry_id'];
        $leadIds[] = $lId;

        // Parse customer name
        $fName = trim($row['enquiry_first_name'] ?? '');
        $lName = trim($row['enquiry_last_name'] ?? '');
        $cName = trim("{$fName} {$lName}");

        $mobile = trim($row['enquiry_mobile'] ?? '');
        $waMobile = '';
        $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($cleanMobile) === 10) {
            $waMobile = '91' . $cleanMobile;
        } elseif (strlen($cleanMobile) >= 11) {
            $waMobile = $cleanMobile;
        }

        // Parse message JSON if present for travel details
        $extra = [];
        if (!empty($row['enquiry_message'])) {
            $parsedMsg = json_decode($row['enquiry_message'], true);
            if (is_array($parsedMsg)) {
                $extra = $parsedMsg;
            }
        }

        $destination = !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : ($extra['destination'] ?? 'Custom Package');
        $travelDate = (!empty($row['profile_travel_date']) && $row['profile_travel_date'] !== '0000-00-00')
            ? date('d M Y', strtotime($row['profile_travel_date']))
            : (!empty($extra['travel_date']) ? $extra['travel_date'] : (!empty($extra['departure_date']) ? $extra['departure_date'] : ''));

        $paxAdults = !empty($row['profile_adults']) ? (int)$row['profile_adults'] : (isset($extra['adults']) ? (int)$extra['adults'] : (isset($extra['pax']) ? (int)$extra['pax'] : 2));
        $paxChildren = isset($row['profile_children']) ? (int)$row['profile_children'] : (isset($extra['children']) ? (int)$extra['children'] : 0);

        $st = (int)$row['enquiry_status'];
        $stMeta = $statusDictionary[$st] ?? ['label' => 'Status ' . $st, 'badge' => 'badge-default', 'color' => '#64748b'];

        $leads[$lId] = [
            'id'            => $lId,
            'encoded_id'    => base64_encode((string)$lId),
            'customer_name' => !empty($cName) ? $cName : 'Guest #' . $lId,
            'mobile'        => $mobile,
            'wa_mobile'     => $waMobile,
            'email'         => trim($row['enquiry_email'] ?? ''),
            'destination'   => $destination,
            'travel_date'   => $travelDate,
            'adults'        => $paxAdults,
            'children'      => $paxChildren,
            'status'        => $st,
            'status_label'  => $stMeta['label'],
            'status_badge'  => $stMeta['badge'],
            'status_color'  => $stMeta['color'],
            'agent_id'      => (int)$row['enquiry_agent'],
            'agent_name'    => !empty($row['agent_name']) ? trim($row['agent_name']) : 'Unassigned',
            'quotation_price' => (float)($row['enquiry_expected_value'] ?? 0),
            'total_paid'    => 0.0,
            'balance_due'   => (float)($row['enquiry_expected_value'] ?? 0),
            'bookings_count'=> 0,
            'created_at'    => $row['enquiry_time']
        ];
    }
}

// Fetch verified payments and booking counts for these leads
if (!empty($leadIds)) {
    $idsIn = implode(',', $leadIds);

    // 1. Fetch verified payments from enquiry notes or payments
    $paySql = "SELECT enquiry_notes_ref_id, enquiry_notes_meta_value 
               FROM {$tblPrefix}enquiry_notes 
               WHERE enquiry_notes_ref_id IN ({$idsIn}) 
                 AND enquiry_notes_meta_type = 'payment_record'";
    if ($payRes = mysqli_query($connection, $paySql)) {
        while ($pRow = mysqli_fetch_assoc($payRes)) {
            $refId = (int)$pRow['enquiry_notes_ref_id'];
            if (isset($leads[$refId]) && !empty($pRow['enquiry_notes_meta_value'])) {
                $pData = json_decode($pRow['enquiry_notes_meta_value'], true);
                if (is_array($pData) && (!isset($pData['status']) || $pData['status'] === 'verified' || $pData['status'] === 'approved')) {
                    $amt = (float)($pData['amount'] ?? 0);
                    $leads[$refId]['total_paid'] += $amt;
                    $leads[$refId]['balance_due'] = max(0, $leads[$refId]['quotation_price'] - $leads[$refId]['total_paid']);
                }
            }
        }
    }

    // 2. Fetch booking count from isw_booking
    $bkgSql = "SELECT booking_enquiry_id, COUNT(*) as bkg_cnt 
               FROM {$tblPrefix}booking 
               WHERE booking_enquiry_id IN ({$idsIn}) 
               GROUP BY booking_enquiry_id";
    if ($bkgRes = mysqli_query($connection, $bkgSql)) {
        while ($bRow = mysqli_fetch_assoc($bkgRes)) {
            $bRef = (int)$bRow['booking_enquiry_id'];
            if (isset($leads[$bRef])) {
                $leads[$bRef]['bookings_count'] = (int)$bRow['bkg_cnt'];
            }
        }
    }
}

echo json_encode([
    'status'     => 'success',
    'total'      => $totalCount,
    'page'       => $page,
    'limit'      => $limit,
    'total_pages'=> ceil($totalCount / $limit),
    'leads'      => array_values($leads)
]);
exit();
