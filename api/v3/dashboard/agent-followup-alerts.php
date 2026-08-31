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

$siteFilter = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
// Check followups assigned to this user
$agentFilter = "e.enquiry_agent = '{$currentUserId}'";

// Ensure tracking table exists
$createTableSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}enquiry_reminder` (
  `reminder_id` INT AUTO_INCREMENT PRIMARY KEY,
  `reminder_enquiry_id` INT NOT NULL,
  `reminder_agent_id` INT NOT NULL,
  `reminder_followup_time` DATETIME NOT NULL,
  `reminder_push_sent` TINYINT(1) DEFAULT 0,
  `reminder_email_sent` TINYINT(1) DEFAULT 0,
  `reminder_sent_at` DATETIME NULL,
  `reminder_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`reminder_enquiry_id`),
  INDEX (`reminder_agent_id`),
  INDEX (`reminder_followup_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($connection, $createTableSql);

// Only select follow-ups due in the 5-minute reminder window (between now - 60s and now + 300s)
// and exclude any lead that has already had a reminder recorded
$sql = "SELECT e.enquiry_id, e.enquiry_agent, e.enquiry_first_name AS name, e.enquiry_mobile,
               e.enquiry_subject, e.enquiry_followup
        FROM {$tblPrefix}enquiry e
        LEFT JOIN {$tblPrefix}enquiry_reminder r 
            ON r.reminder_enquiry_id = e.enquiry_id 
            AND r.reminder_followup_time = e.enquiry_followup
            AND (r.reminder_email_sent = 1 OR r.reminder_push_sent = 1)
        WHERE {$siteFilter}
          AND {$agentFilter}
          AND e.enquiry_status = 3
          AND e.enquiry_followup IS NOT NULL
          AND e.enquiry_followup != '0000-00-00 00:00:00'
          AND e.enquiry_followup <= DATE_ADD(NOW(), INTERVAL 300 SECOND)
          AND e.enquiry_followup >= DATE_SUB(NOW(), INTERVAL 60 SECOND)
          AND r.reminder_id IS NULL
        ORDER BY e.enquiry_followup ASC
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
