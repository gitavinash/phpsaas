<?php

/**
 * V3 WhatsApp Chat Automation REST API
 * Path: /api/v3/integration/whatsapp_automation.php
 */

header('Content-Type: application/json');

if (!isset($connection) || !$connection) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        @require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        @require_once $docRoot . '/lib/modules/connection.php';
    }
    if ((!isset($connection) || !$connection) && file_exists($docRoot . '/dbConfig.php')) {
        require_once $docRoot . '/dbConfig.php';
        $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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

    // Ensure pre-defined workflows are seeded if site has none yet
    seedDefaultWhatsAppWorkflows($connection, $siteId);

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

    // Fetch active tour packages and destinations from the database
    $packagesList = [];
    $packageDestinations = [];
    $packageDurations = [];
    $pkgTbl = "{$prefix}tour_package";
    $chkPkg = @mysqli_query($connection, "SHOW TABLES LIKE '{$pkgTbl}'");
    if ($chkPkg && mysqli_num_rows($chkPkg) > 0) {
        $pSiteFilter = ($siteId > 0) ? "(tour_package_site_id = {$siteId} OR tour_package_site_id = 0)" : "1=1";
        $pQ = mysqli_query($connection, "SELECT tour_package_id, tour_package_title, tour_package_city, tour_package_state, tour_package_country, tour_package_duration, tour_package_price 
            FROM `{$pkgTbl}` 
            WHERE {$pSiteFilter} AND tour_package_status = 1 
            ORDER BY tour_package_id DESC LIMIT 60");
        if ($pQ) {
            while ($p = mysqli_fetch_assoc($pQ)) {
                $packagesList[] = [
                    'id' => (int)$p['tour_package_id'],
                    'title' => trim($p['tour_package_title'] ?? ''),
                    'city' => trim($p['tour_package_city'] ?? ''),
                    'state' => trim($p['tour_package_state'] ?? ''),
                    'country' => trim($p['tour_package_country'] ?? ''),
                    'duration' => trim($p['tour_package_duration'] ?? ''),
                    'price' => trim($p['tour_package_price'] ?? '')
                ];
                $city = trim($p['tour_package_city'] ?? '');
                $state = trim($p['tour_package_state'] ?? '');
                $dest = $city ?: $state;
                if (!empty($dest) && !in_array($dest, $packageDestinations, true)) {
                    $packageDestinations[] = $dest;
                }
                $durVal = (int)($p['tour_package_duration'] ?? 0);
                if ($durVal > 0) {
                    $durStr = $durVal . ' Nights / ' . ($durVal + 1) . ' Days';
                    if (!in_array($durStr, $packageDurations, true)) {
                        $packageDurations[] = $durStr;
                    }
                }
            }
        }
    }

    // Pre-built templates
    $templates = [
        [
            'id' => 'collect_requirement_itinerary',
            'name' => 'Travel Agency Auto-Sales & Itinerary Bot',
            'description' => 'Real-world travel sales flow: collects requirements, sends default destination itinerary, personalizes proposal, and requests advance token deposit.',
            'trigger_type' => 'keyword',
            'keywords' => 'trip, tour, package, travel, holiday, plan, itinerary, book, quote, inquiry, vacations',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                [
                    'id' => 'n_trigger',
                    'type' => 'trigger',
                    'category' => 'trigger',
                    'title' => 'Incoming Keyword: "trip / tour / package / itinerary / quote"',
                    'trigger_type' => 'keyword',
                    'keywords' => 'trip, tour, package, travel, holiday, plan, itinerary, book, quote, inquiry, vacations',
                    'match_type' => 'contains',
                    'is_default' => 0
                ],
                [
                    'id' => 'n_step_1',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'reply_text',
                    'title' => 'Namaste & Welcome from Travel Specialist',
                    'message_text' => "Namaste {name}! 🌍✈️ Welcome to {company}.\n\nI am your dedicated Holiday Specialist. We specialize in curating customized luxury vacations with handpicked 4★/5★ resorts, private sightseeing transfers, and 24/7 on-trip concierge."
                ],
                [
                    'id' => 'n_step_2',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'collect_input',
                    'title' => 'Choose Preferred Holiday Destination',
                    'field_key' => 'destination',
                    'input_mode' => 'buttons',
                    'message_text' => "Which dream holiday destination are you looking to explore next?",
                    'options' => [
                        '🏔️ Manali & Himachal',
                        '🏖️ Kerala & Munnar',
                        '🏙️ Dubai Luxury',
                        '⛺ Ladakh Adventure'
                    ]
                ],
                [
                    'id' => 'n_step_3',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_itinerary',
                    'title' => 'Instant Default Destination Itinerary PDF',
                    'package_id' => 3,
                    'itinerary_title' => '5 Night / 6 Days Manali & Himachal Bestselling Tour Package',
                    'itinerary_url' => 'https://isw-app.test/assets/itineraries/manali-himachal-package.pdf',
                    'brochure_url' => 'https://isw-app.test/assets/itineraries/manali-himachal-package.pdf',
                    'message_text' => "While our destination desk tailors your personalized quotation, here is our bestselling verified itinerary brochure for your selected destination! 📄✨\n\nIncludes day-by-day sightseeing highlights, verified mountain resort options, and private cab transfers: [Download Itinerary Brochure]"
                ],
                [
                    'id' => 'n_step_4',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'collect_input',
                    'title' => 'Collect Number of Travelers & Group Size',
                    'field_key' => 'pax',
                    'input_mode' => 'buttons',
                    'message_text' => "Wonderful! How many travelers will be accompanying you on this journey?",
                    'options' => [
                        '💑 Couple (2 Adults)',
                        '👨‍👩‍👧 Family (3-4 Pax)',
                        '👥 Group Tour (5-10 Pax)',
                        '🏢 Corporate Group (10+ Pax)'
                    ]
                ],
                [
                    'id' => 'n_step_5',
                    'type' => 'condition',
                    'category' => 'logic',
                    'action_type' => 'condition_branch',
                    'title' => 'Smart Route by Group Preference (If / Else)',
                    'condition_target' => 'last_button',
                    'condition_operator' => 'contains',
                    'condition_value' => 'Couple',
                    'if_action_type' => 'send_itinerary',
                    'if_package_id' => 3,
                    'if_itinerary_title' => '5 Night / 6 Days Manali Romantic Holiday Package',
                    'if_label' => 'Dispatch Romantic Couple Mountain Resort Itinerary PDF',
                    'if_message' => "Perfect for couples! 🏔️❤️ Here is your tailored Romantic Itinerary with luxury mountain view jacuzzi cottage, candlelight dinner, snow valley cab, and private airport pickup included: 📄 [Download Couple Itinerary PDF]",
                    'else_action_type' => 'send_itinerary',
                    'else_package_id' => 16,
                    'else_itinerary_title' => 'Curated Family & Group Holiday Package',
                    'else_label' => 'Dispatch Family / Group Curated Holiday Package PDF',
                    'else_message' => "Great choice! 👨‍👩‍👧‍👦 Here is our curated Family & Group Itinerary with interconnected resort suites, private tempo traveler, and all entry tickets included: 📄 [Download Family / Group Itinerary PDF]"
                ],
                [
                    'id' => 'n_step_6',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'delay',
                    'title' => 'Realistic Sales Agent Typing Cadence',
                    'delay_seconds' => 3
                ],
                [
                    'id' => 'n_step_7',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_itinerary',
                    'title' => 'Dispatch Final Tailored Tour Itinerary & Inclusions',
                    'package_id' => 3,
                    'itinerary_title' => 'Final Custom Tour Itinerary & Quotation Summary',
                    'itinerary_url' => 'https://isw-app.test/assets/itineraries/custom-tour-quotation.pdf',
                    'brochure_url' => 'https://isw-app.test/assets/itineraries/custom-tour-quotation.pdf',
                    'message_text' => "Here is your complete Tailored Holiday Quotation, {name}! 🌟\n\n✅ 5 Nights accommodation in 4★/5★ verified resorts\n✅ Daily buffet breakfast & chef dinners\n✅ Dedicated private AC sedan for all airport transfers & local sightseeing\n✅ 24/7 dedicated travel concierge on WhatsApp"
                ],
                [
                    'id' => 'n_step_8',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'payment_link',
                    'title' => 'Token Advance Booking Deposit Link (₹5,000)',
                    'amount' => 5000,
                    'currency' => 'INR',
                    'message_text' => "To lock in early-bird hotel rates and confirm driver allotment, you can secure your reservation with a refundable token booking advance of ₹5,000:\n\n💳 Instant Secure Payment Link: https://isw-app.test/pay/token-advance-deposit\n\n(Supports Google Pay, PhonePe, Paytm, UPI, Cards, and Net Banking)"
                ],
                [
                    'id' => 'n_step_9',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'interactive_menu',
                    'title' => 'Customer Confirmation & Next Steps Buttons',
                    'message_text' => "How would you like to proceed with your booking, {name}?",
                    'options' => [
                        '💳 Pay ₹5,000 Token Advance',
                        '✏️ Customise Hotels / Dates',
                        '📞 Speak with Travel Consultant'
                    ]
                ],
                [
                    'id' => 'n_step_10',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'tag_lead',
                    'title' => 'Update CRM Stage to Proposal Sent & Tag Lead',
                    'lead_stage' => 'Proposal / Itinerary Sent',
                    'tags' => 'travel_agent_bot, itinerary_sent, token_advance_pending, high_intent'
                ]
            ]
        ],
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
            'keywords' => 'price, pricing, cost, rate, quote, charges',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Incoming Message: "Price / Cost"', 'category' => 'trigger'],
                ['id' => 'n2', 'type' => 'action', 'title' => 'Send Pricing Guide', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Thank you for inquiring about tour pricing, {name}! 🏖️\n\nOur customized packages start from ₹7,999/- per person with luxury hotels, transfers, and sightseeing included.\n\nPlease share:\n• Your Destination\n• Number of Travelers\n• Expected Travel Month\n\nOur trip specialist will send you a personalized PDF itinerary right away!"]
            ]
        ],
        [
            'id' => 'payment_token',
            'name' => 'Booking Advance & Payment Link Bot',
            'description' => 'Sends instant online UPI / Card payment checkout link to collect token booking advances.',
            'trigger_type' => 'keyword',
            'keywords' => 'pay, payment, advance, book now, deposit, token',
            'match_type' => 'contains',
            'is_default' => 0,
            'nodes' => [
                ['id' => 'n1', 'type' => 'trigger', 'title' => 'Incoming Message: "Pay / Advance"', 'category' => 'trigger'],
                [
                    'id' => 'n2',
                    'type' => 'action',
                    'title' => 'Send Secure Payment Checkout',
                    'category' => 'action',
                    'action_type' => 'payment_link',
                    'amount' => 5000,
                    'message_text' => "Hi {name}, you can confirm your booking by paying a refundable advance deposit of ₹5,000/-.\n\n🔒 Click the secure link below to complete payment via UPI, Credit/Debit Card, or Net Banking:\n👉 https://pay.aleya.in/book/tour-advance"
                ]
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
                ['id' => 'n2', 'type' => 'action', 'title' => 'Send Friendly Confirmation', 'category' => 'action', 'action_type' => 'reply_text', 'message_text' => "Hello {name}, thanks for messaging {company}! 🌟 We have received your inquiry. One of our destination specialists will get in touch with you very shortly.\n\nType *itinerary* to receive tour plans instantly!"]
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
        'templates' => $templates,
        'tour_packages' => $packagesList,
        'package_destinations' => $packageDestinations,
        'package_durations' => $packageDurations
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
            'reply' => null,
            'simulated_steps' => [
                ['step' => '1. Received Message', 'detail' => '"' . $testText . '" from ' . $contactName],
                ['step' => '2. Rule Evaluation', 'detail' => 'Scanned all active workflows. No keyword match found and no default fallback bot is active.'],
                ['step' => '3. Idle / Human Operator', 'detail' => 'Chatbot remains idle. Pending human agent response.']
            ]
        ]);
        exit;
    }

    $nodes = json_decode($matched['workflow_nodes_json'] ?? '[]', true);
    $replyParts = [];
    $simButtons = [];
    $simSteps = [
        ['step' => '1. Received Message', 'detail' => '"' . $testText . '" from ' . $contactName],
        ['step' => '2. Matched Workflow', 'detail' => $matched['workflow_name'] . ' (' . ($matched['workflow_is_default'] ? 'Default Fallback' : 'Keyword Match') . ')']
    ];

    $isTravelAgencyWorkflow = ((int)$matched['workflow_id'] === 4) || (strpos(strtolower($matched['workflow_name']), 'travel') !== false);

    // Contextual detection for Travel Agency Sales Flow
    $hasDestMatch = (
        strpos($clean, 'manali') !== false || strpos($clean, 'himachal') !== false ||
        strpos($clean, 'kerala') !== false || strpos($clean, 'munnar') !== false ||
        strpos($clean, 'dubai') !== false ||
        strpos($clean, 'ladakh') !== false || strpos($clean, 'leh') !== false ||
        strpos($clean, 'kutch') !== false || strpos($clean, 'gujarat') !== false
    );

    $hasPaxMatch = (
        strpos($clean, 'couple') !== false || strpos($clean, 'family') !== false ||
        strpos($clean, 'group') !== false || strpos($clean, 'corporate') !== false ||
        strpos($clean, 'adult') !== false || strpos($clean, 'pax') !== false
    );

    $hasPaymentMatch = (
        strpos($clean, 'pay') !== false || strpos($clean, 'token') !== false ||
        strpos($clean, 'advance') !== false || strpos($clean, 'deposit') !== false
    );

    $hasHandoffMatch = (
        strpos($clean, 'speak') !== false || strpos($clean, 'consultant') !== false ||
        strpos($clean, 'call') !== false || strpos($clean, 'agent') !== false
    );

    $hasCustomiseMatch = (
        strpos($clean, 'customise') !== false || strpos($clean, 'customize') !== false ||
        strpos($clean, 'dates') !== false || strpos($clean, 'hotel') !== false
    );

    if ($isTravelAgencyWorkflow && $hasDestMatch) {
        // --- TURN 2: Destination Selected -> Query Tour Package API & Send Default Itinerary, then ask Pax ---
        $destMap = [
            'manali' => 'Manali',
            'himachal' => 'Manali',
            'kerala' => 'Munnar',
            'munnar' => 'Munnar',
            'dubai' => 'Dubai',
            'ladakh' => 'Leh',
            'leh' => 'Leh',
            'kutch' => 'Kutch',
            'gujarat' => 'Gujarat'
        ];
        $matchedDest = 'Manali';
        foreach ($destMap as $k => $d) {
            if (strpos($clean, $k) !== false) {
                $matchedDest = $d;
                break;
            }
        }

        // Query database tour packages for relevant itinerary
        $pkgQ = mysqli_query($connection, "SELECT tour_package_id, tour_package_title, tour_package_city, tour_package_state, tour_package_duration, tour_package_hotel 
            FROM `{$prefix}tour_package` 
            WHERE tour_package_status = 1 AND (tour_package_city LIKE '%{$matchedDest}%' OR tour_package_state LIKE '%{$matchedDest}%' OR tour_package_title LIKE '%{$matchedDest}%') 
            ORDER BY tour_package_id ASC LIMIT 1");
        $pkg = ($pkgQ && mysqli_num_rows($pkgQ) > 0) ? mysqli_fetch_assoc($pkgQ) : null;
        $pkgId = $pkg ? (int)$pkg['tour_package_id'] : 3;
        $pkgTitle = $pkg ? $pkg['tour_package_title'] : '5 Night / 6 Days Manali & Himachal Tour Package';
        $pkgDuration = $pkg ? (int)$pkg['tour_package_duration'] : 5;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pkgTitle), '-'));
        $itineraryPdfUrl = "https://isw-app.test/assets/itineraries/{$slug}.pdf";

        $replyParts[] = "Fantastic choice, {$contactName}! 🏔️✨ While our destination team customizes your private pricing, here is our verified bestselling default itinerary brochure for *{$pkgTitle}* ({$pkgDuration} Nights / " . ($pkgDuration + 1) . " Days):\n\n📄 Verified Itinerary Brochure: {$itineraryPdfUrl}\n\n(Includes verified 4★ mountain resort options, daily breakfasts & dinners, and private vehicle cab sightseeing)";
        $replyParts[] = "To customize your private resort room category and dedicated transfers, how many travelers will be accompanying you?";

        $simButtons = [
            '💑 Couple (2 Adults)',
            '👨‍👩‍👧 Family (3-4 Pax)',
            '👥 Group Tour (5-10 Pax)',
            '🏢 Corporate Group (10+ Pax)'
        ];

        $simSteps[] = ['step' => '3. Customer Destination Pick', 'detail' => 'User selected: "' . $testText . '"'];
        $simSteps[] = ['step' => '4. Inventory API Query', 'detail' => 'Fetched matching package from database: #' . $pkgId . ' (' . $pkgTitle . ')'];
        $simSteps[] = ['step' => '5. Send Default Itinerary', 'detail' => 'Dispatched PDF brochure: ' . $itineraryPdfUrl];
        $simSteps[] = ['step' => '6. Collect Requirement (Pax)', 'detail' => 'Prompted for number of travelers & group category'];

    } elseif ($isTravelAgencyWorkflow && $hasPaxMatch) {
        // --- TURN 3: Pax Selected -> Evaluate Condition Branch, Send Tailored Itinerary & Advance Payment Link ---
        $isCouple = (strpos($clean, 'couple') !== false || strpos($clean, '2 adult') !== false);

        if ($isCouple) {
            $replyParts[] = "Perfect for couples, {$contactName}! 🏔️❤️ Here is your Tailored Romantic Itinerary:\n\n✅ 5 Nights in Luxury Mountain View Jacuzzi Suite\n✅ Daily buffet breakfast & 4-course candle-lit dinner with wine\n✅ Dedicated private AC sedan for Rohtang Pass, Solang Valley & Kasol\n✅ 24/7 dedicated travel concierge on WhatsApp\n\nTotal Package: ₹32,000 all-inclusive for 2 Adults.";
            $simSteps[] = ['step' => '3. Smart Logic Condition [TRUE MATCH]', 'detail' => 'Requirement matched "Couple" -> Selected Romantic Mountain Itinerary Path'];
        } else {
            $replyParts[] = "Great choice for family & group travel, {$contactName}! 👨‍👩‍👧‍👦 Here is your Tailored Family Itinerary:\n\n✅ 5 Nights in Interconnected Luxury Mountain Suites\n✅ Daily breakfast & dinner buffet for all members\n✅ Dedicated private Tempo Traveler / SUV for all sightseeing\n✅ All monument passes & state permits included\n\nTotal Package: ₹58,000 all-inclusive for the group.";
            $simSteps[] = ['step' => '3. Smart Logic Condition [FALSE / ELSE PATH]', 'detail' => 'Requirement did not match couple -> Selected Family & Group Itinerary Path'];
        }

        // Real sales agent booking closing with token payment link
        $replyParts[] = "💳 To lock in these early-bird resort dates and confirm private driver allotment, you can secure your reservation with a refundable token deposit of ₹5,000:\n\n👉 Instant Secure Payment Link: https://isw-app.test/pay/token-advance-deposit\n\n(Supports Google Pay, PhonePe, Paytm, UPI, Cards, and Net Banking)";

        $simButtons = [
            '💳 Pay ₹5,000 Token Advance',
            '✏️ Customise Hotels / Dates',
            '📞 Speak with Travel Consultant'
        ];

        $simSteps[] = ['step' => '4. Realistic Cadence Delay', 'detail' => 'Simulated 3s typing cadence while compiling custom quotation'];
        $simSteps[] = ['step' => '5. Dispatch Tailored Itinerary', 'detail' => 'Sent customized quotation & inclusions'];
        $simSteps[] = ['step' => '6. Advance Booking Payment Link', 'detail' => 'Generated token payment link for ₹5,000'];
        $simSteps[] = ['step' => '7. CRM Lead Progression', 'detail' => 'Stage updated to "Proposal / Itinerary Sent" & tagged: [travel_agent_bot, itinerary_sent, token_advance_pending, high_intent]'];

    } elseif ($isTravelAgencyWorkflow && $hasPaymentMatch) {
        // --- TURN 4A: Payment link clicked / requested ---
        $replyParts[] = "Thank you {$contactName}! 🙏 Once your ₹5,000 booking token advance is received, your official Reservation Voucher, Hotel Confirmation Code, and Dedicated Driver Details will be generated immediately on WhatsApp.\n\nOur on-trip concierge desk will be active 24/7 during your journey!";
        $simButtons = ['📞 Call Reservations Desk', '📄 View Booking Terms'];
        $simSteps[] = ['step' => '3. Payment Flow Triggered', 'detail' => 'Customer clicked: ' . $testText];
        $simSteps[] = ['step' => '4. Payment Verification Pending', 'detail' => 'Awaiting gateway webhook for ₹5,000'];
        $simSteps[] = ['step' => '5. Priority Alert', 'detail' => 'Logged in CRM: High-Intent Lead awaiting token deposit confirmation'];

    } elseif ($isTravelAgencyWorkflow && $hasHandoffMatch) {
        // --- TURN 4B: Agent Handoff ---
        $replyParts[] = "Connecting you directly with senior travel specialist Rahul Verma at our reservations desk. 👩‍💼\n\nHe is reviewing your itinerary details and will call you on {$contactPhone} in the next 5-10 minutes! If you prefer chat, you can also continue messaging right here.";
        $simButtons = ['💬 Continue on WhatsApp', '📧 Email Me Quote'];
        $simSteps[] = ['step' => '3. Agent Handoff Requested', 'detail' => 'Customer requested specialist consultation'];
        $simSteps[] = ['step' => '4. Internal Routing', 'detail' => 'Assigned lead to Senior Travel Specialist (Desk #4)'];

    } elseif ($isTravelAgencyWorkflow && $hasCustomiseMatch) {
        // --- TURN 4C: Customise dates / hotels ---
        $replyParts[] = "Certainly! You can customize anything in your plan, {$contactName}. ✏️\n\nPlease reply with your preferred travel dates, or any special requests (e.g. 'Add 1 extra day in Kasol' or 'Upgrade to 5★ mountain resort'), and our team will adjust the quotation immediately!";
        $simButtons = ['🏔️ Manali & Himachal', '🏖️ Kerala & Munnar'];
        $simSteps[] = ['step' => '3. Customization Request', 'detail' => 'Prompted customer for travel dates & accommodation adjustments'];

    } else {
        // --- Standard / Initial Flow Execution ---
        $stepNum = 3;
        if (is_array($nodes)) {
            foreach ($nodes as $n) {
                $type = $n['type'] ?? ($n['category'] ?? '');
                if ($type === 'trigger') continue;

                $actionType = $n['action_type'] ?? 'reply_text';
                $msg = $n['message_text'] ?? ($n['text'] ?? '');

                if ($actionType === 'delay') {
                    $secs = (int)($n['delay_seconds'] ?? 2);
                    $simSteps[] = ['step' => $stepNum . '. Wait / Cadence Delay', 'detail' => 'Paused for ' . $secs . ' seconds (WhatsApp typing indicator shown)'];
                } elseif ($actionType === 'collect_input') {
                    $field = $n['field_key'] ?? 'requirement';
                    $simSteps[] = ['step' => $stepNum . '. Collect Requirement (' . ucfirst($field) . ')', 'detail' => 'Prompted customer: "' . substr($msg, 0, 80) . '..."'];
                    if (!empty($msg) && count($replyParts) < 2) {
                        $replyParts[] = replaceWorkflowTokens($msg, ['name' => $contactName, 'phone' => $contactPhone]);
                    }
                    if (!empty($n['options']) && is_array($n['options']) && empty($simButtons)) {
                        $simButtons = $n['options'];
                    }
                } elseif ($actionType === 'condition_branch') {
                    $cVal = strtolower(trim($n['condition_value'] ?? ''));
                    $op = $n['condition_operator'] ?? 'contains';
                    $isMatch = ($op === 'equals') ? ($clean === $cVal) : (strpos($clean, $cVal) !== false);
                    if ($isMatch) {
                        $ifAct = $n['if_action_type'] ?? 'send_itinerary';
                        $ifLabel = $n['if_label'] ?? 'Send Matched Itinerary Package';
                        $ifMsg = $n['if_message'] ?? '';
                        $simSteps[] = ['step' => $stepNum . '. Condition Branch [TRUE MATCH]', 'detail' => 'User selection/input matched "' . $cVal . '". Executing TRUE action: ' . $ifLabel];
                        if (!empty($ifMsg) && count($replyParts) < 2) {
                            $replyParts[] = replaceWorkflowTokens($ifMsg, ['name' => $contactName, 'phone' => $contactPhone]);
                        }
                    } else {
                        $elseAct = $n['else_action_type'] ?? 'send_itinerary';
                        $elseLabel = $n['else_label'] ?? 'Send Fallback / Alternative Package';
                        $elseMsg = $n['else_message'] ?? '';
                        $simSteps[] = ['step' => $stepNum . '. Condition Branch [FALSE / ELSE PATH]', 'detail' => 'User selection did not match "' . $cVal . '". Executing FALSE action: ' . $elseLabel];
                        if (!empty($elseMsg) && count($replyParts) < 2) {
                            $replyParts[] = replaceWorkflowTokens($elseMsg, ['name' => $contactName, 'phone' => $contactPhone]);
                        }
                    }
                } elseif ($actionType === 'send_itinerary') {
                    $itinTitle = $n['itinerary_title'] ?? 'Curated Itinerary Package';
                    $simSteps[] = ['step' => $stepNum . '. Send Itinerary Package', 'detail' => 'Dispatched PDF brochure: "' . $itinTitle . '"'];
                    if (!empty($msg) && count($replyParts) < 2) {
                        $replyParts[] = replaceWorkflowTokens($msg, ['name' => $contactName, 'phone' => $contactPhone]);
                    }
                } elseif ($actionType === 'interactive_menu') {
                    $opts = is_array($n['options'] ?? null) ? implode(' | ', $n['options']) : 'Choices';
                    $simSteps[] = ['step' => $stepNum . '. Quick Reply Options', 'detail' => 'Offered interactive choices: ' . $opts];
                    if (!empty($msg) && count($replyParts) < 2) {
                        $replyParts[] = replaceWorkflowTokens($msg, ['name' => $contactName, 'phone' => $contactPhone]);
                    }
                    if (!empty($n['options']) && is_array($n['options']) && empty($simButtons)) {
                        $simButtons = $n['options'];
                    }
                } elseif ($actionType === 'payment_link') {
                    $amt = $n['amount'] ?? 5000;
                    $simSteps[] = ['step' => $stepNum . '. Payment Checkout Link', 'detail' => 'Generated token payment link for ₹' . number_format($amt)];
                    if (!empty($msg) && count($replyParts) < 2) {
                        $replyParts[] = replaceWorkflowTokens($msg, ['name' => $contactName, 'phone' => $contactPhone]);
                    }
                } elseif ($actionType === 'handoff_agent') {
                    $dept = $n['department'] ?? 'Sales';
                    $simSteps[] = ['step' => $stepNum . '. Agent Handoff', 'detail' => 'Routed conversation to ' . ucfirst($dept) . ' team desk'];
                } else {
                    // reply_text
                    $simSteps[] = ['step' => $stepNum . '. WhatsApp Auto-Reply', 'detail' => 'Dispatched message: "' . substr($msg, 0, 80) . '..."'];
                    if (!empty($msg) && count($replyParts) < 2) {
                        $replyParts[] = replaceWorkflowTokens($msg, ['name' => $contactName, 'phone' => $contactPhone]);
                    }
                }
                $stepNum++;
            }
        }
    }

    $finalReply = !empty($replyParts) ? implode("\n\n", $replyParts) : "Hello " . $contactName . ", thank you for reaching out!";

    echo json_encode([
        'status' => 'success',
        'matched' => true,
        'workflow_id' => $matched['workflow_id'],
        'workflow_name' => $matched['workflow_name'],
        'reply' => $finalReply,
        'buttons' => $simButtons,
        'simulated_steps' => $simSteps
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
