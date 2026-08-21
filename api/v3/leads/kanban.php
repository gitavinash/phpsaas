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

$statusFilter = "";
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $statusFilter = " AND enquiry_status = " . intval($_GET['status']);
}

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit  = isset($_GET['limit'])  ? min(intval($_GET['limit']), 50) : 15;

// Latest Leads Query — always site-scoped, no date filter for Kanban
$sql = "SELECT enquiry_id, enquiry_first_name AS name, enquiry_mobile,
               enquiry_subject, enquiry_time, enquiry_status, enquiry_source
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}{$userFilter}{$statusFilter}
        ORDER BY enquiry_time DESC
        LIMIT {$offset}, {$limit}";

$leads = [];
if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $leads[] = [
            'id'         => $row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name'       => !empty($row['name'])            ? $row['name']            : 'Lead #' . $row['enquiry_id'],
            'mobile'     => !empty($row['enquiry_mobile'])  ? $row['enquiry_mobile']  : 'N/A',
            'subject'    => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Enquiry',
            'source'     => !empty($row['enquiry_source'])  ? $row['enquiry_source']  : 'Direct',
            'time'       => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status'     => $row['enquiry_status'],
        ];
    }
}

echo json_encode(['status' => 200, 'data' => $leads, 'offset' => $offset, 'limit' => $limit]);
exit();
