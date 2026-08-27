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
    ? " AND (enquiry_agent = '{$userId}')"
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

echo json_encode([
    'status' => 200,
    'labels' => $flowLabels,
    'data'   => $flowData,
    'total'  => array_sum($flowData),
]);
exit();
