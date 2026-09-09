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

$destination = trim($inputData['destination'] ?? $inputData['destination_city'] ?? $inputData['city'] ?? '');
if (empty($package) && !empty($destination)) {
    $package = $destination;
}

if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'First name is required.']);
    exit();
}

if (!empty($dateOfTravel)) {
    $travelTimestamp = strtotime($dateOfTravel);
    $todayTimestamp = strtotime(date('Y-m-d'));
    if ($travelTimestamp !== false && $travelTimestamp < $todayTimestamp) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Date of travel must be today or a future date.']);
        exit();
    }
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

// Extract removed keys (keys mapped to lead fields that must be deleted from JSON)
$removedKeys = [];
if (!empty($inputData['removed_keys'])) {
    if (is_array($inputData['removed_keys'])) {
        $removedKeys = $inputData['removed_keys'];
    } else {
        $decoded = json_decode($inputData['removed_keys'], true);
        if (is_array($decoded)) {
            $removedKeys = $decoded;
        } else {
            $removedKeys = array_filter(array_map('trim', explode(',', $inputData['removed_keys'])));
        }
    }
}

// Preserve and update JSON in enquiry_message
$existingMsg = [];
$msgRes = mysqli_query($connection, "SELECT enquiry_message FROM {$tblPrefix}enquiry WHERE enquiry_id = {$realLeadId} LIMIT 1");
if ($msgRes && $msgRow = mysqli_fetch_assoc($msgRes)) {
    $parsed = json_decode($msgRow['enquiry_message'] ?? '', true);
    if (is_array($parsed)) {
        $existingMsg = $parsed;
    }
}

// Purge mapped keys from JSON
if (!empty($removedKeys) && is_array($existingMsg)) {
    foreach ($removedKeys as $rk) {
        $rkStr = (string)$rk;
        unset($existingMsg[$rkStr]);
        foreach ($existingMsg as $exK => $exV) {
            if (strcasecmp(trim((string)$exK), trim($rkStr)) === 0) {
                unset($existingMsg[$exK]);
            }
        }
        if (isset($existingMsg['requirements']) && is_array($existingMsg['requirements'])) {
            unset($existingMsg['requirements'][$rkStr]);
        }
    }
    // Also purge from field_data if present (Facebook Lead Ads)
    if (!empty($existingMsg['field_data']) && is_array($existingMsg['field_data'])) {
        $existingMsg['field_data'] = array_values(array_filter($existingMsg['field_data'], function ($item) use ($removedKeys) {
            if (!is_array($item) || empty($item['name'])) return true;
            $itemName = trim((string)$item['name']);
            foreach ($removedKeys as $rk) {
                if (strcasecmp($itemName, trim((string)$rk)) === 0) {
                    return false;
                }
            }
            return true;
        }));
        if (empty($existingMsg['field_data'])) {
            unset($existingMsg['field_data']);
        }
    }
}

// If requirements is JSON, ensure removed keys are pruned from it too
if (!empty($requirements)) {
    $parsedReq = json_decode($requirements, true);
    if (is_array($parsedReq)) {
        foreach ($removedKeys as $rk) {
            $rkStr = (string)$rk;
            unset($parsedReq[$rkStr]);
            foreach ($parsedReq as $pK => $pV) {
                if (strcasecmp(trim((string)$pK), trim($rkStr)) === 0) {
                    unset($parsedReq[$pK]);
                }
            }
        }
        if (!empty($parsedReq['field_data']) && is_array($parsedReq['field_data'])) {
            $parsedReq['field_data'] = array_values(array_filter($parsedReq['field_data'], function ($item) use ($removedKeys) {
                if (!is_array($item) || empty($item['name'])) return true;
                $itemName = trim((string)$item['name']);
                foreach ($removedKeys as $rk) {
                    if (strcasecmp($itemName, trim((string)$rk)) === 0) {
                        return false;
                    }
                }
                return true;
            }));
            if (empty($parsedReq['field_data'])) {
                unset($parsedReq['field_data']);
            }
        }
        $requirements = !empty($parsedReq) ? json_encode($parsedReq) : '';
    }
}

$existingMsg['destination'] = $destination;
$existingMsg['date_of_travel'] = $dateOfTravel;
$existingMsg['number_of_days'] = $durationDays;
$existingMsg['number_of_adults'] = $adults;
$existingMsg['number_of_childrens'] = $children;
$existingMsg['number_of_infants'] = $infants;
$existingMsg['current_location'] = $currentLocation;
$existingMsg['pickup_location'] = $pickupLocation;
$existingMsg['drop_location'] = $dropLocation;
$existingMsg['requirements'] = $requirements;
$newMsgEsc = mysqli_real_escape_string($connection, json_encode($existingMsg));

mysqli_query($connection, "START TRANSACTION");

// 1. Update enquiry table
$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$updateEnquirySql = "
    UPDATE {$tblPrefix}enquiry 
    SET enquiry_first_name = '{$firstNameEsc}',
        enquiry_last_name = '{$lastNameEsc}',
        enquiry_email = '{$emailEsc}',
        enquiry_mobile = '{$mobileEsc}',
        enquiry_subject = '{$packageEsc}',
        enquiry_message = '{$newMsgEsc}'
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
