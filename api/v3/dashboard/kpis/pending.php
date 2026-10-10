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

$siteFilter = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
$siteQuotationFilter = ($siteId > 0) ? " AND quotation_site_id = {$siteId}" : "";
$userFilter = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (e.enquiry_agent = '{$userId}')"
    : "";

$daterangeFilter = "";
$t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dr = trim($_GET['daterange']);
    if (($ampPos = strpos($dr, '&')) !== false) {
        $dr = substr($dr, 0, $ampPos);
    }
    $dates = explode(' - ', $dr);
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
        $t2 = date('Y-m-d 23:59:59', strtotime(trim($dates[1])));
    }
}
$count = 0;
$sql = "SELECT COUNT(DISTINCT e.enquiry_id) AS cnt
        FROM {$tblPrefix}enquiry e
        WHERE {$siteFilter}{$userFilter}
          AND e.enquiry_status NOT IN (4, 5, 15, 16, 17, 18)
          AND (
              (e.enquiry_time BETWEEN '{$t1}' AND '{$t2}' AND e.enquiry_status = 9)
              OR e.enquiry_id IN (
                  SELECT quotation_ref_id FROM {$tblPrefix}quotation
                  WHERE quotation_status = 0 AND quotation_ref_type = 'enquiry' {$siteQuotationFilter}
                    AND quotation_time BETWEEN '{$t1}' AND '{$t2}'
              )
          )";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    if ($row = mysqli_fetch_assoc($res)) {
        $count = (int)$row['cnt'];
    }
}

echo json_encode([
    'status' => 200,
    'kpi'    => 'pending',
    'count'  => $count,
]);
exit();
