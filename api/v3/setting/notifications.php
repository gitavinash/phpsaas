<?php

/**
 * V3 Notification Settings API Endpoint
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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$notificationKeys = [
    'notification_email_notification',
    'notification_admin_email_notification',
    'notification_admin_email',
    'notification_enquiry_email',
    'notification_site_email_alert',
    'notification_enable_whatsapp_business_api',
    'notification_admin_whatsapp_number',
    'notification_company_whatsapp_number',
    'notification_sms_enable',
    'notification_sms_default',
    'notification_enable_sms',
    'notification_company_mobile',
    'notification_enable_whatsapp',
    'notification_company_whatsapp',
    'notification_enable_push_notification',
    'notification_enable_inapp_notification',
    'notification_company_whatsapp_enquiry_thank_you',
    'notification_company_whatsapp_payment_request',
    'notification_company_whatsapp_itinerary_sent',
    'notification_enable_sound_alert',
    'notification_new_lead',
    'notification_booking_paid',
    'notification_lead_assigned',
    'notification_invoice_generated',
    // Fallback legacy keys without prefix
    'email_notification',
    'admin_email_notification',
    'admin_email',
    'enquiry_email',
    'site_email_alert',
    'enable_whatsapp_business_api',
    'admin_whatsapp_number',
    'company_whatsapp_number',
    'sms_enable',
    'sms_default',
    'enable_sms',
    'company_mobile',
    'enable_whatsapp',
    'company_whatsapp',
    'enable_push_notification',
    'enable_inapp_notification',
    'company_whatsapp_enquiry_thank_you',
    'company_whatsapp_payment_request',
    'company_whatsapp_itinerary_sent',
    'enable_sound_alert',
    'notify_new_lead',
    'notify_booking_paid',
    'notify_lead_assigned',
    'notify_invoice_generated'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Notification Routing Settings
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) {
        return mysqli_real_escape_string($connection, $k);
    }, $notificationKeys)) . "'";

    $cfgRes = mysqli_query($connection, "
        SELECT site_configuration_key, site_configuration_value 
        FROM {$tblPrefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key IN ({$escKeys}) 
          AND site_configuration_status = 1
    ");

    $configMap = [];
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $configMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $adminEmail = $configMap['notification_admin_email'] 
        ?? ($configMap['admin_email'] 
        ?? ($configMap['notification_admin_email_notification'] 
        ?? ($configMap['admin_email_notification'] 
        ?? ($configMap['notification_email_notification'] 
        ?? ($configMap['email_notification'] ?? '')))));

    $adminWhatsapp = $configMap['notification_admin_whatsapp_number'] 
        ?? ($configMap['admin_whatsapp_number'] 
        ?? ($configMap['notification_company_whatsapp'] 
        ?? ($configMap['company_whatsapp'] 
        ?? ($configMap['company_mobile'] ?? ''))));

    $data = [
        'notification_email_notification'                 => $configMap['notification_email_notification'] ?? ($configMap['email_notification'] ?? '1'),
        'notification_admin_email_notification'           => $configMap['notification_admin_email_notification'] ?? ($configMap['admin_email_notification'] ?? '1'),
        'notification_admin_email'                        => $adminEmail,
        'notification_enquiry_email'                      => $configMap['notification_enquiry_email'] ?? ($configMap['enquiry_email'] ?? $adminEmail),
        'notification_site_email_alert'                   => $configMap['notification_site_email_alert'] ?? ($configMap['site_email_alert'] ?? $adminEmail),
        'notification_enable_whatsapp_business_api'       => $configMap['notification_enable_whatsapp_business_api'] ?? ($configMap['enable_whatsapp_business_api'] ?? ($configMap['notification_enable_whatsapp'] ?? ($configMap['enable_whatsapp'] ?? '1'))),
        'notification_admin_whatsapp_number'              => $adminWhatsapp,
        'notification_company_whatsapp_number'            => $configMap['notification_company_whatsapp_number'] ?? ($configMap['company_whatsapp_number'] ?? $adminWhatsapp),
        'notification_sms_enable'                         => $configMap['notification_sms_enable'] ?? ($configMap['sms_enable'] ?? ($configMap['notification_enable_sms'] ?? ($configMap['enable_sms'] ?? '0'))),
        'notification_sms_default'                        => $configMap['notification_sms_default'] ?? ($configMap['sms_default'] ?? ''),
        'notification_enable_push_notification'           => $configMap['notification_enable_push_notification'] ?? ($configMap['enable_push_notification'] ?? '1'),
        'notification_enable_inapp_notification'          => $configMap['notification_enable_inapp_notification'] ?? ($configMap['enable_inapp_notification'] ?? '1'),
        'notification_company_whatsapp_enquiry_thank_you' => $configMap['notification_company_whatsapp_enquiry_thank_you'] ?? ($configMap['company_whatsapp_enquiry_thank_you'] ?? '1'),
        'notification_company_whatsapp_payment_request'   => $configMap['notification_company_whatsapp_payment_request'] ?? ($configMap['company_whatsapp_payment_request'] ?? '1'),
        'notification_company_whatsapp_itinerary_sent'    => $configMap['notification_company_whatsapp_itinerary_sent'] ?? ($configMap['company_whatsapp_itinerary_sent'] ?? '1'),
        'notification_enable_sound_alert'                 => $configMap['notification_enable_sound_alert'] ?? ($configMap['enable_sound_alert'] ?? '1'),
        'notification_new_lead'                            => $configMap['notification_new_lead'] ?? ($configMap['notify_new_lead'] ?? '1'),
        'notification_booking_paid'                        => $configMap['notification_booking_paid'] ?? ($configMap['notify_booking_paid'] ?? '1'),
        'notification_lead_assigned'                       => $configMap['notification_lead_assigned'] ?? ($configMap['notify_lead_assigned'] ?? '1'),
        'notification_invoice_generated'                   => $configMap['notification_invoice_generated'] ?? ($configMap['notify_invoice_generated'] ?? '1'),

        // Compatibility aliases for UI
        'email_notification'                 => $configMap['notification_email_notification'] ?? ($configMap['email_notification'] ?? '1'),
        'admin_email_notification'           => $configMap['notification_admin_email_notification'] ?? ($configMap['admin_email_notification'] ?? '1'),
        'admin_email'                        => $adminEmail,
        'enquiry_email'                      => $configMap['notification_enquiry_email'] ?? ($configMap['enquiry_email'] ?? $adminEmail),
        'site_email_alert'                   => $configMap['notification_site_email_alert'] ?? ($configMap['site_email_alert'] ?? $adminEmail),
        'enable_whatsapp_business_api'       => $configMap['notification_enable_whatsapp_business_api'] ?? ($configMap['enable_whatsapp_business_api'] ?? ($configMap['notification_enable_whatsapp'] ?? ($configMap['enable_whatsapp'] ?? '1'))),
        'admin_whatsapp_number'              => $adminWhatsapp,
        'company_whatsapp_number'            => $configMap['notification_company_whatsapp_number'] ?? ($configMap['company_whatsapp_number'] ?? $adminWhatsapp),
        'sms_enable'                         => $configMap['notification_sms_enable'] ?? ($configMap['sms_enable'] ?? ($configMap['notification_enable_sms'] ?? ($configMap['enable_sms'] ?? '0'))),
        'sms_default'                        => $configMap['notification_sms_default'] ?? ($configMap['sms_default'] ?? ''),
        'enable_push_notification'           => $configMap['notification_enable_push_notification'] ?? ($configMap['enable_push_notification'] ?? '1'),
        'enable_inapp_notification'          => $configMap['notification_enable_inapp_notification'] ?? ($configMap['enable_inapp_notification'] ?? '1'),
        'company_whatsapp_enquiry_thank_you' => $configMap['notification_company_whatsapp_enquiry_thank_you'] ?? ($configMap['company_whatsapp_enquiry_thank_you'] ?? '1'),
        'company_whatsapp_payment_request'   => $configMap['notification_company_whatsapp_payment_request'] ?? ($configMap['company_whatsapp_payment_request'] ?? '1'),
        'company_whatsapp_itinerary_sent'    => $configMap['notification_company_whatsapp_itinerary_sent'] ?? ($configMap['company_whatsapp_itinerary_sent'] ?? '1'),
        'enable_sound_alert'                 => $configMap['notification_enable_sound_alert'] ?? ($configMap['enable_sound_alert'] ?? '1'),
        'notify_new_lead'                    => $configMap['notification_new_lead'] ?? ($configMap['notify_new_lead'] ?? '1'),
        'notify_booking_paid'                => $configMap['notification_booking_paid'] ?? ($configMap['notify_booking_paid'] ?? '1'),
        'notify_lead_assigned'               => $configMap['notification_lead_assigned'] ?? ($configMap['notify_lead_assigned'] ?? '1'),
        'notify_invoice_generated'           => $configMap['notification_invoice_generated'] ?? ($configMap['notify_invoice_generated'] ?? '1')
    ];

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Notification Routing Settings
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $updatedKeys = [];
    foreach ($data as $rawKey => $value) {
        $val = trim((string)$value);
        // Prefix with notification_ if not already prefixed
        $key = (strpos($rawKey, 'notification_') === 0) ? $rawKey : 'notification_' . ltrim($rawKey, '_');

        $escVal = mysqli_real_escape_string($connection, $val);
        $escKey = mysqli_real_escape_string($connection, $key);

        $checkQuery = mysqli_query($connection, "
            SELECT site_configuration_id 
            FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key = '{$escKey}' 
            LIMIT 1
        ");

        if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
            $row = mysqli_fetch_assoc($checkQuery);
            $cfgId = (int)$row['site_configuration_id'];
            mysqli_query($connection, "
                UPDATE {$tblPrefix}site_configuration 
                SET site_configuration_value = '{$escVal}', 
                    site_configuration_status = 1 
                WHERE site_configuration_id = {$cfgId}
            ");
        } else {
            mysqli_query($connection, "
                INSERT INTO {$tblPrefix}site_configuration 
                    (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES 
                    ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ");
        }
        $updatedKeys[] = $key;
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'Notification routing channels and alert matrix updated successfully.',
        'keys'   => $updatedKeys
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
