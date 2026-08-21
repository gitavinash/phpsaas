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

$statusFilter = "";
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $stVal = intval($_GET['status']);
    $statusFilter = " AND enquiry_status = {$stVal}";
}

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 15;

// Latest Leads Query Logic
$sql = "SELECT enquiry_id, enquiry_agent, enquiry_first_name as name, enquiry_mobile, enquiry_subject, enquiry_time, enquiry_status, enquiry_source FROM {$tblPrefix}enquiry WHERE {$siteFilter} {$userFilter} {$statusFilter} ORDER BY enquiry_time DESC LIMIT {$offset}, {$limit}";

$leads = array();
if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $leads[] = array(
            'id' => $row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name' => !empty($row['name']) ? $row['name'] : 'Lead #' . $row['enquiry_id'],
            'mobile' => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : 'N/A',
            'subject' => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Enquiry',
            'source' => !empty($row['enquiry_source']) ? $row['enquiry_source'] : 'Direct',
            'time' => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status' => $row['enquiry_status']
        );
    }
}

echo json_encode(array(
    'status' => 200,
    'data' => $leads,
    'offset' => $offset,
    'limit' => $limit
));
exit();
