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
$userRole   = strtolower(trim(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : 'agent')));
$userName   = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : 'Accountant';

// Restrict exclusively to Administrator and Accountant
if (!in_array($userRole, ['administrator', 'accountant', 'superadmin'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Unauthorized. Only administrators and accountants can verify payments.',
    ]);
    exit();
}

$noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : 0;
$action = isset($_POST['action']) ? strtolower(trim($_POST['action'])) : 'approve';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if ($noteId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valid transaction note ID is required.']);
    exit();
}

// Fetch the existing payment note
$sql = "SELECT enquiry_notes_id, enquiry_notes_ref_id, enquiry_notes_meta_value 
        FROM {$tblPrefix}enquiry_notes 
        WHERE enquiry_notes_id = {$noteId} AND enquiry_notes_meta_type = 'payment' LIMIT 1";
$res = mysqli_query($connection, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Payment transaction record not found.']);
    exit();
}

$noteRow = mysqli_fetch_assoc($res);
$leadId  = (int)$noteRow['enquiry_notes_ref_id'];
$payData = json_decode($noteRow['enquiry_notes_meta_value'] ?? '', true);

if (!is_array($payData)) {
    echo json_encode(['status' => 'error', 'message' => 'Corrupted payment payload data.']);
    exit();
}

mysqli_query($connection, 'START TRANSACTION');

$newStatus = ($action === 'reject') ? 'rejected' : 'verified';
$payData['paymentStatus'] = $newStatus;

if ($action === 'reject') {
    $payData['rejectedBy'] = $userName;
    $payData['rejectedAt'] = date('Y-m-d H:i:s');
    $payData['rejectionReason'] = $reason ?: 'Rejected by accounts team';
} else {
    $payData['verifiedBy'] = $userName;
    $payData['verifiedAt'] = date('Y-m-d H:i:s');
}

$encodedData = mysqli_real_escape_string($connection, json_encode($payData));

$updateNoteSql = "UPDATE {$tblPrefix}enquiry_notes 
                  SET enquiry_notes_meta_value = '{$encodedData}', enquiry_notes_update = NOW() 
                  WHERE enquiry_notes_id = {$noteId}";

if (!mysqli_query($connection, $updateNoteSql)) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Failed to update transaction status: ' . mysqli_error($connection)]);
    exit();
}

// If approved, update lead status to Confirmed (1) if not already confirmed
$siteFilter = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
if ($action === 'approve') {
    $updateLeadSql = "UPDATE {$tblPrefix}enquiry 
                      SET enquiry_status = 1, enquiry_update = NOW() 
                      WHERE enquiry_id = {$leadId}{$siteFilter}";
    mysqli_query($connection, $updateLeadSql);
} else {
    $updateLeadSql = "UPDATE {$tblPrefix}enquiry 
                      SET enquiry_update = NOW() 
                      WHERE enquiry_id = {$leadId}{$siteFilter}";
    mysqli_query($connection, $updateLeadSql);
}

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status'         => 'success',
    'message'        => ($action === 'reject') ? 'Payment transaction marked as rejected.' : 'Payment transaction verified and lead marked as Confirmed!',
    'note_id'        => $noteId,
    'lead_id'        => $leadId,
    'payment_status' => $newStatus,
    'verified_by'    => $userName,
    'verified_at'    => date('d-m-Y H:i'),
]);
exit();
