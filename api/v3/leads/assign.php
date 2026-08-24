<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')  ? (int) USER_ID : 0;

// Flexible parameter extraction
$rawLeadId = '';
if (isset($_POST['lead_id']))       $rawLeadId = $_POST['lead_id'];
elseif (isset($_POST['leadId']))    $rawLeadId = $_POST['leadId'];
elseif (isset($_POST['id']))        $rawLeadId = $_POST['id'];
elseif (isset($_GET['lead_id']))    $rawLeadId = $_GET['lead_id'];
elseif (isset($_GET['leadId']))     $rawLeadId = $_GET['leadId'];
elseif (isset($_REQUEST['lead_id'])) $rawLeadId = $_REQUEST['lead_id'];
elseif (isset($_REQUEST['leadId']))  $rawLeadId = $_REQUEST['leadId'];
elseif (isset($leadId))              $rawLeadId = $leadId;

$rawAgentId = null;
if (isset($_POST['agent_id']))       $rawAgentId = $_POST['agent_id'];
elseif (isset($_POST['agentId']))    $rawAgentId = $_POST['agentId'];
elseif (isset($_POST['user_id']))    $rawAgentId = $_POST['user_id'];
elseif (isset($_POST['userId']))     $rawAgentId = $_POST['userId'];
elseif (isset($_GET['agent_id']))    $rawAgentId = $_GET['agent_id'];
elseif (isset($_GET['agentId']))     $rawAgentId = $_GET['agentId'];
elseif (isset($_REQUEST['agent_id'])) $rawAgentId = $_REQUEST['agent_id'];
elseif (isset($_REQUEST['agentId']))  $rawAgentId = $_REQUEST['agentId'];
elseif (isset($agentId))              $rawAgentId = $agentId;

if ($rawLeadId !== '' && $rawLeadId !== null && $rawAgentId !== null) {
    $decodedLead = base64_decode(trim($rawLeadId), true);
    $realLeadId  = ($decodedLead !== false && is_numeric($decodedLead)) ? intval($decodedLead) : intval($rawLeadId);

    $cleanAgentId = trim((string)$rawAgentId);
    $decodedAgent = base64_decode($cleanAgentId, true);
    if ($decodedAgent !== false && is_numeric($decodedAgent)) {
        $realAgentId = intval($decodedAgent);
    } elseif ($decodedAgent !== false && strpos($decodedAgent, '_') !== false) {
        $parts = explode('_', $decodedAgent);
        $realAgentId = intval($parts[0]);
    } else {
        $realAgentId = intval($cleanAgentId);
    }

    if ($realLeadId > 0 && isset($connection) && $connection) {
        $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

        // Check if lead exists
        $enquirySubject = 'Lead';
        $leadSql = "SELECT enquiry_id, enquiry_subject FROM {$tblPrefix}enquiry WHERE enquiry_id = {$realLeadId}{$siteClause}";
        if ($resLead = mysqli_query($connection, $leadSql)) {
            if ($lRow = mysqli_fetch_assoc($resLead)) {
                $enquirySubject = $lRow['enquiry_subject'];
            }
        }

        if ($realAgentId > 0) {
            // Check if agent/user exists
            $agentName = '';
            $userCheckSql = "SELECT profile_display_name, user_email 
                             FROM {$tblPrefix}user 
                             INNER JOIN {$tblPrefix}profile ON profile_user_id = user_id 
                             WHERE user_id = {$realAgentId}";
            if ($resUser = mysqli_query($connection, $userCheckSql)) {
                if ($uRow = mysqli_fetch_assoc($resUser)) {
                    $agentName = !empty($uRow['profile_display_name']) ? $uRow['profile_display_name'] : $uRow['user_email'];
                }
            }

            // Update lead agent and status to 2 (Pending/Assigned)
            $sql = "UPDATE {$tblPrefix}enquiry
                    SET enquiry_agent = '{$realAgentId}', enquiry_status = 2
                    WHERE enquiry_id = {$realLeadId}";

            if (mysqli_query($connection, $sql)) {
                // Record in enquiry notes
                $noteText = !empty($agentName) ? "Lead assign to {$agentName}" : "Lead assign to agent #{$realAgentId}";
                $noteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value)
                            VALUES ({$realLeadId}, '{$currentUserId}', 'assign', '" . mysqli_real_escape_string($connection, $noteText) . "')";
                @mysqli_query($connection, $noteSql);

                // Optional push notification dispatch
                if (defined('API_MODULES') && file_exists(API_MODULES . 'notification/send.php')) {
                    $title = "New {$enquirySubject} lead received";
                    $body = "Hi {$agentName}, New {$enquirySubject} lead received.";
                    $redirectURL = '/dashboard/leads/view/?id=' . base64_encode($realLeadId);
                    @require_once API_MODULES . 'notification/send.php';
                }

                echo json_encode([
                    'status'      => 200,
                    'status_code' => 200,
                    'success'     => true,
                    'msg'         => 'Agent assigned successfully',
                    'message'     => 'Agent assigned successfully',
                    'lead_id'     => $realLeadId,
                    'agent_id'    => $realAgentId,
                    'agent'       => $agentName,
                ]);
                exit();
            }
        } else {
            // Unassign lead (agent = 0)
            $sql = "UPDATE {$tblPrefix}enquiry
                    SET enquiry_agent = '0'
                    WHERE enquiry_id = {$realLeadId}";

            if (mysqli_query($connection, $sql)) {
                $noteText = "Lead unassigned";
                $noteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value)
                            VALUES ({$realLeadId}, '{$currentUserId}', 'assign', '" . mysqli_real_escape_string($connection, $noteText) . "')";
                @mysqli_query($connection, $noteSql);

                echo json_encode([
                    'status'      => 200,
                    'status_code' => 200,
                    'success'     => true,
                    'msg'         => 'Lead unassigned successfully',
                    'message'     => 'Lead unassigned successfully',
                    'lead_id'     => $realLeadId,
                    'agent_id'    => 0,
                    'agent'       => 'Unassigned',
                ]);
                exit();
            }
        }

        echo json_encode(['status' => 500, 'msg' => 'Failed to update agent: ' . mysqli_error($connection)]);
        exit();
    }
}

echo json_encode([
    'status' => 400,
    'msg' => 'Invalid lead assignment parameters',
    'debug' => [
        'rawLeadId'  => $rawLeadId,
        'rawAgentId' => $rawAgentId,
    ]
]);
