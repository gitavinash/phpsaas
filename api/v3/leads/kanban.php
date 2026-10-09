<?php

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once LOGIN_CHECK;
if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';
$isCallingAgent = in_array(strtolower($accessType), ['agent', 'calling_agent', 'sales_executive'], true);

if ($accessType === 'digital_marketing') {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Access denied. Leads page is restricted for digital marketing.'
    ]);
    exit;
}

$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
$userFilter = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}')"
    : "";

$daterangeFilter = "";
$hasExplicitDate = false;
if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dr = trim($_GET['daterange']);
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
                $daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
                $hasExplicitDate = true;
            }
        }
    }
}

// By default show last 30 days of data if no explicit date range is specified and not performing an open search
if (!$hasExplicitDate && (!isset($_GET['search']) || trim($_GET['search']) === '')) {
    $t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
    $t2 = date('Y-m-d 23:59:59');
    $daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
}

$searchFilter = "";
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchKey = mysqli_real_escape_string($connection, trim($_GET['search']));
    if ($accessType === 'digital_marketing') {
        $searchFilter = " AND (enquiry_first_name LIKE '%{$searchKey}%' 
                           OR enquiry_last_name LIKE '%{$searchKey}%'
                           OR enquiry_subject LIKE '%{$searchKey}%' 
                           OR enquiry_source LIKE '%{$searchKey}%'
                           OR enquiry_id = '{$searchKey}')";
    } else {
        $searchFilter = " AND (enquiry_first_name LIKE '%{$searchKey}%' 
                           OR enquiry_last_name LIKE '%{$searchKey}%'
                           OR enquiry_mobile LIKE '%{$searchKey}%' 
                           OR enquiry_email LIKE '%{$searchKey}%' 
                           OR enquiry_subject LIKE '%{$searchKey}%' 
                           OR enquiry_source LIKE '%{$searchKey}%'
                           OR enquiry_id = '{$searchKey}')";
    }
    // When specific search is provided without explicit daterange, search across all time
    if (!$hasExplicitDate) {
        $daterangeFilter = "";
    }
}

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

// Case 1: Fetching single column status (e.g. Load More pagination)
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $targetStatus = intval($_GET['status']);
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
    $limit  = isset($_GET['limit'])  ? min(max(1, intval($_GET['limit'])), 50) : 10;

    // Allowed active kanban columns: 0 (New Leads), 2 (Assigned), 3 (Follow Up), 9 (Itinerary Sent), 6 (Payment Requested), 1 (Confirmed)
    // Calling agents do not have access to Column 0 (New Leads)
    $allowedStatuses = $isCallingAgent ? [1, 2, 3, 6, 9] : [0, 1, 2, 3, 6, 9];
    if (!in_array($targetStatus, $allowedStatuses, true)) {
        echo json_encode([
            'status' => 200,
            'data'   => [],
            'offset' => $offset,
            'limit'  => $limit,
            'total'  => 0,
        ]);
        exit();
    }

    $statusClause = "enquiry_status = {$targetStatus}";

    $sql = "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_email,
                   enquiry_subject, enquiry_time, enquiry_status, enquiry_source
            FROM {$tblPrefix}enquiry
            WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}
              AND {$statusClause}
              AND enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
            ORDER BY enquiry_time DESC
            LIMIT {$offset}, {$limit}";

    $leads = [];
    if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
        while ($row = mysqli_fetch_assoc($res)) {
            $stInt = (int)$row['enquiry_status'];
            if (!in_array($stInt, [0, 1, 2, 3, 6, 9], true)) {
                continue;
            }
            $meta = getLeadStatusMeta($stInt);
            $fullName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
            if (empty($fullName)) {
                $fullName = 'Lead #' . $row['enquiry_id'];
            }
            $isDigMkt = ($accessType === 'digital_marketing');
            $leads[] = [
                'id'           => (int)$row['enquiry_id'],
                'encoded_id'   => base64_encode($row['enquiry_id']),
                'name'         => $fullName,
                'first_name'   => $row['enquiry_first_name'] ?? '',
                'last_name'    => $row['enquiry_last_name'] ?? '',
                'mobile'       => ($isDigMkt || empty($row['enquiry_mobile'])) ? '' : $row['enquiry_mobile'],
                'email'        => ($isDigMkt || empty($row['enquiry_email']))  ? '' : $row['enquiry_email'],
                'subject'      => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'General Enquiry',
                'source'       => cleanLeadSource($row['enquiry_source']),
                'time'         => date('d M, h:i A', strtotime($row['enquiry_time'])),
                'status'       => $stInt,
                'status_label' => $meta['label'],
                'status_class' => $meta['class'],
                'status_color' => $meta['color'],
            ];
        }
    }

    echo json_encode([
        'status' => 200,
        'data'   => $leads,
        'offset' => $offset,
        'limit'  => $limit,
        'total'  => count($leads),
    ]);
    exit();
}

// Case 2: Full Board Load (for calling agents, status 0 'New Leads' is excluded)
$allowedStatuses = $isCallingAgent ? [2, 3, 9, 6, 1] : [0, 2, 3, 9, 6, 1];
$allowedStatusesStr = implode(', ', $allowedStatuses);
$grouped = [];
foreach ($allowedStatuses as $st) {
    $grouped[(string)$st] = [];
}
$flatLeads = [];

$limitClause = !empty($searchFilter) ? "LIMIT 100" : "LIMIT 150";

$sql = "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_email,
               enquiry_subject, enquiry_time, enquiry_status, enquiry_source
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}
          AND enquiry_status IN ({$allowedStatusesStr})
          AND enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
        ORDER BY enquiry_time DESC
        {$limitClause}";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $stInt = (int)$row['enquiry_status'];
        if (!in_array($stInt, $allowedStatuses, true)) {
            continue;
        }
        $meta = getLeadStatusMeta($stInt);
        $groupKey = (string)$stInt;
        $fullName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = 'Lead #' . $row['enquiry_id'];
        }
        $isDigMkt = ($accessType === 'digital_marketing');
        $item = [
            'id'         => (int)$row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name'       => $fullName,
            'first_name' => $row['enquiry_first_name'] ?? '',
            'last_name'  => $row['enquiry_last_name'] ?? '',
            'mobile'     => ($isDigMkt || empty($row['enquiry_mobile'])) ? '' : $row['enquiry_mobile'],
            'email'      => ($isDigMkt || empty($row['enquiry_email']))  ? '' : $row['enquiry_email'],
            'subject'    => !empty($row['enquiry_subject']) ? $row['enquiry_subject']       : 'General Enquiry',
            'source'     => cleanLeadSource($row['enquiry_source']),
            'time'         => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status'       => $stInt,
            'status_label' => $meta['label'],
            'status_class' => $meta['class'],
            'status_color' => $meta['color'],
        ];
        $flatLeads[] = $item;
        if (isset($grouped[$groupKey])) {
            $grouped[$groupKey][] = $item;
        }
    }
}

$statusCounts = [];
foreach ($allowedStatuses as $st) {
    $statusCounts[(string)$st] = 0;
}
$countSql = "SELECT enquiry_status, COUNT(*) as cnt
             FROM {$tblPrefix}enquiry
             WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}
               AND enquiry_status IN ({$allowedStatusesStr})
               AND enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
             GROUP BY enquiry_status";
if (isset($connection) && $connection && ($cRes = mysqli_query($connection, $countSql))) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        $stVal = (int)$cRow['enquiry_status'];
        $cnt   = (int)$cRow['cnt'];
        $stKey = (string)$stVal;
        if (isset($statusCounts[$stKey])) {
            $statusCounts[$stKey] = $cnt;
        }
    }
}

echo json_encode([
    'status'  => 200,
    'data'    => $grouped,
    'grouped' => $grouped,
    'counts'  => $statusCounts,
    'leads'   => $flatLeads,
    'total'   => count($flatLeads),
]);
exit();
