<?php

/**
 * V3 Alert & Notification Receiving Settings API Endpoint
 * Multi-Channel: Email, WhatsApp, SMS, Web Push, In-App (Powered by Notify360)
 * Path: /api/v3/setting/notifications.php
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

require_once LOGIN_CHECK;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
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

// Managed database keys from isw_site_configuration:
// email_notification / admin_email_notification is used for alert inbox routing (email_enable is reserved for SMTP engine)
$managedKeys = [
    // Email Alert Receiving Keys
    'email_notification',
    'admin_email_notification',
    'admin_email',
    'enquiry_email',
    'site_email_alert',

    // WhatsApp Keys
    'enable_whatsapp_business_api',
    'admin_whatsapp_number',
    'company_whatsapp_number',

    // SMS Keys
    'sms_enable',
    'sms_default',

    // WhatsApp Trigger Keys
    'company_whatsapp_enquiry_thank_you',
    'company_whatsapp_payment_request',
    'company_whatsapp_itinerary_sent',

    // Feature keys
    'enable_push_notification',
    'enable_inapp_notification'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Notification Receiving Settings
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $keyListStr = "'" . implode("','", $managedKeys) . "'";
    $sql = "SELECT site_configuration_key, site_configuration_value 
            FROM " . TABLE_PREFIX . "site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key IN ({$keyListStr}) 
              AND site_configuration_status = 1";

    $res = mysqli_query($connection, $sql);
    $data = [];

    // Defaults
    foreach ($managedKeys as $k) {
        $data[$k] = '';
    }
    $data['email_notification'] = '1';
    $data['admin_email_notification'] = '1';
    $data['enable_whatsapp_business_api'] = '1';
    $data['sms_enable'] = '0';
    $data['enable_push_notification'] = '1';
    $data['enable_inapp_notification'] = '1';
    $data['company_whatsapp_enquiry_thank_you'] = '1';
    $data['company_whatsapp_payment_request'] = '1';
    $data['company_whatsapp_itinerary_sent'] = '1';

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }

    // Sync email_notification fallback if only admin_email_notification was set
    if ($data['email_notification'] === '' && $data['admin_email_notification'] !== '') {
        $data['email_notification'] = $data['admin_email_notification'];
    }

    // Fallbacks from profile / site if empty
    if (empty($data['admin_email']) && defined('PROFILE_EMAIL') && PROFILE_EMAIL != '') {
        $data['admin_email'] = PROFILE_EMAIL;
    }
    if (empty($data['enquiry_email']) && !empty($data['admin_email'])) {
        $data['enquiry_email'] = $data['admin_email'];
    }

    echo json_encode([
        'status'  => 'success',
        'data'    => $data,
        'powered' => 'Notify360'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Save Notification Receiving Settings
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $isCheck = function ($val, $default = '0') {
        if ($val === null || $val === '') return $default;
        return ($val === '1' || $val === 1 || $val === true || $val === 'true') ? '1' : '0';
    };

    $emailNotification   = $isCheck($data['email_notification'] ?? $data['admin_email_notification'] ?? '1');
    $adminEmail          = trim($data['admin_email'] ?? '');
    $enquiryEmail        = trim($data['enquiry_email'] ?? '');
    $siteEmailAlert      = trim($data['site_email_alert'] ?? '');

    $whatsappEnable      = $isCheck($data['enable_whatsapp_business_api'] ?? '1');
    $adminWhatsapp       = trim($data['admin_whatsapp_number'] ?? '');
    $companyWhatsapp     = trim($data['company_whatsapp_number'] ?? '');

    $smsEnable           = $isCheck($data['sms_enable'] ?? '0');
    $smsDefault          = trim($data['sms_default'] ?? '');

    $pushEnable          = $isCheck($data['enable_push_notification'] ?? '1');
    $inappEnable         = $isCheck($data['enable_inapp_notification'] ?? '1');

    $evtEnquiry          = $isCheck($data['company_whatsapp_enquiry_thank_you'] ?? '1');
    $evtPayment          = $isCheck($data['company_whatsapp_payment_request'] ?? '1');
    $evtItinerary        = $isCheck($data['company_whatsapp_itinerary_sent'] ?? '1');

    if (!empty($adminEmail) && !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'warning', 'msg' => 'Primary Admin Alert Email address is invalid.']);
        exit;
    }

    if (!empty($enquiryEmail) && !filter_var($enquiryEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'warning', 'msg' => 'Inquiry Notification Email address is invalid.']);
        exit;
    }

    $valuesToSave = [
        'email_notification'                 => $emailNotification,
        'admin_email_notification'           => $emailNotification,
        'admin_email'                        => $adminEmail,
        'enquiry_email'                      => $enquiryEmail,
        'site_email_alert'                   => $siteEmailAlert,
        'enable_whatsapp_business_api'       => $whatsappEnable,
        'admin_whatsapp_number'              => $adminWhatsapp,
        'company_whatsapp_number'            => $companyWhatsapp,
        'sms_enable'                         => $smsEnable,
        'sms_default'                        => $smsDefault,
        'enable_push_notification'           => $pushEnable,
        'enable_inapp_notification'          => $inappEnable,
        'company_whatsapp_enquiry_thank_you' => $evtEnquiry,
        'company_whatsapp_payment_request'   => $evtPayment,
        'company_whatsapp_itinerary_sent'    => $evtItinerary
    ];

    foreach ($valuesToSave as $cfgKey => $cfgVal) {
        $escVal = mysqli_real_escape_string($connection, (string)$cfgVal);
        $check = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key = '{$cfgKey}' AND site_configuration_site_id = {$siteId} LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            $cfgId = (int)$row['site_configuration_id'];
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value = '{$escVal}', site_configuration_status = 1 WHERE site_configuration_id = {$cfgId} AND site_configuration_site_id = {$siteId}");
        } else {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES ({$siteId}, '{$cfgKey}', '{$escVal}', 1, 0)");
        }
    }

    echo json_encode([
        'status'  => 'success',
        'msg'     => 'Notification settings saved successfully.',
        'powered' => 'Notify360'
    ]);
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'error',
    'msg'    => 'Method not allowed.'
]);
