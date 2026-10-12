<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

require_once LOGIN_CHECK;

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'customer';

if ($accessType === 'digital_marketing') {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Access denied. Leads page is restricted for digital marketing.'
    ]);
    exit;
}

$reqData = $_POST;
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $reqData = array_merge($reqData, $decoded);
    }
}
$reqData = array_merge($_GET, $_REQUEST, $reqData);

$siteFilter = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
$siteQuotationFilter = ($siteId > 0) ? " AND quotation_site_id = {$siteId}" : "";
$userRole = strtolower(isset($site_access_type) ? $site_access_type : ($accessType ?? ''));
$isPrivilegedUser = in_array($userRole, ['administrator', 'admin', 'superadmin', 'operations', 'owner'], true);
$isCallingAgent = in_array($userRole, ['agent', 'calling_agent', 'sales_executive'], true);

$userFilter = (!$isPrivilegedUser && $userId > 0)
    ? " AND (e.enquiry_agent = '{$userId}')"
    : "";

// Specific agent filter from request
$reqAgent = $reqData['agentId'] ?? ($reqData['agent_id'] ?? ($reqData['agent'] ?? ''));
if ($reqAgent !== '' && $reqAgent !== 'all') {
    if ($reqAgent === '0' || $reqAgent === 'unassigned') {
        $userFilter .= " AND (e.enquiry_agent = '0' OR e.enquiry_agent = '' OR e.enquiry_agent IS NULL)";
    } elseif (is_numeric($reqAgent) && (int)$reqAgent > 0) {
        $filterAgentId = (int)$reqAgent;
        $userFilter .= " AND e.enquiry_agent = '{$filterAgentId}'";
    }
}

// Date Range Filter
$daterangeFilter = "";
$hasExplicitDate = false;
$t1 = "";
$t2 = "";
if (isset($reqData['daterange']) && !empty($reqData['daterange']) && $reqData['daterange'] !== 'null') {
    $dr = trim($reqData['daterange']);
    if (($ampPos = strpos($dr, '&')) !== false) {
        $dr = substr($dr, 0, $ampPos);
    }
    if ($dr === 'all') {
        $daterangeFilter = "";
        $hasExplicitDate = true;
    } else {
        $dates = explode(' - ', $dr);
        if (count($dates) == 2) {
            $parsedT1 = strtotime(trim($dates[0]));
            $parsedT2 = strtotime(trim($dates[1]));
            if ($parsedT1 && $parsedT2) {
                $t1 = date('Y-m-d 00:00:00', $parsedT1);
                $t2 = date('Y-m-d 23:59:59', $parsedT2);
                $daterangeFilter = " AND e.enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
                $hasExplicitDate = true;
            }
        }
    }
}

// By default show last 30 days of data if no explicit date range is specified and not performing an open search
if (!$hasExplicitDate && (!isset($reqData['search']) || trim($reqData['search']) === '')) {
    $t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
    $t2 = date('Y-m-d 23:59:59');
    $daterangeFilter = " AND e.enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
}

// Search Filter
$searchFilter = "";
if (isset($reqData['search']) && trim($reqData['search']) !== '') {
    $searchKey = mysqli_real_escape_string($connection, trim($reqData['search']));
    if ($accessType === 'digital_marketing') {
        $searchFilter = " AND (e.enquiry_first_name LIKE '%{$searchKey}%' 
                           OR e.enquiry_last_name LIKE '%{$searchKey}%'
                           OR e.enquiry_subject LIKE '%{$searchKey}%' 
                           OR e.enquiry_source LIKE '%{$searchKey}%'
                           OR e.enquiry_id = '{$searchKey}')";
    } else {
        $searchFilter = " AND (e.enquiry_first_name LIKE '%{$searchKey}%' 
                           OR e.enquiry_last_name LIKE '%{$searchKey}%'
                           OR e.enquiry_mobile LIKE '%{$searchKey}%' 
                           OR e.enquiry_email LIKE '%{$searchKey}%' 
                           OR e.enquiry_subject LIKE '%{$searchKey}%' 
                           OR e.enquiry_source LIKE '%{$searchKey}%'
                           OR e.enquiry_id = '{$searchKey}')";
    }
    // When specific search is provided without explicit daterange, search across all time
    if (!$hasExplicitDate) {
        $daterangeFilter = "";
    }
}

// Stats Date Filter (used for top KPI stats breakdown)
$statsDateFilter = $daterangeFilter;

// Status Filter
$statusFilter = "";
$rawStatus = isset($reqData['status']) ? trim((string)$reqData['status']) : (isset($reqData['leadStatus']) ? trim((string)$reqData['leadStatus']) : '');
if ($rawStatus !== '' && $rawStatus !== 'all') {
    if (strpos($rawStatus, ',') !== false) {
        $stParts = array_filter(array_map('intval', explode(',', $rawStatus)), function($v) {
            return $v >= 0;
        });
        if (!empty($stParts)) {
            $statusFilter = " AND e.enquiry_status IN (" . implode(',', $stParts) . ")";
        }
    } elseif ($rawStatus === 'cancel' || $rawStatus === 'cancelled' || $rawStatus === '16' || $rawStatus === '4') {
        // Status 16 (Cancelled) and status 4 (historical failed/cancelled) both represent cancelled enquiries
        $statusFilter = " AND e.enquiry_status IN (4, 16)";
    } elseif ($rawStatus === '9' || $rawStatus === 'itinerary_sent' || $rawStatus === 'quote_sent') {
        $statusFilter = " AND (e.enquiry_status = 9 OR (e.enquiry_status NOT IN (1, 6, 4, 5, 7, 15, 16, 17, 18) AND e.enquiry_id IN (SELECT quotation_ref_id FROM {$tblPrefix}quotation WHERE quotation_status = 0{$siteQuotationFilter})))";
    } elseif (in_array((int)$rawStatus, [0, 2, 3], true)) {
        $targetStatus = (int)$rawStatus;
        $statusFilter = " AND e.enquiry_status = {$targetStatus} AND e.enquiry_id NOT IN (SELECT quotation_ref_id FROM {$tblPrefix}quotation WHERE quotation_status = 0{$siteQuotationFilter})";
    } else {
        $targetStatus = (int)$rawStatus;
        $statusFilter = " AND e.enquiry_status = {$targetStatus}";
    }
}

// Source Filter
$sourceFilter = "";
if (isset($reqData['source']) && trim($reqData['source']) !== '' && $reqData['source'] !== 'all') {
    $sourceKey = mysqli_real_escape_string($connection, trim($reqData['source']));
    $sourceFilter = " AND e.enquiry_source LIKE '%{$sourceKey}%'";
}

// Interest Level Filter
$interestFilter = "";
$rawInterest = isset($reqData['interest']) ? trim((string)$reqData['interest']) : (isset($reqData['interest_level']) ? trim((string)$reqData['interest_level']) : '');
if ($rawInterest !== '' && $rawInterest !== 'all') {
    $escInterest = mysqli_real_escape_string($connection, $rawInterest);
    $interestFilter = " AND e.enquiry_interest_level = '{$escInterest}'";
}

// Response Indicator Filter
$responseFilter = "";
$rawResp = isset($reqData['response_status']) ? trim((string)$reqData['response_status']) : (isset($reqData['response']) ? trim((string)$reqData['response']) : '');
if ($rawResp !== '' && $rawResp !== 'all') {
    if ($rawResp === 'no_activity') {
        $responseFilter = " AND (e.enquiry_last_contacted_at IS NULL AND e.enquiry_last_customer_response_at IS NULL)";
    } elseif ($rawResp === 'awaiting_response') {
        $responseFilter = " AND e.enquiry_last_contacted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
    } elseif ($rawResp === 'responded') {
        $responseFilter = " AND e.enquiry_last_customer_response_at IS NOT NULL AND e.enquiry_last_customer_response_at >= COALESCE(e.enquiry_last_contacted_at, '1970-01-01')";
    } elseif ($rawResp === 'no_response_24h') {
        $responseFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND e.enquiry_last_contacted_at > DATE_SUB(NOW(), INTERVAL 48 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
    } elseif ($rawResp === 'no_response_48h') {
        $responseFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 48 HOUR) AND e.enquiry_last_contacted_at > DATE_SUB(NOW(), INTERVAL 7 DAY) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
    } elseif ($rawResp === 'no_response_7d') {
        $responseFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 7 DAY) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
    }
}

// Operational Follow-Up Filter
$followupFilter = "";
$rawFollowupF = isset($reqData['followup_filter']) ? trim((string)$reqData['followup_filter']) : '';
$todayStart = date('Y-m-d 00:00:00');
$todayEnd   = date('Y-m-d 23:59:59');
if ($rawFollowupF === 'due_today') {
    $followupFilter = " AND e.enquiry_followup BETWEEN '{$todayStart}' AND '{$todayEnd}'";
} elseif ($rawFollowupF === 'overdue') {
    $followupFilter = " AND e.enquiry_followup < '{$todayStart}' AND e.enquiry_followup != '0000-00-00 00:00:00'";
} elseif ($rawFollowupF === 'no_response_24h') {
    $followupFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
} elseif ($rawFollowupF === 'no_response_48h') {
    $followupFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 48 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
} elseif ($rawFollowupF === 'no_response_7d') {
    $followupFilter = " AND e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 7 DAY) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29)";
}

// Travel Date Range Filter
$travelDateFilter = "";
if (isset($reqData['travel_date']) && !empty($reqData['travel_date']) && $reqData['travel_date'] !== 'all') {
    $tdParts = explode(' - ', trim($reqData['travel_date']));
    if (count($tdParts) === 2) {
        $p1 = strtotime(trim($tdParts[0]));
        $p2 = strtotime(trim($tdParts[1]));
        if ($p1 && $p2) {
            $td1 = date('Y-m-d', $p1);
            $td2 = date('Y-m-d', $p2);
            $travelDateFilter = " AND e.enquiry_id IN (SELECT enquiry_profile_ref_id FROM {$tblPrefix}enquiry_profile WHERE date_of_travel BETWEEN '{$td1}' AND '{$td2}')";
        }
    }
}

$combinedFilters = "{$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}{$statusFilter}{$sourceFilter}{$interestFilter}{$responseFilter}{$followupFilter}{$travelDateFilter}";

function cleanLeadSource($src)
{
    if (empty($src) || trim($src) === '') return 'Direct';
    $c = trim($src);
    $c = preg_replace('#^https?://#i', '', $c);
    $c = preg_replace('#^www\.#i', '', $c);
    $c = rtrim($c, '/');
    return ucfirst($c);
}

require_once __DIR__ . '/status-config.php';

function getRelativeTime($timestamp)
{
    $time = strtotime($timestamp);
    if (!$time) return '';
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 172800) return 'Yesterday';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('d M Y', $time);
}

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($num)
    {
        $num = round((float)$num);
        $isNegative = $num < 0;
        $numStr = (string)abs($num);
        if (strlen($numStr) <= 3) return ($isNegative ? '-' : '') . $numStr;
        $lastThree = substr($numStr, -3);
        $remaining = substr($numStr, 0, -3);
        $formattedRemaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);
        return ($isNegative ? '-' : '') . $formattedRemaining . ',' . $lastThree;
    }
}

// 1. Fetch Summary Stats for KPIs
$stats = [
    'total'           => 0,
    'fresh'           => 0,
    'pending'         => 0,
    'followup'        => 0,
    'itinerary'       => 0,
    'payment'         => 0,
    'confirmed'       => 0,
    'closed'          => 0,
    'hot_leads'       => 0,
    'warm_leads'      => 0,
    'due_today'       => 0,
    'overdue'         => 0,
    'no_response_24h' => 0,
    'no_response_48h' => 0,
];

$statsQuery = "SELECT 
    COUNT(DISTINCT e.enquiry_id) AS total_count,
    SUM(CASE WHEN e.enquiry_status = 0 AND q.quotation_id IS NULL THEN 1 ELSE 0 END) AS fresh_count,
    SUM(CASE WHEN e.enquiry_status = 2 AND q.quotation_id IS NULL THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN e.enquiry_status = 3 AND q.quotation_id IS NULL THEN 1 ELSE 0 END) AS followup_count,
    SUM(CASE WHEN (e.enquiry_status = 9 OR (e.enquiry_status NOT IN (1, 6, 4, 5, 7, 15, 16, 17, 18, 28, 29) AND q.quotation_id IS NOT NULL)) THEN 1 ELSE 0 END) AS itinerary_count,
    SUM(CASE WHEN e.enquiry_status = 6 THEN 1 ELSE 0 END) AS payment_count,
    SUM(CASE WHEN e.enquiry_status = 1 THEN 1 ELSE 0 END) AS confirmed_count,
    SUM(CASE WHEN e.enquiry_status IN (4, 5, 7, 15, 16, 17, 18, 28, 29) THEN 1 ELSE 0 END) AS closed_count,
    SUM(CASE WHEN e.enquiry_interest_level = 'Hot' THEN 1 ELSE 0 END) AS hot_count,
    SUM(CASE WHEN e.enquiry_interest_level = 'Warm' THEN 1 ELSE 0 END) AS warm_count,
    SUM(CASE WHEN e.enquiry_followup BETWEEN '{$todayStart}' AND '{$todayEnd}' THEN 1 ELSE 0 END) AS due_today_count,
    SUM(CASE WHEN e.enquiry_followup < '{$todayStart}' AND e.enquiry_followup != '0000-00-00 00:00:00' AND e.enquiry_status NOT IN (1, 4, 5, 15, 16, 17, 18, 28, 29) THEN 1 ELSE 0 END) AS overdue_count,
    SUM(CASE WHEN e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29) THEN 1 ELSE 0 END) AS no_resp_24h_count,
    SUM(CASE WHEN e.enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 48 HOUR) AND (e.enquiry_last_customer_response_at IS NULL OR e.enquiry_last_customer_response_at < e.enquiry_last_contacted_at) AND e.enquiry_status NOT IN (1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29) THEN 1 ELSE 0 END) AS no_resp_48h_count
FROM {$tblPrefix}enquiry e
LEFT JOIN (
    SELECT quotation_ref_id, MIN(quotation_id) AS quotation_id
    FROM {$tblPrefix}quotation
    WHERE quotation_status = 0{$siteQuotationFilter}
    GROUP BY quotation_ref_id
) q ON e.enquiry_id = q.quotation_ref_id
WHERE {$siteFilter}{$userFilter}{$statsDateFilter}{$sourceFilter}";

if (isset($connection) && ($sRes = mysqli_query($connection, $statsQuery))) {
    if ($sRow = mysqli_fetch_assoc($sRes)) {
        $stats['total']           = (int)($sRow['total_count'] ?? 0);
        $stats['fresh']           = (int)($sRow['fresh_count'] ?? 0);
        $stats['pending']         = (int)($sRow['pending_count'] ?? 0);
        $stats['followup']        = (int)($sRow['followup_count'] ?? 0);
        $stats['itinerary']       = (int)($sRow['itinerary_count'] ?? 0);
        $stats['payment']         = (int)($sRow['payment_count'] ?? 0);
        $stats['confirmed']       = (int)($sRow['confirmed_count'] ?? 0);
        $stats['closed']          = (int)($sRow['closed_count'] ?? 0);
        $stats['hot_leads']       = (int)($sRow['hot_count'] ?? 0);
        $stats['warm_leads']      = (int)($sRow['warm_count'] ?? 0);
        $stats['due_today']       = (int)($sRow['due_today_count'] ?? 0);
        $stats['overdue']         = (int)($sRow['overdue_count'] ?? 0);
        $stats['no_response_24h'] = (int)($sRow['no_resp_24h_count'] ?? 0);
        $stats['no_response_48h'] = (int)($sRow['no_resp_48h_count'] ?? 0);
        $stats['hot']             = $stats['hot_leads'];
    }
}

// 2. Pagination Calculation
$page  = isset($reqData['page']) ? max(1, (int)$reqData['page']) : 1;
$limit = isset($reqData['limit']) ? min(max(5, (int)$reqData['limit']), 200) : 10;
$offset = ($page - 1) * $limit;

// Total Count matching current filters
$countQuery = "SELECT COUNT(DISTINCT e.enquiry_id) AS filtered_count
               FROM {$tblPrefix}enquiry e
               WHERE {$combinedFilters}";

$totalRecords = 0;
if (isset($connection) && ($cRes = mysqli_query($connection, $countQuery))) {
    $cRow = mysqli_fetch_assoc($cRes);
    $totalRecords = (int)($cRow['filtered_count'] ?? 0);
}
$totalPages = $limit > 0 ? ceil($totalRecords / $limit) : 1;

// 3. Fetch Leads Query
$leadsQuery = "SELECT 
    e.enquiry_id,
    e.enquiry_time,
    e.enquiry_first_name,
    e.enquiry_last_name,
    e.enquiry_email,
    e.enquiry_mobile,
    e.enquiry_alt_mobile,
    e.enquiry_subject,
    e.enquiry_source,
    e.enquiry_status,
    e.enquiry_interest_level,
    e.enquiry_last_contacted_at,
    e.enquiry_last_customer_response_at,
    e.enquiry_response_status,
    e.enquiry_expected_value,
    e.enquiry_followup,
    e.enquiry_agent,
    p.profile_display_name AS agent_name,
    n.enquiry_notes_meta_type,
    n.enquiry_notes_meta_value,
    MAX(CAST(q.quotation_price AS DECIMAL(15,2))) AS quotation_price,
    MAX(q.quotation_id) AS has_quotation,
    MAX(ep.date_of_travel) AS travel_date
FROM {$tblPrefix}enquiry e
LEFT JOIN {$tblPrefix}profile p ON e.enquiry_agent = p.profile_user_id
LEFT JOIN {$tblPrefix}enquiry_profile ep ON e.enquiry_id = ep.enquiry_profile_ref_id
LEFT JOIN (
    SELECT enquiry_notes_ref_id, MAX(enquiry_notes_update) AS latest_notes_time
    FROM {$tblPrefix}enquiry_notes
    WHERE enquiry_notes_meta_value NOT LIKE '%Lead status updated to Itinerary%'
      AND enquiry_notes_meta_value NOT LIKE '%Itinerary / Quotation Sent%'
    GROUP BY enquiry_notes_ref_id
) latest_notes ON e.enquiry_id = latest_notes.enquiry_notes_ref_id
LEFT JOIN {$tblPrefix}enquiry_notes n ON e.enquiry_id = n.enquiry_notes_ref_id AND latest_notes.latest_notes_time = n.enquiry_notes_update
LEFT JOIN {$tblPrefix}quotation q ON e.enquiry_id = q.quotation_ref_id AND q.quotation_status = 0" . ($siteId > 0 ? " AND q.quotation_site_id = {$siteId}" : "") . "
WHERE {$combinedFilters}
GROUP BY e.enquiry_id
ORDER BY e.enquiry_time DESC
LIMIT {$offset}, {$limit}";

$leadsList = [];
if (isset($connection) && ($res = mysqli_query($connection, $leadsQuery))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $leadId = (int)$row['enquiry_id'];
        $firstName = trim($row['enquiry_first_name'] ?? '');
        $lastName = trim($row['enquiry_last_name'] ?? '');
        $fullName = trim($firstName . ' ' . $lastName);
        if (empty($fullName)) $fullName = 'Lead #' . $leadId;

        $rawSt = (int)$row['enquiry_status'];
        $effectiveStatus = $rawSt;
        if (!in_array($rawSt, [1, 6, 4, 5, 7, 15, 16, 17, 18, 28, 29], true) && !empty($row['has_quotation'])) {
            $effectiveStatus = 9;
        }

        $statusMeta = getLeadStatusMeta($effectiveStatus);

        // Interest Level
        $interestLvl = !empty($row['enquiry_interest_level']) ? $row['enquiry_interest_level'] : 'Unknown';
        $interestMeta = getInterestLevelMeta($interestLvl);

        // Customer Response Calculation
        $respTracking = calculateCustomerResponseStatus($row['enquiry_last_contacted_at'], $row['enquiry_last_customer_response_at'], $effectiveStatus);

        // Next Follow-Up Operational Indicator
        $nextFollowupData = [
            'is_scheduled'   => false,
            'time_formatted' => 'Not Scheduled',
            'badge'          => 'none',
            'is_overdue'     => false,
            'is_today'       => false,
        ];
        if (!empty($row['enquiry_followup']) && $row['enquiry_followup'] !== '0000-00-00 00:00:00') {
            $fTs = strtotime($row['enquiry_followup']);
            if ($fTs && date('Y', $fTs) > 2000) {
                $now = time();
                $isToday = (date('Y-m-d', $fTs) === date('Y-m-d', $now));
                $isOverdue = ($fTs < $now && !$isToday);
                $badge = $isOverdue ? 'overdue' : ($isToday ? 'today' : 'upcoming');
                $nextFollowupData = [
                    'is_scheduled'   => true,
                    'datetime'       => date('Y-m-d H:i:s', $fTs),
                    'time_formatted' => date('d M Y, h:i A', $fTs),
                    'badge'          => $badge,
                    'badge_label'    => $isOverdue ? 'Overdue' : ($isToday ? 'Due Today' : 'Upcoming'),
                    'is_overdue'     => $isOverdue,
                    'is_today'       => $isToday,
                ];
            }
        }

        $travelDateFormatted = '';
        if (!empty($row['travel_date']) && $row['travel_date'] !== '0000-00-00') {
            $travelDateFormatted = date('d M Y', strtotime($row['travel_date']));
        }

        $agentId = (int)($row['enquiry_agent'] ?? 0);
        $agentName = !empty($row['agent_name']) ? trim($row['agent_name']) : '';

        $isDigMkt = ($accessType === 'digital_marketing');
        $cleanMobile = $isDigMkt ? '' : preg_replace('/[^0-9]/', '', (string)($row['enquiry_mobile'] ?? ''));
        $waMobile = ($isDigMkt || empty($cleanMobile)) ? '' : ((strlen($cleanMobile) == 10) ? ('91' . $cleanMobile) : $cleanMobile);

        $qPrice = isset($row['quotation_price']) && is_numeric($row['quotation_price']) && (float)$row['quotation_price'] > 0
            ? (float)$row['quotation_price']
            : (float)($row['enquiry_expected_value'] ?? 0);

        $leadsList[] = [
            'id'                  => $leadId,
            'encoded_id'          => base64_encode($leadId),
            'name'                => ucwords(strtolower($fullName)),
            'first_name'          => ucwords(strtolower($firstName)),
            'email'               => $isDigMkt ? '' : trim($row['enquiry_email'] ?? ''),
            'mobile'              => $isDigMkt ? '' : trim($row['enquiry_mobile'] ?? ''),
            'alt_mobile'          => $isDigMkt ? '' : trim($row['enquiry_alt_mobile'] ?? ''),
            'wa_mobile'           => $waMobile,
            'subject'             => !empty($row['enquiry_subject']) ? ucwords(strtolower(trim($row['enquiry_subject']))) : 'Tour Package Enquiry',
            'source'              => cleanLeadSource($row['enquiry_source'] ?? ''),
            'source_raw'          => trim($row['enquiry_source'] ?? ''),
            'status'              => $effectiveStatus,
            'status_label'        => $statusMeta['label'],
            'status_class'        => $statusMeta['class'],
            'status_icon'         => $statusMeta['icon'],
            'status_color'        => $statusMeta['color'],
            'interest_level'      => $interestLvl,
            'interest_meta'       => $interestMeta,
            'response_indicator'  => $respTracking,
            'next_followup'       => $nextFollowupData,
            'travel_date'         => $travelDateFormatted,
            'time'                => date('d M Y, h:i A', strtotime($row['enquiry_time'])),
            'time_relative'       => getRelativeTime($row['enquiry_time']),
            'agent_id'            => $agentId,
            'agent_name'          => $agentName,
            'recent_note_type'    => trim($row['enquiry_notes_meta_type'] ?? ''),
            'recent_note'         => (function() use ($row) {
                $type = trim($row['enquiry_notes_meta_type'] ?? '');
                $val  = trim($row['enquiry_notes_meta_value'] ?? '');
                if (stripos($val, 'Lead status updated to Itinerary') !== false || (stripos($val, 'Itinerary / Quotation Sent') !== false && ($type === 'status' || stripos($val, 'Lead status') !== false))) {
                    return '';
                }
                if ($type === 'payment' || (str_starts_with($val, '{') && str_contains($val, 'paymentAmount'))) {
                    $dec = json_decode($val, true);
                    if (is_array($dec)) {
                        $pAmt = isset($dec['paymentAmount']) ? '₹ ' . number_format((float)$dec['paymentAmount'], 2) : '';
                        $pMethod = $dec['paymentMethod'] ?? 'Payment';
                        return "Payment: {$pAmt} ({$pMethod})";
                    }
                }
                return $val;
            })(),
            'quotation_price'     => $qPrice,
            'quotation_formatted' => $qPrice > 0 ? '₹' . formatIndianCurrency($qPrice) : '',
        ];
    }
}

// 4. Fetch Active Agents List for Assignment Dropdowns
$agentsList = [];
$agentsSql = "SELECT u.user_id, u.user_email, p.profile_display_name, s.site_access_type
              FROM {$tblPrefix}user u
              INNER JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
              INNER JOIN {$tblPrefix}site_access s ON s.site_access_user_id = u.user_id
              WHERE s.site_access_status = 1 AND s.site_access_site_id = " . ($siteId > 0 ? $siteId : 1) . "
              ORDER BY p.profile_display_name ASC";

if (isset($connection) && ($aRes = mysqli_query($connection, $agentsSql))) {
    while ($aRow = mysqli_fetch_assoc($aRes)) {
        $aName = !empty($aRow['profile_display_name']) ? trim($aRow['profile_display_name']) : $aRow['user_email'];
        $agentsList[] = [
            'id'    => (int)$aRow['user_id'],
            'name'  => $aName,
            'email' => $aRow['user_email'],
            'role'  => ucwords(str_replace('_', ' ', $aRow['site_access_type'] ?? 'agent')),
        ];
    }
}

echo json_encode([
    'status'     => 200,
    'data'       => $leadsList,
    'stats'      => $stats,
    'pagination' => [
        'current_page'  => $page,
        'total_pages'   => $totalPages,
        'total_records' => $totalRecords,
        'limit'         => $limit,
        'offset'        => $offset,
    ],
    'agents'     => $agentsList,
    'user_role'  => $accessType,
]);
exit();
