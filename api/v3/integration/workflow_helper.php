<?php

/**
 * V3 Workflow Helper & Automation Engine
 * Path: /api/v3/integration/workflow_helper.php
 * Handles self-healing schema creation and Lead Distribution Engine.
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

    // Lead Distribution Workflows
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
 * Seed pre-defined WhatsApp workflows (e.g. Collect Requirement & Send Itinerary, Default Concierge)
 * if no workflows exist yet for this site.
 */
function seedDefaultWhatsAppWorkflows($connection, $siteId)
{
    if (!$connection || $siteId <= 0) {
        return;
    }
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    $chk = mysqli_query($connection, "SELECT workflow_id FROM `{$prefix}whatsapp_workflow` WHERE workflow_site_id = {$siteId} LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        return;
    }

    // 1. Pre-defined: Collect Requirement and Send Itinerary Bot (with Buttons & If/Else Logic)
    $reqWfNodes = [
        [
            'id' => 'n_trigger',
            'type' => 'trigger',
            'category' => 'trigger',
            'title' => 'Incoming Keyword: "itinerary / package / trip"',
            'trigger_type' => 'keyword',
            'keywords' => 'itinerary, package, tour, trip, plan, holiday, quote',
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
            'itinerary_url' => 'https://blumox-app.test/assets/itineraries/manali-himachal-package.pdf',
            'brochure_url' => 'https://blumox-app.test/assets/itineraries/manali-himachal-package.pdf',
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
            'itinerary_url' => 'https://blumox-app.test/assets/itineraries/custom-tour-quotation.pdf',
            'brochure_url' => 'https://blumox-app.test/assets/itineraries/custom-tour-quotation.pdf',
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
            'message_text' => "To lock in early-bird hotel rates and confirm driver allotment, you can secure your reservation with a refundable token booking advance of ₹5,000:\n\n💳 Instant Secure Payment Link: https://blumox-app.test/pay/token-advance-deposit\n\n(Supports Google Pay, PhonePe, Paytm, UPI, Cards, and Net Banking)"
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
    ];

    $reqNodesJson = mysqli_real_escape_string($connection, json_encode($reqWfNodes));
    $reqSql = "INSERT INTO `{$prefix}whatsapp_workflow` (
        workflow_site_id, workflow_name, workflow_description, workflow_trigger_type,
        workflow_keywords, workflow_match_type, workflow_nodes_json, workflow_is_default,
        workflow_status, workflow_created_at
    ) VALUES (
        {$siteId}, 'Travel Agency Auto-Sales & Itinerary Bot',
        'Real-world travel sales flow: collects requirements, sends default destination itinerary, personalizes proposal, and requests advance token deposit.',
        'keyword', 'trip, tour, package, travel, holiday, plan, itinerary, book, quote, inquiry, vacations, manali, kerala, dubai, ladakh, himachal, munnar, couple, family, group, advance, token, pay, booking', 'contains',
        '{$reqNodesJson}', 0, 1, NOW()
    )";
    @mysqli_query($connection, $reqSql);

    // 2. Pre-defined: Default Fallback Concierge
    $defaultWfNodes = [
        [
            'id' => 'n_trigger',
            'type' => 'trigger',
            'category' => 'trigger',
            'title' => 'Any Unmatched Incoming Message',
            'trigger_type' => 'default_fallback',
            'keywords' => '',
            'match_type' => 'contains',
            'is_default' => 1
        ],
        [
            'id' => 'n_action_1',
            'type' => 'action',
            'category' => 'action',
            'action_type' => 'reply_text',
            'title' => 'Instant Helpful Acknowledgment',
            'message_text' => "Hello {name}! 🌟 Thank you for messaging {company}. We have received your inquiry and our trip specialist will reach out to you shortly.\n\nType *itinerary* to get instant travel plans or *agent* to speak with our desk."
        ]
    ];

    $defNodesJson = mysqli_real_escape_string($connection, json_encode($defaultWfNodes));
    $defSql = "INSERT INTO `{$prefix}whatsapp_workflow` (
        workflow_site_id, workflow_name, workflow_description, workflow_trigger_type,
        workflow_keywords, workflow_match_type, workflow_nodes_json, workflow_is_default,
        workflow_status, workflow_created_at
    ) VALUES (
        {$siteId}, 'Default 24/7 Smart Concierge',
        'Answers any unrecognized incoming chat immediately and alerts available travel executives.',
        'default_fallback', '', 'contains',
        '{$defNodesJson}', 1, 1, NOW()
    )";
    @mysqli_query($connection, $defSql);
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
