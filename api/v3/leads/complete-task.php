<?php
/**
 * API: Complete Follow-Up Task & Optionally Schedule Next Follow-up
 * File: api/v3/leads/complete-task.php
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

$taskId    = (int)($_POST['task_id'] ?? ($jsonData['task_id'] ?? 0));
$outcome   = trim($_POST['outcome'] ?? ($jsonData['outcome'] ?? 'Completed'));
$notes     = trim($_POST['notes'] ?? ($jsonData['notes'] ?? ''));

// Optional Next Follow-Up Schedule
$scheduleNext   = !empty($_POST['schedule_next']) || !empty($jsonData['schedule_next']);
$nextType       = ucwords(strtolower(trim($_POST['next_task_type'] ?? ($jsonData['next_task_type'] ?? 'Call'))));
$nextDueTime    = trim($_POST['next_due_at'] ?? ($jsonData['next_due_at'] ?? ''));
$nextNotes      = trim($_POST['next_notes'] ?? ($jsonData['next_notes'] ?? ''));
$nextAction     = trim($_POST['next_action'] ?? ($jsonData['next_action'] ?? ''));

if ($taskId <= 0 || !isset($connection) || !$connection) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid Task ID is required.']);
    exit();
}

$taskQ = mysqli_query($connection, "SELECT t.*, e.enquiry_id, e.enquiry_agent, e.enquiry_first_name, e.enquiry_last_name 
                                     FROM {$tblPrefix}enquiry_task t 
                                     LEFT JOIN {$tblPrefix}enquiry e ON t.task_enquiry_id = e.enquiry_id 
                                     WHERE t.task_id = {$taskId} 
                                     LIMIT 1");

if (!$taskQ || mysqli_num_rows($taskQ) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Task not found.']);
    exit();
}

$task = mysqli_fetch_assoc($taskQ);
$leadId = (int)$task['task_enquiry_id'];

// Get user name
$agentName = 'Agent';
if ($currentUserId > 0) {
    $uRes = mysqli_query($connection, "SELECT profile_display_name FROM {$tblPrefix}profile WHERE profile_user_id = {$currentUserId} LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        if (!empty($uRow['profile_display_name'])) {
            $agentName = trim($uRow['profile_display_name']);
        }
    }
}

mysqli_query($connection, 'START TRANSACTION');

$nowStr = date('Y-m-d H:i:s');
$safeOutcome = mysqli_real_escape_string($connection, $outcome);
$safeNotes = mysqli_real_escape_string($connection, $notes);

// Complete task
$updateTaskSql = "UPDATE {$tblPrefix}enquiry_task 
                  SET task_status = 'completed',
                      task_outcome = '{$safeOutcome}',
                      task_completed_at = '{$nowStr}',
                      task_completed_by = {$currentUserId}
                  WHERE task_id = {$taskId}";

if (!mysqli_query($connection, $updateTaskSql)) {
    mysqli_query($connection, 'ROLLBACK');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to complete task: ' . mysqli_error($connection)]);
    exit();
}

// Log completion to timeline
$completionNote = "Completed {$task['task_type']} Follow-Up: {$outcome}" . (!empty($notes) ? " - {$notes}" : "") . " (by {$agentName})";
$safeCompNote = mysqli_real_escape_string($connection, $completionNote);
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes 
                  (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', 'followup_completed', '{$safeCompNote}')";
mysqli_query($connection, $insertNoteSql);

// Optional: schedule next follow-up
$nextTaskId = null;
if ($scheduleNext && !empty($nextDueTime)) {
    $nextDueTs = strtotime($nextDueTime);
    if ($nextDueTs && $nextDueTs > 0) {
        $nextDueFormatted = date('Y-m-d H:i:s', $nextDueTs);
        $taskSiteId = (int)($task['task_site_id'] ?? 0);
        $taskAgentId = (int)($task['task_agent_id'] ?? $currentUserId);
        $safeNextNotes = mysqli_real_escape_string($connection, $nextNotes);
        $safeNextAction = mysqli_real_escape_string($connection, $nextAction);

        $insNextSql = "INSERT INTO {$tblPrefix}enquiry_task 
            (task_site_id, task_enquiry_id, task_agent_id, task_type, task_due_at, task_notes, task_next_action, task_status) 
            VALUES 
            ({$taskSiteId}, {$leadId}, {$taskAgentId}, '{$nextType}', '{$nextDueFormatted}', '{$safeNextNotes}', '{$safeNextAction}', 'pending')";
        if (mysqli_query($connection, $insNextSql)) {
            $nextTaskId = mysqli_insert_id($connection);
            // Update enquiry_followup
            mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_followup = '{$nextDueFormatted}', enquiry_update = NOW() WHERE enquiry_id = {$leadId}");
        }
    }
} else {
    // Check if there are other pending tasks for this lead to update next follow-up timestamp
    $nextPendQ = mysqli_query($connection, "SELECT task_due_at FROM {$tblPrefix}enquiry_task WHERE task_enquiry_id = {$leadId} AND task_status = 'pending' ORDER BY task_due_at ASC LIMIT 1");
    if ($nextPendQ && mysqli_num_rows($nextPendQ) > 0) {
        $npRow = mysqli_fetch_assoc($nextPendQ);
        $nextDue = $npRow['task_due_at'];
        mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_followup = '{$nextDue}', enquiry_update = NOW() WHERE enquiry_id = {$leadId}");
    }
}

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status'       => 'success',
    'message'      => 'Task marked as completed.',
    'task_id'      => $taskId,
    'lead_id'      => $leadId,
    'outcome'      => $outcome,
    'completed_at' => date('d M Y, h:i A', strtotime($nowStr)),
    'next_task_id' => $nextTaskId,
]);
exit();
