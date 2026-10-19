<?php

/**
 * V3 WhatsApp Templates & Action Engine REST API
 * Path: /api/v3/whatsapp/templates.php
 * Handles template creation, action defaults, dynamic placeholder merge, and dispatch.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        @require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        @require_once $docRoot . '/lib/modules/connection.php';
    }
    if (!isset($connection) && file_exists($docRoot . '/dbConfig.php')) {
        require_once $docRoot . '/dbConfig.php';
        $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}

$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . (defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_') . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
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

$currentUserId = defined('USER_ID') ? (int)USER_ID : 0;
$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

// Require device helper for sending
require_once __DIR__ . '/db_init.php';
initWhatsAppDatabase($connection);
if (file_exists(__DIR__ . '/device_helper.php')) {
    require_once __DIR__ . '/device_helper.php';
}

/**
 * Metadata definition of standard CRM Action Triggers
 */
function getWhatsAppActionTriggers()
{
    return [
        'lead_welcome' => [
            'key' => 'lead_welcome',
            'name' => 'New Lead Welcome',
            'badge' => 'Lead Welcome',
            'icon' => 'fi fi-rr-bullseye-arrow',
            'color' => '#2563eb',
            'bg' => '#eff6ff',
            'desc' => 'Triggered on new incoming lead ingestion (Web Form, Facebook, Manual inquiry).',
            'variables' => ['{customer_name}', '{company_name}', '{destination}', '{agent_name}']
        ],
        'itinerary_share' => [
            'key' => 'itinerary_share',
            'name' => 'Itinerary Proposal',
            'badge' => 'Itinerary Proposal',
            'icon' => 'fi fi-rr-document-signed',
            'color' => '#059669',
            'bg' => '#ecfdf5',
            'desc' => 'Sent when sharing day-wise tour itinerary and quotation proposal with customer.',
            'variables' => ['{customer_name}', '{destination}', '{itinerary_link}', '{company_name}', '{agent_name}']
        ],
        'followup_reminder' => [
            'key' => 'followup_reminder',
            'name' => 'Follow-up Reminder',
            'badge' => 'Follow-up Reminder',
            'icon' => 'fi fi-rr-bell-ring',
            'color' => '#d97706',
            'bg' => '#fffbeb',
            'desc' => 'Scheduled or 1-click check-in sent to leads pending review or decision.',
            'variables' => ['{customer_name}', '{destination}', '{agent_name}', '{company_name}']
        ],
        'booking_confirmed' => [
            'key' => 'booking_confirmed',
            'name' => 'Booking Confirmed',
            'badge' => 'Booking Confirmed',
            'icon' => 'fi fi-rr-check-circle',
            'color' => '#7c3aed',
            'bg' => '#f5f3ff',
            'desc' => 'Celebratory receipt and welcome message when lead is marked Confirmed Sale.',
            'variables' => ['{customer_name}', '{destination}', '{lead_id}', '{company_name}']
        ],
        'payment_request' => [
            'key' => 'payment_request',
            'name' => 'Payment Link Request',
            'badge' => 'Payment Link',
            'icon' => 'fi fi-rr-credit-card',
            'color' => '#4f46e5',
            'bg' => '#eef2ff',
            'desc' => 'Dispatched when sharing online deposit or balance payment link with traveler.',
            'variables' => ['{customer_name}', '{destination}', '{payment_link}', '{lead_id}', '{company_name}']
        ],
        'trip_voucher' => [
            'key' => 'trip_voucher',
            'name' => 'Tour Voucher Issued',
            'badge' => 'Tour Voucher',
            'icon' => 'fi fi-rr-plane-departure',
            'color' => '#0d9488',
            'bg' => '#f0fdfa',
            'desc' => 'Dispatched with confirmed hotel, cab, and excursion vouchers before departure.',
            'variables' => ['{customer_name}', '{destination}', '{agent_name}', '{agent_mobile}', '{company_name}']
        ],
        'feedback_review' => [
            'key' => 'feedback_review',
            'name' => 'Post-Trip Review',
            'badge' => 'Post-Trip Review',
            'icon' => 'fi fi-rr-star',
            'color' => '#e11d48',
            'bg' => '#fff1f2',
            'desc' => 'Sent after trip completion to collect review, rating, and feedback.',
            'variables' => ['{customer_name}', '{destination}', '{review_link}', '{company_name}']
        ],
        'manual_quick_reply' => [
            'key' => 'manual_quick_reply',
            'name' => 'Live Chat Concierge',
            'badge' => 'Quick Reply',
            'icon' => 'fi fi-rr-comment-alt-middle',
            'color' => '#0891b2',
            'bg' => '#ecfeff',
            'desc' => '1-Click quick response available directly inside WhatsApp live chat composer.',
            'variables' => ['{customer_name}', '{agent_name}', '{company_name}']
        ]
    ];
}

/**
 * Initialize Schema self-healingly
 */
function initWhatsAppTemplatesTable($connection)
{
    if (!$connection) return;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    $tableSql = "CREATE TABLE IF NOT EXISTS `{$prefix}whatsapp_template` (
      `template_id` INT AUTO_INCREMENT PRIMARY KEY,
      `template_site_id` INT NOT NULL DEFAULT 0,
      `template_name` VARCHAR(255) NOT NULL,
      `template_code` VARCHAR(100) DEFAULT NULL,
      `template_category` VARCHAR(50) NOT NULL DEFAULT 'lead_welcome',
      `template_action_trigger` VARCHAR(50) NOT NULL DEFAULT 'lead_welcome',
      `template_content` TEXT NOT NULL,
      `template_header_media` VARCHAR(500) DEFAULT NULL,
      `template_is_system_default` TINYINT(1) NOT NULL DEFAULT 0,
      `template_is_action_active` TINYINT(1) NOT NULL DEFAULT 0,
      `template_status` TINYINT(1) NOT NULL DEFAULT 1,
      `template_usage_count` INT NOT NULL DEFAULT 0,
      `template_created_by` INT NOT NULL DEFAULT 0,
      `template_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `template_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX (`template_site_id`),
      INDEX (`template_action_trigger`),
      INDEX (`template_is_action_active`),
      INDEX (`template_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    @mysqli_query($connection, $tableSql);

    // Ensure columns exist if table was created previously with older schema
    try {
        $cRes = @mysqli_query($connection, "SHOW COLUMNS FROM `{$prefix}whatsapp_template` LIKE 'template_is_action_active'");
        if ($cRes && mysqli_num_rows($cRes) === 0) {
            @mysqli_query($connection, "ALTER TABLE `{$prefix}whatsapp_template` ADD COLUMN `template_is_action_active` TINYINT(1) NOT NULL DEFAULT 0 AFTER `template_is_system_default`");
            @mysqli_query($connection, "ALTER TABLE `{$prefix}whatsapp_template` ADD INDEX (`template_is_action_active`)");
        }
    } catch (Throwable $e) {
    }
}

/**
 * Seed Curated Default Action Templates for Site
 */
function getDefaultTemplatesList()
{
    return [
        [
            'name' => 'Warm Welcome & Trip Inquiry Acknowledgment',
            'code' => 'lead_welcome_default',
            'category' => 'lead_welcome',
            'action' => 'lead_welcome',
            'content' => "Namaste {customer_name}! 🙏 Welcome to *{company_name}*.\n\nThank you for choosing us for your upcoming *{destination}* getaway! 🌴✨\n\nI am *{agent_name}*, your dedicated Holiday Specialist. We are carefully curating a tailor-made travel proposal designed around your preferences, style, and dates.\n\n📋 *What we're assembling for you:*\n• Handpicked boutique & luxury accommodations\n• Private sanitized vehicle & verified driver-guide\n• Scenic highlights, hidden gems & authentic local meals\n• 24/7 dedicated on-tour concierge support\n\n💡 *Help us personalize:*\nIf you have preferred travel dates (*{dates}*), specific hotel tier preferences, or a targeted budget in mind, simply reply directly to this message! ✈️\n\nWarm regards,\n*{agent_name}*\nHoliday Specialist | *{company_name}*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Personalized Tour Itinerary & Quotation',
            'code' => 'itinerary_share_default',
            'category' => 'itinerary_share',
            'action' => 'itinerary_share',
            'content' => "Hello {customer_name}! 🎒✨\n\nExciting news! Your personalized vacation proposal and day-by-day itinerary for *{destination}* is ready.\n\n🗺️ *Interactive Itinerary & Pricing:*\n👉 {itinerary_link}\n\n✨ *Proposal Highlights:*\n• Complete day-wise sightseeing plan\n• Premium accommodations with confirmed meal plans\n• Private cab allocations for hassle-free transfers\n• Transparent all-inclusive quote with zero hidden charges\n\nTake your time to explore the proposal! If you'd like to adjust hotel categories, add activities, or tweak dates, just reply here and I will revise it immediately.\n\nBest regards,\n*{agent_name}*\nTravel Consultant | *{company_name}*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Friendly Trip Follow-up & Discussion',
            'code' => 'followup_reminder_default',
            'category' => 'followup_reminder',
            'action' => 'followup_reminder',
            'content' => "Hi {customer_name}, hope you are having a wonderful day! ☀️\n\nFollowing up regarding your upcoming holiday proposal to *{destination}*. 🏔️✨\n\nYou can view your latest itinerary anytime here:\n👉 {itinerary_link}\n\nPeak travel dates and preferred resort room categories fill up quickly. I would love to ensure you get the best rates and availability!\n\nWould you like to:\n1️⃣ Tweak hotel categories or adjust the budget?\n2️⃣ Add or swap specific sightseeing destinations?\n3️⃣ Proceed with reserving your preferred dates?\n\nFeel free to reply right here or call me directly at *{agent_mobile}*. Looking forward to helping you plan an incredible holiday! 🌟\n\nWarmly,\n*{agent_name}* | *{company_name}*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Booking Confirmed & Celebration Welcome',
            'code' => 'booking_confirmed_default',
            'category' => 'booking_confirmed',
            'action' => 'booking_confirmed',
            'content' => "🎉 *BOOKING CONFIRMED!* 🎉\n\nDear {customer_name}, pack your bags — your holiday to *{destination}* is officially locked in! 🏖️✈️\n\n📌 *Reservation Details:*\n• *Booking Ref:* #{lead_id}\n• *Destination:* {destination}\n• *Travel Dates:* {dates}\n• *Status:* Confirmed & Active ✅\n\nOur operations team is finalizing your hotel confirmation vouchers, private vehicle assignment, and special tour inclusions. Your complete Travel Voucher Pack will be delivered to you shortly.\n\nThank you for trusting *{company_name}* with your holiday memories. We are committed to making this journey extraordinary! 🌟\n\nNeed any pre-trip assistance?\n📞 Concierge Helpline: *{agent_mobile}*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Secure Online Payment Link',
            'code' => 'payment_request_default',
            'category' => 'payment_request',
            'action' => 'payment_request',
            'content' => "Dear {customer_name},\n\nA secure payment link has been generated for your *{destination}* holiday reservation (*Ref #{lead_id}*).\n\n💳 *Instant Secure Payment Link:*\n👉 {payment_link}\n\n🔒 *Flexible & Safe Payment Options:*\n• UPI (Google Pay, PhonePe, Paytm, BHIM)\n• Credit & Debit Cards (Visa, Mastercard, RuPay, Amex)\n• Net Banking across all major banks & EMI options\n\nOnce payment is processed, your official payment receipt and booking confirmation will be generated automatically.\n\nPlease reach out if you have any questions or require an invoice breakdown.\n\nWarm regards,\n*{company_name} Finance & Reservations Desk*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Confirmed Tour Vouchers & Travel Documents',
            'code' => 'trip_voucher_default',
            'category' => 'trip_voucher',
            'action' => 'trip_voucher',
            'content' => "Namaste {customer_name}! 📄✈️\n\nAll confirmed travel documents, hotel vouchers, and transport passes for your *{destination}* tour are ready! 🏔️✨\n\n🚗 *On-Ground Transportation & Coordination:*\n• Dedicated Coordinator: *{agent_name}*\n• Concierge Helpline: *{agent_mobile}*\n• Vehicle: Sanitized Private Cab for all scheduled transfers\n\n📋 *Important Pre-Departure Checklist:*\n✅ Save digital copies of vouchers on your smartphone\n✅ Carry valid original Govt Photo IDs (Aadhaar / Passport)\n✅ Review destination weather & pack appropriately\n\nOur 24/7 on-tour guest support is active throughout your journey to ensure a flawless experience. Have a safe, breathtaking trip! 🌟\n\nHappy Travels,\n*{company_name} Operations Team*",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Welcome Back & Trip Feedback Request',
            'code' => 'feedback_review_default',
            'category' => 'feedback_review',
            'action' => 'feedback_review',
            'content' => "Welcome back home, {customer_name}! 🏡✨\n\nWe hope you had a rejuvenating vacation in *{destination}* and brought home memories that will last a lifetime! 🌟📸\n\nYour feedback means everything to us and helps our team continually elevate every traveler's experience. Could you please take 30 seconds to share your review?\n\n⭐⭐⭐⭐⭐ *Share Your Google Review:*\n👉 {review_link}\n\nAs a token of our heartfelt appreciation, our privilege club has unlocked an exclusive discount for you on your next holiday with us!\n\nThank you for traveling with *{company_name}*! 🌍❤️",
            'is_default' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Instant Live Chat Greeting & Concierge',
            'code' => 'manual_quick_reply_default',
            'category' => 'manual_quick_reply',
            'action' => 'manual_quick_reply',
            'content' => "Hello {customer_name}! 👋 Welcome to *{company_name}*.\n\nI am *{agent_name}*, your dedicated Travel Specialist. I would be thrilled to help you design a customized itinerary, share hotel options, or check rates for *{destination}*.\n\nHow can I assist you today? Feel free to share your travel dates, group size, or preferred holiday style! ✈️",
            'is_default' => 1,
            'is_active' => 1
        ]
    ];
}

/**
 * Seed or update Curated Default Action Templates for Site
 */
function seedDefaultWhatsAppTemplates($connection, $siteId, $forceUpdate = false)
{
    if (!$connection || $siteId <= 0) return;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // Remove duplicates if any were previously inserted
    @mysqli_query($connection, "DELETE t1 FROM `{$prefix}whatsapp_template` t1 
        INNER JOIN `{$prefix}whatsapp_template` t2 
        WHERE t1.template_id > t2.template_id 
          AND t1.template_code = t2.template_code 
          AND t1.template_site_id = t2.template_site_id 
          AND t1.template_is_system_default = 1");

    $defaults = getDefaultTemplatesList();

    foreach ($defaults as $d) {
        $escCode = mysqli_real_escape_string($connection, $d['code']);
        $escName = mysqli_real_escape_string($connection, $d['name']);
        $escCat = mysqli_real_escape_string($connection, $d['category']);
        $escAction = mysqli_real_escape_string($connection, $d['action']);
        $escContent = mysqli_real_escape_string($connection, $d['content']);

        $chk = mysqli_query($connection, "SELECT template_id FROM `{$prefix}whatsapp_template` WHERE template_site_id = {$siteId} AND template_code = '{$escCode}' LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $row = mysqli_fetch_assoc($chk);
            $tplId = (int)$row['template_id'];
            if ($forceUpdate) {
                mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` SET 
                    template_name = '{$escName}',
                    template_content = '{$escContent}',
                    template_updated_at = NOW()
                    WHERE template_id = {$tplId} AND template_is_system_default = 1");
            }
        } else {
            $insSql = "INSERT INTO `{$prefix}whatsapp_template` (
                template_site_id, template_name, template_code, template_category,
                template_action_trigger, template_content, template_is_system_default,
                template_is_action_active, template_status, template_created_at
            ) VALUES (
                {$siteId}, '{$escName}', '{$escCode}', '{$escCat}',
                '{$escAction}', '{$escContent}', {$d['is_default']},
                {$d['is_active']}, 1, NOW()
            )";
            @mysqli_query($connection, $insSql);
        }
    }
}

/**
 * Replace placeholders with contextual data
 */
function substituteWhatsAppTemplateVariables($connection, $siteId, $content, $leadId = 0, $overrideVars = [])
{
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Fetch site name
    $siteName = 'Our Travel Team';
    $siteDomain = $_SERVER['HTTP_HOST'] ?? 'aleya.in';
    $sRes = mysqli_query($connection, "SELECT site_name, site_domain FROM `{$prefix}site` WHERE site_id = {$siteId} LIMIT 1");
    if ($sRes && $sRow = mysqli_fetch_assoc($sRes)) {
        if (!empty($sRow['site_name'])) $siteName = trim($sRow['site_name']);
        if (!empty($sRow['site_domain'])) $siteDomain = trim($sRow['site_domain']);
    }

    $lead = null;
    if ($leadId > 0) {
        $lRes = mysqli_query($connection, "SELECT e.*, 
            TRIM(CONCAT(COALESCE(e.enquiry_first_name, ''), ' ', COALESCE(e.enquiry_last_name, ''))) as full_name,
            u.user_username, u.user_mobile as agent_phone,
            p.profile_display_name, p.profile_first_name, p.profile_last_name
            FROM `{$prefix}enquiry` e
            LEFT JOIN `{$prefix}user` u ON u.user_id = e.enquiry_agent
            LEFT JOIN `{$prefix}profile` p ON p.profile_user_id = e.enquiry_agent
            WHERE e.enquiry_id = {$leadId} AND e.enquiry_site_id = {$siteId} LIMIT 1");
        if ($lRes && mysqli_num_rows($lRes) > 0) {
            $lead = mysqli_fetch_assoc($lRes);
        }
    }

    // Default values
    $custName = !empty($lead['full_name']) ? trim($lead['full_name']) : (!empty($overrideVars['customer_name']) ? $overrideVars['customer_name'] : 'Traveler');
    $dest = !empty($lead['enquiry_subject']) ? trim($lead['enquiry_subject']) : (!empty($overrideVars['destination']) ? $overrideVars['destination'] : 'Your Dream Destination');
    $agentName = !empty($lead['profile_display_name']) ? trim($lead['profile_display_name']) : (!empty($lead['user_username']) ? $lead['user_username'] : 'Holiday Specialist');
    $agentMobile = !empty($lead['agent_phone']) ? trim($lead['agent_phone']) : (!empty($overrideVars['agent_mobile']) ? $overrideVars['agent_mobile'] : '+91 9876543210');
    $leadRef = $leadId > 0 ? (string)$leadId : (!empty($overrideVars['lead_id']) ? (string)$overrideVars['lead_id'] : '1084');

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $baseUrl = $protocol . $siteDomain;

    $itineraryLink = !empty($overrideVars['itinerary_link']) ? $overrideVars['itinerary_link'] : "{$baseUrl}/itinerary/view/" . ($leadId > 0 ? base64_encode((string)$leadId) : 'DEMO1084');
    $paymentLink = !empty($overrideVars['payment_link']) ? $overrideVars['payment_link'] : "{$baseUrl}/pay/" . ($leadId > 0 ? base64_encode((string)$leadId) : 'DEMO1084');
    $reviewLink = !empty($overrideVars['review_link']) ? $overrideVars['review_link'] : "{$baseUrl}/review/";
    $dates = !empty($overrideVars['dates']) ? $overrideVars['dates'] : 'Upcoming Season';

    $replacements = [
        '{customer_name}' => $custName,
        '{name}'          => $custName,
        '{company_name}'  => $siteName,
        '{company}'       => $siteName,
        '{destination}'   => $dest,
        '{agent_name}'    => $agentName,
        '{agent_mobile}'  => $agentMobile,
        '{lead_id}'       => $leadRef,
        '{itinerary_link}' => $itineraryLink,
        '{payment_link}'  => $paymentLink,
        '{review_link}'   => $reviewLink,
        '{dates}'         => $dates
    ];

    if (!empty($overrideVars) && is_array($overrideVars)) {
        foreach ($overrideVars as $k => $v) {
            $tag = str_starts_with($k, '{') ? $k : ('{' . $k . '}');
            $replacements[$tag] = (string)$v;
        }
    }

    $parsed = str_replace(array_keys($replacements), array_values($replacements), $content);
    return [
        'parsed_text' => $parsed,
        'variables_used' => $replacements
    ];
}

initWhatsAppTemplatesTable($connection);
seedDefaultWhatsAppTemplates($connection, $siteId);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
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
$actionTriggersMeta = getWhatsAppActionTriggers();

// --------------------------------------------------------------------------
// 1. GET: List All Templates with Active Action Mapping
// --------------------------------------------------------------------------
if ($method === 'GET' && $action === 'list') {
    $filterAction = trim($input['action_trigger'] ?? ($input['trigger'] ?? ''));
    $filterSearch = trim($input['search'] ?? '');
    $filterDefaultOnly = isset($input['active_only']) && ($input['active_only'] === '1' || $input['active_only'] === 'true');

    $where = ["template_site_id = {$siteId}"];
    if (!empty($filterAction) && $filterAction !== 'all') {
        $escAct = mysqli_real_escape_string($connection, $filterAction);
        $where[] = "template_action_trigger = '{$escAct}'";
    }
    if (!empty($filterSearch)) {
        $escS = mysqli_real_escape_string($connection, $filterSearch);
        $where[] = "(template_name LIKE '%{$escS}%' OR template_content LIKE '%{$escS}%' OR template_category LIKE '%{$escS}%')";
    }
    if ($filterDefaultOnly) {
        $where[] = "template_is_action_active = 1";
    }

    $whereSql = implode(' AND ', $where);
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` WHERE {$whereSql} ORDER BY template_is_action_active DESC, template_is_system_default DESC, template_id ASC");

    $templates = [];
    $activeActionMap = [];
    $totalUsage = 0;
    $customCount = 0;

    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $actKey = $r['template_action_trigger'];
            $r['action_meta'] = $actionTriggersMeta[$actKey] ?? [
                'key' => $actKey,
                'name' => ucwords(str_replace('_', ' ', $actKey)),
                'badge' => ucwords(str_replace('_', ' ', $actKey)),
                'icon' => 'fi fi-rr-bolt',
                'color' => '#6366f1',
                'bg' => '#e0e7ff',
                'desc' => 'Custom CRM action trigger',
                'variables' => ['{customer_name}', '{company_name}']
            ];

            if ((int)$r['template_is_action_active'] === 1) {
                $activeActionMap[$actKey] = (int)$r['template_id'];
            }
            if ((int)$r['template_is_system_default'] === 0) {
                $customCount++;
            }
            $totalUsage += (int)$r['template_usage_count'];

            $templates[] = $r;
        }
    }

    // Total template count on site
    $totQ = mysqli_query($connection, "SELECT COUNT(*) as total FROM `{$prefix}whatsapp_template` WHERE template_site_id = {$siteId}");
    $totalCount = ($totQ && $tr = mysqli_fetch_assoc($totQ)) ? (int)$tr['total'] : count($templates);

    echo json_encode([
        'status' => 'success',
        'templates' => $templates,
        'count' => count($templates),
        'stats' => [
            'total_templates' => $totalCount,
            'active_actions_count' => count($activeActionMap),
            'custom_templates_count' => $customCount,
            'total_usage_count' => $totalUsage
        ],
        'action_triggers' => array_values($actionTriggersMeta),
        'active_action_map' => $activeActionMap
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. GET: Single Template by ID
// --------------------------------------------------------------------------
if ($method === 'GET' && ($action === 'get' || !empty($input['id']))) {
    $tId = (int)($input['id'] ?? 0);
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` WHERE template_id = {$tId} AND template_site_id = {$siteId} LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $t = mysqli_fetch_assoc($q);
        $actKey = $t['template_action_trigger'];
        $t['action_meta'] = $actionTriggersMeta[$actKey] ?? null;
        echo json_encode(['status' => 'success', 'template' => $t]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'WhatsApp template not found']);
    }
    exit;
}

// --------------------------------------------------------------------------
// 3. GET/POST: Preview Template with Real/Sample Variables
// --------------------------------------------------------------------------
if ($action === 'preview') {
    $content = trim($input['content'] ?? '');
    $tId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    $leadId = (int)($input['lead_id'] ?? 0);

    if ($tId > 0 && empty($content)) {
        $q = mysqli_query($connection, "SELECT template_content FROM `{$prefix}whatsapp_template` WHERE template_id = {$tId} AND template_site_id = {$siteId} LIMIT 1");
        if ($q && $r = mysqli_fetch_assoc($q)) {
            $content = $r['template_content'];
        }
    }

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'msg' => 'Template content is empty']);
        exit;
    }

    $sub = substituteWhatsAppTemplateVariables($connection, $siteId, $content, $leadId, $input['vars'] ?? []);
    echo json_encode([
        'status' => 'success',
        'raw_content' => $content,
        'preview_text' => $sub['parsed_text'],
        'variables_used' => $sub['variables_used']
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 4. GET: Search CRM Leads for Test/Send Dropdown
// --------------------------------------------------------------------------
if ($action === 'leads_search') {
    $qStr = trim($input['q'] ?? '');
    $escQ = mysqli_real_escape_string($connection, $qStr);

    $leadFilter = "enquiry_site_id = {$siteId}";
    if (!empty($qStr)) {
        $leadFilter .= " AND (enquiry_first_name LIKE '%{$escQ}%' OR enquiry_last_name LIKE '%{$escQ}%' OR enquiry_mobile LIKE '%{$escQ}%' OR enquiry_subject LIKE '%{$escQ}%' OR enquiry_id = '{$escQ}')";
    }

    $lQ = mysqli_query($connection, "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_subject, enquiry_status 
        FROM `{$prefix}enquiry` 
        WHERE {$leadFilter} 
        ORDER BY enquiry_id DESC LIMIT 20");

    $leads = [];
    if ($lQ) {
        while ($row = mysqli_fetch_assoc($lQ)) {
            $name = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
            if (empty($name)) $name = 'Lead #' . $row['enquiry_id'];
            $leads[] = [
                'id' => (int)$row['enquiry_id'],
                'name' => $name,
                'phone' => trim($row['enquiry_mobile'] ?? ''),
                'destination' => trim($row['enquiry_subject'] ?? 'Trip')
            ];
        }
    }
    echo json_encode(['status' => 'success', 'leads' => $leads]);
    exit;
}

// --------------------------------------------------------------------------
// 5. POST: Save / Create / Duplicate Template
// --------------------------------------------------------------------------
if ($method === 'POST' && $action === 'save') {
    $templateId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    $name = trim($input['template_name'] ?? ($input['name'] ?? ''));
    $actionTrigger = trim($input['template_action_trigger'] ?? ($input['action_trigger'] ?? 'lead_welcome'));
    $category = trim($input['template_category'] ?? ($input['category'] ?? $actionTrigger));
    $content = trim($input['template_content'] ?? ($input['content'] ?? ''));
    $mediaUrl = trim($input['template_header_media'] ?? ($input['media_url'] ?? ''));
    $isActionActive = isset($input['template_is_action_active']) ? (int)$input['template_is_action_active'] : (isset($input['is_action_active']) ? (int)$input['is_action_active'] : 0);
    $status = isset($input['template_status']) ? (int)$input['template_status'] : 1;

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Template name is required']);
        exit;
    }
    if (empty($content)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Template message content cannot be blank']);
        exit;
    }

    if (!array_key_exists($actionTrigger, $actionTriggersMeta)) {
        $actionTrigger = 'manual_quick_reply';
    }

    $escName = mysqli_real_escape_string($connection, $name);
    $escCat = mysqli_real_escape_string($connection, $category);
    $escAction = mysqli_real_escape_string($connection, $actionTrigger);
    $escContent = mysqli_real_escape_string($connection, $content);
    $escMedia = mysqli_real_escape_string($connection, $mediaUrl);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($name)));
    $escCode = mysqli_real_escape_string($connection, substr($slug, 0, 80));

    // If marked as active default for this action trigger, reset previous active defaults
    if ($isActionActive === 1) {
        mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` 
            SET template_is_action_active = 0 
            WHERE template_site_id = {$siteId} AND template_action_trigger = '{$escAction}'");
    }

    if ($templateId > 0) {
        // Update existing template
        $upSql = "UPDATE `{$prefix}whatsapp_template` SET 
            template_name = '{$escName}',
            template_category = '{$escCat}',
            template_action_trigger = '{$escAction}',
            template_content = '{$escContent}',
            template_header_media = '{$escMedia}',
            template_is_action_active = {$isActionActive},
            template_status = {$status},
            template_updated_at = NOW()
            WHERE template_id = {$templateId} AND template_site_id = {$siteId}";
        mysqli_query($connection, $upSql);
        $savedId = $templateId;
    } else {
        // Insert new custom template
        $insSql = "INSERT INTO `{$prefix}whatsapp_template` (
            template_site_id, template_name, template_code, template_category,
            template_action_trigger, template_content, template_header_media,
            template_is_system_default, template_is_action_active, template_status,
            template_created_by, template_created_at
        ) VALUES (
            {$siteId}, '{$escName}', '{$escCode}', '{$escCat}',
            '{$escAction}', '{$escContent}', '{$escMedia}',
            0, {$isActionActive}, {$status},
            {$currentUserId}, NOW()
        )";
        mysqli_query($connection, $insSql);
        $savedId = (int)mysqli_insert_id($connection);
    }

    // Fetch saved record
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` WHERE template_id = {$savedId} LIMIT 1");
    $savedRecord = $q ? mysqli_fetch_assoc($q) : null;

    echo json_encode([
        'status' => 'success',
        'msg' => $templateId > 0 ? 'Template updated successfully!' : 'Template created successfully!',
        'template' => $savedRecord
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 6. POST: Set as Action Default ("change default action template")
// --------------------------------------------------------------------------
if ($method === 'POST' && ($action === 'set_default' || $action === 'set_active_action')) {
    $templateId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    if ($templateId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid template ID required']);
        exit;
    }

    $q = mysqli_query($connection, "SELECT template_id, template_name, template_action_trigger FROM `{$prefix}whatsapp_template` WHERE template_id = {$templateId} AND template_site_id = {$siteId} LIMIT 1");
    if (!$q || mysqli_num_rows($q) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Template not found']);
        exit;
    }

    $tRow = mysqli_fetch_assoc($q);
    $actTrigger = $tRow['template_action_trigger'];
    $escAct = mysqli_real_escape_string($connection, $actTrigger);

    // Reset others for this action trigger
    mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` 
        SET template_is_action_active = 0 
        WHERE template_site_id = {$siteId} AND template_action_trigger = '{$escAct}'");

    // Activate this template
    mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` 
        SET template_is_action_active = 1, template_status = 1 
        WHERE template_id = {$templateId} AND template_site_id = {$siteId}");

    $actMeta = $actionTriggersMeta[$actTrigger] ?? null;
    $actionName = $actMeta ? $actMeta['name'] : ucwords(str_replace('_', ' ', $actTrigger));

    echo json_encode([
        'status' => 'success',
        'msg' => "'{$tRow['template_name']}' is now the Active Default template for {$actionName}!",
        'template_id' => $templateId,
        'action_trigger' => $actTrigger
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 7. POST: Toggle Status (Active / Inactive)
// --------------------------------------------------------------------------
if ($method === 'POST' && $action === 'toggle_status') {
    $templateId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    $newStatus = isset($input['status']) ? ((int)$input['status'] ? 1 : 0) : 1;

    mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` 
        SET template_status = {$newStatus} 
        WHERE template_id = {$templateId} AND template_site_id = {$siteId}");

    echo json_encode(['status' => 'success', 'msg' => 'Template status updated', 'template_id' => $templateId, 'new_status' => $newStatus]);
    exit;
}

// --------------------------------------------------------------------------
// 8. POST: Delete Template
// --------------------------------------------------------------------------
if ($method === 'POST' && $action === 'delete') {
    $templateId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` WHERE template_id = {$templateId} AND template_site_id = {$siteId} LIMIT 1");
    if (!$q || mysqli_num_rows($q) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Template not found']);
        exit;
    }

    $tRow = mysqli_fetch_assoc($q);
    $wasActive = (int)$tRow['template_is_action_active'] === 1;
    $isSystemDef = (int)$tRow['template_is_system_default'] === 1;
    $actTrigger = $tRow['template_action_trigger'];
    $escAct = mysqli_real_escape_string($connection, $actTrigger);

    if ($isSystemDef) {
        // Don't physically delete system defaults, just disable or reset
        mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` SET template_status = 0 WHERE template_id = {$templateId}");
        echo json_encode(['status' => 'success', 'msg' => 'Default system template deactivated. You can re-enable it at any time.']);
        exit;
    }

    // Delete custom template
    mysqli_query($connection, "DELETE FROM `{$prefix}whatsapp_template` WHERE template_id = {$templateId} AND template_site_id = {$siteId}");

    // If it was active default, fallback to system default for this action trigger
    if ($wasActive) {
        mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` 
            SET template_is_action_active = 1, template_status = 1 
            WHERE template_site_id = {$siteId} AND template_action_trigger = '{$escAct}' AND template_is_system_default = 1 LIMIT 1");
    }

    echo json_encode(['status' => 'success', 'msg' => 'Custom template deleted successfully']);
    exit;
}

// --------------------------------------------------------------------------
// 9. POST: Send Template to Recipient / Lead
// --------------------------------------------------------------------------
if ($method === 'POST' && $action === 'send') {
    $templateId = (int)($input['template_id'] ?? ($input['id'] ?? 0));
    $phone = trim($input['phone'] ?? ($input['mobile'] ?? ''));
    $leadId = (int)($input['lead_id'] ?? 0);
    $deviceId = (int)($input['device_id'] ?? 0);

    if (empty($phone) && $leadId > 0) {
        $lQ = mysqli_query($connection, "SELECT enquiry_mobile FROM `{$prefix}enquiry` WHERE enquiry_id = {$leadId} AND enquiry_site_id = {$siteId} LIMIT 1");
        if ($lQ && $lr = mysqli_fetch_assoc($lQ)) {
            $phone = trim($lr['enquiry_mobile'] ?? '');
        }
    }

    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Recipient phone number is required']);
        exit;
    }

    // Resolve template
    $content = '';
    $templateName = 'Custom Message';
    if ($templateId > 0) {
        $tQ = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` WHERE template_id = {$templateId} AND template_site_id = {$siteId} LIMIT 1");
        if ($tQ && $tr = mysqli_fetch_assoc($tQ)) {
            $content = $tr['template_content'];
            $templateName = $tr['template_name'];
        }
    } elseif (!empty($input['content'])) {
        $content = trim($input['content']);
    }

    if (empty($content)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Template message content could not be found']);
        exit;
    }

    // Substitute variables
    $sub = substituteWhatsAppTemplateVariables($connection, $siteId, $content, $leadId, $input['vars'] ?? []);
    $finalText = $sub['parsed_text'];

    // Send via direct WhatsApp device helper
    if (function_exists('sendWhatsAppDirectMessage')) {
        $sendOpts = [
            'lead_id' => $leadId,
            'type' => 'text'
        ];
        if ($deviceId > 0) {
            $sendOpts['device_id'] = $deviceId;
        }

        $res = sendWhatsAppDirectMessage($connection, $siteId, $currentUserId, $phone, $finalText, $sendOpts);
        if ($res && isset($res['status']) && $res['status'] === 'success') {
            // Increment usage count
            if ($templateId > 0) {
                mysqli_query($connection, "UPDATE `{$prefix}whatsapp_template` SET template_usage_count = template_usage_count + 1 WHERE template_id = {$templateId}");
            }
            echo json_encode([
                'status' => 'success',
                'msg' => "Template '{$templateName}' sent successfully to {$phone}!",
                'dispatch_details' => $res,
                'sent_text' => $finalText
            ]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg' => !empty($res['msg']) ? $res['msg'] : 'Failed to send WhatsApp template message. Make sure your WhatsApp device is connected.',
                'dispatch_details' => $res
            ]);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'WhatsApp message dispatcher helper not available']);
        exit;
    }
}

// --------------------------------------------------------------------------
// 10. GET: Helper to resolve Active Template for any Action Trigger
// --------------------------------------------------------------------------
if ($method === 'GET' && $action === 'get_active_actions') {
    $q = mysqli_query($connection, "SELECT * FROM `{$prefix}whatsapp_template` 
        WHERE template_site_id = {$siteId} AND template_is_action_active = 1 AND template_status = 1");
    $activeTemplates = [];
    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $actKey = $r['template_action_trigger'];
            $activeTemplates[$actKey] = $r;
        }
    }
    echo json_encode([
        'status' => 'success',
        'active_action_templates' => $activeTemplates
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'msg' => 'Unsupported API action: ' . $action]);
exit;
