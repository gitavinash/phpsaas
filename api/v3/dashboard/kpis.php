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

$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
$userFilter = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}' OR enquiry_agent = '0')"
    : "";

$daterangeFilter = "";
$t1 = date('Y-m-d 00:00:00', strtotime('-1 month'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
    }
}
$daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";

$kpis = array(
    'totalLeads'     => 0,
    'freshLeads'     => 0,
    'confirmedLeads' => 0,
    'pendingLeads'   => 0,
    'followupLeads'  => 0,
    'duplicateLeads' => 0,
    'cancelLeads'    => 0,
    'pendingRequests'=> 0,
    'conversionRate' => '0%'
);

$sqlKpi = "SELECT enquiry_status, COUNT(*) AS count
           FROM {$tblPrefix}enquiry
           WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
           GROUP BY enquiry_status";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sqlKpi))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cnt = intval($row['count']);
        $kpis['totalLeads'] += $cnt;
        switch (intval($row['enquiry_status'])) {
            case 0: $kpis['freshLeads']     = $cnt; break;
            case 1: $kpis['confirmedLeads'] = $cnt; break;
            case 2: $kpis['pendingLeads']   = $cnt; break;
            case 3: $kpis['followupLeads']  = $cnt; break;
            case 4: $kpis['duplicateLeads'] = $cnt; break;
            case 5: $kpis['cancelLeads']    = $cnt; break;
        }
    }
}

if ($kpis['totalLeads'] > 0) {
    $kpis['conversionRate'] = round(($kpis['confirmedLeads'] / $kpis['totalLeads']) * 100, 1) . '%';
}

// Pending Requests (Unassigned Fresh Leads)
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

echo json_encode([
    'status' => 200,
    'kpis'   => $kpis,
]);
exit();
