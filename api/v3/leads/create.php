<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

require_once LOGIN_CHECK;

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? intval(SITE_ID) : 0;
$userId = defined('USER_ID') ? intval(USER_ID) : 0;
$userRole = isset($site_access_type) ? strtolower($site_access_type) : 'agent';
$userName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : 'Representative';

$reqData = $_POST;
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $reqData = array_merge($reqData, $decoded);
    }
}
$reqData = array_merge($_GET, $_REQUEST, $reqData);

$firstName = trim($reqData['first_name'] ?? $reqData['firstName'] ?? '');
$middleName = trim($reqData['middle_name'] ?? $reqData['middleName'] ?? '');
$lastName = trim($reqData['last_name'] ?? $reqData['lastName'] ?? '');

// If first name is empty, check if full name was passed
if (empty($firstName)) {
    $rawFullName = trim($reqData['name'] ?? $reqData['full_name'] ?? '');
    if (!empty($rawFullName)) {
        $parts = preg_split('/\s+/', $rawFullName, 2);
        $firstName = trim($parts[0]);
        if (empty($lastName) && isset($parts[1])) {
            $lastName = trim($parts[1]);
        }
    }
}

$mobile = trim($reqData['mobile'] ?? $reqData['phone'] ?? $reqData['enquiry_mobile'] ?? '');
$email = trim($reqData['email'] ?? $reqData['enquiry_email'] ?? '');
$subject = trim($reqData['package'] ?? $reqData['subject'] ?? $reqData['destination'] ?? $reqData['enquiry_subject'] ?? 'Tour Inquiry');
$source = trim($reqData['source'] ?? $reqData['enquiry_source'] ?? 'direct');
$status = isset($reqData['status']) && $reqData['status'] !== '' ? intval($reqData['status']) : 0;

// Travel Details
$travelDateRaw = trim($reqData['date_of_travel'] ?? $reqData['travel_date'] ?? '');
$travelDate = !empty($travelDateRaw) ? date('Y-m-d', strtotime($travelDateRaw)) : date('Y-m-d', strtotime('+7 days'));
$numberOfDays = isset($reqData['number_of_days']) && intval($reqData['number_of_days']) > 0 ? intval($reqData['number_of_days']) : 3;
$numberOfAdults = isset($reqData['number_of_adults']) && intval($reqData['number_of_adults']) > 0 ? intval($reqData['number_of_adults']) : 2;
$numberOfChildren = isset($reqData['number_of_childrens']) ? intval($reqData['number_of_childrens']) : (isset($reqData['number_of_children']) ? intval($reqData['number_of_children']) : 0);
$numberOfInfants = isset($reqData['number_of_infants']) ? intval($reqData['number_of_infants']) : 0;

$currentLocation = trim($reqData['current_location'] ?? $reqData['city'] ?? '');
$pickupLocation = trim($reqData['pickup_location'] ?? $reqData['pickup'] ?? $currentLocation);
$dropLocation = trim($reqData['drop_location'] ?? $reqData['drop'] ?? $pickupLocation);
$requirements = trim($reqData['requirements'] ?? $reqData['notes'] ?? $reqData['message'] ?? '');

// Representative Assignment
$assignedAgent = 0;
if ($userRole === 'agent') {
    $assignedAgent = $userId;
} else {
    if (isset($reqData['agent_id']) && $reqData['agent_id'] !== '' && $reqData['agent_id'] !== 'all') {
        $assignedAgent = intval($reqData['agent_id']);
    } else {
        $assignedAgent = $userId;
    }
}

// Validation
if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'First name is required.']);
    exit();
}

if (empty($mobile)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Mobile number is required.']);
    exit();
}

// Sanitize mobile number
$cleanMobile = preg_replace('/[^\d]/', '', $mobile);
if (substr($cleanMobile, 0, 2) === '91' && strlen($cleanMobile) === 12) {
    $cleanMobile = substr($cleanMobile, 2);
} elseif (substr($cleanMobile, 0, 1) === '0' && strlen($cleanMobile) === 11) {
    $cleanMobile = substr($cleanMobile, 1);
}
if (!empty($cleanMobile)) {
    $mobile = $cleanMobile;
}

if (!isset($connection)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

// Escapes
$firstEsc = mysqli_real_escape_string($connection, $firstName);
$middleEsc = mysqli_real_escape_string($connection, $middleName);
$lastEsc = mysqli_real_escape_string($connection, $lastName);
$emailEsc = mysqli_real_escape_string($connection, $email);
$mobileEsc = mysqli_real_escape_string($connection, $mobile);
$subjectEsc = mysqli_real_escape_string($connection, $subject);
$sourceEsc = mysqli_real_escape_string($connection, $source);
$currentLocEsc = mysqli_real_escape_string($connection, $currentLocation);
$pickupLocEsc = mysqli_real_escape_string($connection, $pickupLocation);
$dropLocEsc = mysqli_real_escape_string($connection, $dropLocation);
$reqEsc = mysqli_real_escape_string($connection, $requirements);
$userEsc = mysqli_real_escape_string($connection, $userName);

mysqli_query($connection, "START TRANSACTION");

// Check if customer user exists
$foundUserId = 0;
$findUserSql = "SELECT user_id FROM {$tblPrefix}user WHERE (user_email = '{$emailEsc}' AND user_email != '') OR user_mobile = '{$mobileEsc}' LIMIT 1";
if ($findRes = mysqli_query($connection, $findUserSql)) {
    if ($uRow = mysqli_fetch_assoc($findRes)) {
        $foundUserId = intval($uRow['user_id']);
    }
}

// Build extra data payload
$extraData = [
    'first_name' => $firstName,
    'middle_name' => $middleName,
    'last_name' => $lastName,
    'email' => $email,
    'mobile' => $mobile,
    'package' => $subject,
    'destination' => $subject,
    'date_of_travel' => $travelDate,
    'number_of_days' => $numberOfDays,
    'number_of_adults' => $numberOfAdults,
    'number_of_childrens' => $numberOfChildren,
    'number_of_infants' => $numberOfInfants,
    'current_location' => $currentLocation,
    'pickup_location' => $pickupLocation,
    'drop_location' => $dropLocation,
    'requirements' => $requirements,
    'source' => $source
];
$messageJson = json_encode($extraData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$messageJsonEsc = mysqli_real_escape_string($connection, $messageJson);

// Insert Enquiry
$insertLeadSql = "INSERT INTO {$tblPrefix}enquiry (
    enquiry_site_id, 
    enquiry_user_id, 
    enquiry_agent, 
    enquiry_first_name, 
    enquiry_middle_name, 
    enquiry_last_name, 
    enquiry_email, 
    enquiry_mobile, 
    enquiry_subject, 
    enquiry_message,
    enquiry_type, 
    enquiry_source, 
    enquiry_status, 
    enquiry_time
) VALUES (
    {$siteId}, 
    {$foundUserId}, 
    '{$assignedAgent}', 
    '{$firstEsc}', 
    '{$middleEsc}', 
    '{$lastEsc}', 
    '{$emailEsc}', 
    '{$mobileEsc}', 
    '{$subjectEsc}', 
    '{$messageJsonEsc}',
    'enquiry', 
    '{$sourceEsc}', 
    {$status}, 
    NOW()
)";

$leadCreated = mysqli_query($connection, $insertLeadSql);
$leadId = $leadCreated ? mysqli_insert_id($connection) : 0;

if (!$leadCreated || $leadId <= 0) {
    mysqli_query($connection, "ROLLBACK");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create lead record: ' . mysqli_error($connection)]);
    exit();
}

// Insert Enquiry Profile
$insertProfileSql = "INSERT INTO {$tblPrefix}enquiry_profile (
    enquiry_profile_ref_id, 
    enquiry_profile_agent, 
    date_of_travel, 
    number_of_days, 
    number_of_adults, 
    number_of_childrens, 
    number_of_infants, 
    current_location, 
    pickup_location, 
    drop_location, 
    requirements, 
    status
) VALUES (
    '{$leadId}', 
    '{$assignedAgent}', 
    '{$travelDate}', 
    '{$numberOfDays}', 
    '{$numberOfAdults}', 
    '{$numberOfChildren}', 
    '{$numberOfInfants}', 
    '{$currentLocEsc}', 
    '{$pickupLocEsc}', 
    '{$dropLocEsc}', 
    '{$reqEsc}', 
    '1'
)";

$profileCreated = mysqli_query($connection, $insertProfileSql);

// Insert Activity Log Note
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes (
    enquiry_notes_ref_id, 
    enquiry_notes_user_id, 
    enquiry_notes_meta_type, 
    enquiry_notes_meta_value
) VALUES (
    '{$leadId}', 
    {$userId}, 
    'Generate', 
    'Lead created by {$userEsc}'
)";
$noteCreated = mysqli_query($connection, $insertNoteSql);

// If Special Requirements provided, save as requirement note
if (!empty($requirements)) {
    $reqNoteEsc = mysqli_real_escape_string($connection, "Client Requirements: " . $requirements);
    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
        enquiry_notes_ref_id, 
        enquiry_notes_user_id, 
        enquiry_notes_meta_type, 
        enquiry_notes_meta_value
    ) VALUES (
        '{$leadId}', 
        {$userId}, 
        'Requirement', 
        '{$reqNoteEsc}'
    )");
}

if ($leadCreated && $profileCreated) {
    mysqli_query($connection, "COMMIT");

    // Trigger Lead Distribution Automation Engine
    try {
        $wfHelperPath = dirname(__DIR__) . '/integration/workflow_helper.php';
        if (file_exists($wfHelperPath)) {
            require_once $wfHelperPath;
            if (function_exists('distributeLeadViaAutomation')) {
                @distributeLeadViaAutomation($connection, $siteId, $leadId, [
                    'source'      => $source,
                    'destination' => $subject,
                    'name'        => trim($firstName . ' ' . $lastName)
                ]);
            }
        }
    } catch (Throwable $distEx) {
        error_log("Lead Distribution Automation error: " . $distEx->getMessage());
    }

    echo json_encode([
        'status'  => 200,
        'code'    => 'success',
        'lead_id' => $leadId,
        'message' => 'New lead created successfully.'
    ]);
} else {
    mysqli_query($connection, "ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while saving lead details.'
    ]);
}
