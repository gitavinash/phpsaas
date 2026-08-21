<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';

// Smart site ID filtering
$siteFilter = "1=1";
if (defined('SITE_ID') && SITE_ID > 0) {
    if (isset($connection) && $connection) {
        $checkSql = "SELECT enquiry_id FROM {$tblPrefix}enquiry WHERE enquiry_site_id = " . SITE_ID . " LIMIT 1";
        if ($checkRes = mysqli_query($connection, $checkSql)) {
            if (mysqli_num_rows($checkRes) > 0) {
                $siteFilter = "enquiry_site_id = " . SITE_ID;
            }
        }
    }
}

$userId = defined('USER_ID') ? USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';
$userFilter = ($accessType != 'administrator' && $userId > 0) ? " AND (enquiry_agent = '{$userId}' OR enquiry_agent = '0')" : "";

// Date Duration Logic: Default to Last 30 Days if no daterange query provided
$daterangeFilter = "";
$t1 = date('Y-m-d 00:00:00', strtotime('-1 month'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
        $daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
    }
} else {
    $daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
}

// 1. KPI COUNTERS SUMMARY
$kpis = array(
    'totalLeads' => 0,
    'freshLeads' => 0,
    'confirmedLeads' => 0,
    'pendingLeads' => 0,
    'followupLeads' => 0,
    'duplicateLeads' => 0,
    'cancelLeads' => 0,
    'pendingRequests' => 0,
    'conversionRate' => '0%',
    'totalRevenue' => '₹0'
);

$sqlKpi = "SELECT enquiry_status, COUNT(*) as count FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$daterangeFilter} GROUP BY enquiry_status";
if (isset($connection) && $connection && ($res = mysqli_query($connection, $sqlKpi))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cnt = intval($row['count']);
        $st = intval($row['enquiry_status']);
        $kpis['totalLeads'] += $cnt;

        switch ($st) {
            case 0: $kpis['freshLeads'] = $cnt; break;
            case 1: $kpis['confirmedLeads'] = $cnt; break;
            case 2: $kpis['pendingLeads'] = $cnt; break;
            case 3: $kpis['followupLeads'] = $cnt; break;
            case 4: $kpis['duplicateLeads'] = $cnt; break;
            case 5: $kpis['cancelLeads'] = $cnt; break;
        }
    }
}

// Calculate Conversion Rate %
if ($kpis['totalLeads'] > 0) {
    $rate = round(($kpis['confirmedLeads'] / $kpis['totalLeads']) * 100, 1);
    $kpis['conversionRate'] = $rate . '%';
}

// Revenue Summary from transaction table
$sqlRev = "SELECT SUM(transaction_amount) as total_rev FROM {$tblPrefix}transaction WHERE transaction_status = 1";
if (defined('SITE_ID') && SITE_ID > 0 && $siteFilter !== "1=1") {
    $sqlRev .= " AND transaction_site_id = " . SITE_ID;
}
if (!empty($daterangeFilter)) {
    $sqlRev .= str_replace('enquiry_time', 'transaction_time', $daterangeFilter);
}
if (isset($connection) && $connection && ($resRev = mysqli_query($connection, $sqlRev))) {
    if ($rowRev = mysqli_fetch_assoc($resRev)) {
        $amt = floatval($rowRev['total_rev']);
        $kpis['totalRevenue'] = '₹' . number_format($amt);
    }
}

// Pending Requests
$sqlReq = "SELECT COUNT(*) as cnt FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$daterangeFilter} AND enquiry_agent=0 AND enquiry_status=0";
if (isset($connection) && $connection && ($resReq = mysqli_query($connection, $sqlReq))) {
    if ($rowReq = mysqli_fetch_assoc($resReq)) {
        $kpis['pendingRequests'] = intval($rowReq['cnt']);
    }
}

// 2. ENQUIRY SOURCE DISTRIBUTION (Chart Data)
$sourceLabels = array();
$sourceData = array();
$sqlSource = "SELECT 
    CASE 
        WHEN enquiry_source IS NULL OR enquiry_source = '' THEN 'Direct'
        ELSE enquiry_source 
    END AS source_name, 
    COUNT(*) as count 
FROM {$tblPrefix}enquiry 
WHERE {$siteFilter} {$userFilter} {$daterangeFilter} 
GROUP BY source_name 
ORDER BY count DESC LIMIT 6";

if (isset($connection) && $connection && ($resSource = mysqli_query($connection, $sqlSource))) {
    while ($row = mysqli_fetch_assoc($resSource)) {
        $sourceLabels[] = ucfirst($row['source_name']);
        $sourceData[] = intval($row['count']);
    }
}

// 3. ENQUIRY PERFORMANCE DISTRIBUTION (Chart Data)
$perfLabels = array('Fresh', 'Pending', 'Follow Up', 'Confirmed', 'Cancelled');
$perfData = array(
    isset($kpis['freshLeads']) ? $kpis['freshLeads'] : 0,
    $kpis['pendingLeads'],
    $kpis['followupLeads'],
    $kpis['confirmedLeads'],
    $kpis['cancelLeads']
);

// 4. ENQUIRY TIMELINE FLOW (Day-wise / Month-wise / Hour-wise)
$flowLabels = array();
$flowData = array();

$diffDays = round(abs(strtotime($t2) - strtotime($t1)) / 86400) + 1;

if ($diffDays <= 7) {
    // Hour-wise flow
    $sqlFlow = "SELECT DATE_FORMAT(enquiry_time, '%H:00') as label, COUNT(*) as count FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$daterangeFilter} GROUP BY label ORDER BY label ASC";
} elseif ($diffDays <= 90) {
    // Day-wise flow
    $sqlFlow = "SELECT DATE_FORMAT(enquiry_time, '%d %b') as label, COUNT(*) as count FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$daterangeFilter} GROUP BY label ORDER BY enquiry_time ASC";
} else {
    // Month-wise flow
    $sqlFlow = "SELECT DATE_FORMAT(enquiry_time, '%b %Y') as label, COUNT(*) as count FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$daterangeFilter} GROUP BY label ORDER BY enquiry_time ASC";
}

if (isset($connection) && $connection && ($resFlow = mysqli_query($connection, $sqlFlow))) {
    while ($row = mysqli_fetch_assoc($resFlow)) {
        $flowLabels[] = $row['label'];
        $flowData[] = intval($row['count']);
    }
}

// 5. ENQUIRY SOURCE FLOW BARS (Comparison Breakdown)
$sourceFlowBars = array();
$totalSourceLeads = array_sum($sourceData);
foreach ($sourceLabels as $idx => $sName) {
    $sCount = $sourceData[$idx];
    $sPct = ($totalSourceLeads > 0) ? round(($sCount / $totalSourceLeads) * 100, 1) : 0;
    $sourceFlowBars[] = array(
        'source' => $sName,
        'count' => $sCount,
        'percentage' => $sPct
    );
}

// 6. KANBAN BOARD LEADS (LATEST LEADS LOGIC)
$kanbanLeads = array(
    '0' => array(),
    '2' => array(),
    '3' => array(),
    '1' => array()
);

$sqlKanban = "SELECT enquiry_id, enquiry_agent, enquiry_first_name as name, enquiry_mobile, enquiry_subject, enquiry_time, enquiry_status, enquiry_source FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} ORDER BY enquiry_time DESC LIMIT 150";
if (isset($connection) && $connection && ($resKanban = mysqli_query($connection, $sqlKanban))) {
    while ($row = mysqli_fetch_assoc($resKanban)) {
        $st = (string)$row['enquiry_status'];
        $item = array(
            'id' => $row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name' => !empty($row['name']) ? $row['name'] : 'Lead #' . $row['enquiry_id'],
            'mobile' => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : 'N/A',
            'subject' => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Enquiry',
            'source' => !empty($row['enquiry_source']) ? $row['enquiry_source'] : 'Direct',
            'time' => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status' => $row['enquiry_status']
        );
        if (isset($kanbanLeads[$st])) {
            $kanbanLeads[$st][] = $item;
        } else {
            $kanbanLeads['0'][] = $item;
        }
    }
}

echo json_encode(array(
    'status' => 200,
    'kpis' => $kpis,
    'charts' => array(
        'enquirySource' => array(
            'labels' => $sourceLabels,
            'data' => $sourceData
        ),
        'enquiryPerformance' => array(
            'labels' => $perfLabels,
            'data' => $perfData
        ),
        'enquiryFlow' => array(
            'labels' => $flowLabels,
            'data' => $flowData
        ),
        'sourceFlowBars' => $sourceFlowBars
    ),
    'kanban' => $kanbanLeads
));
exit();
