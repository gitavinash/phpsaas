<?php
/**
 * API: Log Lead Communication Interaction (Outbound Contact / Inbound Response)
 * File: api/v3/leads/log-interaction.php
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
$direction = strtolower(trim($_POST['direction'] ?? ($jsonData['direction'] ?? 'outbound'))); // outbound or inbound
$channel   = ucwords(strtolower(trim($_POST['channel'] ?? ($_POST['type'] ?? ($jsonData['channel'] ?? ($jsonData['type'] ?? 'Call')))))); // Call, WhatsApp, Email, Meeting, Other
$outcome   = trim($_POST['outcome'] ?? ($jsonData['outcome'] ?? ''));
$notes     = trim($_POST['notes'] ?? ($_POST['message'] ?? ($jsonData['notes'] ?? ($jsonData['message'] ?? ''))));

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

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

// Fetch current lead state
$leadQ = mysqli_query($connection, "SELECT enquiry_id, enquiry_status, enquiry_last_contacted_at, enquiry_last_customer_response_at, enquiry_response_status 
                                     FROM {$tblPrefix}enquiry 
                                     WHERE enquiry_id = {$leadId}{$siteClause} 
                                     LIMIT 1");

if (!$leadQ || mysqli_num_rows($leadQ) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$leadData = mysqli_fetch_assoc($leadQ);
$currentStatus = (int)($leadData['enquiry_status'] ?? 0);

// Get agent name
$agentName = 'Agent';
if ($currentUserId > 0) {
    $uRes = mysqli_query($connection, "SELECT profile_display_name FROM {$tblPrefix}profile WHERE profile_user_id = {$currentUserId} LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        if (!empty($uRow['profile_display_name'])) {
            $agentName = trim($uRow['profile_display_name']);
        }
    }
}

$nowStr = date('Y-m-d H:i:s');
$updateFields = [];

if ($direction === 'inbound') {
    // Inbound customer reply received
    $updateFields[] = "enquiry_last_customer_response_at = '{$nowStr}'";
    $calcResult = calculateCustomerResponseStatus($leadData['enquiry_last_contacted_at'], $nowStr, $currentStatus);
    $newResponseStatus = $calcResult['status'];
    $updateFields[] = "enquiry_response_status = '" . mysqli_real_escape_string($connection, $newResponseStatus) . "'";
    
    // Create rich log
    $interactionData = [
        'direction' => 'inbound',
        'channel'   => $channel,
        'summary'   => $outcome ?: 'Customer Replied',
        'notes'     => $notes,
        'logged_by' => $agentName,
        'timestamp' => $nowStr
    ];
    $noteType = 'customer_reply';
    $noteText = "Customer Inbound Reply ({$channel})" . (!empty($outcome) ? ": {$outcome}" : "") . (!empty($notes) ? " - {$notes}" : "");
} else {
    // Outbound contact attempt
    $updateFields[] = "enquiry_last_contacted_at = '{$nowStr}'";
    $calcResult = calculateCustomerResponseStatus($nowStr, $leadData['enquiry_last_customer_response_at'], $currentStatus);
    $newResponseStatus = $calcResult['status'];
    $updateFields[] = "enquiry_response_status = '" . mysqli_real_escape_string($connection, $newResponseStatus) . "'";

    // If currently fresh (0) or assigned (2) and agent contacted, transition to Contacted (19) unless already further in pipeline
    if ($currentStatus === 0 || $currentStatus === 2) {
        $updateFields[] = "enquiry_status = 19"; // Contacted
        $currentStatus = 19;
    }

    $interactionData = [
        'direction' => 'outbound',
        'channel'   => $channel,
        'outcome'   => $outcome ?: 'Contact Attempted',
        'notes'     => $notes,
        'logged_by' => $agentName,
        'timestamp' => $nowStr
    ];
    $noteType = 'contact_attempt';
    $noteText = "Outbound {$channel} Contact" . (!empty($outcome) ? ": {$outcome}" : "") . (!empty($notes) ? " - {$notes}" : "");
}

$updateFields[] = "enquiry_update = NOW()";

$updateSql = "UPDATE {$tblPrefix}enquiry 
              SET " . implode(', ', $updateFields) . " 
              WHERE enquiry_id = {$leadId}{$siteClause}";

if (!mysqli_query($connection, $updateSql)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to record interaction: ' . mysqli_error($connection)]);
    exit();
}

// Insert into enquiry_notes
$safeNoteText = mysqli_real_escape_string($connection, $noteText);
$safeJsonData = mysqli_real_escape_string($connection, json_encode($interactionData));
$insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes 
                  (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                  VALUES ('{$leadId}', '{$currentUserId}', '{$noteType}', '{$safeNoteText}')";
@mysqli_query($connection, $insertNoteSql);

echo json_encode([
    'status'          => 'success',
    'message'         => ($direction === 'inbound' ? 'Customer response recorded.' : 'Contact attempt logged successfully.'),
    'lead_id'         => $leadId,
    'direction'       => $direction,
    'channel'         => $channel,
    'response_status' => $newResponseStatus,
    'calc_details'    => $calcResult,
    'new_lead_status' => $currentStatus,
    'timestamp'       => $nowStr,
]);
exit();
