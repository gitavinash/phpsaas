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

$t1 = date('Y-m-d 00:00:00', strtotime('-1 month'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
    }
}

$daterangeFilter = " AND transaction_time BETWEEN '{$t1}' AND '{$t2}'";
$siteFilter = ($siteId > 0) ? " AND transaction_site_id = {$siteId}" : "";

$totalRevenueRaw = 0;
$totalRevenueFormatted = '₹0';

$sqlRev = "SELECT SUM(transaction_amount) AS total_rev
           FROM {$tblPrefix}transaction
           WHERE transaction_status = 1{$siteFilter}{$daterangeFilter}";

if (isset($connection) && $connection && ($resRev = mysqli_query($connection, $sqlRev))) {
    if ($rowRev = mysqli_fetch_assoc($resRev)) {
        $totalRevenueRaw = floatval($rowRev['total_rev']);
        $totalRevenueFormatted = '₹' . number_format($totalRevenueRaw);
    }
}

echo json_encode([
    'status'       => 200,
    'totalRevenue' => $totalRevenueFormatted,
    'amount'       => $totalRevenueRaw,
]);
exit();
