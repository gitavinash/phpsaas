<?php

header("Content-type: application/json; charset=utf-8");

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
$accessType = isset($site_access_type) ? $site_access_type : 'customer';

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

$count = 0;
$sql = "SELECT COUNT(*) AS cnt
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
          AND enquiry_status = 5";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    if ($row = mysqli_fetch_assoc($res)) {
        $count = (int)$row['cnt'];
    }
}

echo json_encode([
    'status' => 200,
    'kpi'    => 'cancel',
    'count'  => $count,
]);
exit();
