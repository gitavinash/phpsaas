<?php
/**
 * API: Update Lead Customer Interest Level
 * File: api/v3/leads/update-interest.php
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

$rawLeadId = $_POST['leadId'] ?? ($_POST['lead_id'] ?? ($_POST['id'] ?? ($jsonData['leadId'] ?? ($jsonData['lead_id'] ?? ($jsonData['id'] ?? '')))));
$rawInterest = $_POST['interest'] ?? ($_POST['interest_level'] ?? ($jsonData['interest'] ?? ($jsonData['interest_level'] ?? '')));

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

require_once __DIR__ . '/status-config.php';

$validLevels = ['Unknown', 'Hot', 'Warm', 'Cold', 'Not Interested'];
$normalizedInterest = 'Unknown';
foreach ($validLevels as $lvl) {
    if (strcasecmp(trim((string)$rawInterest), $lvl) === 0) {
        $normalizedInterest = $lvl;
        break;
    }
}

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

// Fetch lead
$leadQ = mysqli_query($connection, "SELECT enquiry_id, enquiry_interest_level, enquiry_status, enquiry_first_name, enquiry_last_name 
                                     FROM {$tblPrefix}enquiry 
                                     WHERE enquiry_id = {$leadId}{$siteClause} 
                                     LIMIT 1");

if (!$leadQ || mysqli_num_rows($leadQ) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$leadData = mysqli_fetch_assoc($leadQ);
$oldInterest = trim($leadData['enquiry_interest_level'] ?? 'Unknown');

// Fetch user name
$userName = 'Agent';
if ($currentUserId > 0) {
    $uRes = mysqli_query($connection, "SELECT profile_display_name FROM {$tblPrefix}profile WHERE profile_user_id = {$currentUserId} LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        if (!empty($uRow['profile_display_name'])) {
            $userName = trim($uRow['profile_display_name']);
        }
    }
}

// Update interest
$safeInterest = mysqli_real_escape_string($connection, $normalizedInterest);
$updateSql = "UPDATE {$tblPrefix}enquiry 
              SET enquiry_interest_level = '{$safeInterest}', enquiry_update = NOW() 
              WHERE enquiry_id = {$leadId}{$siteClause}";

if (!mysqli_query($connection, $updateSql)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error while updating interest: ' . mysqli_error($connection)]);
    exit();
}

// Log into enquiry_notes
$noteMsg = "Customer interest level updated to {$normalizedInterest} by {$userName}";
$safeNoteMsg = mysqli_real_escape_string($connection, $noteMsg);
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes 
                  (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', 'interest', '{$safeNoteMsg}')";
@mysqli_query($connection, $insertNoteSql);

$meta = getInterestLevelMeta($normalizedInterest);

echo json_encode([
    'status'         => 'success',
    'message'        => "Interest level updated to {$normalizedInterest}.",
    'lead_id'        => $leadId,
    'interest_level' => $normalizedInterest,
    'meta'           => $meta,
]);
exit();
