<?php
require_once LOGIN_CHECK;
header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';

if ($currentUserId <= 0) {
    echo json_encode(['status' => 'success', 'alerts' => [], 'total' => 0]);
    exit();
}

$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
// Check followups assigned to this user
$agentFilter = "enquiry_agent = '{$currentUserId}'";

// Due within next 5 minutes or in the past 15 minutes
$sql = "SELECT enquiry_id, enquiry_agent, enquiry_first_name AS name, enquiry_mobile,
               enquiry_subject, enquiry_followup
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}
          AND {$agentFilter}
          AND enquiry_status = 3
          AND enquiry_followup IS NOT NULL
          AND enquiry_followup != '0000-00-00 00:00:00'
          AND enquiry_followup <= DATE_ADD(NOW(), INTERVAL 330 SECOND)
          AND enquiry_followup >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ORDER BY enquiry_followup ASC
        LIMIT 10";

$alerts = [];
if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $fTs = strtotime($row['enquiry_followup']);
        $alerts[] = [
            'lead_id'            => (int)$row['enquiry_id'],
            'encoded_id'         => base64_encode((string)$row['enquiry_id']),
            'customer_name'      => !empty($row['name']) ? $row['name'] : 'Lead #' . $row['enquiry_id'],
            'mobile'             => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile'] : '',
            'subject'            => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'Follow-Up',
            'followup_time'      => $row['enquiry_followup'],
            'followup_formatted' => $fTs ? date('d M Y, h:i A', $fTs) : $row['enquiry_followup']
        ];
    }
}

echo json_encode([
    'status'  => 'success',
    'alerts'  => $alerts,
    'total'   => count($alerts),
    'user_id' => $currentUserId
]);
exit();
