<?php

header('Content-Type: application/json; charset=utf-8');

require_once LOGIN_CHECK;

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? intval(SITE_ID) : 0;
$userId = defined('USER_ID') ? intval(USER_ID) : 0;

$inputData = $_POST;
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $inputData = array_merge($inputData, $decoded);
    }
}
$inputData = array_merge($_GET, $_REQUEST, $inputData);

$rawLeadId = '';
if (!empty($_POST['leadId']))           $rawLeadId = $_POST['leadId'];
elseif (!empty($_POST['lead_id']))      $rawLeadId = $_POST['lead_id'];
elseif (!empty($_POST['id']))           $rawLeadId = $_POST['id'];
elseif (!empty($_POST['enquiry_id']))   $rawLeadId = $_POST['enquiry_id'];
elseif (!empty($_GET['leadId']))        $rawLeadId = $_GET['leadId'];
elseif (!empty($_GET['lead_id']))       $rawLeadId = $_GET['lead_id'];
elseif (!empty($_GET['id']))            $rawLeadId = $_GET['id'];
elseif (!empty($_GET['enquiry_id']))    $rawLeadId = $_GET['enquiry_id'];
elseif (!empty($_REQUEST['leadId']))    $rawLeadId = $_REQUEST['leadId'];
elseif (!empty($_REQUEST['lead_id']))   $rawLeadId = $_REQUEST['lead_id'];
elseif (!empty($_REQUEST['id']))        $rawLeadId = $_REQUEST['id'];
elseif (!empty($_REQUEST['enquiry_id'])) $rawLeadId = $_REQUEST['enquiry_id'];
elseif (!empty($inputData['leadId']))   $rawLeadId = $inputData['leadId'];
elseif (!empty($inputData['lead_id']))  $rawLeadId = $inputData['lead_id'];
elseif (!empty($inputData['id']))       $rawLeadId = $inputData['id'];
elseif (!empty($inputData['enquiry_id'])) $rawLeadId = $inputData['enquiry_id'];
elseif (isset($leadId) && !empty($leadId)) $rawLeadId = $leadId;
elseif (isset($id) && !empty($id))      $rawLeadId = $id;

$rawLeadId = trim((string)$rawLeadId);
if (empty($rawLeadId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is missing.']);
    exit();
}

$realLeadId = 0;
if (is_numeric($rawLeadId)) {
    $realLeadId = intval($rawLeadId);
} else {
    $decoded = @base64_decode($rawLeadId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $realLeadId = intval($decoded);
    }
}

if ($realLeadId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid Lead ID.']);
    exit();
}

$firstName = trim($inputData['first_name'] ?? $inputData['firstName'] ?? $inputData['enquiry_first_name'] ?? '');
$lastName = trim($inputData['last_name'] ?? $inputData['lastName'] ?? $inputData['enquiry_last_name'] ?? '');

// If first name is empty, check if 'name' or 'full_name' was passed
if (empty($firstName)) {
    $rawFullName = trim($inputData['name'] ?? $inputData['full_name'] ?? $inputData['customer_name'] ?? '');
    if (!empty($rawFullName)) {
        $parts = preg_split('/\s+/', $rawFullName, 2);
        $firstName = trim($parts[0]);
        if (empty($lastName) && isset($parts[1])) {
            $lastName = trim($parts[1]);
        }
    }
}

// Split first name into first and last name if space exists and last name is empty
if (empty($lastName) && strpos($firstName, ' ') !== false) {
    $parts = preg_split('/\s+/', $firstName, 2);
    $firstName = trim($parts[0]);
    $lastName = trim($parts[1] ?? '');
}

$email = trim($inputData['email'] ?? '');
$mobile = trim($inputData['mobile'] ?? '');
$altMobile = trim($inputData['alt_mobile'] ?? '');
$package = trim($inputData['package'] ?? $inputData['subject'] ?? '');

$dateOfTravel = trim($inputData['date_of_travel'] ?? '');
$durationDays = intval($inputData['duration_days'] ?? $inputData['number_of_days'] ?? 0);
$adults = intval($inputData['adults'] ?? $inputData['number_of_adults'] ?? 1);
$children = intval($inputData['children'] ?? $inputData['number_of_childrens'] ?? 0);
$infants = intval($inputData['infants'] ?? $inputData['number_of_infants'] ?? 0);
$currentLocation = trim($inputData['current_location'] ?? '');
$pickupLocation = trim($inputData['pickup_location'] ?? '');
$dropLocation = trim($inputData['drop_location'] ?? '');
$requirements = trim($inputData['requirements'] ?? '');

if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'First name is required.']);
    exit();
}

// Clean mobile
$cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
if (strlen($cleanMobile) > 10 && substr($cleanMobile, 0, 2) === '91') {
    $cleanMobile = substr($cleanMobile, 2);
}

$firstNameEsc = mysqli_real_escape_string($connection, $firstName);
$lastNameEsc = mysqli_real_escape_string($connection, $lastName);
$emailEsc = mysqli_real_escape_string($connection, $email);
$mobileEsc = mysqli_real_escape_string($connection, $cleanMobile);
$packageEsc = mysqli_real_escape_string($connection, $package);

mysqli_query($connection, "START TRANSACTION");

// 1. Update enquiry table
$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$updateEnquirySql = "
    UPDATE {$tblPrefix}enquiry 
    SET enquiry_first_name = '{$firstNameEsc}',
        enquiry_last_name = '{$lastNameEsc}',
        enquiry_email = '{$emailEsc}',
        enquiry_mobile = '{$mobileEsc}',
        enquiry_subject = '{$packageEsc}'
    WHERE enquiry_id = {$realLeadId} {$siteClause}
";

$updateEnquiry = mysqli_query($connection, $updateEnquirySql);

// 2. Update/Insert enquiry_profile
$travelDateSql = !empty($dateOfTravel) ? "'" . date('Y-m-d', strtotime($dateOfTravel)) . "'" : "NULL";
$currentLocationEsc = mysqli_real_escape_string($connection, $currentLocation);
$pickupLocationEsc = mysqli_real_escape_string($connection, $pickupLocation);
$dropLocationEsc = mysqli_real_escape_string($connection, $dropLocation);
$requirementsEsc = mysqli_real_escape_string($connection, $requirements);

// Check if profile exists
$checkProfile = mysqli_query($connection, "SELECT enquiry_profile_id FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = '{$realLeadId}' LIMIT 1");

if ($checkProfile && mysqli_num_rows($checkProfile) > 0) {
    $profileSql = "
        UPDATE {$tblPrefix}enquiry_profile 
        SET date_of_travel = {$travelDateSql},
            number_of_days = {$durationDays},
            number_of_adults = {$adults},
            number_of_childrens = {$children},
            number_of_infants = {$infants},
            current_location = '{$currentLocationEsc}',
            pickup_location = '{$pickupLocationEsc}',
            drop_location = '{$dropLocationEsc}',
            requirements = '{$requirementsEsc}'
        WHERE enquiry_profile_ref_id = '{$realLeadId}'
    ";
    $profileResult = mysqli_query($connection, $profileSql);
} else {
    $profileSql = "
        INSERT INTO {$tblPrefix}enquiry_profile(
            enquiry_profile_ref_id, enquiry_profile_agent, date_of_travel,
            number_of_days, number_of_adults, number_of_childrens, number_of_infants,
            current_location, pickup_location, drop_location, requirements, status
        ) VALUES (
            '{$realLeadId}', {$userId}, {$travelDateSql},
            {$durationDays}, {$adults}, {$children}, {$infants},
            '{$currentLocationEsc}', '{$pickupLocationEsc}', '{$dropLocationEsc}', '{$requirementsEsc}', 1
        )
    ";
    $profileResult = mysqli_query($connection, $profileSql);
}

// 3. Log history update
$logUser = defined('LOGGIN_USER_NAME') ? LOGGIN_USER_NAME : 'User';
$logUserEsc = mysqli_real_escape_string($connection, $logUser);
mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$realLeadId}', {$userId}, 'update', '{$logUserEsc}')");

if ($updateEnquiry && $profileResult) {
    mysqli_query($connection, "COMMIT");
    echo json_encode([
        'status' => 'success',
        'message' => 'Lead details updated successfully.'
    ]);
} else {
    mysqli_query($connection, "ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update lead records in database.'
    ]);
}
exit();
