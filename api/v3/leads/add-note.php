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
$checkSql = "SELECT enquiry_id, enquiry_status, enquiry_agent FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId} LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$leadData = mysqli_fetch_assoc($checkRes);

$category = isset($_POST['category']) ? trim($_POST['category']) : $title;
if (empty($category) || $category === 'Note') {
    $category = 'General Follow-up';
}

if (!empty($followupTime)) {
    $forceOverride = isset($_POST['force_override']) && ($_POST['force_override'] === '1' || $_POST['force_override'] === 'true' || $_POST['force_override'] === true);
    $fTimeFormatted = date('Y-m-d H:i:s', strtotime($followupTime));
    $agentId = (isset($leadData['enquiry_agent']) && (int)$leadData['enquiry_agent'] > 0) ? (int)$leadData['enquiry_agent'] : $currentUserId;

    if (!$forceOverride) {
        $clashSql = "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_subject 
                     FROM {$tblPrefix}enquiry 
                     WHERE enquiry_site_id = {$siteId} 
                       AND enquiry_agent = '{$agentId}' 
                       AND enquiry_id != {$leadId} 
                       AND enquiry_status = 3 
                       AND enquiry_followup = '{$fTimeFormatted}' 
                     LIMIT 1";
        $clashRes = mysqli_query($connection, $clashSql);
        if ($clashRes && mysqli_num_rows($clashRes) > 0) {
            mysqli_query($connection, 'ROLLBACK');
            $clashRow = mysqli_fetch_assoc($clashRes);
            $custName = trim(($clashRow['enquiry_first_name'] ?? '') . ' ' . ($clashRow['enquiry_last_name'] ?? ''));
            if (empty($custName)) {
                $custName = 'Lead #' . $clashRow['enquiry_id'];
            }
            $timeStr = date('h:i A', strtotime($followupTime));
            $dateStr = date('d M Y', strtotime($followupTime));
            echo json_encode([
                'status' => 'conflict',
                'has_conflict' => true,
                'conflict_lead_id' => (int)$clashRow['enquiry_id'],
                'conflict_customer' => $custName,
                'conflict_time' => $timeStr,
                'conflict_date' => $dateStr,
                'message' => "Time Conflict: You already have a follow-up scheduled with {$custName} (Lead #{$clashRow['enquiry_id']}) at {$timeStr} on {$dateStr}. Please select a different time slot."
            ]);
            exit();
        }
    }

    $metaType = 'followup';
    $fTimeFormatted = date('Y-m-d H:i:s', strtotime($followupTime));
    $followupObj = [
        'category' => $category,
        'note' => $msg,
        'followup_time' => $fTimeFormatted,
        'followup_formatted' => date('d M Y, h:i A', strtotime($followupTime))
    ];
    $metaVal = mysqli_real_escape_string($connection, json_encode($followupObj));
} else {
    $metaType = 'note';
    $metaVal  = mysqli_real_escape_string($connection, $msg);
}

// Insert note into enquiry_notes
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', '{$metaType}', '{$metaVal}')";

if (!mysqli_query($connection, $insertNoteSql)) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Failed to log activity: ' . mysqli_error($connection)]);
    exit();
}

// Update lead status and follow-up time if specified
$updateFields = [];
$curStatus = (int)($leadData['enquiry_status'] ?? 0);
$newStatus = $curStatus;

if (!empty($followupTime)) {
    $fTimeFormatted = date('Y-m-d H:i:s', strtotime($followupTime));
    $updateFields[] = "enquiry_followup = '{$fTimeFormatted}'";
    // Only move to status 3 if currently in fresh (0) or assigned (2)
    // Do not demote leads that are already at Itinerary Sent (9), Payment Requested (6), or Confirmed (1)
    if ($curStatus === 0 || $curStatus === 2) {
        $updateFields[] = "enquiry_status = 3"; // Follow-Up status
        $newStatus = 3;
    }
} elseif ($isFinalNote) {
    $closeStatus = isset($_POST['close_status']) ? (int)$_POST['close_status'] : (isset($_POST['status']) ? (int)$_POST['status'] : 4);
    if (!in_array($closeStatus, [4, 5, 15, 16, 17, 18], true)) {
        $closeStatus = 4; // 4 = Closed / Cancelled
    }
    $updateFields[] = "enquiry_status = {$closeStatus}";
    $newStatus = $closeStatus;
} elseif ($curStatus === 2 || $curStatus === 0) {
    // Stage 3: Adding note/activity moves fresh/assigned lead into Follow-up
    $updateFields[] = "enquiry_status = 3";
    $newStatus = 3;
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
    'new_status' => $newStatus,
    'followup_time' => !empty($followupTime) ? date('Y-m-d H:i:s', strtotime($followupTime)) : null,
    'followup_formatted' => !empty($followupTime) ? date('d M Y, h:i A', strtotime($followupTime)) : null,
]);
exit();
