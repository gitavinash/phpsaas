<?php

/**
 * Operations Lead Assignment & Reassignment Desk API
 * Path: /api/v3/operations/assign.php
 */

header("Content-Type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID') ? (int) USER_ID : 0;

$userRole = '';
if (!empty($site_access_type)) {
    $userRole = strtolower(trim($site_access_type));
} elseif (defined('USER_TYPE') && !empty(USER_TYPE)) {
    $userRole = strtolower(trim(USER_TYPE));
} elseif (!empty($_SESSION['user_type'])) {
    $userRole = strtolower(trim($_SESSION['user_type']));
} elseif (!empty($_COOKIE['user_type'])) {
    $userRole = strtolower(trim(base64_decode($_COOKIE['user_type'])));
}

if (!in_array($userRole, ['administrator', 'operations', 'super_administrator'])) {
    echo json_encode([
        'status' => 'error',
        'code'   => 403,
        'message' => 'Access denied. Only Operations and Administrators can assign or reassign leads.'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch active sales agents for assignment
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $siteClause = ($siteId > 0) ? " AND sa.site_access_site_id = {$siteId}" : "";
    
    // Fetch users with access via site_access
    $agentsSql = "SELECT 
            u.user_id,
            u.user_email,
            u.user_mobile,
            p.profile_display_name,
            p.profile_first_name,
            p.profile_last_name,
            sa.site_access_type as user_type_code
        FROM {$tblPrefix}user u
        INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_status = 1
        LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
        WHERE u.user_status = 1 {$siteClause}
        ORDER BY p.profile_display_name ASC";

    $agents = [];
    if ($res = mysqli_query($connection, $agentsSql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $uId = (int)$row['user_id'];
            $name = trim($row['profile_display_name'] ?? '');
            if (empty($name)) {
                $name = trim(($row['profile_first_name'] ?? '') . ' ' . ($row['profile_last_name'] ?? ''));
            }
            if (empty($name)) {
                $name = $row['user_email'];
            }

            $rawRole = $row['user_type_code'] ?? 'agent';
            $roleLabel = ucwords(str_replace('_', ' ', $rawRole));

            $agents[] = [
                'id'        => $uId,
                'name'      => $name,
                'email'     => $row['user_email'],
                'mobile'    => $row['user_mobile'],
                'role'      => strtolower(trim($rawRole)),
                'role_name' => $roleLabel
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'agents' => $agents
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Assign or Reassign Leads
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $leadIds = [];
    if (isset($input['lead_ids']) && is_array($input['lead_ids'])) {
        foreach ($input['lead_ids'] as $l) {
            $clean = (int)$l;
            if ($clean > 0) $leadIds[] = $clean;
        }
    } elseif (!empty($input['lead_id'])) {
        $clean = (int)$input['lead_id'];
        if ($clean > 0) $leadIds[] = $clean;
    }

    $targetAgentId = isset($input['agent_id']) ? (int)$input['agent_id'] : 0;
    $reason = isset($input['reason']) ? trim($input['reason']) : '';

    if (empty($leadIds)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select at least one lead to assign.']);
        exit();
    }

    if ($targetAgentId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please select an agent to assign the lead(s) to.']);
        exit();
    }

    // Verify agent exists
    $agentName = 'Sales Agent';
    $chkAgent = mysqli_query($connection, "SELECT p.profile_display_name, u.user_email FROM {$tblPrefix}user u LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id WHERE u.user_id = {$targetAgentId} LIMIT 1");
    if ($chkAgent && $agRow = mysqli_fetch_assoc($chkAgent)) {
        $agentName = !empty($agRow['profile_display_name']) ? trim($agRow['profile_display_name']) : $agRow['user_email'];
    }

    // Actor name
    $actorName = 'Operations Team';
    $chkActor = mysqli_query($connection, "SELECT profile_display_name FROM {$tblPrefix}profile WHERE profile_user_id = {$currentUserId} LIMIT 1");
    if ($chkActor && $actRow = mysqli_fetch_assoc($chkActor)) {
        if (!empty($actRow['profile_display_name'])) $actorName = trim($actRow['profile_display_name']);
    }

    $assignedCount = 0;
    $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

    foreach ($leadIds as $lId) {
        // Fetch current lead state
        $lRes = mysqli_query($connection, "SELECT enquiry_id, enquiry_status, enquiry_agent FROM {$tblPrefix}enquiry WHERE enquiry_id = {$lId}{$siteClause} LIMIT 1");
        if (!$lRes || mysqli_num_rows($lRes) === 0) continue;

        $lead = mysqli_fetch_assoc($lRes);
        $oldAgent = (int)$lead['enquiry_agent'];
        $oldStatus = (int)$lead['enquiry_status'];

        // If status was 0 (Initiate), transition to 2 (Pending Assigned)
        $newStatus = ($oldStatus === 0) ? 2 : $oldStatus;

        $updSql = "UPDATE {$tblPrefix}enquiry 
                   SET enquiry_agent = {$targetAgentId}, 
                       enquiry_status = {$newStatus}, 
                       enquiry_update = CURRENT_TIMESTAMP 
                   WHERE enquiry_id = {$lId}{$siteClause}";

        if (mysqli_query($connection, $updSql)) {
            $assignedCount++;

            // Create audit note log
            $noteAction = ($oldAgent > 0) ? "Reassigned to {$agentName}" : "Assigned to {$agentName}";
            if (!empty($reason)) {
                $noteAction .= " (Reason: {$reason})";
            }
            $noteMsg = "Lead {$noteAction} by {$actorName}";

            $noteEsc = mysqli_real_escape_string($connection, $noteMsg);
            $metaValEsc = mysqli_real_escape_string($connection, json_encode([
                'type'         => 'assignment',
                'assigned_by'  => $currentUserId,
                'assigned_to'  => $targetAgentId,
                'target_name'  => $agentName,
                'actor_name'   => $actorName,
                'reason'       => $reason,
                'timestamp'    => date('Y-m-d H:i:s')
            ]));

            $insNote = "INSERT INTO {$tblPrefix}enquiry_notes 
                (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value, enquiry_notes_update) 
                VALUES ({$lId}, {$currentUserId}, 'assignment_log', '{$metaValEsc}', CURRENT_TIMESTAMP)";
            mysqli_query($connection, $insNote);
        }
    }

    echo json_encode([
        'status'         => 'success',
        'assigned_count' => $assignedCount,
        'message'        => "Successfully assigned {$assignedCount} lead(s) to {$agentName}."
    ]);
    exit();
}
