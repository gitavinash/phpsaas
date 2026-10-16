<?php

/**
 * V3 WhatsApp Chat Automation REST API
 * Path: /api/v3/integration/whatsapp_automation.php
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
// 1. GET: Fetch Single Workflow, Logs, or List with Stats
// --------------------------------------------------------------------------
if ($method === 'GET') {
    if (!empty($input['id'])) {
        $wfId = (int)$input['id'];
        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId} LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $wf = mysqli_fetch_assoc($q);
            $wf['workflow_nodes'] = json_decode($wf['workflow_nodes_json'] ?? '[]', true);
            echo json_encode(['status' => 'success', 'workflow' => $wf]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Workflow not found']);
        }
        exit;
    }

    if ($action === 'logs') {
        $logs = [];
        $logQ = mysqli_query($connection, "SELECT l.*, w.workflow_name 
            FROM `{$prefix}whatsapp_workflow_log` l
            LEFT JOIN `{$prefix}whatsapp_workflow` w ON w.workflow_id = l.log_workflow_id
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

    // Default: List all workflows with KPIs
    $workflows = [];
    $wQ = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_workflow` 
        WHERE workflow_site_id = {$siteId} 
        ORDER BY workflow_is_default ASC, workflow_id DESC");
    if ($wQ) {
        while ($r = mysqli_fetch_assoc($wQ)) {
            $r['workflow_nodes'] = json_decode($r['workflow_nodes_json'] ?? '[]', true);
            $workflows[] = $r;
        }
    }

    // Stats
    $statTotal = count($workflows);
    $statActive = 0;
    $statExecutions = 0;
    foreach ($workflows as $w) {
        if (!empty($w['workflow_status'])) $statActive++;
        $statExecutions += (int)($w['workflow_execution_count'] ?? 0);
    }

    // Read site configuration flag
    $cfgQ = mysqli_query($connection, "SELECT site_configuration_value FROM `{$prefix}site_configuration` 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key = 'enable_whatsapp_automation' 
          AND site_configuration_status = 1 LIMIT 1");
    $isPluginActive = false;
    if ($cfgQ && mysqli_num_rows($cfgQ) > 0) {
        $cfgRow = mysqli_fetch_assoc($cfgQ);
        $isPluginActive = ($cfgRow['site_configuration_value'] === '1' || $cfgRow['site_configuration_value'] === 1);
    }

    // Pre-built templates
    $templates = [
        [
            'id' => 'welcome_faq',
            'name' => 'Instant Welcome & Menu Bot',
            'description' => 'Greets travelers when they say hi or hello and offers interactive service choices.',
            'trigger_type' => 'keyword',
            'keywords' => 'hi, hello, hey, start, hola',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Incoming Message: "Hi / Hello"', 'category' => 'trigger'],
                ['id' => 'n2', 'type' => 'action', 'title' => 'Send Welcome Menu', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Hello {name}! 👋 Welcome to {company}.\n\nHow can we help you plan your journey today?\n1️⃣ Explore Tour Packages\n2️⃣ Get Customized Itinerary Quote\n3️⃣ Talk to Travel Consultant\n\nReply with a number or type your inquiry!"]
            ]
        ],
        [
            'id' => 'pricing_inquiry',
            'name' => 'Package & Price Inquiries Bot',
            'description' => 'Instantly responds to questions about costs, rates, and package quotes.',
            'trigger_type' => 'keyword',
            'keywords' => 'price, pricing, cost, rate, quote, package, charges',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Incoming Message: "Price / Package"', 'category' => 'trigger'],
                ['id' => 'n2', 'type' => 'action', 'title' => 'Send Pricing Guide', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Thank you for inquiring about tour pricing, {name}! 🏖️\n\nOur customized packages start from ₹7,999/- per person with luxury hotels, transfers, and sightseeing included.\n\nPlease share:\n• Your Destination\n• Number of Travelers\n• Expected Travel Month\n\nOur trip specialist will send you a personalized PDF itinerary right away!"]
            ]
        ],
        [
            'id' => 'agent_handoff',
            'name' => 'Human Agent Transfer Bot',
            'description' => 'Connects customer with an active team specialist when they ask for a human.',
            'trigger_type' => 'keyword',
            'keywords' => 'agent, human, executive, call me, talk to agent, support',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Incoming Message: "Agent / Human"', 'category' => 'trigger'],
                ['id' => 'n2', 'type' => 'action', 'title' => 'Notify & Confirm Handoff', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Connecting you with our next available travel specialist, {name}! 👩‍💼\n\nA team member will review your conversation and reply shortly. If urgent, you can also reach our desk directly."]
            ]
        ],
        [
            'id' => 'after_hours',
            'name' => 'Default Auto-Responder (Fallback)',
            'description' => 'Catches any incoming message that does not match a specific keyword.',
            'trigger_type' => 'default_fallback',
            'keywords' => '',
            'match_type' => 'contains',
            'is_default' => 1,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Any Unmatched Message', 'category' => 'trigger'],
                ['id' => 'n2', 'type' => 'action', 'title' => 'Send Friendly Confirmation', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Hello {name}, thanks for messaging {company}! 🌟 We have received your inquiry. One of our destination specialists will get in touch with you very shortly."]
            ]
        ]
    ];

    echo json_encode([
        'status' => 'success',
        'is_plugin_active' => $isPluginActive,
        'stats' => [
            'total_workflows' => $statTotal,
            'active_workflows' => $statActive,
            'total_executions' => $statExecutions,
        ],
        'workflows' => $workflows,
        'templates' => $templates
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST: Actions (save, toggle, delete, duplicate, test_simulate, toggle_plugin)
// --------------------------------------------------------------------------

// Toggle entire plugin
if ($action === 'toggle_plugin') {
    $newState = !empty($input['is_active']) ? '1' : '0';
    $chk = mysqli_query($connection, "SELECT site_configuration_id FROM `{$prefix}site_configuration` 
        WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'enable_whatsapp_automation' LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        mysqli_query($connection, "UPDATE `{$prefix}site_configuration` 
            SET site_configuration_value = '{$newState}', site_configuration_status = 1 
            WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'enable_whatsapp_automation'");
    } else {
        mysqli_query($connection, "INSERT INTO `{$prefix}site_configuration` 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) 
            VALUES ({$siteId}, 'enable_whatsapp_automation', '{$newState}', 1)");
    }
    echo json_encode([
        'status' => 'success',
        'msg' => $newState === '1' ? 'WhatsApp Chat Automation activated!' : 'WhatsApp Chat Automation disabled.',
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
        echo json_encode(['status' => 'error', 'msg' => 'Invalid workflow ID']);
        exit;
    }
    mysqli_query($connection, "UPDATE `{$prefix}whatsapp_workflow` SET workflow_status = {$status} WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}");
    echo json_encode(['status' => 'success', 'msg' => 'Workflow status updated successfully', 'workflow_id' => $wfId, 'new_status' => $status]);
    exit;
}

// Delete workflow
if ($action === 'delete') {
    $wfId = (int)($input['id'] ?? 0);
    if ($wfId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid workflow ID']);
        exit;
    }
    mysqli_query($connection, "DELETE FROM `{$prefix}whatsapp_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}");
    echo json_encode(['status' => 'success', 'msg' => 'Workflow deleted successfully']);
    exit;
}

// Duplicate workflow
if ($action === 'duplicate') {
    $wfId = (int)($input['id'] ?? 0);
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_workflow` WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId} LIMIT 1");
    if (!$q || mysqli_num_rows($q) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Workflow to clone not found']);
        exit;
    }
    $w = mysqli_fetch_assoc($q);
    $newName = mysqli_real_escape_string($connection, $w['workflow_name'] . ' (Copy)');
    $newDesc = mysqli_real_escape_string($connection, $w['workflow_description'] ?? '');
    $newTrigger = mysqli_real_escape_string($connection, $w['workflow_trigger_type'] ?? 'keyword');
    $newKeywords = mysqli_real_escape_string($connection, $w['workflow_keywords'] ?? '');
    $newMatch = mysqli_real_escape_string($connection, $w['workflow_match_type'] ?? 'contains');
    $newNodes = mysqli_real_escape_string($connection, $w['workflow_nodes_json'] ?? '[]');

    $insSql = "INSERT INTO `{$prefix}whatsapp_workflow` 
        (workflow_site_id, workflow_name, workflow_description, workflow_trigger_type, workflow_keywords, workflow_match_type, workflow_nodes_json, workflow_is_default, workflow_status, workflow_created_at)
        VALUES ({$siteId}, '{$newName}', '{$newDesc}', '{$newTrigger}', '{$newKeywords}', '{$newMatch}', '{$newNodes}', 0, 1, NOW())";
    mysqli_query($connection, $insSql);
    $newId = mysqli_insert_id($connection);
    echo json_encode(['status' => 'success', 'msg' => 'Workflow cloned successfully', 'new_id' => $newId]);
    exit;
}

// Interactive Simulator test
if ($action === 'test_simulate') {
    $testText = trim($input['test_text'] ?? 'hi');
    $contactName = trim($input['contact_name'] ?? 'Priya Sharma');
    $contactPhone = trim($input['contact_phone'] ?? '+919876543210');

    $clean = strtolower($testText);
    $wQ = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_workflow` 
        WHERE workflow_site_id = {$siteId} AND workflow_status = 1 
        ORDER BY workflow_is_default ASC, workflow_id ASC");

    $matched = null;
    $default = null;
    if ($wQ) {
        while ($wf = mysqli_fetch_assoc($wQ)) {
            if (!empty($wf['workflow_is_default'])) {
                $default = $wf;
                continue;
            }
            $kwStr = trim($wf['workflow_keywords'] ?? '');
            $matchType = $wf['workflow_match_type'] ?? 'contains';
            $keywords = array_map('trim', array_map('strtolower', explode(',', $kwStr)));
            foreach ($keywords as $kw) {
                if ($kw === '') continue;
                if ($matchType === 'exact' && $clean === $kw) {
                    $matched = $wf;
                    break 2;
                } elseif ($matchType === 'starts_with' && str_starts_with($clean, $kw)) {
                    $matched = $wf;
                    break 2;
                } elseif (strpos($clean, $kw) !== false) {
                    $matched = $wf;
                    break 2;
                }
            }
        }
    }
    if (!$matched && $default) {
        $matched = $default;
    }

    if (!$matched) {
        echo json_encode([
            'status' => 'success',
            'matched' => false,
            'msg' => 'No workflow matched this incoming text.',
            'reply' => null
        ]);
        exit;
    }

    $nodes = json_decode($matched['workflow_nodes_json'] ?? '[]', true);
    $replyText = "Thank you for reaching out!";
    foreach ($nodes as $n) {
        if (($n['type'] ?? '') === 'action' || ($n['category'] ?? '') === 'action') {
            $replyText = $n['message_text'] ?? ($n['text'] ?? ($n['reply'] ?? $replyText));
            break;
        }
    }
    $renderedReply = replaceWorkflowTokens($replyText, [
        'name'  => $contactName,
        'phone' => $contactPhone
    ]);

    echo json_encode([
        'status' => 'success',
        'matched' => true,
        'workflow_id' => $matched['workflow_id'],
        'workflow_name' => $matched['workflow_name'],
        'reply' => $renderedReply,
        'simulated_steps' => [
            ['step' => '1. Received Message', 'detail' => '"' . $testText . '" from ' . $contactName],
            ['step' => '2. Matched Workflow', 'detail' => $matched['workflow_name'] . ' (' . ($matched['workflow_is_default'] ? 'Default Fallback' : 'Keyword Match') . ')'],
            ['step' => '3. Action Executed', 'detail' => 'Dispatched automated reply to WhatsApp contact']
        ]
    ]);
    exit;
}

// Save (Create or Update) Workflow
if ($action === 'save') {
    $wfId = (int)($input['id'] ?? 0);
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');
    $triggerType = trim($input['trigger_type'] ?? 'keyword');
    $keywords = trim($input['keywords'] ?? '');
    $matchType = trim($input['match_type'] ?? 'contains');
    $isDefault = !empty($input['is_default']) ? 1 : 0;
    $status = isset($input['status']) ? (!empty($input['status']) ? 1 : 0) : 1;

    $nodes = $input['nodes'] ?? [];
    if (is_string($nodes)) {
        $nodes = json_decode($nodes, true);
    }
    if (!is_array($nodes) || empty($nodes)) {
        // Fallback minimal node
        $nodes = [
            ['id' => 't1', 'type' => 'trigger', 'title' => 'Keyword Match', 'category' => 'trigger'],
            ['id' => 'a1', 'type' => 'action', 'title' => 'Send WhatsApp Reply', 'category' => 'action', 'message_text' => trim($input['message_text'] ?? 'Hello {name}, thanks for contacting us!')]
        ];
    }

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Workflow title is required']);
        exit;
    }

    $escName = mysqli_real_escape_string($connection, $name);
    $escDesc = mysqli_real_escape_string($connection, $description);
    $escTrigger = mysqli_real_escape_string($connection, $triggerType);
    $escKeywords = mysqli_real_escape_string($connection, $keywords);
    $escMatch = mysqli_real_escape_string($connection, $matchType);
    $escNodes = mysqli_real_escape_string($connection, json_encode($nodes));
    $userId = defined('USER_ID') ? (int)USER_ID : 0;

    if ($isDefault) {
        // Unset other defaults for this site
        mysqli_query($connection, "UPDATE `{$prefix}whatsapp_workflow` SET workflow_is_default = 0 WHERE workflow_site_id = {$siteId}");
    }

    if ($wfId > 0) {
        $upSql = "UPDATE `{$prefix}whatsapp_workflow` SET
            workflow_name = '{$escName}',
            workflow_description = '{$escDesc}',
            workflow_trigger_type = '{$escTrigger}',
            workflow_keywords = '{$escKeywords}',
            workflow_match_type = '{$escMatch}',
            workflow_nodes_json = '{$escNodes}',
            workflow_is_default = {$isDefault},
            workflow_status = {$status}
            WHERE workflow_id = {$wfId} AND workflow_site_id = {$siteId}";
        $ok = mysqli_query($connection, $upSql);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to update workflow: ' . mysqli_error($connection)]);
            exit;
        }
        $savedId = $wfId;
    } else {
        $insSql = "INSERT INTO `{$prefix}whatsapp_workflow`
            (workflow_site_id, workflow_name, workflow_description, workflow_trigger_type, workflow_keywords, workflow_match_type, workflow_nodes_json, workflow_is_default, workflow_status, workflow_created_by, workflow_created_at)
            VALUES ({$siteId}, '{$escName}', '{$escDesc}', '{$escTrigger}', '{$escKeywords}', '{$escMatch}', '{$escNodes}', {$isDefault}, {$status}, {$userId}, NOW())";
        $ok = mysqli_query($connection, $insSql);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to create workflow: ' . mysqli_error($connection)]);
            exit;
        }
        $savedId = mysqli_insert_id($connection);
    }

    echo json_encode([
        'status' => 'success',
        'msg' => $wfId > 0 ? 'Workflow updated successfully!' : 'Workflow created successfully!',
        'workflow_id' => $savedId
    ]);
    exit;
}
