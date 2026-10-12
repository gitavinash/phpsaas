<?php
/**
 * API: Create Lead Follow-Up Task
 * File: api/v3/leads/add-task.php
 */

header("Content-Type: application/json; charset=utf-8");

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

$rawInput = file_get_contents('php://input');
$jsonData = !empty($rawInput) ? json_decode($rawInput, true) : [];
if (!is_array($jsonData)) $jsonData = [];

$rawLeadId    = $_POST['leadId'] ?? ($_POST['lead_id'] ?? ($_POST['id'] ?? ($jsonData['leadId'] ?? ($jsonData['lead_id'] ?? ($jsonData['id'] ?? '')))));
$taskType     = ucwords(strtolower(trim($_POST['task_type'] ?? ($_POST['type'] ?? ($jsonData['task_type'] ?? ($jsonData['type'] ?? 'Call'))))));
$rawDueTime   = trim($_POST['due_at'] ?? ($_POST['followup_time'] ?? ($_POST['followupTime'] ?? ($jsonData['due_at'] ?? ($jsonData['followup_time'] ?? '')))));
$notes        = trim($_POST['notes'] ?? ($_POST['msg'] ?? ($jsonData['notes'] ?? ($jsonData['msg'] ?? ''))));
$nextAction   = trim($_POST['next_action'] ?? ($jsonData['next_action'] ?? ''));
$reqAgentId   = $_POST['agent_id'] ?? ($jsonData['agent_id'] ?? null);

$allowedTypes = ['Call', 'WhatsApp', 'Email', 'Meeting', 'Other'];
if (!in_array($taskType, $allowedTypes, true)) {
    $taskType = 'Call';
}

if (empty($rawLeadId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
    exit();
}

$decoded = base64_decode(trim((string)$rawLeadId), true);
$leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;

if ($leadId <= 0 || !isset($connection) || !$connection) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid lead identifier.']);
    exit();
}

if (empty($rawDueTime)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please select a follow-up date and time.']);
    exit();
}

$dueTs = strtotime($rawDueTime);
if (!$dueTs || $dueTs <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid date/time format.']);
    exit();
}
$dueFormatted = date('Y-m-d H:i:s', $dueTs);

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

// Verify lead exists
$leadQ = mysqli_query($connection, "SELECT enquiry_id, enquiry_site_id, enquiry_agent, enquiry_status, enquiry_first_name, enquiry_last_name 
                                     FROM {$tblPrefix}enquiry 
                                     WHERE enquiry_id = {$leadId}{$siteClause} 
                                     LIMIT 1");

if (!$leadQ || mysqli_num_rows($leadQ) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$leadData = mysqli_fetch_assoc($leadQ);
$assignedAgent = (int)($leadData['enquiry_agent'] ?? 0);
$taskAgentId = ($reqAgentId !== null && is_numeric($reqAgentId) && (int)$reqAgentId > 0)
    ? (int)$reqAgentId
    : ($assignedAgent > 0 ? $assignedAgent : $currentUserId);

$taskSiteId = ($siteId > 0) ? $siteId : (int)($leadData['enquiry_site_id'] ?? 0);

// Prevent accidental duplicate active task
$dupCheckSql = "SELECT task_id FROM {$tblPrefix}enquiry_task 
                WHERE task_enquiry_id = {$leadId} 
                  AND task_status = 'pending' 
                  AND task_type = '{$taskType}' 
                  AND task_due_at = '{$dueFormatted}' 
                LIMIT 1";
$dupRes = mysqli_query($connection, $dupCheckSql);
if ($dupRes && mysqli_num_rows($dupRes) > 0) {
    $dupRow = mysqli_fetch_assoc($dupRes);
    echo json_encode([
        'status'  => 'warning',
        'message' => "A pending {$taskType} task is already scheduled for this lead at " . date('d M Y, h:i A', $dueTs) . ".",
        'task_id' => (int)$dupRow['task_id']
    ]);
    exit();
}

// Get creator name
$creatorName = 'Agent';
if ($currentUserId > 0) {
    $uRes = mysqli_query($connection, "SELECT profile_display_name FROM {$tblPrefix}profile WHERE profile_user_id = {$currentUserId} LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        if (!empty($uRow['profile_display_name'])) {
            $creatorName = trim($uRow['profile_display_name']);
        }
    }
}

mysqli_query($connection, 'START TRANSACTION');

// Insert into isw_enquiry_task
$safeNotes = mysqli_real_escape_string($connection, $notes);
$safeAction = mysqli_real_escape_string($connection, $nextAction);

$insertTaskSql = "INSERT INTO {$tblPrefix}enquiry_task 
    (task_site_id, task_enquiry_id, task_agent_id, task_type, task_due_at, task_notes, task_next_action, task_status) 
    VALUES 
    ({$taskSiteId}, {$leadId}, {$taskAgentId}, '{$taskType}', '{$dueFormatted}', '{$safeNotes}', '{$safeAction}', 'pending')";

if (!mysqli_query($connection, $insertTaskSql)) {
    mysqli_query($connection, 'ROLLBACK');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error creating task: ' . mysqli_error($connection)]);
    exit();
}

$newTaskId = mysqli_insert_id($connection);

// Synchronize lead enquiry_followup timestamp
$currentStatus = (int)($leadData['enquiry_status'] ?? 0);
$updateFields = ["enquiry_followup = '{$dueFormatted}'", "enquiry_update = NOW()"];

// If lead is at New Enquiry (0) or Assigned (2), mark as Follow Up (3)
if ($currentStatus === 0 || $currentStatus === 2) {
    $updateFields[] = "enquiry_status = 3";
    $currentStatus = 3;
}

$updateLeadSql = "UPDATE {$tblPrefix}enquiry SET " . implode(', ', $updateFields) . " WHERE enquiry_id = {$leadId}";
mysqli_query($connection, $updateLeadSql);

// Log to enquiry_notes for timeline continuity
$followupObj = [
    'task_id'            => $newTaskId,
    'type'               => $taskType,
    'category'           => "{$taskType} Follow-Up",
    'note'               => $notes,
    'next_action'        => $nextAction,
    'followup_time'      => $dueFormatted,
    'followup_formatted' => date('d M Y, h:i A', $dueTs),
    'scheduled_by'       => $creatorName
];
$metaVal = mysqli_real_escape_string($connection, json_encode($followupObj));
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes 
                  (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', 'followup', '{$metaVal}')";
mysqli_query($connection, $insertNoteSql);

// Ensure isw_enquiry_reminder record is prepared for notifications
$insRemSql = "INSERT INTO {$tblPrefix}enquiry_reminder 
              (reminder_enquiry_id, reminder_agent_id, reminder_followup_time, reminder_push_sent, reminder_email_sent) 
              VALUES ({$leadId}, {$taskAgentId}, '{$dueFormatted}', 0, 0)";
@mysqli_query($connection, $insRemSql);

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status'        => 'success',
    'message'       => "Follow-up task scheduled for " . date('d M Y, h:i A', $dueTs) . ".",
    'task_id'       => $newTaskId,
    'lead_id'       => $leadId,
    'task_type'     => $taskType,
    'due_at'        => $dueFormatted,
    'due_formatted' => date('d M Y, h:i A', $dueTs),
    'agent_id'      => $taskAgentId,
    'lead_status'   => $currentStatus,
]);
exit();
