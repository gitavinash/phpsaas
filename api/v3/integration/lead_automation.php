<?php

/**
 * V3 Lead Distribution Automation REST API
 * Path: /api/v3/integration/lead_automation.php
 */

header('Content-Type: application/json');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}
if ($siteId <= 0) {
    $siteId = 1;
}
if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

// Authentication
if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}
if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

require_once __DIR__ . '/workflow_helper.php';
initWorkflowDatabase($connection);
seedDefaultLeadDistributionRule($connection, $siteId);

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Parse input
$input = $_GET;
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $json = !empty($raw) ? json_decode($raw, true) : [];
    if (is_array($json)) {
        $input = array_merge($input, $_POST, $json);
    } else {
        $input = array_merge($input, $_POST);
    }
}

$action = $input['action'] ?? ($method === 'POST' ? 'save' : 'list');

// --------------------------------------------------------------------------
// 1. GET: Fetch Single Rule, Logs, or List with Active Agents & Stats
// --------------------------------------------------------------------------
if ($method === 'GET') {
    // Single workflow lookup
    if (!empty($input['id'])) {
        $wfId = (int)$input['id'];
        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}lead_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId} LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $wf = mysqli_fetch_assoc($q);
            $wf['assigned_agents_list'] = json_decode($wf['workflow_assigned_agents'] ?? '[]', true);
            echo json_encode(['status' => 'success', 'workflow' => $wf]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Lead workflow not found']);
        }
        exit;
    }

    // Execution logs
    if ($action === 'logs') {
        $logs = [];
        $logQ = mysqli_query($connection, "SELECT l.*, w.workflow_name 
            FROM `{$prefix}lead_workflow_log` l
            LEFT JOIN `{$prefix}lead_workflow` w ON w.workflow_id = l.log_workflow_id
            WHERE l.log_site_id = {$siteId} 
            ORDER BY l.log_id DESC LIMIT 50");
        if ($logQ) {
            while ($r = mysqli_fetch_assoc($logQ)) {
                $logs[] = $r;
            }
        }
        echo json_encode(['status' => 'success', 'logs' => $logs]);
        exit;
    }

    // Fetch active agents for this site
    $agents = [];
    $agQ = mysqli_query($connection, "SELECT u.user_id, u.user_email, p.profile_display_name, sa.site_access_type,
        (SELECT COUNT(*) FROM `{$prefix}enquiry` WHERE enquiry_site_id = {$siteId} AND enquiry_agent = u.user_id AND enquiry_status NOT IN (1, 4)) as active_leads_count
        FROM `{$prefix}user` u
        INNER JOIN `{$prefix}profile` p ON p.profile_user_id = u.user_id
        INNER JOIN `{$prefix}site_access` sa ON sa.site_access_user_id = u.user_id
        WHERE sa.site_access_site_id = {$siteId} AND sa.site_access_status = 1
        ORDER BY p.profile_display_name ASC");
    if ($agQ) {
        while ($r = mysqli_fetch_assoc($agQ)) {
            $agents[] = [
                'id'                 => (int)$r['user_id'],
                'name'               => !empty($r['profile_display_name']) ? trim($r['profile_display_name']) : $r['user_email'],
                'email'              => $r['user_email'],
                'role'               => ucwords(str_replace('_', ' ', $r['site_access_type'] ?? 'agent')),
                'active_leads_count' => (int)($r['active_leads_count'] ?? 0)
            ];
        }
    }

    // List all workflows
    $workflows = [];
    $wQ = mysqli_query($connection, "SELECT * FROM `{$prefix}lead_workflow` 
        WHERE workflow_site_id = {$siteId} 
        ORDER BY workflow_priority ASC, workflow_id DESC");
    if ($wQ) {
        while ($r = mysqli_fetch_assoc($wQ)) {
            $r['assigned_agents_list'] = json_decode($r['workflow_assigned_agents'] ?? '[]', true);
            $workflows[] = $r;
        }
    }

    // Statistics
    $statTotal = count($workflows);
    $statActive = 0;
    $statDistributed = 0;
    foreach ($workflows as $w) {
        if (!empty($w['workflow_status'])) $statActive++;
        $statDistributed += (int)($w['workflow_execution_count'] ?? 0);
    }

    // Lead distribution is active by default unless explicitly disabled
    $cfgQ = mysqli_query($connection, "SELECT site_configuration_value FROM `{$prefix}site_configuration` 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key = 'enable_lead_automation' 
          AND site_configuration_status = 1 LIMIT 1");
    $isPluginActive = true; // Active by default
    if ($cfgQ && mysqli_num_rows($cfgQ) > 0) {
        $cfgRow = mysqli_fetch_assoc($cfgQ);
        $isPluginActive = ($cfgRow['site_configuration_value'] === '1' || $cfgRow['site_configuration_value'] === 1);
    }

    echo json_encode([
        'status' => 'success',
        'is_plugin_active' => $isPluginActive,
        'stats' => [
            'total_rules'            => $statTotal,
            'active_rules'           => $statActive,
            'total_leads_distributed'=> $statDistributed,
            'total_agents'           => count($agents)
        ],
        'agents'    => $agents,
        'workflows' => $workflows
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST: Actions (save, toggle, delete, duplicate, test_simulate, toggle_plugin)
// --------------------------------------------------------------------------

// Toggle plugin master switch
if ($action === 'toggle_plugin') {
    $newState = !empty($input['is_active']) ? '1' : '0';
    $chk = mysqli_query($connection, "SELECT site_configuration_id FROM `{$prefix}site_configuration` 
        WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'enable_lead_automation' LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        mysqli_query($connection, "UPDATE `{$prefix}site_configuration` 
            SET site_configuration_value = '{$newState}', site_configuration_status = 1 
            WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'enable_lead_automation'");
    } else {
        mysqli_query($connection, "INSERT INTO `{$prefix}site_configuration` 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) 
            VALUES ({$siteId}, 'enable_lead_automation', '{$newState}', 1)");
    }
    echo json_encode([
        'status' => 'success',
        'msg' => $newState === '1' ? 'Lead Distribution Automation activated!' : 'Lead Distribution Automation disabled.',
        'is_active' => $newState === '1'
    ]);
    exit;
}

// Toggle individual workflow
if ($action === 'toggle') {
    $wfId = (int)($input['id'] ?? 0);
    $status = !empty($input['status']) ? 1 : 0;
    if ($wfId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid rule ID']);
        exit;
    }
    mysqli_query($connection, "UPDATE `{$prefix}lead_workflow` SET workflow_status = {$status} WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}");
    echo json_encode(['status' => 'success', 'msg' => 'Rule status updated successfully', 'workflow_id' => $wfId, 'new_status' => $status]);
    exit;
}

// Delete rule
if ($action === 'delete') {
    $wfId = (int)($input['id'] ?? 0);
    if ($wfId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid rule ID']);
        exit;
    }
    mysqli_query($connection, "DELETE FROM `{$prefix}lead_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}");
    echo json_encode(['status' => 'success', 'msg' => 'Distribution rule deleted successfully']);
    exit;
}

// Duplicate rule
if ($action === 'duplicate') {
    $wfId = (int)($input['id'] ?? 0);
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}lead_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId} LIMIT 1");
    if (!$q || mysqli_num_rows($q) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Rule to clone not found']);
        exit;
    }
    $w = mysqli_fetch_assoc($q);
    $newName = mysqli_real_escape_string($connection, $w['workflow_name'] . ' (Copy)');
    $newDesc = mysqli_real_escape_string($connection, $w['workflow_description'] ?? '');
    $newSources = mysqli_real_escape_string($connection, $w['workflow_sources'] ?? 'all');
    $newDest = mysqli_real_escape_string($connection, $w['workflow_destination_filter'] ?? '');
    $newMode = mysqli_real_escape_string($connection, $w['workflow_distribution_mode'] ?? 'round_robin');
    $newAgents = mysqli_real_escape_string($connection, $w['workflow_assigned_agents'] ?? '[]');
    $sendWa = (int)($w['workflow_send_whatsapp_greeting'] ?? 0);
    $waMsg = mysqli_real_escape_string($connection, $w['workflow_whatsapp_message'] ?? '');
    $createTask = (int)($w['workflow_create_task'] ?? 0);
    $taskType = mysqli_real_escape_string($connection, $w['workflow_task_type'] ?? 'Call');
    $taskHours = (int)($w['workflow_task_due_hours'] ?? 2);
    $taskNote = mysqli_real_escape_string($connection, $w['workflow_task_note'] ?? '');
    $priority = (int)($w['workflow_priority'] ?? 10);

    $insSql = "INSERT INTO `{$prefix}lead_workflow` 
        (workflow_site_id, workflow_name, workflow_description, workflow_trigger_event, workflow_sources, workflow_destination_filter, workflow_distribution_mode, workflow_assigned_agents, workflow_send_whatsapp_greeting, workflow_whatsapp_message, workflow_create_task, workflow_task_type, workflow_task_due_hours, workflow_task_note, workflow_priority, workflow_status, workflow_created_at)
        VALUES ({$siteId}, '{$newName}', '{$newDesc}', 'lead_created', '{$newSources}', '{$newDest}', '{$newMode}', '{$newAgents}', {$sendWa}, '{$waMsg}', {$createTask}, '{$taskType}', {$taskHours}, '{$taskNote}', {$priority}, 1, NOW())";
    mysqli_query($connection, $insSql);
    $newId = mysqli_insert_id($connection);
    echo json_encode(['status' => 'success', 'msg' => 'Distribution rule cloned successfully', 'new_id' => $newId]);
    exit;
}

// Test / Simulate Lead Distribution
if ($action === 'test_simulate') {
    $simSource = strtolower(trim($input['source'] ?? 'facebook'));
    $simSubject = strtolower(trim($input['subject'] ?? 'kashmir'));
    $simName = trim($input['name'] ?? 'Aarav Patel');

    $wfQ = mysqli_query($connection, "SELECT * FROM `{$prefix}lead_workflow` 
        WHERE workflow_site_id = {$siteId} AND workflow_status = 1 
        ORDER BY workflow_priority ASC, workflow_id ASC");

    $matched = null;
    if ($wfQ) {
        while ($wf = mysqli_fetch_assoc($wfQ)) {
            $srcStr = trim($wf['workflow_sources'] ?? 'all');
            if ($srcStr !== '' && $srcStr !== 'all') {
                $allowed = array_map('trim', array_map('strtolower', explode(',', $srcStr)));
                if (!in_array($simSource, $allowed, true)) {
                    continue;
                }
            }
            $destFilter = trim($wf['workflow_destination_filter'] ?? '');
            if ($destFilter !== '') {
                $destKws = array_map('trim', array_map('strtolower', explode(',', $destFilter)));
                $destMatch = false;
                foreach ($destKws as $dk) {
                    if ($dk !== '' && strpos($simSubject, $dk) !== false) {
                        $destMatch = true;
                        break;
                    }
                }
                if (!$destMatch) {
                    continue;
                }
            }
            $matched = $wf;
            break;
        }
    }

    if (!$matched) {
        echo json_encode([
            'status' => 'success',
            'matched' => false,
            'msg' => 'No active distribution rule matches these lead attributes.',
            'assigned_agent' => null
        ]);
        exit;
    }

    $agentsList = json_decode($matched['workflow_assigned_agents'] ?? '[]', true);
    $nextAgentId = 0;
    if (!empty($agentsList)) {
        if ($matched['workflow_distribution_mode'] === 'specific_agent' || count($agentsList) === 1) {
            $nextAgentId = (int)$agentsList[0];
        } else {
            $lastId = (int)$matched['workflow_last_agent_id'];
            $idx = array_search($lastId, $agentsList, true);
            $nextIdx = ($idx === false || $idx >= (count($agentsList) - 1)) ? 0 : ($idx + 1);
            $nextAgentId = (int)$agentsList[$nextIdx];
        }
    }

    $agentName = 'Agent #' . $nextAgentId;
    if ($nextAgentId > 0) {
        $agQ = mysqli_query($connection, "SELECT profile_display_name, user_email FROM `{$prefix}user` LEFT JOIN `{$prefix}profile` ON profile_user_id = user_id WHERE user_id = {$nextAgentId} LIMIT 1");
        if ($agQ && mysqli_num_rows($agQ) > 0) {
            $row = mysqli_fetch_assoc($agQ);
            $agentName = !empty($row['profile_display_name']) ? $row['profile_display_name'] : $row['user_email'];
        }
    }

    echo json_encode([
        'status' => 'success',
        'matched' => true,
        'workflow_id' => $matched['workflow_id'],
        'workflow_name' => $matched['workflow_name'],
        'distribution_mode' => $matched['workflow_distribution_mode'],
        'assigned_agent' => [
            'id' => $nextAgentId,
            'name' => $agentName
        ],
        'simulated_steps' => [
            ['step' => '1. Lead Ingested', 'detail' => $simName . ' from ' . ucfirst($simSource) . ' (Interest: ' . ucwords($simSubject) . ')'],
            ['step' => '2. Matched Distribution Rule', 'detail' => $matched['workflow_name'] . ' (Priority #' . $matched['workflow_priority'] . ')'],
            ['step' => '3. Agent Assigned', 'detail' => 'Assigned to ' . $agentName . ' via ' . ($matched['workflow_distribution_mode'] === 'round_robin' ? 'Round-Robin Rotation' : 'Specific Agent Routing')],
            ['step' => '4. Follow-up Automated', 'detail' => (!empty($matched['workflow_create_task']) ? 'Created ' . $matched['workflow_task_type'] . ' Task due in ' . $matched['workflow_task_due_hours'] . ' hours' : 'No task rule set')]
        ]
    ]);
    exit;
}

// Save (Create or Update) Distribution Rule
if ($action === 'save') {
    $wfId = (int)($input['id'] ?? 0);
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');
    $sources = trim($input['sources'] ?? 'all');
    $destination = trim($input['destination_filter'] ?? '');
    $mode = trim($input['distribution_mode'] ?? 'round_robin');
    $priority = (int)($input['priority'] ?? 10);
    $status = isset($input['status']) ? (!empty($input['status']) ? 1 : 0) : 1;

    $sendWa = !empty($input['send_whatsapp_greeting']) ? 1 : 0;
    $waMsg = trim($input['whatsapp_message'] ?? '');
    $createTask = !empty($input['create_task']) ? 1 : 0;
    $taskType = trim($input['task_type'] ?? 'Call');
    $taskHours = (int)($input['task_due_hours'] ?? 2);
    $taskNote = trim($input['task_note'] ?? 'Follow up with newly distributed lead');

    $assignedAgents = $input['assigned_agents'] ?? [];
    if (is_string($assignedAgents)) {
        $assignedAgents = json_decode($assignedAgents, true) ?: array_map('intval', explode(',', $assignedAgents));
    }
    if (!is_array($assignedAgents) || empty($assignedAgents)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Please select at least one agent in the distribution pool']);
        exit;
    }
    $assignedAgents = array_values(array_filter(array_map('intval', $assignedAgents)));

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Distribution rule name is required']);
        exit;
    }

    $escName = mysqli_real_escape_string($connection, $name);
    $escDesc = mysqli_real_escape_string($connection, $description);
    $escSources = mysqli_real_escape_string($connection, $sources);
    $escDest = mysqli_real_escape_string($connection, $destination);
    $escMode = mysqli_real_escape_string($connection, $mode);
    $escAgents = mysqli_real_escape_string($connection, json_encode($assignedAgents));
    $escWaMsg = mysqli_real_escape_string($connection, $waMsg);
    $escTaskType = mysqli_real_escape_string($connection, $taskType);
    $escTaskNote = mysqli_real_escape_string($connection, $taskNote);
    $userId = defined('USER_ID') ? (int)USER_ID : 0;

    if ($wfId > 0) {
        $upSql = "UPDATE `{$prefix}lead_workflow` SET
            workflow_name = '{$escName}',
            workflow_description = '{$escDesc}',
            workflow_sources = '{$escSources}',
            workflow_destination_filter = '{$escDest}',
            workflow_distribution_mode = '{$escMode}',
            workflow_assigned_agents = '{$escAgents}',
            workflow_send_whatsapp_greeting = {$sendWa},
            workflow_whatsapp_message = '{$escWaMsg}',
            workflow_create_task = {$createTask},
            workflow_task_type = '{$escTaskType}',
            workflow_task_due_hours = {$taskHours},
            workflow_task_note = '{$escTaskNote}',
            workflow_priority = {$priority},
            workflow_status = {$status}
            WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}";
        $ok = mysqli_query($connection, $upSql);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to update distribution rule: ' . mysqli_error($connection)]);
            exit;
        }
        $savedId = $wfId;
    } else {
        $insSql = "INSERT INTO `{$prefix}lead_workflow`
            (workflow_site_id, workflow_name, workflow_description, workflow_trigger_event, workflow_sources, workflow_destination_filter, workflow_distribution_mode, workflow_assigned_agents, workflow_send_whatsapp_greeting, workflow_whatsapp_message, workflow_create_task, workflow_task_type, workflow_task_due_hours, workflow_task_note, workflow_priority, workflow_status, workflow_created_by, workflow_created_at)
            VALUES ({$siteId}, '{$escName}', '{$escDesc}', 'lead_created', '{$escSources}', '{$escDest}', '{$escMode}', '{$escAgents}', {$sendWa}, '{$escWaMsg}', {$createTask}, '{$escTaskType}', {$taskHours}, '{$escTaskNote}', {$priority}, {$status}, {$userId}, NOW())";
        $ok = mysqli_query($connection, $insSql);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to create distribution rule: ' . mysqli_error($connection)]);
            exit;
        }
        $savedId = mysqli_insert_id($connection);
    }

    echo json_encode([
        'status' => 'success',
        'msg' => $wfId > 0 ? 'Distribution rule updated successfully!' : 'Distribution rule created successfully!',
        'workflow_id' => $savedId
    ]);
    exit;
}
