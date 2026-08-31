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
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;

$rawLeadId = isset($_REQUEST['lead_id']) ? $_REQUEST['lead_id'] : (isset($_REQUEST['leadId']) ? $_REQUEST['leadId'] : '');
$leadId = 0;
if (!empty($rawLeadId)) {
    $decoded = base64_decode(trim($rawLeadId), true);
    $leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;
}

$agentId = isset($_REQUEST['agent_id']) ? (int)$_REQUEST['agent_id'] : $currentUserId;
if ($agentId <= 0) {
    $agentId = $currentUserId;
}

$datetimeStr = isset($_REQUEST['datetime']) ? trim($_REQUEST['datetime']) : '';
if (empty($datetimeStr)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Datetime is required for conflict check.'
    ]);
    exit();
}

$targetTimestamp = strtotime($datetimeStr);
if (!$targetTimestamp) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid datetime format.'
    ]);
    exit();
}

$targetDbTime = date('Y-m-d H:i:s', $targetTimestamp);
$targetTimeFormatted = date('h:i A', $targetTimestamp);
$targetDateFormatted = date('d M Y', $targetTimestamp);

// Check if agent already has another active lead (status = 3) scheduled for this exact follow-up timestamp
$clashSql = "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_subject, enquiry_followup 
             FROM {$tblPrefix}enquiry 
             WHERE enquiry_site_id = {$siteId} 
               AND enquiry_agent = '{$agentId}' 
               AND enquiry_id != {$leadId} 
               AND enquiry_status = 3 
               AND enquiry_followup = '{$targetDbTime}' 
             LIMIT 1";

$clashRes = mysqli_query($connection, $clashSql);

if ($clashRes && mysqli_num_rows($clashRes) > 0) {
    $clashRow = mysqli_fetch_assoc($clashRes);
    $custName = trim(($clashRow['enquiry_first_name'] ?? '') . ' ' . ($clashRow['enquiry_last_name'] ?? ''));
    if (empty($custName)) {
        $custName = 'Lead #' . $clashRow['enquiry_id'];
    }

    echo json_encode([
        'status' => 'conflict',
        'has_conflict' => true,
        'conflict_lead_id' => (int)$clashRow['enquiry_id'],
        'conflict_lead_encoded' => base64_encode((string)$clashRow['enquiry_id']),
        'conflict_customer' => $custName,
        'conflict_subject' => $clashRow['enquiry_subject'] ?? 'Follow-Up',
        'conflict_time' => $targetTimeFormatted,
        'conflict_date' => $targetDateFormatted,
        'message' => "Time Conflict: You already have a follow-up with {$custName} (Lead #{$clashRow['enquiry_id']}) scheduled at {$targetTimeFormatted} on {$targetDateFormatted}."
    ]);
    exit();
}

echo json_encode([
    'status' => 'available',
    'has_conflict' => false,
    'time_formatted' => $targetTimeFormatted,
    'date_formatted' => $targetDateFormatted,
    'message' => "Time slot ({$targetTimeFormatted}) is available."
]);
exit();
