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

$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'cash';
$date   = isset($_POST['payment_date']) ? trim($_POST['payment_date']) : date('d-m-Y');
$ref    = isset($_POST['reference_number']) ? trim($_POST['reference_number']) : '';
$notes  = isset($_POST['payment_notes']) ? trim($_POST['payment_notes']) : '';

if (empty($amount) || !is_numeric($amount) || (float)$amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid payment amount.']);
    exit();
}

// Handle optional file/receipt upload if present
$paymentImages = '';
$uploadedFileKey = isset($_FILES['files']) ? 'files' : (isset($_FILES['file']) ? 'file' : (isset($_FILES['payment_image']) ? 'payment_image' : ''));

if (!empty($uploadedFileKey) && !empty($_FILES[$uploadedFileKey]['name']) && $_FILES[$uploadedFileKey]['error'] === 0) {
    // If not named 'files', alias it for singleFileUploader
    if ($uploadedFileKey !== 'files') {
        $_FILES['files'] = $_FILES[$uploadedFileKey];
    }
    if (defined('MODULES') && file_exists(MODULES . 'singleFileUploader.php')) {
        require_once MODULES . 'singleFileUploader.php';
        if (isset($files) && !empty($files)) {
            $paymentImages = is_array($files) ? json_encode($files) : $files;
        }
    }
}

$siteAccessRole = strtolower(trim(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : 'agent')));
$isAgent = ($siteAccessRole === 'agent');
$paymentStatus = $isAgent ? 'pending' : 'verified';
$userName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : 'Representative';

$payData = [
    'paymentAmount' => $amount,
    'paymentImages' => $paymentImages,
    'paymentMethod' => $method,
    'paymentDate' => $date,
    'paymentReference' => $ref,
    'paymentMsg' => $notes,
    'paymentStatus' => $paymentStatus,
    'paymentByCustomer' => false,
    'recordedBy' => $userName,
    'recordedByRole' => $siteAccessRole,
    'verifiedBy' => $isAgent ? null : $userName,
    'verifiedAt' => $isAgent ? null : date('Y-m-d H:i:s'),
];

$encodedPayData = mysqli_real_escape_string($connection, json_encode($payData));

mysqli_query($connection, 'START TRANSACTION');

$insertSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
              VALUES ('{$leadId}', '{$currentUserId}', 'payment', '{$encodedPayData}')";

if (!mysqli_query($connection, $insertSql)) {
    mysqli_query($connection, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Failed to record payment in database: ' . mysqli_error($connection)]);
    exit();
}

// Fetch current lead status
$currentStatus = 0;
$siteFilter = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$checkStatusSql = "SELECT enquiry_status FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteFilter} LIMIT 1";
$checkRes = mysqli_query($connection, $checkStatusSql);
if ($checkRes && mysqli_num_rows($checkRes) > 0) {
    $row = mysqli_fetch_assoc($checkRes);
    $currentStatus = (int)($row['enquiry_status'] ?? 0);
}

// Only confirm lead (status = 1) if recorded by Admin / Accountant / Operations
$newStatus = $currentStatus;
if (!$isAgent) {
    if ($currentStatus !== 1) {
        $updateLead = "UPDATE {$tblPrefix}enquiry SET enquiry_status = 1, enquiry_update = NOW() WHERE enquiry_id = {$leadId}{$siteFilter}";
        mysqli_query($connection, $updateLead);
        $newStatus = 1;
    } else {
        $updateLead = "UPDATE {$tblPrefix}enquiry SET enquiry_update = NOW() WHERE enquiry_id = {$leadId}{$siteFilter}";
        mysqli_query($connection, $updateLead);
    }
} else {
    $updateLead = "UPDATE {$tblPrefix}enquiry SET enquiry_update = NOW() WHERE enquiry_id = {$leadId}{$siteFilter}";
    mysqli_query($connection, $updateLead);
}

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status' => 'success',
    'message' => $isAgent ? 'Payment record submitted for accountant verification.' : 'Payment record added & verified successfully.',
    'lead_id' => $leadId,
    'payment_status' => $paymentStatus,
    'new_status' => $newStatus,
    'is_confirmed' => ($newStatus === 1),
]);
exit();
