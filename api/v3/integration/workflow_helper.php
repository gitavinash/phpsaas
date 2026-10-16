<?php

/**
 * V3 Workflow Helper & Automation Engine
 * Path: /api/v3/integration/workflow_helper.php
 * Handles self-healing schema creation, Lead Distribution Engine, and WhatsApp Chat Automation Engine.
 */

if (!defined('TABLE_PREFIX')) {
    define('TABLE_PREFIX', 'isw_');
}

/**
 * Initialize all database tables for automation plugins.
 */
function initWorkflowDatabase($connection)
{
    if (!$connection) {
        return;
    }
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. WhatsApp Chat Automation Workflows
    $waWorkflowSql = "CREATE TABLE IF NOT EXISTS `{$prefix}whatsapp_workflow` (
      `workflow_id` INT AUTO_INCREMENT PRIMARY KEY,
      `workflow_site_id` INT NOT NULL DEFAULT 0,
      `workflow_name` VARCHAR(255) NOT NULL,
      `workflow_description` VARCHAR(500) DEFAULT NULL,
      `workflow_trigger_type` VARCHAR(50) NOT NULL DEFAULT 'keyword',
      `workflow_keywords` TEXT DEFAULT NULL,
      `workflow_match_type` VARCHAR(30) NOT NULL DEFAULT 'contains',
      `workflow_nodes_json` LONGTEXT NOT NULL,
      `workflow_is_default` TINYINT(1) NOT NULL DEFAULT 0,
      `workflow_status` TINYINT(1) NOT NULL DEFAULT 1,
      `workflow_execution_count` INT NOT NULL DEFAULT 0,
      `workflow_created_by` INT NOT NULL DEFAULT 0,
      `workflow_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `workflow_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX (`workflow_site_id`),
      INDEX (`workflow_status`),
      INDEX (`workflow_is_default`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($connection, $waWorkflowSql);

    // 2. WhatsApp Workflow Audit Logs
    $waLogSql = "CREATE TABLE IF NOT EXISTS `{$prefix}whatsapp_workflow_log` (
      `log_id` INT AUTO_INCREMENT PRIMARY KEY,
      `log_site_id` INT NOT NULL DEFAULT 0,
      `log_workflow_id` INT NOT NULL DEFAULT 0,
      `log_chat_id` INT NOT NULL DEFAULT 0,
      `log_message_id` INT NOT NULL DEFAULT 0,
      `log_phone` VARCHAR(30) NOT NULL,
      `log_trigger_input` TEXT DEFAULT NULL,
      `log_executed_action` VARCHAR(100) DEFAULT NULL,
      `log_response_text` TEXT DEFAULT NULL,
      `log_status` VARCHAR(20) NOT NULL DEFAULT 'success',
      `log_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX (`log_site_id`),
      INDEX (`log_workflow_id`),
      INDEX (`log_phone`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($connection, $waLogSql);

    // 3. Lead Distribution Workflows
    $leadWorkflowSql = "CREATE TABLE IF NOT EXISTS `{$prefix}lead_workflow` (
      `workflow_id` INT AUTO_INCREMENT PRIMARY KEY,
      `workflow_site_id` INT NOT NULL DEFAULT 0,
      `workflow_name` VARCHAR(255) NOT NULL,
      `workflow_description` VARCHAR(500) DEFAULT NULL,
      `workflow_trigger_event` VARCHAR(50) NOT NULL DEFAULT 'lead_created',
      `workflow_sources` TEXT DEFAULT NULL,
      `workflow_destination_filter` VARCHAR(255) DEFAULT NULL,
      `workflow_distribution_mode` VARCHAR(50) NOT NULL DEFAULT 'round_robin',
      `workflow_assigned_agents` TEXT NOT NULL,
      `workflow_last_agent_id` INT NOT NULL DEFAULT 0,
      `workflow_send_whatsapp_greeting` TINYINT(1) NOT NULL DEFAULT 0,
      `workflow_whatsapp_message` TEXT DEFAULT NULL,
      `workflow_create_task` TINYINT(1) NOT NULL DEFAULT 0,
      `workflow_task_type` VARCHAR(50) DEFAULT 'Call',
      `workflow_task_due_hours` INT DEFAULT 2,
      `workflow_task_note` VARCHAR(255) DEFAULT 'Follow up with newly distributed lead',
      `workflow_priority` INT NOT NULL DEFAULT 10,
      `workflow_status` TINYINT(1) NOT NULL DEFAULT 1,
      `workflow_execution_count` INT NOT NULL DEFAULT 0,
      `workflow_created_by` INT NOT NULL DEFAULT 0,
      `workflow_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `workflow_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX (`workflow_site_id`),
      INDEX (`workflow_status`),
      INDEX (`workflow_priority`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($connection, $leadWorkflowSql);

    // 4. Lead Distribution Audit Logs
    $leadLogSql = "CREATE TABLE IF NOT EXISTS `{$prefix}lead_workflow_log` (
      `log_id` INT AUTO_INCREMENT PRIMARY KEY,
      `log_site_id` INT NOT NULL DEFAULT 0,
      `log_workflow_id` INT NOT NULL DEFAULT 0,
      `log_lead_id` INT NOT NULL DEFAULT 0,
      `log_lead_name` VARCHAR(150) DEFAULT NULL,
      `log_lead_mobile` VARCHAR(30) DEFAULT NULL,
      `log_lead_source` VARCHAR(50) DEFAULT NULL,
      `log_assigned_agent_id` INT NOT NULL DEFAULT 0,
      `log_assigned_agent_name` VARCHAR(150) DEFAULT NULL,
      `log_distribution_mode` VARCHAR(50) DEFAULT 'round_robin',
      `log_status` VARCHAR(20) NOT NULL DEFAULT 'success',
      `log_details` TEXT DEFAULT NULL,
      `log_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX (`log_site_id`),
      INDEX (`log_workflow_id`),
      INDEX (`log_lead_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($connection, $leadLogSql);
}

/**
 * Seed default distribution rule for a site if no rules exist yet.
 */
function seedDefaultLeadDistributionRule($connection, $siteId)
{
    if (!$connection || $siteId <= 0) {
        return;
    }
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    $chk = mysqli_query($connection, "SELECT workflow_id FROM `{$prefix}lead_workflow` WHERE workflow_site_id = {$siteId} LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        return;
    }

    // Get active agents for this site
    $agents = [];
    $agQ = mysqli_query($connection, "SELECT user_id, profile_display_name FROM `{$prefix}user`
        INNER JOIN `{$prefix}profile` ON profile_user_id = user_id
        INNER JOIN `{$prefix}site_access` ON site_access_user_id = user_id
        WHERE site_access_site_id = {$siteId} AND site_access_status = 1
        ORDER BY user_id ASC LIMIT 10");
    if ($agQ) {
        while ($r = mysqli_fetch_assoc($agQ)) {
            $agents[] = (int)$r['user_id'];
        }
    }

    $agentsJson = mysqli_real_escape_string($connection, json_encode($agents));
    $defaultSql = "INSERT INTO `{$prefix}lead_workflow` (
        workflow_site_id, workflow_name, workflow_description, workflow_trigger_event,
        workflow_sources, workflow_distribution_mode, workflow_assigned_agents,
        workflow_priority, workflow_status, workflow_created_at
    ) VALUES (
        {$siteId}, 'Default Round-Robin Lead Distribution',
        'Evenly distribute all incoming leads from Facebook, Web Forms, WhatsApp, and Website among active team members.',
        'lead_created', 'all', 'round_robin', '{$agentsJson}', 1, 1, NOW()
    )";
    @mysqli_query($connection, $defaultSql);
}

/**
 * Replace placeholders like {name}, {phone}, {company}, {destination} with contextual data.
 */
function replaceWorkflowTokens($template, array $data = [])
{
    $placeholders = [
        '{name}'        => $data['name'] ?? $data['lead_name'] ?? 'Traveler',
        '{first_name}'  => $data['first_name'] ?? 'Traveler',
        '{phone}'       => $data['phone'] ?? $data['mobile'] ?? '',
        '{destination}' => $data['destination'] ?? $data['subject'] ?? 'your dream destination',
        '{source}'      => $data['source'] ?? 'our channel',
        '{agent_name}'  => $data['agent_name'] ?? 'our team specialist',
        '{company}'     => defined('SITE_NAME') ? SITE_NAME : 'Aleya Support',
    ];
    return str_replace(array_keys($placeholders), array_values($placeholders), $template);
}

/**
 * LEAD DISTRIBUTION ENGINE
 * Evaluates active lead distribution workflows and routes the lead.
 */
function distributeLeadViaAutomation($connection, $siteId, $leadId, array $contextData = [])
{
    if (!$connection || $siteId <= 0 || $leadId <= 0) {
        return ['status' => 'skipped', 'reason' => 'Invalid parameters'];
    }

    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // Verify lead exists
    $leadQ = mysqli_query($connection, "SELECT * FROM `{$prefix}enquiry` WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId} LIMIT 1");
    if (!$leadQ || mysqli_num_rows($leadQ) === 0) {
        return ['status' => 'skipped', 'reason' => 'Lead not found'];
    }
    $lead = mysqli_fetch_assoc($leadQ);

    $leadSource = strtolower(trim($lead['enquiry_source'] ?? ($contextData['source'] ?? 'direct')));
    $leadSubject = strtolower(trim($lead['enquiry_subject'] ?? ''));
    $leadName = trim(($lead['enquiry_first_name'] ?? '') . ' ' . ($lead['enquiry_last_name'] ?? ''));
    if (empty($leadName)) {
        $leadName = 'Lead #' . $leadId;
    }
    $leadMobile = $lead['enquiry_mobile'] ?? '';

    // Fetch active workflows ordered by priority ASC
    $wfQ = mysqli_query($connection, "SELECT * FROM `{$prefix}lead_workflow` 
        WHERE workflow_site_id = {$siteId} AND workflow_status = 1 
        ORDER BY workflow_priority ASC, workflow_id ASC");
    if (!$wfQ || mysqli_num_rows($wfQ) === 0) {
        return ['status' => 'skipped', 'reason' => 'No active distribution rules'];
    }

    $matchedWorkflow = null;
    while ($wf = mysqli_fetch_assoc($wfQ)) {
        // 1. Source filter
        $sourcesStr = trim($wf['workflow_sources'] ?? 'all');
        if ($sourcesStr !== '' && $sourcesStr !== 'all') {
            $allowedSources = array_map('trim', array_map('strtolower', explode(',', $sourcesStr)));
            if (!in_array($leadSource, $allowedSources, true)) {
                continue; // Source mismatch
            }
        }

        // 2. Destination / Subject filter
        $destFilter = trim($wf['workflow_destination_filter'] ?? '');
        if ($destFilter !== '') {
            $destKeywords = array_map('trim', array_map('strtolower', explode(',', $destFilter)));
            $destMatch = false;
            foreach ($destKeywords as $dk) {
                if ($dk !== '' && strpos($leadSubject, $dk) !== false) {
                    $destMatch = true;
                    break;
                }
            }
            if (!$destMatch) {
                continue; // Destination mismatch
            }
        }

        // Match found!
        $matchedWorkflow = $wf;
        break;
    }

    if (!$matchedWorkflow) {
        return ['status' => 'skipped', 'reason' => 'No matching rule for this lead'];
    }

    // Determine target agent
    $agentsList = json_decode($matchedWorkflow['workflow_assigned_agents'] ?? '[]', true);
    if (!is_array($agentsList) || empty($agentsList)) {
        return ['status' => 'skipped', 'reason' => 'No agents configured in matched rule'];
    }

    $assignedAgentId = 0;
    $assignedAgentName = '';
    $mode = $matchedWorkflow['workflow_distribution_mode'];

    if ($mode === 'specific_agent' || count($agentsList) === 1) {
        $assignedAgentId = (int)$agentsList[0];
    } else {
        // Round-Robin Distribution
        $lastAgentId = (int)$matchedWorkflow['workflow_last_agent_id'];
        $lastIdx = array_search($lastAgentId, $agentsList, true);
        if ($lastIdx === false || $lastIdx >= (count($agentsList) - 1)) {
            $nextIdx = 0;
        } else {
            $nextIdx = $lastIdx + 1;
        }
        $assignedAgentId = (int)$agentsList[$nextIdx];

        // Update last agent pointer
        @mysqli_query($connection, "UPDATE `{$prefix}lead_workflow` 
            SET workflow_last_agent_id = {$assignedAgentId}, 
                workflow_execution_count = workflow_execution_count + 1 
            WHERE workflow_id = " . (int)$matchedWorkflow['workflow_id']);
    }

    // Resolve assigned agent name
    if ($assignedAgentId > 0) {
        $agNameQ = mysqli_query($connection, "SELECT profile_display_name, user_email FROM `{$prefix}user` 
            LEFT JOIN `{$prefix}profile` ON profile_user_id = user_id 
            WHERE user_id = {$assignedAgentId} LIMIT 1");
        if ($agNameQ && mysqli_num_rows($agNameQ) > 0) {
            $agRow = mysqli_fetch_assoc($agNameQ);
            $assignedAgentName = !empty($agRow['profile_display_name']) ? $agRow['profile_display_name'] : $agRow['user_email'];
        }
    }

    // Apply assignment to lead
    $escAgentId = (int)$assignedAgentId;
    @mysqli_query($connection, "UPDATE `{$prefix}enquiry` 
        SET enquiry_agent = '{$escAgentId}' 
        WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId}");
    @mysqli_query($connection, "UPDATE `{$prefix}enquiry_profile` 
        SET enquiry_profile_agent = '{$escAgentId}' 
        WHERE enquiry_profile_ref_id = '{$leadId}'");

    // Add timeline note
    $noteText = mysqli_real_escape_string($connection, "Automated Distribution: Assigned to " . ($assignedAgentName ?: 'Agent #' . $assignedAgentId) . " via rule '" . $matchedWorkflow['workflow_name'] . "'");
    @mysqli_query($connection, "INSERT INTO `{$prefix}enquiry_notes` 
        (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
        VALUES ({$leadId}, 0, 'Automation', '{$noteText}')");

    // Optional Action 1: Create Follow-up Task
    if (!empty($matchedWorkflow['workflow_create_task'])) {
        $taskType = !empty($matchedWorkflow['workflow_task_type']) ? $matchedWorkflow['workflow_task_type'] : 'Call';
        $dueHours = (int)($matchedWorkflow['workflow_task_due_hours'] ?? 2);
        if ($dueHours <= 0) $dueHours = 2;
        $dueTime = date('Y-m-d H:i:s', strtotime("+{$dueHours} hours"));
        $taskNote = mysqli_real_escape_string($connection, $matchedWorkflow['workflow_task_note'] ?? 'Follow up with newly distributed lead');
        @mysqli_query($connection, "INSERT INTO `{$prefix}enquiry_task` 
            (task_site_id, task_ref_id, task_user_id, task_agent_id, task_type, task_due_at, task_notes, task_status) 
            VALUES ({$siteId}, {$leadId}, 0, {$assignedAgentId}, '{$taskType}', '{$dueTime}', '{$taskNote}', 'pending')");
    }

    // Optional Action 2: Send WhatsApp Greeting
    if (!empty($matchedWorkflow['workflow_send_whatsapp_greeting']) && !empty($matchedWorkflow['workflow_whatsapp_message']) && !empty($leadMobile)) {
        $helperPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/api/v3/whatsapp/device_helper.php';
        if (file_exists($helperPath)) {
            require_once $helperPath;
            if (function_exists('sendWhatsAppDirectMessage')) {
                $customMsg = replaceWorkflowTokens($matchedWorkflow['workflow_whatsapp_message'], [
                    'name'        => $leadName,
                    'phone'       => $leadMobile,
                    'destination' => $lead['enquiry_subject'] ?? '',
                    'agent_name'  => $assignedAgentName
                ]);
                @sendWhatsAppDirectMessage($connection, $siteId, $assignedAgentId, $leadMobile, $customMsg);
            }
        }
    }

    // Log the distribution execution
    $escWfId = (int)$matchedWorkflow['workflow_id'];
    $escLeadName = mysqli_real_escape_string($connection, $leadName);
    $escLeadMobile = mysqli_real_escape_string($connection, $leadMobile);
    $escSource = mysqli_real_escape_string($connection, $leadSource);
    $escAgentName = mysqli_real_escape_string($connection, $assignedAgentName);
    $escMode = mysqli_real_escape_string($connection, $mode);
    $details = mysqli_real_escape_string($connection, json_encode([
        'workflow_name' => $matchedWorkflow['workflow_name'],
        'assigned_agent_id' => $assignedAgentId,
        'assigned_agent_name' => $assignedAgentName,
        'mode' => $mode
    ]));

    @mysqli_query($connection, "INSERT INTO `{$prefix}lead_workflow_log` 
        (log_site_id, log_workflow_id, log_lead_id, log_lead_name, log_lead_mobile, log_lead_source, log_assigned_agent_id, log_assigned_agent_name, log_distribution_mode, log_status, log_details) 
        VALUES ({$siteId}, {$escWfId}, {$leadId}, '{$escLeadName}', '{$escLeadMobile}', '{$escSource}', {$assignedAgentId}, '{$escAgentName}', '{$escMode}', 'success', '{$details}')");

    return [
        'status' => 'success',
        'workflow_id' => $matchedWorkflow['workflow_id'],
        'workflow_name' => $matchedWorkflow['workflow_name'],
        'assigned_agent_id' => $assignedAgentId,
        'assigned_agent_name' => $assignedAgentName
    ];
}

/**
 * WHATSAPP CHAT AUTOMATION ENGINE
 * Evaluates active chat automation workflows upon incoming message.
 */
function executeWhatsAppChatWorkflows($connection, $siteId, $chatId, $messageId, $incomingText, $senderPhone, array $messagePayload = [])
{
    if (!$connection || $siteId <= 0 || empty($senderPhone)) {
        return ['status' => 'skipped', 'reason' => 'Invalid parameters'];
    }

    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Check if WhatsApp Chat Automation is enabled in site configuration
    $cfgQ = mysqli_query($connection, "SELECT site_configuration_value FROM `{$prefix}site_configuration` 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key = 'enable_whatsapp_automation' 
          AND site_configuration_status = 1 LIMIT 1");
    $isEnabled = false;
    if ($cfgQ && mysqli_num_rows($cfgQ) > 0) {
        $cfgRow = mysqli_fetch_assoc($cfgQ);
        $isEnabled = ($cfgRow['site_configuration_value'] === '1' || $cfgRow['site_configuration_value'] === 1);
    }
    if (!$isEnabled) {
        return ['status' => 'skipped', 'reason' => 'WhatsApp Chat Automation plugin is inactive'];
    }

    $cleanText = strtolower(trim((string)$incomingText));
    if ($cleanText === '') {
        return ['status' => 'skipped', 'reason' => 'Empty incoming text'];
    }

    // 2. Fetch active workflows
    $wfQ = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_workflow` 
        WHERE workflow_site_id = {$siteId} AND workflow_status = 1 
        ORDER BY workflow_is_default ASC, workflow_id ASC");
    if (!$wfQ || mysqli_num_rows($wfQ) === 0) {
        return ['status' => 'skipped', 'reason' => 'No active WhatsApp chat workflows'];
    }

    $matchedWorkflow = null;
    $defaultWorkflow = null;

    while ($wf = mysqli_fetch_assoc($wfQ)) {
        if (!empty($wf['workflow_is_default'])) {
            $defaultWorkflow = $wf;
            continue;
        }

        $keywordsStr = trim($wf['workflow_keywords'] ?? '');
        $matchType = $wf['workflow_match_type'] ?? 'contains';
        $keywords = array_map('trim', array_map('strtolower', explode(',', $keywordsStr)));

        $matched = false;
        foreach ($keywords as $kw) {
            if ($kw === '') continue;
            if ($matchType === 'exact') {
                if ($cleanText === $kw) {
                    $matched = true;
                    break;
                }
            } elseif ($matchType === 'starts_with') {
                if (str_starts_with($cleanText, $kw)) {
                    $matched = true;
                    break;
                }
            } else { // contains
                if (strpos($cleanText, $kw) !== false) {
                    $matched = true;
                    break;
                }
            }
        }

        if ($matched) {
            $matchedWorkflow = $wf;
            break;
        }
    }

    if (!$matchedWorkflow && $defaultWorkflow) {
        $matchedWorkflow = $defaultWorkflow;
    }

    if (!$matchedWorkflow) {
        return ['status' => 'skipped', 'reason' => 'No keyword matched and no default fallback'];
    }

    // Parse workflow nodes
    $nodesData = json_decode($matchedWorkflow['workflow_nodes_json'] ?? '[]', true);
    if (!is_array($nodesData) || empty($nodesData)) {
        return ['status' => 'skipped', 'reason' => 'Workflow has no nodes configured'];
    }

    // Execute first action node
    $actionNode = null;
    foreach ($nodesData as $node) {
        if (($node['type'] ?? '') === 'action' || ($node['category'] ?? '') === 'action') {
            $actionNode = $node;
            break;
        }
    }

    if (!$actionNode) {
        return ['status' => 'skipped', 'reason' => 'No action node found'];
    }

    $actionType = $actionNode['action_type'] ?? ($actionNode['subType'] ?? 'reply_text');
    $responseText = $actionNode['message_text'] ?? ($actionNode['text'] ?? ($actionNode['reply'] ?? ''));

    // Resolve contact name
    $contactName = $messagePayload['pushname'] ?? ($messagePayload['name'] ?? 'Traveler');

    $responseText = replaceWorkflowTokens($responseText, [
        'name'  => $contactName,
        'phone' => $senderPhone
    ]);

    // Send direct WhatsApp reply
    $helperPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/api/v3/whatsapp/device_helper.php';
    $sendResult = null;
    if (file_exists($helperPath)) {
        require_once $helperPath;
        if (function_exists('sendWhatsAppDirectMessage') && !empty($responseText)) {
            $sendResult = sendWhatsAppDirectMessage($connection, $siteId, 0, $senderPhone, $responseText);
        }
    }

    // Increment execution count
    @mysqli_query($connection, "UPDATE `{$prefix}whatsapp_workflow` 
        SET workflow_execution_count = workflow_execution_count + 1 
        WHERE workflow_id = " . (int)$matchedWorkflow['workflow_id']);

    // Log the execution
    $escWfId = (int)$matchedWorkflow['workflow_id'];
    $escChatId = (int)$chatId;
    $escMsgId = (int)$messageId;
    $escPhone = mysqli_real_escape_string($connection, $senderPhone);
    $escInput = mysqli_real_escape_string($connection, $incomingText);
    $escAction = mysqli_real_escape_string($connection, $actionType);
    $escReply = mysqli_real_escape_string($connection, $responseText);

    @mysqli_query($connection, "INSERT INTO `{$prefix}whatsapp_workflow_log` 
        (log_site_id, log_workflow_id, log_chat_id, log_message_id, log_phone, log_trigger_input, log_executed_action, log_response_text, log_status) 
        VALUES ({$siteId}, {$escWfId}, {$escChatId}, {$escMsgId}, '{$escPhone}', '{$escInput}', '{$escAction}', '{$escReply}', 'success')");

    return [
        'status'         => 'success',
        'workflow_id'    => $matchedWorkflow['workflow_id'],
        'workflow_name'  => $matchedWorkflow['workflow_name'],
        'action_type'    => $actionType,
        'response_text'  => $responseText,
        'send_result'    => $sendResult
    ];
}
