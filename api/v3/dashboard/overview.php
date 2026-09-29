<?php

header("Content-type: application/json; charset=utf-8");

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

// Always enforce site_id filter on every query
$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
$userFilter  = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}' OR enquiry_agent = '0')"
    : "";

// Date Duration Logic: Default to Last 30 Days if no daterange query provided
$daterangeFilter = "";
$t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
    }
}
$daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";

// ---- 1. KPI COUNTERS ----
$kpis = array(
    'totalLeads'     => 0,
    'freshLeads'     => 0,
    'confirmedLeads' => 0,
    'pendingLeads'   => 0,
    'followupLeads'  => 0,
    'duplicateLeads' => 0,
    'cancelLeads'    => 0,
    'pendingRequests'=> 0,
    'conversionRate' => '0%',
    'totalRevenue'   => '₹0'
);

$sqlKpi = "SELECT enquiry_status, COUNT(*) AS count
           FROM {$tblPrefix}enquiry
           WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
           GROUP BY enquiry_status";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sqlKpi))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cnt = intval($row['count']);
        $kpis['totalLeads'] += $cnt;
        $stVal = intval($row['enquiry_status']);
        switch ($stVal) {
            case 0: $kpis['freshLeads']     += $cnt; break;
            case 1: $kpis['confirmedLeads'] += $cnt; break;
            case 2: $kpis['pendingLeads']   += $cnt; break;
            case 3: $kpis['followupLeads']  += $cnt; break;
            case 5: $kpis['duplicateLeads'] += $cnt; break;
            case 4:
            case 16: $kpis['cancelLeads']   += $cnt; break;
        }
    }
}

if ($kpis['totalLeads'] > 0) {
    $kpis['conversionRate'] = round(($kpis['confirmedLeads'] / $kpis['totalLeads']) * 100, 1) . '%';
}

// ---- 2. REVENUE ----
$revDateFilter = str_replace('enquiry_time', 'transaction_time', $daterangeFilter);
$sqlRev = "SELECT SUM(transaction_amount) AS total_rev
           FROM {$tblPrefix}transaction
           WHERE transaction_status = 1"
        . ($siteId > 0 ? " AND transaction_site_id = {$siteId}" : "")
        . $revDateFilter;

if (isset($connection) && $connection && ($resRev = mysqli_query($connection, $sqlRev))) {
    if ($rowRev = mysqli_fetch_assoc($resRev)) {
        $kpis['totalRevenue'] = '₹' . number_format(floatval($rowRev['total_rev']));
    }
}

// ---- 3. PENDING REQUESTS (unassigned fresh leads) ----
$sqlReq = "SELECT COUNT(*) AS cnt
           FROM {$tblPrefix}enquiry
           WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
             AND enquiry_agent = 0
             AND enquiry_status = 0";

if (isset($connection) && $connection && ($resReq = mysqli_query($connection, $sqlReq))) {
    if ($rowReq = mysqli_fetch_assoc($resReq)) {
        $kpis['pendingRequests'] = intval($rowReq['cnt']);
    }
}

// ---- 4. ENQUIRY SOURCE DISTRIBUTION ----
function cleanSourceName($source) {
    if (empty($source) || trim($source) === '') {
        return 'Direct';
    }
    $cleaned = trim($source);
    $cleaned = preg_replace('#^https?://#i', '', $cleaned);
    $cleaned = preg_replace('#^www\.#i', '', $cleaned);
    $cleaned = rtrim($cleaned, '/');
    return ucfirst($cleaned);
}

$sourceMap = [];
$sqlSource = "SELECT enquiry_source, COUNT(*) AS count
              FROM {$tblPrefix}enquiry
              WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
              GROUP BY enquiry_source
              ORDER BY count DESC";

if (isset($connection) && $connection && ($resSource = mysqli_query($connection, $sqlSource))) {
    while ($row = mysqli_fetch_assoc($resSource)) {
        $name = cleanSourceName($row['enquiry_source']);
        $cnt = intval($row['count']);
        if (!isset($sourceMap[$name])) {
            $sourceMap[$name] = 0;
        }
        $sourceMap[$name] += $cnt;
    }
}

arsort($sourceMap);
$topSources   = array_slice($sourceMap, 0, 6, true);
$sourceLabels = array_keys($topSources);
$sourceData   = array_values($topSources);

// ---- 5. ENQUIRY PERFORMANCE DISTRIBUTION ----
$perfLabels = ['Fresh', 'Pending', 'Follow Up', 'Confirmed', 'Cancelled'];
$perfData   = [
    $kpis['freshLeads'],
    $kpis['pendingLeads'],
    $kpis['followupLeads'],
    $kpis['confirmedLeads'],
    $kpis['cancelLeads'],
];

// ---- 6. ENQUIRY TIMELINE FLOW (hourly / daily / monthly) ----
$flowLabels = [];
$flowData   = [];
$diffDays   = (int) ceil(abs(strtotime($t2) - strtotime($t1)) / 86400) + 1;

if ($diffDays <= 2) {
    $flowFmt = "DATE_FORMAT(enquiry_time,'%H:00')";
    $orderBy = "enquiry_time ASC";
} elseif ($diffDays <= 90) {
    $flowFmt = "DATE_FORMAT(enquiry_time,'%d %b')";
    $orderBy = "MIN(enquiry_time) ASC";
} else {
    $flowFmt = "DATE_FORMAT(enquiry_time,'%b %Y')";
    $orderBy = "MIN(enquiry_time) ASC";
}

$sqlFlow = "SELECT {$flowFmt} AS label, COUNT(*) AS count
            FROM {$tblPrefix}enquiry
            WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
            GROUP BY label
            ORDER BY {$orderBy}";

if (isset($connection) && $connection && ($resFlow = mysqli_query($connection, $sqlFlow))) {
    while ($row = mysqli_fetch_assoc($resFlow)) {
        $flowLabels[] = $row['label'];
        $flowData[]   = intval($row['count']);
    }
}

// ---- 7. SOURCE FLOW BARS (comparison %) ----
$sourceFlowBars   = [];
$totalSourceLeads = array_sum($sourceData);
foreach ($sourceLabels as $idx => $sName) {
    $cnt  = $sourceData[$idx];
    $pct  = $totalSourceLeads > 0 ? round(($cnt / $totalSourceLeads) * 100, 1) : 0;
    $sourceFlowBars[] = ['source' => $sName, 'count' => $cnt, 'percentage' => $pct];
}

// ---- 8. KANBAN (latest leads – no date filter, site-scoped) ----
$kanbanLeads = ['0' => [], '2' => [], '3' => [], '1' => []];

$sqlKanban = "SELECT enquiry_id, enquiry_first_name AS name, enquiry_mobile,
                     enquiry_subject, enquiry_time, enquiry_status, enquiry_source
              FROM {$tblPrefix}enquiry
              WHERE {$siteFilter}{$userFilter}
                AND enquiry_status IN (0, 1, 2, 3)
                AND enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
              ORDER BY enquiry_time DESC
              LIMIT 150";

if (isset($connection) && $connection && ($resKanban = mysqli_query($connection, $sqlKanban))) {
    while ($row = mysqli_fetch_assoc($resKanban)) {
        $stInt = (int)$row['enquiry_status'];
        if (in_array($stInt, [4, 5, 7, 15, 16, 17, 18], true) || !in_array($stInt, [0, 1, 2, 3], true)) {
            continue;
        }
        $st   = (string)$stInt;
        $mobileVal = ($accessType === 'digital_marketing') ? '' : (!empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : 'N/A');
        $item = [
            'id'         => $row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name'       => !empty($row['name'])               ? $row['name']               : 'Lead #' . $row['enquiry_id'],
            'mobile'     => $mobileVal,
            'subject'    => !empty($row['enquiry_subject'])    ? $row['enquiry_subject']     : 'Enquiry',
            'source'     => !empty($row['enquiry_source'])     ? $row['enquiry_source']      : 'Direct',
            'time'       => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status'     => $stInt,
        ];
        if (isset($kanbanLeads[$st])) {
            $kanbanLeads[$st][] = $item;
        }
    }
}

// ---- 9. UPCOMING FOLLOWUPS (Earliest followups) ----
$followups = [];
$sqlFollowups = "SELECT enquiry_id, enquiry_agent, enquiry_first_name AS name, enquiry_mobile,
                        enquiry_subject, enquiry_time, enquiry_status, enquiry_source, enquiry_followup
                 FROM {$tblPrefix}enquiry
                 WHERE {$siteFilter}{$userFilter} AND enquiry_status = 3
                 ORDER BY enquiry_followup ASC
                 LIMIT 10";

if (isset($connection) && $connection && ($resFollow = mysqli_query($connection, $sqlFollowups))) {
    while ($row = mysqli_fetch_assoc($resFollow)) {
        $followupMobile = ($accessType === 'digital_marketing') ? '' : (!empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : 'N/A');
        $followups[] = [
            'id'          => $row['enquiry_id'],
            'encoded_id'  => base64_encode($row['enquiry_id']),
            'name'        => !empty($row['name']) ? $row['name'] : 'Lead #' . $row['enquiry_id'],
            'mobile'      => $followupMobile,
            'subject'     => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Enquiry',
            'source'      => !empty($row['enquiry_source']) ? $row['enquiry_source'] : 'Direct',
            'time'        => !empty($row['enquiry_followup']) ? date('d-m-Y h:i A', strtotime($row['enquiry_followup'])) : 'N/A',
            'followupRaw' => $row['enquiry_followup']
        ];
    }
}

echo json_encode([
    'status'    => 200,
    'kpis'      => $kpis,
    'charts'    => [
        'enquirySource'      => ['labels' => $sourceLabels, 'data' => $sourceData],
        'enquiryPerformance' => ['labels' => $perfLabels,   'data' => $perfData],
        'enquiryFlow'        => ['labels' => $flowLabels,   'data' => $flowData],
        'sourceFlowBars'     => $sourceFlowBars,
    ],
    'kanban'    => $kanbanLeads,
    'followups' => $followups,
]);
exit();
