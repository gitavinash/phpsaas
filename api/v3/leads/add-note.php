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
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;

$rawLeadId = isset($_POST['leadId']) ? $_POST['leadId'] : (isset($_POST['lead_id']) ? $_POST['lead_id'] : '');
if (empty($rawLeadId)) {
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
    exit();
}

$decoded = base64_decode(trim($rawLeadId), true);
$leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;

if ($leadId <= 0 || !isset($connection) || !$connection) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid lead request.']);
    exit();
}

$title = isset($_POST['title']) ? mysqli_real_escape_string($connection, trim($_POST['title'])) : 'Note';
$msg   = isset($_POST['msg']) ? mysqli_real_escape_string($connection, trim($_POST['msg'])) : '';
$followupTime = isset($_POST['followupTime']) ? trim($_POST['followupTime']) : '';
$isFinalNote = isset($_POST['finalNotes']) && ($_POST['finalNotes'] === 'true' || $_POST['finalNotes'] === true || $_POST['finalNotes'] === 1 || $_POST['finalNotes'] === '1');

if (empty($msg) && empty($title)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a note or summary.']);
    exit();
}

mysqli_query($connection, 'START TRANSACTION');

// Check if lead exists
$checkSql = "SELECT enquiry_id, enquiry_status FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId} LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$leadData = mysqli_fetch_assoc($checkRes);

// Insert note into enquiry_notes
$metaType = $title;
$metaVal  = $msg;
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', '{$metaType}', '{$metaVal}')";

if (!mysqli_query($connection, $insertNoteSql)) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Failed to log activity: ' . mysqli_error($connection)]);
    exit();
}

// Update lead status and follow-up time if specified
$updateFields = [];
if (!empty($followupTime)) {
    $fTimeFormatted = date('Y-m-d H:i:s', strtotime($followupTime));
    $updateFields[] = "enquiry_status = 3"; // Follow-Up status
    $updateFields[] = "enquiry_followup = '{$fTimeFormatted}'";
} elseif ($isFinalNote) {
    $updateFields[] = "enquiry_status = 4"; // Closed / Cancelled
}

if (!empty($updateFields)) {
    $updateFields[] = "enquiry_update = NOW()";
    $updateSql = "UPDATE {$tblPrefix}enquiry SET " . implode(', ', $updateFields) . " WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId}";
    if (!mysqli_query($connection, $updateSql)) {
        mysqli_query($connection, 'ROLLBACK');
        echo json_encode(['status' => 'error', 'message' => 'Failed to update lead status: ' . mysqli_error($connection)]);
        exit();
    }
}

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status' => 'success',
    'message' => !empty($followupTime) ? 'Follow-up reminder set successfully.' : 'Note added successfully.',
    'lead_id' => $leadId,
]);
exit();
