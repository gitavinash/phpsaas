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
$userFilter  = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}' OR enquiry_agent = '0')"
    : "";

$limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;

$followups = [];
$sql = "SELECT enquiry_id, enquiry_agent, enquiry_first_name AS name, enquiry_mobile,
               enquiry_subject, enquiry_time, enquiry_status, enquiry_source, enquiry_followup
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}{$userFilter} AND enquiry_status = 3
        ORDER BY enquiry_followup ASC
        LIMIT {$limit}";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $followups[] = [
            'id'          => (int)$row['enquiry_id'],
            'encoded_id'  => base64_encode($row['enquiry_id']),
            'name'        => !empty($row['name']) ? $row['name'] : 'Lead #' . $row['enquiry_id'],
            'mobile'      => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : 'N/A',
            'subject'     => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Enquiry',
            'source'      => !empty($row['enquiry_source']) ? $row['enquiry_source'] : 'Direct',
            'time'        => !empty($row['enquiry_followup']) ? date('d-m-Y h:i A', strtotime($row['enquiry_followup'])) : 'N/A',
            'followupRaw' => $row['enquiry_followup']
        ];
    }
}

echo json_encode([
    'status'    => 200,
    'followups' => $followups,
    'total'     => count($followups),
]);
exit();
