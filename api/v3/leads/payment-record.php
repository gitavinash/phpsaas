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

$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'cash';
$date   = isset($_POST['payment_date']) ? trim($_POST['payment_date']) : date('d-m-Y');
$ref    = isset($_POST['reference_number']) ? trim($_POST['reference_number']) : '';
$notes  = isset($_POST['payment_notes']) ? trim($_POST['payment_notes']) : '';

if (empty($amount) || !is_numeric($amount) || (float)$amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid payment amount.']);
    exit();
}

// Handle file upload if present
$paymentImages = '';
if (isset($_FILES['files']) && !empty($_FILES['files']['name'])) {
    if (defined('MODULES') && file_exists(MODULES . 'singleFileUploader.php')) {
        require_once MODULES . 'singleFileUploader.php';
        if (isset($files)) {
            $paymentImages = json_encode($files, true);
        }
    }
}

$payData = [
    'paymentAmount' => $amount,
    'paymentImages' => $paymentImages,
    'paymentMethod' => $method,
    'paymentDate' => $date,
    'paymentReference' => $ref,
    'paymentMsg' => $notes,
    'paymentByCustomer' => false,
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

// Update lead status to Payment Initiated / Partial (6) or Confirmed (1)
$updateLead = "UPDATE {$tblPrefix}enquiry SET enquiry_status = 6, enquiry_update = NOW() WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId}";
mysqli_query($connection, $updateLead);

mysqli_query($connection, 'COMMIT');

echo json_encode([
    'status' => 'success',
    'message' => 'Payment record added successfully.',
    'lead_id' => $leadId,
]);
exit();
