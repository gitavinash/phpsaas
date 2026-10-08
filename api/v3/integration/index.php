<?php

/**
 * V3 Integrations Hub Master API Endpoint
 * Path: /api/v3/integration/index.php
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
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Active site identifier could not be resolved.'
    ]);
    exit;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// Payment Gateway Exclusion List (Only ONE active at a time - excluding UPI)
// --------------------------------------------------------------------------
$paymentGateways = [
    'razorpay'     => 'enable_razorpay',
    'instamojo'    => 'enable_instamojo',
    'sabpaisa'     => 'enable_sabpaisa',
    'phonepe'      => 'enable_phonepe',
    'worldline'    => 'enable_worldline',
    'payu'         => 'enable_payu',
    'phi_commerce' => 'enable_phi_commerce',
    'cashfree'     => 'enable_cashfree'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch List of Installed Plugins with Active Status & Details
// --------------------------------------------------------------------------
if ($method === 'GET') {
    // Read site configuration values
    $configMap = [];
    $cfgRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
                                         FROM " . TABLE_PREFIX . "site_configuration 
                                         WHERE site_configuration_site_id = {$siteId} 
                                           AND site_configuration_status = 1");
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $configMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    // Determine currently active single payment gateway
    // Default to razorpay if none explicitly set, or pick the first active
    $activePgFound = null;
    foreach ($paymentGateways as $slug => $key) {
        if (isset($configMap[$key]) && $configMap[$key] === '1') {
            if ($activePgFound === null) {
                $activePgFound = $slug;
            }
        }
    }
    // If no configuration row exists yet, default active to razorpay
    if ($activePgFound === null) {
        $activePgFound = 'razorpay';
    }

    // Check connected Facebook pages
    $fbCount = 0;
    $fbRes = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM " . TABLE_PREFIX . "fb_pages WHERE fb_page_site_id = {$siteId} AND fb_page_status = 1");
    if ($fbRes) {
        $fbRow = mysqli_fetch_assoc($fbRes);
        $fbCount = (int)($fbRow['cnt'] ?? 0);
    }

    $plugins = [
        // Lead Gen & Marketing
        [
            'id'            => 'facebook',
            'slug'          => 'facebook',
            'title'         => 'Facebook Lead Ads',
            'category'      => 'leadgen',
            'category_name' => 'Lead Generation & Ads',
            'description'   => 'Capture real-time traveler inquiries from Facebook & Instagram ad campaigns with automated webhook synchronization.',
            'icon'          => 'facebook',
            'config_url'    => '/dashboard/integration/facebook/',
            'toggle_key'    => 'enable_facebook_lead_ads',
            'is_active'     => ($configMap['enable_facebook_lead_ads'] ?? '1') === '1',
            'status_label'  => $fbCount > 0 ? "Connected ({$fbCount} Pages)" : 'Ready to Connect',
            'is_connected'  => $fbCount > 0,
            'features'      => [
                'Instant real-time lead ingestion into CRM',
                'Automated page & form webhook subscription',
                'Adset, campaign, and form attribution tags'
            ]
        ],
        [
            'id'            => 'google_ads',
            'slug'          => 'google_ads',
            'title'         => 'Google Ads & Lead Forms',
            'category'      => 'leadgen',
            'category_name' => 'Lead Generation & Ads',
            'description'   => 'Track offline conversion milestones, ingest Google Search lead extension forms, and calculate tour campaign ROI.',
            'icon'          => 'google',
            'config_url'    => '/dashboard/integration/google_ads/',
            'toggle_key'    => 'enable_google_ads',
            'is_active'     => ($configMap['enable_google_ads'] ?? '0') === '1',
            'status_label'  => !empty($configMap['google_ads_customer_id']) ? 'Connected' : 'Available',
            'is_connected'  => !empty($configMap['google_ads_customer_id']),
            'features'      => [
                'GCLID conversion tracking & offline sync',
                'Performance Max form webhook receiver',
                'Multi-channel campaign attribution ROI'
            ]
        ],
        // Messaging

        [
            'id'            => 'whatsapp_web',
            'slug'          => 'whatsapp_web',
            'title'         => 'WhatsApp Web API',
            'category'      => 'messaging',
            'category_name' => 'Messaging & Chat',
            'description'   => 'Connect your existing WhatsApp by scanning a QR code. Sync incoming traveler messages directly into the CRM and send replies from your dashboard.',
            'icon'          => 'whatsapp',
            'config_url'    => '/dashboard/integration/whatsapp_web/',
            'toggle_key'    => 'enable_whatsapp_web_api',
            'is_active'     => ($configMap['enable_whatsapp_web_api'] ?? '0') === '1',
            'status_label'  => !empty($configMap['whatsapp_web_phone']) ? 'Connected (' . $configMap['whatsapp_web_phone'] . ')' : (($configMap['enable_whatsapp_web_api'] ?? '0') === '1' ? 'Ready to Scan' : 'Available'),
            'is_connected'  => !empty($configMap['whatsapp_web_phone']),
            'features'      => [
                'Scan QR code with existing personal or business WhatsApp',
                'Real-time incoming message synchronization to CRM',
                'Live interactive two-way WhatsApp chat inbox in CRM'
            ]
        ],
        [
            'id'            => 'smtp',
            'slug'          => 'smtp',
            'title'         => 'SMTP Email Server',
            'category'      => 'messaging',
            'category_name' => 'Messaging & Email',
            'description'   => 'Route outgoing notifications, quotation PDFs, payment receipts, and team alerts through your custom authenticated SMTP mail server.',
            'icon'          => 'smtp',
            'config_url'    => '/dashboard/integration/smtp/',
            'toggle_key'    => 'enable_smtp_email',
            'is_active'     => ($configMap['enable_smtp_email'] ?? '1') === '1',
            'status_label'  => !empty($configMap['email_smtp_host']) ? 'Configured' : 'Available',
            'is_connected'  => !empty($configMap['email_smtp_host']),
            'features'      => [
                'Custom outgoing mail host & TLS/SSL encryption',
                'SPF/DKIM compatible sender identity & reply-to',
                'Instant diagnostic test email dispatcher'
            ]
        ],
        // Single Active Payment Gateways
        [
            'id'            => 'razorpay',
            'slug'          => 'razorpay',
            'title'         => 'Razorpay Gateway',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Collect online credit/debit card, netbanking, and EMI payments with automatic invoice generation and booking status updates.',
            'icon'          => 'razorpay',
            'config_url'    => '/dashboard/integration/razorpay/',
            'toggle_key'    => 'enable_razorpay',
            'is_active'     => ($activePgFound === 'razorpay'),
            'status_label'  => !empty($configMap['razorpay_key_id']) ? 'Live' : 'Connected',
            'is_connected'  => true,
            'features'      => [
                'Dynamic booking payment links with expiry',
                'Auto-reconcile booking milestone status',
                'Instant automated receipt & GST invoice PDF'
            ]
        ],
        [
            'id'            => 'instamojo',
            'slug'          => 'instamojo',
            'title'         => 'Instamojo Gateway',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Collect online debit/credit card, netbanking, and UPI payments with instant settlement and automated receipts.',
            'icon'          => 'instamojo',
            'config_url'    => '/dashboard/integration/instamojo/',
            'toggle_key'    => 'enable_instamojo',
            'is_active'     => ($activePgFound === 'instamojo'),
            'status_label'  => !empty($configMap['instamojo_api_key']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Multi-channel payment link creation',
                'Automated webhook status reconciliations',
                'Convenience fee and GST invoice handling'
            ]
        ],
        [
            'id'            => 'sabpaisa',
            'slug'          => 'sabpaisa',
            'title'         => 'SabPaisa Gateway',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Unified payment gateway supporting multi-banking aggregator channels, offline cash counters, UPI, and cards.',
            'icon'          => 'sabpaisa',
            'config_url'    => '/dashboard/integration/sabpaisa/',
            'toggle_key'    => 'enable_sabpaisa',
            'is_active'     => ($activePgFound === 'sabpaisa'),
            'status_label'  => !empty($configMap['sabpaisa_client_code']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                '1.4M+ physical cash collection counters',
                'Multi-bank netbanking and dynamic UPI',
                'AES encrypted transaction verification'
            ]
        ],
        [
            'id'            => 'phonepe',
            'slug'          => 'phonepe',
            'title'         => 'PhonePe Gateway',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Integrate PhonePe Standard Checkout with zero friction, automated UPI Intent flow, and card processing.',
            'icon'          => 'phonepe',
            'config_url'    => '/dashboard/integration/phonepe/',
            'toggle_key'    => 'enable_phonepe',
            'is_active'     => ($activePgFound === 'phonepe'),
            'status_label'  => !empty($configMap['phonepe_merchant_id']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Seamless zero-redirect UPI Intent flow',
                'Instant QR code on desktop checkout',
                'SHA-256 HMAC cryptographic signature'
            ]
        ],
        [
            'id'            => 'worldline',
            'slug'          => 'worldline',
            'title'         => 'Worldline (Ingenico)',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Enterprise multi-currency global payments gateway supporting Cards, Net Banking, e-Mandates, and SHA-512 validation.',
            'icon'          => 'worldline',
            'config_url'    => '/dashboard/integration/worldline/',
            'toggle_key'    => 'enable_worldline',
            'is_active'     => ($activePgFound === 'worldline'),
            'status_label'  => !empty($configMap['worldline_merchant_code']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Enterprise Tier-1 multi-bank routing',
                'Domestic and international credit cards',
                'Automated settlement reconciliation feeds'
            ]
        ],
        [
            'id'            => 'payu',
            'slug'          => 'payu',
            'title'         => 'PayU Gateway',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Seamless checkout experience supporting Credit/Debit cards, Net Banking across 50+ banks, UPI QR, and PayLater.',
            'icon'          => 'payu',
            'config_url'    => '/dashboard/integration/payu/',
            'toggle_key'    => 'enable_payu',
            'is_active'     => ($activePgFound === 'payu'),
            'status_label'  => !empty($configMap['payu_merchant_key']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Industry-leading payment success rates',
                'One-click saved cards and native OTP',
                'Seamless split settlement for tour vendors'
            ]
        ],
        [
            'id'            => 'phi_commerce',
            'slug'          => 'phi_commerce',
            'title'         => 'Phi Commerce (PayPhi)',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Omni-channel payment gateway solution providing unified checkout across POS, Payment Links, UPI, and Cards.',
            'icon'          => 'phicommerce',
            'config_url'    => '/dashboard/integration/phi_commerce/',
            'toggle_key'    => 'enable_phi_commerce',
            'is_active'     => ($activePgFound === 'phi_commerce'),
            'status_label'  => !empty($configMap['phi_commerce_merchant_id']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Omni-channel payment link & QR dispatch',
                'Real-time fraud screening and monitoring',
                'Dynamic multi-currency conversion (DCC)'
            ]
        ],
        [
            'id'            => 'cashfree',
            'slug'          => 'cashfree',
            'title'         => 'Cashfree Payments',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'High-speed payments suite offering Seamless Checkout, Auto-collect virtual accounts, and Instant Vendor Payouts.',
            'icon'          => 'cashfree',
            'config_url'    => '/dashboard/integration/cashfree/',
            'toggle_key'    => 'enable_cashfree',
            'is_active'     => ($activePgFound === 'cashfree'),
            'status_label'  => !empty($configMap['cashfree_app_id']) ? 'Live' : 'Active',
            'is_connected'  => true,
            'features'      => [
                'Auto-collect virtual bank account numbers',
                '120+ payment modes & instant settlements',
                'Automated multi-vendor commission splits'
            ]
        ],
        // Independent UPI Dynamic QR (Not mutually exclusive with payment gateways)
        [
            'id'            => 'upi',
            'slug'          => 'upi',
            'title'         => 'UPI & Dynamic QR Code',
            'category'      => 'payments',
            'category_name' => 'Payments & Banking',
            'description'   => 'Generate dynamic NPCI-compliant UPI QR codes on quotations and vouchers for direct zero-surcharge bank remittances.',
            'icon'          => 'upi',
            'config_url'    => '/dashboard/integration/upi/',
            'toggle_key'    => 'enable_upi',
            'is_active'     => ($configMap['enable_upi'] ?? '1') === '1',
            'status_label'  => !empty($configMap['upi_details']) ? 'Active' : 'Configured',
            'is_connected'  => true,
            'features'      => [
                'Zero gateway fee direct bank transfers',
                'Dynamic amount-encoded UPI QR generation',
                'Compatible with GPay, PhonePe, Paytm, BHIM'
            ]
        ],
        // Developer & Automation
        [
            'id'            => 'webhooks',
            'slug'          => 'webhooks',
            'title'         => 'Custom CRM Webhooks',
            'category'      => 'developer',
            'category_name' => 'Developer & Webhooks',
            'description'   => 'Stream real-time lead and booking payloads to external endpoints, Zapier, Make, and backend microservices.',
            'icon'          => 'webhooks',
            'config_url'    => '/dashboard/integration/webhooks/',
            'toggle_key'    => 'enable_webhooks',
            'is_active'     => ($configMap['enable_webhooks'] ?? '1') === '1',
            'status_label'  => (!empty($configMap['webhook_lead_created_url']) || !empty($configMap['webhook_payment_received_url']) || !empty($configMap['webhook_tour_booked_url'])) ? 'Configured' : 'Available',
            'is_connected'  => (!empty($configMap['webhook_lead_created_url']) || !empty($configMap['webhook_payment_received_url']) || !empty($configMap['webhook_tour_booked_url'])),
            'features'      => [
                'Live outbound triggers for leads & bookings',
                'HMAC SHA-256 webhook secret verification',
                'Plug-and-play Zapier & Make integration'
            ]
        ]
    ];

    if (isset($site_access_type) && $site_access_type === 'digital_marketing') {
        $plugins = array_values(array_filter($plugins, function ($p) {
            return ($p['category'] ?? '') !== 'payments';
        }));
    }

    echo json_encode([
        'status'  => 'success',
        'plugins' => $plugins
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Toggle Plugin Active State
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $pluginSlug = trim($data['plugin_slug'] ?? ($data['slug'] ?? ''));
    $toggleKey  = trim($data['toggle_key'] ?? '');
    $isActive   = isset($data['is_active']) ? (int)$data['is_active'] : (isset($data['active']) ? (int)$data['active'] : 1);

    if (empty($toggleKey)) {
        $slugMap = array_merge([
            'facebook'     => 'enable_facebook_lead_ads',
            'whatsapp_web' => 'enable_whatsapp_web_api',
            'google_ads'   => 'enable_google_ads',
            'upi'          => 'enable_upi',
            'webhooks'     => 'enable_webhooks'
        ], $paymentGateways);
        $toggleKey = $slugMap[$pluginSlug] ?? '';
    }

    if (isset($site_access_type) && $site_access_type === 'digital_marketing') {
        if (in_array($pluginSlug, array_keys($paymentGateways), true) || in_array($toggleKey, array_values($paymentGateways), true) || $pluginSlug === 'upi' || $toggleKey === 'enable_upi') {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Access denied: Digital marketing team cannot modify payment gateways.'
            ]);
            exit;
        }
    }

    if (empty($toggleKey)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Invalid plugin or toggle key specified.'
        ]);
        exit;
    }

    $valStr = $isActive === 1 ? '1' : '0';
    $escKey = mysqli_real_escape_string($connection, $toggleKey);

    $sql = "INSERT INTO " . TABLE_PREFIX . "site_configuration 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES ({$siteId}, '{$escKey}', '{$valStr}', 1)
            ON DUPLICATE KEY UPDATE site_configuration_value = '{$valStr}', site_configuration_status = 1";

    $ok = mysqli_query($connection, $sql);

    $deactivatedSlugs = [];

    // If a Payment Gateway is activated, deactivate all OTHER payment gateways (Mutually Exclusive)
    if ($ok && $isActive === 1 && isset($paymentGateways[$pluginSlug])) {
        // Set pg_name to this payment gateway
        $escPg = mysqli_real_escape_string($connection, $pluginSlug);
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES ({$siteId}, 'pg_name', '{$escPg}', 1)
            ON DUPLICATE KEY UPDATE site_configuration_value = '{$escPg}', site_configuration_status = 1");

        // Deactivate all other payment gateways
        foreach ($paymentGateways as $otherSlug => $otherKey) {
            if ($otherSlug !== $pluginSlug) {
                $escOtherKey = mysqli_real_escape_string($connection, $otherKey);
                mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                    (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                    VALUES ({$siteId}, '{$escOtherKey}', '0', 1)
                    ON DUPLICATE KEY UPDATE site_configuration_value = '0', site_configuration_status = 1");
                $deactivatedSlugs[] = $otherSlug;
            }
        }
    }

    if ($ok) {
        $stateWord = $isActive === 1 ? 'activated' : 'deactivated';
        echo json_encode([
            'status'            => 'success',
            'msg'               => "Integration {$stateWord} successfully.",
            'is_active'         => $isActive === 1,
            'is_payment_gateway'=> isset($paymentGateways[$pluginSlug]),
            'active_slug'       => $pluginSlug,
            'deactivated_slugs' => $deactivatedSlugs
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Database error updating plugin status.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
