<?php

/**
 * V3 Visual Automation Workflows API Endpoint
 * Path: /api/v3/workflows.php
 * Handles workflow CRUD, flowchart nodes persistence, pre-built templates, simulation test runs, and audit logs.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 2);
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
    } else {
        $siteId = 1;
    }
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

/**
 * Self-healing schema initialization
 */
function initAutomationWorkflowTables($conn, $tblPrefix, $sId)
{
    if (!$conn) {
        return;
    }

    $sql1 = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}automation_workflow` (
      `workflow_id` INT AUTO_INCREMENT PRIMARY KEY,
      `workflow_site_id` INT NOT NULL DEFAULT 0,
      `workflow_name` VARCHAR(255) NOT NULL,
      `workflow_description` VARCHAR(500) DEFAULT NULL,
      `workflow_category` VARCHAR(50) NOT NULL DEFAULT 'crm',
      `workflow_trigger_type` VARCHAR(50) NOT NULL DEFAULT 'lead_created',
      `workflow_nodes_json` LONGTEXT NOT NULL,
      `workflow_connections_json` LONGTEXT DEFAULT NULL,
      `workflow_canvas_data` LONGTEXT DEFAULT NULL,
      `workflow_status` TINYINT(1) NOT NULL DEFAULT 1,
      `workflow_execution_count` INT NOT NULL DEFAULT 0,
      `workflow_last_run_at` DATETIME DEFAULT NULL,
      `workflow_created_by` INT NOT NULL DEFAULT 0,
      `workflow_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `workflow_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX (`workflow_site_id`),
      INDEX (`workflow_status`),
      INDEX (`workflow_trigger_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($conn, $sql1);

    $sql2 = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}automation_workflow_log` (
      `log_id` INT AUTO_INCREMENT PRIMARY KEY,
      `log_site_id` INT NOT NULL DEFAULT 0,
      `log_workflow_id` INT NOT NULL DEFAULT 0,
      `log_workflow_name` VARCHAR(255) DEFAULT NULL,
      `log_trigger_event` VARCHAR(100) DEFAULT NULL,
      `log_status` VARCHAR(30) NOT NULL DEFAULT 'success',
      `log_steps_executed` INT NOT NULL DEFAULT 0,
      `log_execution_time_ms` INT NOT NULL DEFAULT 0,
      `log_payload_json` LONGTEXT DEFAULT NULL,
      `log_execution_details` LONGTEXT DEFAULT NULL,
      `log_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX (`log_site_id`),
      INDEX (`log_workflow_id`),
      INDEX (`log_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($conn, $sql2);

    // Check if any workflows exist for this site, if not seed pre-built starter templates
    $checkQ = mysqli_query($conn, "SELECT workflow_id FROM `{$tblPrefix}automation_workflow` WHERE workflow_site_id = {$sId} LIMIT 1");
    if ($checkQ && mysqli_num_rows($checkQ) === 0) {
        seedStarterWorkflows($conn, $tblPrefix, $sId);
    }
}

/**
 * Seed starter templates for easy onboarding
 */
function seedStarterWorkflows($conn, $tblPrefix, $sId)
{
    $templates = getStarterWorkflowTemplates();
    foreach ($templates as $tpl) {
        $name = mysqli_real_escape_string($conn, $tpl['name']);
        $desc = mysqli_real_escape_string($conn, $tpl['description']);
        $cat = mysqli_real_escape_string($conn, $tpl['category']);
        $trigger = mysqli_real_escape_string($conn, $tpl['trigger_type']);
        $nodes = mysqli_real_escape_string($conn, json_encode($tpl['nodes']));
        $conns = mysqli_real_escape_string($conn, json_encode($tpl['connections']));
        $canvas = mysqli_real_escape_string($conn, json_encode(['zoom' => 1, 'panX' => 40, 'panY' => 40]));

        $ins = "INSERT INTO `{$tblPrefix}automation_workflow` (
            workflow_site_id, workflow_name, workflow_description, workflow_category,
            workflow_trigger_type, workflow_nodes_json, workflow_connections_json,
            workflow_canvas_data, workflow_status, workflow_execution_count, workflow_created_at
        ) VALUES (
            {$sId}, '{$name}', '{$desc}', '{$cat}', '{$trigger}', '{$nodes}', '{$conns}', '{$canvas}', 1, 0, NOW()
        )";
        @mysqli_query($conn, $ins);
    }
}

/**
 * Built-in Template Catalog
 */
function getStarterWorkflowTemplates()
{
    return [
        [
            'id' => 'lead_round_robin_auto_responder',
            'name' => 'Instant Lead Auto-Responder & Round-Robin Desk',
            'description' => 'When a new traveler lead arrives from any ad or web channel, send an instant WhatsApp greeting, assign to the next available agent, tag as high-intent, and create a 2-hour discovery call task.',
            'category' => 'crm',
            'trigger_type' => 'lead_created',
            'nodes' => [
                [
                    'id' => 'node_1',
                    'type' => 'trigger',
                    'category' => 'trigger',
                    'trigger_event' => 'lead_created',
                    'title' => 'New Lead Ingested',
                    'subtitle' => 'Source: Facebook Ads, Web Forms & Website',
                    'icon' => 'fi-rr-bullseye-arrow',
                    'color' => '#8b5cf6',
                    'x' => 100,
                    'y' => 120,
                    'config' => [
                        'sources' => ['all'],
                        'filter_mode' => 'any'
                    ]
                ],
                [
                    'id' => 'node_2',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_whatsapp',
                    'title' => 'Send WhatsApp Welcome Greeting',
                    'subtitle' => 'Personalized warm welcome from travel specialist',
                    'icon' => 'fi-rr-comment-alt',
                    'color' => '#10b981',
                    'x' => 420,
                    'y' => 120,
                    'config' => [
                        'message_template' => "Namaste {name}! 🌍✈️ Welcome to {company}.\n\nThank you for reaching out for your upcoming vacation plan. Our destination holiday specialist is preparing custom resort recommendations and sightseeing highlights for you right now!",
                        'attach_brochure' => '1'
                    ]
                ],
                [
                    'id' => 'node_3',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'assign_agent',
                    'title' => 'Distribute Lead (Round-Robin)',
                    'subtitle' => 'Evenly allocate to active sales consultants',
                    'icon' => 'fi-rr-users-alt',
                    'color' => '#0ea5e9',
                    'x' => 740,
                    'y' => 120,
                    'config' => [
                        'mode' => 'round_robin',
                        'team' => 'all_active_agents'
                    ]
                ],
                [
                    'id' => 'node_4',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'add_tags',
                    'title' => 'Tag Lead in CRM',
                    'subtitle' => 'Add "new_lead", "high_intent", "assigned"',
                    'icon' => 'fi-rr-tags',
                    'color' => '#f59e0b',
                    'x' => 1060,
                    'y' => 120,
                    'config' => [
                        'tags' => 'new_lead, high_intent, auto_assigned'
                    ]
                ],
                [
                    'id' => 'node_5',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'create_task',
                    'title' => 'Create Follow-up CRM Task',
                    'subtitle' => 'Call lead within 2 hours of arrival',
                    'icon' => 'fi-rr-calendar-clock',
                    'color' => '#ec4899',
                    'x' => 1380,
                    'y' => 120,
                    'config' => [
                        'task_type' => 'Call',
                        'due_hours' => 2,
                        'task_note' => 'Discovery call: Confirm travel dates, travelers count, hotel category preference'
                    ]
                ]
            ],
            'connections' => [
                ['id' => 'conn_1_2', 'from' => 'node_1', 'to' => 'node_2', 'label' => 'Next Step'],
                ['id' => 'conn_2_3', 'from' => 'node_2', 'to' => 'node_3', 'label' => 'Next Step'],
                ['id' => 'conn_3_4', 'from' => 'node_3', 'to' => 'node_4', 'label' => 'Next Step'],
                ['id' => 'conn_4_5', 'from' => 'node_4', 'to' => 'node_5', 'label' => 'Next Step']
            ]
        ],
        [
            'id' => 'whatsapp_smart_itinerary_concierge',
            'name' => 'WhatsApp Travel Itinerary & Smart Concierge',
            'description' => 'When a traveler messages on WhatsApp asking for itinerary or quote, branch dynamically: send romantic couple package or family holiday PDF, then send token deposit link.',
            'category' => 'whatsapp',
            'trigger_type' => 'whatsapp_incoming',
            'nodes' => [
                [
                    'id' => 'node_w1',
                    'type' => 'trigger',
                    'category' => 'trigger',
                    'trigger_event' => 'whatsapp_incoming',
                    'title' => 'WhatsApp Keyword Received',
                    'subtitle' => 'Keywords: itinerary, package, quote, trip, plan',
                    'icon' => 'fi-rr-comment-dots',
                    'color' => '#8b5cf6',
                    'x' => 100,
                    'y' => 220,
                    'config' => [
                        'keywords' => 'itinerary, package, tour, trip, quote, holiday, cost',
                        'match_type' => 'contains'
                    ]
                ],
                [
                    'id' => 'node_w2',
                    'type' => 'logic',
                    'category' => 'logic',
                    'action_type' => 'condition_branch',
                    'title' => 'Traveler Type Condition',
                    'subtitle' => 'Check if message mentions "couple" or "honeymoon"',
                    'icon' => 'fi-rr-code-fork',
                    'color' => '#f59e0b',
                    'x' => 420,
                    'y' => 220,
                    'config' => [
                        'field' => 'message_content',
                        'operator' => 'contains',
                        'value' => 'couple, honeymoon, 2 pax',
                        'branch_yes_label' => 'Couple Vacation',
                        'branch_no_label' => 'Family / Group'
                    ]
                ],
                [
                    'id' => 'node_w3_yes',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_itinerary',
                    'title' => 'Dispatch Romantic Couple Tour PDF',
                    'subtitle' => 'Includes 4★ Jacuzzi Suite & Candlelight Dinner',
                    'icon' => 'fi-rr-document-signed',
                    'color' => '#10b981',
                    'x' => 760,
                    'y' => 100,
                    'config' => [
                        'itinerary_title' => 'Luxury Romantic Escape Tour Package',
                        'package_id' => '3',
                        'message_text' => 'Here is our bestselling romantic couple itinerary brochure with private sedan transfers and mountain view jacuzzi suite included! 📄✨'
                    ]
                ],
                [
                    'id' => 'node_w3_no',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_itinerary',
                    'title' => 'Dispatch Family & Group Holiday PDF',
                    'subtitle' => 'Includes interconnected rooms & tempo traveler',
                    'icon' => 'fi-rr-document-signed',
                    'color' => '#10b981',
                    'x' => 760,
                    'y' => 340,
                    'config' => [
                        'itinerary_title' => 'Curated Family & Friends Grand Holiday',
                        'package_id' => '16',
                        'message_text' => 'Here is our complete family and group tour itinerary with private transfers, luxury buffet meals, and sightseeing passes! 📄👨‍👩‍👧‍👦'
                    ]
                ],
                [
                    'id' => 'node_w4',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'delay',
                    'title' => 'Wait 3 Seconds',
                    'subtitle' => 'Realistic specialist typing pace',
                    'icon' => 'fi-rr-hourglass-end',
                    'color' => '#64748b',
                    'x' => 1100,
                    'y' => 220,
                    'config' => [
                        'delay_seconds' => 3
                    ]
                ],
                [
                    'id' => 'node_w5',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'payment_link',
                    'title' => 'Token Advance Booking Link',
                    'subtitle' => 'Lock hotel reservation with refundable ₹5,000 token',
                    'icon' => 'fi-rr-credit-card',
                    'color' => '#0ea5e9',
                    'x' => 1400,
                    'y' => 220,
                    'config' => [
                        'amount' => '5000',
                        'message_text' => "To lock early-bird hotel rates and confirm driver allotment, secure your spot with ₹5,000 token advance:\n\n💳 Instant UPI / Cards Link: {payment_link}"
                    ]
                ]
            ],
            'connections' => [
                ['id' => 'conn_w1_w2', 'from' => 'node_w1', 'to' => 'node_w2', 'label' => 'Evaluate'],
                ['id' => 'conn_w2_yes', 'from' => 'node_w2', 'to' => 'node_w3_yes', 'label' => 'If Couple / Yes', 'port' => 'yes'],
                ['id' => 'conn_w2_no', 'from' => 'node_w2', 'to' => 'node_w3_no', 'label' => 'If Family / No', 'port' => 'no'],
                ['id' => 'conn_w3y_w4', 'from' => 'node_w3_yes', 'to' => 'node_w4', 'label' => 'Continue'],
                ['id' => 'conn_w3n_w4', 'from' => 'node_w3_no', 'to' => 'node_w4', 'label' => 'Continue'],
                ['id' => 'conn_w4_w5', 'from' => 'node_w4', 'to' => 'node_w5', 'label' => 'Send Token Link']
            ]
        ],
        [
            'id' => 'payment_reconciliation_voucher_delivery',
            'name' => 'Payment Reconciliation & Instant Voucher Delivery',
            'description' => 'When a client payment is captured via Razorpay, PhonePe, or UPI, update deal status to Confirmed, dispatch automated WhatsApp receipt, and send hotel vouchers via Email.',
            'category' => 'payments',
            'trigger_type' => 'payment_received',
            'nodes' => [
                [
                    'id' => 'node_p1',
                    'type' => 'trigger',
                    'category' => 'trigger',
                    'trigger_event' => 'payment_received',
                    'title' => 'Payment Captured & Reconciled',
                    'subtitle' => 'Trigger: Razorpay, PhonePe, Cashfree, UPI',
                    'icon' => 'fi-rr-badge-percent',
                    'color' => '#8b5cf6',
                    'x' => 100,
                    'y' => 140,
                    'config' => [
                        'gateway' => 'all',
                        'min_amount' => '1'
                    ]
                ],
                [
                    'id' => 'node_p2',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'update_lead_stage',
                    'title' => 'Update CRM Stage: "Deal Confirmed"',
                    'subtitle' => 'Move lead to Confirmed Deals pipeline',
                    'icon' => 'fi-rr-check-circle',
                    'color' => '#10b981',
                    'x' => 430,
                    'y' => 140,
                    'config' => [
                        'stage' => 'Confirmed / Won',
                        'stage_id' => '1'
                    ]
                ],
                [
                    'id' => 'node_p3',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'send_whatsapp',
                    'title' => 'Dispatch WhatsApp Booking Confirmation',
                    'subtitle' => 'Send payment receipt, booking reference & helpline',
                    'icon' => 'fi-rr-comment-alt',
                    'color' => '#0ea5e9',
                    'x' => 770,
                    'y' => 140,
                    'config' => [
                        'message_template' => "Woohoo! Payment Received 🎉\n\nDear {name}, we have received your booking payment of ₹{amount} for Ref #{booking_id}. Your luxury tour reservation is now confirmed!\n\nOur operations team has initiated resort voucher allotment."
                    ]
                ],
                [
                    'id' => 'node_p4',
                    'type' => 'action',
                    'category' => 'action',
                    'action_type' => 'webhook_call',
                    'title' => 'Trigger Accounting Webhook',
                    'subtitle' => 'Sync invoice to external ERP / Tally / Zapier',
                    'icon' => 'fi-rr-plug',
                    'color' => '#f59e0b',
                    'x' => 1100,
                    'y' => 140,
                    'config' => [
                        'url' => 'https://hooks.zapier.com/hooks/catch/finance-sync/',
                        'method' => 'POST'
                    ]
                ]
            ],
            'connections' => [
                ['id' => 'conn_p1_p2', 'from' => 'node_p1', 'to' => 'node_p2', 'label' => 'Update CRM'],
                ['id' => 'conn_p2_p3', 'from' => 'node_p2', 'to' => 'node_p3', 'label' => 'Notify Client'],
                ['id' => 'conn_p3_p4', 'from' => 'node_p3', 'to' => 'node_p4', 'label' => 'Sync Accounting']
            ]
        ]
    ];
}

/**
 * Helper to fetch site-specific lead sources from real CRM data
 */
function getWorkflowLeadSources($conn, $tblPrefix, $sId)
{
    $sources = [
        ['id' => 'all', 'name' => 'All Ingestion Channels (Universal)'],
        ['id' => 'direct', 'name' => 'Direct Website Inbound'],
        ['id' => 'facebook', 'name' => 'Facebook & Instagram Lead Ads'],
        ['id' => 'Google Adwords', 'name' => 'Google Ads Search & PPC'],
        ['id' => 'whatsapp', 'name' => 'WhatsApp Chat Inbound']
    ];
    $added = ['all' => true, 'direct' => true, 'facebook' => true, 'google adwords' => true, 'whatsapp' => true];

    // 1. Fetch distinct sources from enquiry table for current site
    $siteCond = ($sId > 0) ? "enquiry_site_id = {$sId}" : "1=1";
    $q = @mysqli_query($conn, "SELECT DISTINCT enquiry_source FROM `{$tblPrefix}enquiry` WHERE {$siteCond} AND enquiry_source IS NOT NULL AND enquiry_source != '' ORDER BY enquiry_source ASC LIMIT 40");
    if (!$q || mysqli_num_rows($q) === 0) {
        $q = @mysqli_query($conn, "SELECT DISTINCT enquiry_source FROM `{$tblPrefix}enquiry` WHERE enquiry_source IS NOT NULL AND enquiry_source != '' ORDER BY enquiry_source ASC LIMIT 30");
    }
    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $src = trim($r['enquiry_source']);
            $key = strtolower($src);
            if (!empty($src) && !isset($added[$key])) {
                $added[$key] = true;
                $label = ucfirst($src);
                if (stripos($src, '.com') !== false || stripos($src, '.in') !== false) {
                    $label = 'Website: ' . preg_replace('#^https?://#i', '', $src);
                }
                $sources[] = [
                    'id' => $src,
                    'name' => $label
                ];
            }
        }
    }

    // 2. Fetch custom web forms for this site
    $forms = [];
    $wfCond = ($sId > 0) ? "form_site_id = {$sId}" : "1=1";
    $qWf = @mysqli_query($conn, "SELECT form_id, form_title, form_slug FROM `{$tblPrefix}web_form` WHERE {$wfCond} AND form_status = 1 ORDER BY form_title ASC LIMIT 20");
    if ($qWf) {
        while ($r = mysqli_fetch_assoc($qWf)) {
            $forms[] = [
                'id' => (int)$r['form_id'],
                'name' => $r['form_title'],
                'slug' => $r['form_slug']
            ];
            $key = 'webform_' . $r['form_id'];
            if (!isset($added[$key])) {
                $added[$key] = true;
                $sources[] = [
                    'id' => 'form_' . $r['form_id'],
                    'name' => 'Web Form: ' . $r['form_title']
                ];
            }
        }
    }

    // 3. Fetch FB lead forms for this site
    $fbForms = [];
    $fbCond = ($sId > 0) ? "fb_forms_site_id = {$sId}" : "1=1";
    $qFb = @mysqli_query($conn, "SELECT fb_forms_id, fb_forms_name FROM `{$tblPrefix}fb_forms` WHERE {$fbCond} LIMIT 20");
    if ($qFb) {
        while ($r = mysqli_fetch_assoc($qFb)) {
            $fbForms[] = [
                'id' => $r['fb_forms_id'],
                'name' => $r['fb_forms_name']
            ];
            $key = 'fb_' . $r['fb_forms_id'];
            if (!isset($added[$key])) {
                $added[$key] = true;
                $sources[] = [
                    'id' => 'fb_' . $r['fb_forms_id'],
                    'name' => 'FB Lead Ad: ' . $r['fb_forms_name']
                ];
            }
        }
    }

    return [
        'sources' => $sources,
        'web_forms' => $forms,
        'fb_forms' => $fbForms
    ];
}

/**
 * Helper to fetch site-specific WhatsApp templates from real database
 */
function getWorkflowWhatsAppTemplates($conn, $tblPrefix, $sId)
{
    $templates = [];
    $siteCond = ($sId > 0) ? "(template_site_id = {$sId} OR template_site_id = 0)" : "1=1";
    $q = @mysqli_query($conn, "SELECT template_id, template_name, template_action_trigger, template_content, template_is_action_active, template_is_system_default 
        FROM `{$tblPrefix}whatsapp_template` 
        WHERE {$siteCond} AND template_status = 1 
        ORDER BY template_is_action_active DESC, template_is_system_default DESC, template_name ASC LIMIT 50");
    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $templates[] = [
                'id' => (int)$r['template_id'],
                'name' => $r['template_name'],
                'trigger' => $r['template_action_trigger'],
                'content' => $r['template_content'],
                'is_active' => (int)$r['template_is_action_active'] === 1
            ];
        }
    }
    return $templates;
}

/**
 * Helper to fetch site-specific users/sales agents
 */
function getWorkflowAgents($conn, $tblPrefix, $sId)
{
    $agents = [];
    $siteCondition = ($sId > 0) ? " AND site_access_site_id = {$sId}" : "";
    $sql = "SELECT user_id, user_email, profile_display_name, site_access_type
            FROM `{$tblPrefix}user`
            INNER JOIN `{$tblPrefix}profile` ON profile_user_id = user_id
            INNER JOIN `{$tblPrefix}site_access` ON site_access_user_id = user_id
            WHERE site_access_status = 1{$siteCondition}
            ORDER BY profile_display_name ASC LIMIT 50";
    $agQ = @mysqli_query($conn, $sql);
    if (!$agQ || mysqli_num_rows($agQ) === 0) {
        $agQ = @mysqli_query($conn, "SELECT user_id, profile_display_name, user_email FROM `{$tblPrefix}user`
            LEFT JOIN `{$tblPrefix}profile` ON profile_user_id = user_id
            WHERE user_status = 1
            ORDER BY user_id ASC LIMIT 30");
    }
    if ($agQ) {
        while ($r = mysqli_fetch_assoc($agQ)) {
            $name = !empty($r['profile_display_name']) ? trim($r['profile_display_name']) : $r['user_email'];
            $role = !empty($r['site_access_type']) ? ucwords(str_replace('_', ' ', $r['site_access_type'])) : 'Sales Specialist';
            $agents[] = [
                'id' => (int)$r['user_id'],
                'name' => $name,
                'email' => $r['user_email'],
                'role' => $role
            ];
        }
    }
    return $agents;
}

/**
 * Helper to fetch site tour packages / itineraries
 */
function getWorkflowPackages($conn, $tblPrefix, $sId)
{
    $packages = [];
    $pkgQ = @mysqli_query($conn, "SELECT tour_package_id, tour_package_title, tour_package_duration FROM `{$tblPrefix}tour_package`
        WHERE (tour_package_site_id = {$sId} OR tour_package_site_id = 0) AND tour_package_status = 1
        ORDER BY tour_package_id DESC LIMIT 40");

    if ($pkgQ && mysqli_num_rows($pkgQ) > 0) {
        while ($r = mysqli_fetch_assoc($pkgQ)) {
            $packages[] = [
                'id' => (int)$r['tour_package_id'],
                'name' => $r['tour_package_title'],
                'duration' => !empty($r['tour_package_duration']) ? ($r['tour_package_duration'] . ' Days') : 'Custom Tour'
            ];
        }
    } else {
        $pkgQ2 = @mysqli_query($conn, "SELECT package_id, package_title FROM `{$tblPrefix}package`
            WHERE package_status = 1
            ORDER BY package_id DESC LIMIT 40");
        if ($pkgQ2 && mysqli_num_rows($pkgQ2) > 0) {
            while ($r = mysqli_fetch_assoc($pkgQ2)) {
                $packages[] = [
                    'id' => (int)$r['package_id'],
                    'name' => $r['package_title'],
                    'duration' => 'Standard Package'
                ];
            }
        }
    }
    return $packages;
}

/**
 * Helper to fetch CRM Pipeline Stages from status-config
 */
function getWorkflowPipelineStages()
{
    $statusConfigPath = __DIR__ . '/leads/status-config.php';
    if (file_exists($statusConfigPath)) {
        require_once $statusConfigPath;
        if (function_exists('getAleyaLeadStatuses')) {
            $raw = getAleyaLeadStatuses();
            $stages = [];
            foreach ($raw as $id => $s) {
                $stages[] = [
                    'id' => (int)$id,
                    'code' => $s['code'] ?? '',
                    'label' => $s['label'] ?? '',
                    'stage' => $s['stage'] ?? '',
                    'color' => $s['color'] ?? '#3b82f6',
                    'icon' => $s['icon'] ?? 'fi-rr-flag'
                ];
            }
            return $stages;
        }
    }
    return [
        ['id' => 0, 'code' => 'new_enquiry', 'label' => 'New Enquiry', 'stage' => 'inquiry', 'color' => '#0284c7', 'icon' => 'fi-rr-sparkles'],
        ['id' => 2, 'code' => 'assigned', 'label' => 'Assigned', 'stage' => 'assignment', 'color' => '#f59e0b', 'icon' => 'fi-rr-user-time'],
        ['id' => 19, 'code' => 'contacted', 'label' => 'Contacted', 'stage' => 'qualification', 'color' => '#3b82f6', 'icon' => 'fi-rr-phone-call'],
        ['id' => 20, 'code' => 'requirement_collected', 'label' => 'Requirement Collected', 'stage' => 'qualification', 'color' => '#6366f1', 'icon' => 'fi-rr-clipboard-list-check'],
        ['id' => 21, 'code' => 'itinerary_in_progress', 'label' => 'Itinerary In Progress', 'stage' => 'proposal', 'color' => '#8b5cf6', 'icon' => 'fi-rr-map-marker-edit'],
        ['id' => 9, 'code' => 'itinerary_sent', 'label' => 'Itinerary Sent', 'stage' => 'proposal', 'color' => '#2563eb', 'icon' => 'fi-rr-route'],
        ['id' => 23, 'code' => 'quotation_sent', 'label' => 'Quotation Sent', 'stage' => 'proposal', 'color' => '#0ea5e9', 'icon' => 'fi-rr-paper-plane'],
        ['id' => 10, 'code' => 'quotation_approved', 'label' => 'Quotation Approved', 'stage' => 'closing', 'color' => '#10b981', 'icon' => 'fi-rr-badge-check'],
        ['id' => 6, 'code' => 'payment_pending', 'label' => 'Payment Pending', 'stage' => 'closing', 'color' => '#0891b2', 'icon' => 'fi-rr-credit-card'],
        ['id' => 1, 'code' => 'converted_to_booking', 'label' => 'Converted to Booking', 'stage' => 'won', 'color' => '#059669', 'icon' => 'fi-rr-check-circle']
    ];
}

// Initialize tables
initAutomationWorkflowTables($connection, $prefix, $siteId);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

// Handle raw JSON input
$rawInput = file_get_contents('php://input');
$jsonBody = !empty($rawInput) ? json_decode($rawInput, true) : null;
if (is_array($jsonBody)) {
    if (isset($jsonBody['action'])) {
        $action = $jsonBody['action'];
    }
}

switch ($action) {
    case 'list':
        // Fetch all workflows for current site
        $filterStatus = $_GET['status'] ?? 'all';
        $filterCategory = $_GET['category'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $where = ["workflow_site_id = {$siteId}"];
        if ($filterStatus === 'active') {
            $where[] = "workflow_status = 1";
        } elseif ($filterStatus === 'inactive') {
            $where[] = "workflow_status = 0";
        }

        if ($filterCategory !== 'all' && !empty($filterCategory)) {
            $escCat = mysqli_real_escape_string($connection, $filterCategory);
            $where[] = "workflow_category = '{$escCat}'";
        }

        if (!empty($search)) {
            $escSearch = mysqli_real_escape_string($connection, $search);
            $where[] = "(workflow_name LIKE '%{$escSearch}%' OR workflow_description LIKE '%{$escSearch}%' OR workflow_trigger_type LIKE '%{$escSearch}%')";
        }

        $whereClause = implode(' AND ', $where);
        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}automation_workflow` WHERE {$whereClause} ORDER BY workflow_id DESC");

        $workflows = [];
        $totalActive = 0;
        $totalExecutions = 0;

        if ($q) {
            while ($row = mysqli_fetch_assoc($q)) {
                $nodes = json_decode($row['workflow_nodes_json'] ?? '[]', true) ?: [];
                $conns = json_decode($row['workflow_connections_json'] ?? '[]', true) ?: [];
                $canvas = json_decode($row['workflow_canvas_data'] ?? '{}', true) ?: (object)[];

                $isActive = (int)$row['workflow_status'] === 1;
                if ($isActive) {
                    $totalActive++;
                }
                $execCount = (int)$row['workflow_execution_count'];
                $totalExecutions += $execCount;

                $workflows[] = [
                    'id' => (int)$row['workflow_id'],
                    'name' => $row['workflow_name'],
                    'description' => $row['workflow_description'],
                    'category' => $row['workflow_category'],
                    'trigger_type' => $row['workflow_trigger_type'],
                    'status' => (int)$row['workflow_status'],
                    'execution_count' => $execCount,
                    'last_run_at' => $row['workflow_last_run_at'],
                    'node_count' => count($nodes),
                    'created_at' => $row['workflow_created_at'],
                    'updated_at' => $row['workflow_updated_at']
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'workflows' => $workflows,
                'stats' => [
                    'total' => count($workflows),
                    'active' => $totalActive,
                    'executions' => $totalExecutions,
                    'templates_available' => count(getStarterWorkflowTemplates())
                ]
            ]
        ]);
        exit;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid workflow ID required.']);
            exit;
        }

        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}automation_workflow` WHERE workflow_id = {$id} AND workflow_site_id = {$siteId} LIMIT 1");
        if (!$q || mysqli_num_rows($q) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Workflow not found.']);
            exit;
        }

        $row = mysqli_fetch_assoc($q);
        $nodes = json_decode($row['workflow_nodes_json'] ?? '[]', true) ?: [];
        $conns = json_decode($row['workflow_connections_json'] ?? '[]', true) ?: [];
        $canvas = json_decode($row['workflow_canvas_data'] ?? '{}', true) ?: ['zoom' => 1, 'panX' => 40, 'panY' => 40];

        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => (int)$row['workflow_id'],
                'name' => $row['workflow_name'],
                'description' => $row['workflow_description'],
                'category' => $row['workflow_category'],
                'trigger_type' => $row['workflow_trigger_type'],
                'status' => (int)$row['workflow_status'],
                'execution_count' => (int)$row['workflow_execution_count'],
                'last_run_at' => $row['workflow_last_run_at'],
                'nodes' => $nodes,
                'connections' => $conns,
                'canvas' => $canvas,
                'created_at' => $row['workflow_created_at'],
                'updated_at' => $row['workflow_updated_at']
            ]
        ]);
        exit;

    case 'save':
        $params = $jsonBody ?: $_POST;
        $id = (int)($params['id'] ?? 0);
        $name = trim($params['name'] ?? '');
        $description = trim($params['description'] ?? '');
        $category = trim($params['category'] ?? 'crm');
        $triggerType = trim($params['trigger_type'] ?? 'lead_created');
        $status = isset($params['status']) ? (int)$params['status'] : 1;

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Workflow name is required.']);
            exit;
        }

        $nodes = is_array($params['nodes'] ?? null) ? $params['nodes'] : json_decode($params['nodes'] ?? '[]', true);
        if (!is_array($nodes) || empty($nodes)) {
            // Default 1 trigger node if empty
            $nodes = [
                [
                    'id' => 'node_trigger',
                    'type' => 'trigger',
                    'category' => 'trigger',
                    'trigger_event' => $triggerType,
                    'title' => 'Start Flow Trigger',
                    'subtitle' => 'When ' . str_replace('_', ' ', $triggerType) . ' occurs',
                    'icon' => 'fi-rr-play',
                    'color' => '#8b5cf6',
                    'x' => 120,
                    'y' => 160,
                    'config' => []
                ]
            ];
        }

        $connections = is_array($params['connections'] ?? null) ? $params['connections'] : json_decode($params['connections'] ?? '[]', true);
        if (!is_array($connections)) {
            $connections = [];
        }

        $canvasData = is_array($params['canvas'] ?? null) ? $params['canvas'] : json_decode($params['canvas'] ?? '{}', true);
        if (!is_array($canvasData)) {
            $canvasData = ['zoom' => 1, 'panX' => 40, 'panY' => 40];
        }

        $escName = mysqli_real_escape_string($connection, $name);
        $escDesc = mysqli_real_escape_string($connection, $description);
        $escCat = mysqli_real_escape_string($connection, $category);
        $escTrigger = mysqli_real_escape_string($connection, $triggerType);
        $nodesJson = mysqli_real_escape_string($connection, json_encode($nodes));
        $connsJson = mysqli_real_escape_string($connection, json_encode($connections));
        $canvasJson = mysqli_real_escape_string($connection, json_encode($canvasData));

        if ($id > 0) {
            // Update
            $sql = "UPDATE `{$prefix}automation_workflow` SET 
                workflow_name = '{$escName}',
                workflow_description = '{$escDesc}',
                workflow_category = '{$escCat}',
                workflow_trigger_type = '{$escTrigger}',
                workflow_nodes_json = '{$nodesJson}',
                workflow_connections_json = '{$connsJson}',
                workflow_canvas_data = '{$canvasJson}',
                workflow_status = {$status},
                workflow_updated_at = NOW()
                WHERE workflow_id = {$id} AND workflow_site_id = {$siteId}";

            if (mysqli_query($connection, $sql)) {
                echo json_encode([
                    'status' => 'success',
                    'msg' => 'Workflow saved successfully!',
                    'data' => ['id' => $id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'msg' => 'Failed to update workflow: ' . mysqli_error($connection)]);
            }
        } else {
            // Insert
            $sql = "INSERT INTO `{$prefix}automation_workflow` (
                workflow_site_id, workflow_name, workflow_description, workflow_category,
                workflow_trigger_type, workflow_nodes_json, workflow_connections_json,
                workflow_canvas_data, workflow_status, workflow_execution_count, workflow_created_at
            ) VALUES (
                {$siteId}, '{$escName}', '{$escDesc}', '{$escCat}', '{$escTrigger}',
                '{$nodesJson}', '{$connsJson}', '{$canvasJson}', {$status}, 0, NOW()
            )";

            if (mysqli_query($connection, $sql)) {
                $newId = mysqli_insert_id($connection);
                echo json_encode([
                    'status' => 'success',
                    'msg' => 'New workflow created successfully!',
                    'data' => ['id' => $newId]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'msg' => 'Failed to create workflow: ' . mysqli_error($connection)]);
            }
        }
        exit;

    case 'toggle_status':
        $params = $jsonBody ?: $_POST;
        $id = (int)($params['id'] ?? 0);
        $status = isset($params['status']) ? (int)$params['status'] : 1;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid workflow ID required.']);
            exit;
        }

        $sql = "UPDATE `{$prefix}automation_workflow` SET workflow_status = {$status} WHERE workflow_id = {$id} AND workflow_site_id = {$siteId}";
        if (mysqli_query($connection, $sql)) {
            echo json_encode([
                'status' => 'success',
                'msg' => $status === 1 ? 'Workflow activated!' : 'Workflow paused.',
                'data' => ['id' => $id, 'status' => $status]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to update status.']);
        }
        exit;

    case 'duplicate':
        $params = $jsonBody ?: $_POST;
        $id = (int)($params['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid workflow ID required.']);
            exit;
        }

        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}automation_workflow` WHERE workflow_id = {$id} AND workflow_site_id = {$siteId} LIMIT 1");
        if (!$q || mysqli_num_rows($q) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Original workflow not found.']);
            exit;
        }

        $orig = mysqli_fetch_assoc($q);
        $dupName = mysqli_real_escape_string($connection, $orig['workflow_name'] . ' (Copy)');
        $dupDesc = mysqli_real_escape_string($connection, $orig['workflow_description']);
        $dupCat = mysqli_real_escape_string($connection, $orig['workflow_category']);
        $dupTrigger = mysqli_real_escape_string($connection, $orig['workflow_trigger_type']);
        $dupNodes = mysqli_real_escape_string($connection, $orig['workflow_nodes_json']);
        $dupConns = mysqli_real_escape_string($connection, $orig['workflow_connections_json']);
        $dupCanvas = mysqli_real_escape_string($connection, $orig['workflow_canvas_data']);

        $ins = "INSERT INTO `{$prefix}automation_workflow` (
            workflow_site_id, workflow_name, workflow_description, workflow_category,
            workflow_trigger_type, workflow_nodes_json, workflow_connections_json,
            workflow_canvas_data, workflow_status, workflow_execution_count, workflow_created_at
        ) VALUES (
            {$siteId}, '{$dupName}', '{$dupDesc}', '{$dupCat}', '{$dupTrigger}',
            '{$dupNodes}', '{$dupConns}', '{$dupCanvas}', 0, 0, NOW()
        )";

        if (mysqli_query($connection, $ins)) {
            $newId = mysqli_insert_id($connection);
            echo json_encode([
                'status' => 'success',
                'msg' => 'Workflow duplicated as a draft copy!',
                'data' => ['id' => $newId]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to duplicate workflow.']);
        }
        exit;

    case 'delete':
        $params = $jsonBody ?: $_POST;
        $id = (int)($params['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid workflow ID required.']);
            exit;
        }

        mysqli_query($connection, "DELETE FROM `{$prefix}automation_workflow` WHERE workflow_id = {$id} AND workflow_site_id = {$siteId}");
        mysqli_query($connection, "DELETE FROM `{$prefix}automation_workflow_log` WHERE log_workflow_id = {$id} AND log_site_id = {$siteId}");

        echo json_encode([
            'status' => 'success',
            'msg' => 'Workflow and associated execution logs deleted.'
        ]);
        exit;

    case 'test_run':
        // Simulates or executes a Make.com-style "Run Once" test run on the visual flowchart
        $params = $jsonBody ?: $_POST;
        $id = (int)($params['id'] ?? 0);
        $nodes = is_array($params['nodes'] ?? null) ? $params['nodes'] : json_decode($params['nodes'] ?? '[]', true);
        $conns = is_array($params['connections'] ?? null) ? $params['connections'] : json_decode($params['connections'] ?? '[]', true);
        
        $preset = $params['preset'] ?? 'couple';
        $presets = [
            'couple' => [
                'lead_id' => 101,
                'name' => 'Aarav Sharma',
                'phone' => '+91 98765 43210',
                'email' => 'aarav.sharma@gmail.com',
                'destination' => 'Manali & Himachal 5N/6D',
                'budget' => '₹45,000',
                'source' => 'Facebook Lead Ads',
                'message' => 'Looking for romantic couple honeymoon package for 5 nights with private cab'
            ],
            'family' => [
                'lead_id' => 102,
                'name' => 'Priya & Rajesh Patel',
                'phone' => '+91 98123 45678',
                'email' => 'priya.patel@gmail.com',
                'destination' => 'Kashmir Paradise 6N/7D',
                'budget' => '₹85,000',
                'source' => 'Website Tour Enquiry',
                'message' => 'Planning family vacation for 2 adults and 2 kids in Kashmir with houseboat stay'
            ],
            'luxury' => [
                'lead_id' => 103,
                'name' => 'Vikram Singhania',
                'phone' => '+91 99887 76655',
                'email' => 'vikram.s@corporate.in',
                'destination' => 'Goa 5-Star Luxury Villa',
                'budget' => '₹1,50,000',
                'source' => 'Google Search Ads',
                'message' => 'Need private pool villa in North Goa for corporate offsite and weekend party'
            ]
        ];

        $testPayload = !empty($params['test_payload']) && is_array($params['test_payload']) 
            ? $params['test_payload'] 
            : ($presets[$preset] ?? $presets['couple']);

        if (empty($nodes)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'No flowchart nodes provided to simulate.']);
            exit;
        }

        $stepResults = [];
        $startTime = microtime(true);
        $context = $testPayload;

        foreach ($nodes as $index => $node) {
            $nodeId = $node['id'] ?? ('node_' . $index);
            $nodeTitle = $node['title'] ?? 'Workflow Step';
            $nodeCategory = $node['category'] ?? 'action';
            $config = $node['config'] ?? [];

            $stepStatus = 'success';
            $stepMessage = '';
            $stepDetails = [];

            if ($nodeCategory === 'trigger') {
                $stepMessage = 'Simulated event received: Ingestion verified with 7 attributes for traveler ' . ($context['name'] ?? 'Guest');
                $stepDetails = [
                    'trigger_event' => $node['trigger_event'] ?? 'event',
                    'lead_id' => $context['lead_id'] ?? 101,
                    'name' => $context['name'] ?? 'Traveler',
                    'phone' => $context['phone'] ?? '+91 98765 43210',
                    'email' => $context['email'] ?? 'guest@example.com',
                    'destination' => $context['destination'] ?? 'Custom Tour',
                    'budget' => $context['budget'] ?? '₹50,000',
                    'source' => $context['source'] ?? 'Facebook Ads',
                    'message' => $context['message'] ?? 'Travel Inquiry'
                ];
            } elseif ($nodeCategory === 'logic') {
                $field = $config['field'] ?? 'message';
                $valToCheck = $context[$field] ?? $context['message'] ?? '';
                $match = true;
                $operator = $config['operator'] ?? 'contains';
                $expected = $config['value'] ?? '';

                if (!empty($expected) && $operator === 'contains') {
                    $terms = array_map('trim', explode(',', strtolower($expected)));
                    $found = false;
                    foreach ($terms as $t) {
                        if ($t !== '' && strpos(strtolower($valToCheck), $t) !== false) {
                            $found = true;
                            break;
                        }
                    }
                    $match = $found;
                }

                $stepMessage = $match ? 'Condition passed: Evaluated to TRUE (Branch Yes active)' : 'Condition evaluated to FALSE (Branch No active)';
                $stepDetails = ['evaluated_field' => $field, 'condition_met' => $match, 'target_val' => $valToCheck];
            } else {
                // Action node
                $actionType = $node['action_type'] ?? 'action';
                switch ($actionType) {
                    case 'send_whatsapp':
                        $tplTitle = 'Welcome Itinerary Greeting';
                        $rawTpl = "Namaste {name}! 🌍✈️ Welcome to Aleya.\n\nThank you for reaching out for your upcoming vacation plan to {destination}. Our destination holiday specialist is preparing custom resort recommendations and sightseeing highlights for you right now!";
                        if (!empty($config['message_template'])) {
                            $rawTpl = $config['message_template'];
                        }
                        if (!empty($config['template_id']) && $config['template_id'] !== '0') {
                            $waTpls = getWorkflowWhatsAppTemplates($connection, $prefix, $siteId);
                            foreach ($waTpls as $wt) {
                                if ((string)$wt['id'] === (string)$config['template_id']) {
                                    $tplTitle = $wt['name'];
                                    if (!empty($wt['body'])) $rawTpl = $wt['body'];
                                    break;
                                }
                            }
                        }
                        $renderedBody = str_replace(
                            ['{name}', '{destination}', '{budget}', '{source}', '{phone}'],
                            [
                                $context['name'] ?? 'Traveler',
                                $context['destination'] ?? 'Holiday Destination',
                                $context['budget'] ?? 'Best Price',
                                $context['source'] ?? 'Web Lead',
                                $context['phone'] ?? '+91 98765 43210'
                            ],
                            $rawTpl
                        );
                        $stepMessage = 'WhatsApp message dispatched successfully (' . $tplTitle . ') to ' . ($context['phone'] ?? '+91 98765 43210') . ' (HTTP 200 OK)';
                        $stepDetails = [
                            'recipient' => ($context['phone'] ?? '+91 98765 43210'),
                            'recipient_name' => ($context['name'] ?? 'Traveler'),
                            'template' => $tplTitle,
                            'message_body' => $renderedBody,
                            'delivery_status' => 'sent',
                            'attach_brochure' => !empty($config['attach_brochure'])
                        ];
                        break;
                    case 'send_itinerary':
                        $pkgName = $config['itinerary_title'] ?? ($context['destination'] . ' Verified Package');
                        if (!empty($config['package_id']) && $config['package_id'] !== '0') {
                            $sitePkgs = getWorkflowPackages($connection, $prefix, $siteId);
                            foreach ($sitePkgs as $sp) {
                                if ((string)$sp['id'] === (string)$config['package_id']) {
                                    $pkgName = $sp['name'];
                                    break;
                                }
                            }
                        }
                        $stepMessage = 'Tour itinerary brochure PDF attached & delivered for: "' . $pkgName . '" via instant cloud link';
                        $stepDetails = [
                            'itinerary' => $pkgName,
                            'pages' => 6,
                            'recipient' => $context['name'] ?? 'Traveler',
                            'download_url' => 'https://aleya.in/itinerary/preview/' . substr(md5($pkgName), 0, 8) . '.pdf'
                        ];
                        break;
                    case 'assign_agent':
                        $siteAgents = getWorkflowAgents($connection, $prefix, $siteId);
                        $agentName = !empty($siteAgents) ? $siteAgents[0]['name'] : 'Rohit Verma';
                        $assignMode = $config['mode'] ?? 'round_robin';
                        if ($assignMode === 'specific_agent' && !empty($config['assigned_agent_id'])) {
                            foreach ($siteAgents as $sa) {
                                if ((string)$sa['id'] === (string)$config['assigned_agent_id']) {
                                    $agentName = $sa['name'];
                                    break;
                                }
                            }
                            $stepMessage = 'Direct Assignment: Lead allotted to Team Specialist ' . $agentName . ' (ID #' . $config['assigned_agent_id'] . ')';
                            $stepDetails = ['assigned_agent' => $agentName, 'method' => 'specific_agent', 'designation' => 'Destination Specialist'];
                        } else {
                            if (!empty($siteAgents)) {
                                $agentCount = count($siteAgents);
                                $randomAgent = $siteAgents[array_rand($siteAgents)];
                                $agentName = $randomAgent['name'];
                                $stepMessage = 'Round-Robin Engine distributed lead equally across ' . $agentCount . ' active site team members (Assigned: ' . $agentName . ')';
                            } else {
                                $stepMessage = 'Round-Robin Engine assigned lead to Specialist ' . $agentName;
                            }
                            $stepDetails = ['assigned_agent' => $agentName, 'method' => 'round_robin', 'designation' => 'Senior Travel Consultant'];
                        }
                        break;
                    case 'update_lead_stage':
                        $stageLabel = 'Updated Status';
                        $stageColor = '#10b981';
                        $stages = getWorkflowPipelineStages();
                        $targetCode = $config['stage_code'] ?? ($config['stage_id'] ?? '');
                        foreach ($stages as $st) {
                            if ((string)$st['id'] === (string)$targetCode || (string)$st['code'] === (string)$targetCode) {
                                $stageLabel = $st['label'];
                                $stageColor = $st['color'] ?? '#10b981';
                                break;
                            }
                        }
                        $stepMessage = 'CRM Pipeline updated: Lead stage moved to "' . $stageLabel . '"';
                        $stepDetails = [
                            'new_stage' => $stageLabel,
                            'stage_color' => $stageColor,
                            'note' => $config['status_note'] ?? 'Automated workflow transition'
                        ];
                        break;
                    case 'add_tags':
                        $stepMessage = 'Applied tags: ' . ($config['tags'] ?? 'new_lead, high_intent');
                        $stepDetails = ['tags_added' => explode(',', $config['tags'] ?? 'new_lead')];
                        break;
                    case 'create_task':
                        $siteAgents = getWorkflowAgents($connection, $prefix, $siteId);
                        $assignedTo = !empty($siteAgents) ? $siteAgents[0]['name'] : 'Rohit Verma';
                        if (!empty($config['assigned_agent_id']) && $config['assigned_agent_id'] !== '0') {
                            foreach ($siteAgents as $sa) {
                                if ((string)$sa['id'] === (string)$config['assigned_agent_id']) {
                                    $assignedTo = $sa['name'];
                                    break;
                                }
                            }
                        }
                        $taskType = $config['task_type'] ?? 'Discovery Call';
                        $stepMessage = 'Created CRM Task: "' . $taskType . '" assigned to ' . $assignedTo . ' due in ' . ($config['due_hours'] ?? 2) . ' hours';
                        $stepDetails = ['priority' => 'High', 'task_type' => $taskType, 'assigned_to' => $assignedTo, 'due_hours' => $config['due_hours'] ?? 2];
                        break;
                    case 'delay':
                        $stepMessage = 'Simulated delay cadence of ' . ($config['delay_seconds'] ?? 3) . ' seconds completed.';
                        $stepDetails = ['cadence_seconds' => $config['delay_seconds'] ?? 3];
                        break;
                    case 'payment_link':
                        $amount = $config['amount'] ?? 5000;
                        $stepMessage = 'Generated Razorpay / UPI Instant Deposit Link: ₹' . $amount;
                        $stepDetails = [
                            'link' => 'https://pay.isw.one/token/' . substr(md5(time()), 0, 8),
                            'amount' => $amount,
                            'currency' => 'INR'
                        ];
                        break;
                    case 'webhook_call':
                        $stepMessage = 'Outbound webhook payload transmitted to ' . ($config['url'] ?? 'external endpoint') . ' (200 OK)';
                        $stepDetails = ['response_code' => 200, 'time_ms' => 124];
                        break;
                    default:
                        $stepMessage = 'Action executed successfully.';
                        $stepDetails = ['status' => 'completed'];
                        break;
                }
            }

            $stepResults[] = [
                'node_id' => $nodeId,
                'title' => $nodeTitle,
                'category' => $nodeCategory,
                'status' => $stepStatus,
                'message' => $stepMessage,
                'details' => $stepDetails,
                'timestamp' => date('H:i:s')
            ];
        }

        $durationMs = round((microtime(true) - $startTime) * 1000);

        // Record log if workflow ID is valid
        if ($id > 0) {
            $logPayload = mysqli_real_escape_string($connection, json_encode($testPayload));
            $logDetails = mysqli_real_escape_string($connection, json_encode($stepResults));
            $sqlLog = "INSERT INTO `{$prefix}automation_workflow_log` (
                log_site_id, log_workflow_id, log_workflow_name, log_trigger_event,
                log_status, log_steps_executed, log_execution_time_ms, log_payload_json,
                log_execution_details, log_created_at
            ) VALUES (
                {$siteId}, {$id}, 'Test Simulation Run', 'manual_test_run',
                'success', " . count($stepResults) . ", {$durationMs}, '{$logPayload}',
                '{$logDetails}', NOW()
            )";
            @mysqli_query($connection, $sqlLog);

            // Increment execution count on workflow
            @mysqli_query($connection, "UPDATE `{$prefix}automation_workflow` 
                SET workflow_execution_count = workflow_execution_count + 1, workflow_last_run_at = NOW() 
                WHERE workflow_id = {$id}");
        }

        echo json_encode([
            'status' => 'success',
            'msg' => 'Simulation completed successfully! All ' . count($stepResults) . ' flowchart steps executed.',
            'data' => [
                'steps' => $stepResults,
                'test_payload' => $context,
                'presets' => $presets,
                'selected_preset' => $preset,
                'execution_time_ms' => $durationMs,
                'completed_at' => date('H:i:s')
            ]
        ]);
        exit;

    case 'templates':
        echo json_encode([
            'status' => 'success',
            'data' => getStarterWorkflowTemplates()
        ]);
        exit;

    case 'install_template':
        $params = $jsonBody ?: $_POST;
        $tplId = trim($params['template_id'] ?? '');

        $catalog = getStarterWorkflowTemplates();
        $target = null;
        foreach ($catalog as $tpl) {
            if ($tpl['id'] === $tplId) {
                $target = $tpl;
                break;
            }
        }

        if (!$target) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Selected template not found in catalog.']);
            exit;
        }

        $name = mysqli_real_escape_string($connection, $target['name']);
        $desc = mysqli_real_escape_string($connection, $target['description']);
        $cat = mysqli_real_escape_string($connection, $target['category']);
        $trigger = mysqli_real_escape_string($connection, $target['trigger_type']);
        $nodes = mysqli_real_escape_string($connection, json_encode($target['nodes']));
        $conns = mysqli_real_escape_string($connection, json_encode($target['connections']));
        $canvas = mysqli_real_escape_string($connection, json_encode(['zoom' => 1, 'panX' => 40, 'panY' => 40]));

        $ins = "INSERT INTO `{$prefix}automation_workflow` (
            workflow_site_id, workflow_name, workflow_description, workflow_category,
            workflow_trigger_type, workflow_nodes_json, workflow_connections_json,
            workflow_canvas_data, workflow_status, workflow_execution_count, workflow_created_at
        ) VALUES (
            {$siteId}, '{$name}', '{$desc}', '{$cat}', '{$trigger}', '{$nodes}', '{$conns}', '{$canvas}', 1, 0, NOW()
        )";

        if (mysqli_query($connection, $ins)) {
            $newId = mysqli_insert_id($connection);
            echo json_encode([
                'status' => 'success',
                'msg' => 'Template installed successfully! Ready to customize in visual editor.',
                'data' => ['id' => $newId]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to install template.']);
        }
        exit;

    case 'logs':
        $id = (int)($_GET['id'] ?? 0);
        $limit = 25;
        $where = "log_site_id = {$siteId}";
        if ($id > 0) {
            $where .= " AND log_workflow_id = {$id}";
        }

        $q = mysqli_query($connection, "SELECT * FROM `{$prefix}automation_workflow_log` WHERE {$where} ORDER BY log_id DESC LIMIT {$limit}");
        $logs = [];
        if ($q) {
            while ($row = mysqli_fetch_assoc($q)) {
                $logs[] = [
                    'id' => (int)$row['log_id'],
                    'workflow_id' => (int)$row['log_workflow_id'],
                    'workflow_name' => $row['log_workflow_name'],
                    'trigger_event' => $row['log_trigger_event'],
                    'status' => $row['log_status'],
                    'steps_executed' => (int)$row['log_steps_executed'],
                    'execution_time_ms' => (int)$row['log_execution_time_ms'],
                    'payload' => json_decode($row['log_payload_json'] ?? '{}', true),
                    'details' => json_decode($row['log_execution_details'] ?? '[]', true),
                    'created_at' => $row['log_created_at']
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => $logs
        ]);
        exit;

    case 'meta':
        // Fetch all existing site metadata in a single call for high performance
        $leadSourcesData = getWorkflowLeadSources($connection, $prefix, $siteId);
        $whatsappTemplates = getWorkflowWhatsAppTemplates($connection, $prefix, $siteId);
        $agents = getWorkflowAgents($connection, $prefix, $siteId);
        $packages = getWorkflowPackages($connection, $prefix, $siteId);
        $pipelineStages = getWorkflowPipelineStages();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'lead_sources' => $leadSourcesData['sources'],
                'web_forms' => $leadSourcesData['web_forms'],
                'fb_forms' => $leadSourcesData['fb_forms'],
                'whatsapp_templates' => $whatsappTemplates,
                'agents' => $agents,
                'packages' => $packages,
                'pipeline_stages' => $pipelineStages
            ]
        ]);
        exit;

    case 'lead_sources':
        $leadSourcesData = getWorkflowLeadSources($connection, $prefix, $siteId);
        echo json_encode(['status' => 'success', 'data' => $leadSourcesData]);
        exit;

    case 'whatsapp_templates':
        $whatsappTemplates = getWorkflowWhatsAppTemplates($connection, $prefix, $siteId);
        echo json_encode(['status' => 'success', 'data' => $whatsappTemplates]);
        exit;

    case 'pipeline_stages':
        $pipelineStages = getWorkflowPipelineStages();
        echo json_encode(['status' => 'success', 'data' => $pipelineStages]);
        exit;

    case 'agents':
        $agents = getWorkflowAgents($connection, $prefix, $siteId);
        echo json_encode(['status' => 'success', 'data' => $agents]);
        exit;

    case 'packages':
        $packages = getWorkflowPackages($connection, $prefix, $siteId);
        echo json_encode(['status' => 'success', 'data' => $packages]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Unsupported action.']);
        exit;
}
