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

    $adminEmail = $configMap['admin_email'] ?? ($configMap['admin_email_notification'] ?? ($configMap['email_notification'] ?? ''));
    $adminWhatsapp = $configMap['admin_whatsapp_number'] ?? ($configMap['company_whatsapp'] ?? ($configMap['company_mobile'] ?? ''));

    $data = [
        'email_notification'                 => $configMap['email_notification'] ?? '1',
        'admin_email_notification'           => $configMap['admin_email_notification'] ?? '1',
        'admin_email'                        => $adminEmail,
        'enquiry_email'                      => $configMap['enquiry_email'] ?? $adminEmail,
        'site_email_alert'                   => $configMap['site_email_alert'] ?? $adminEmail,
        'enable_whatsapp_business_api'       => $configMap['enable_whatsapp_business_api'] ?? ($configMap['enable_whatsapp'] ?? '1'),
        'admin_whatsapp_number'              => $adminWhatsapp,
        'company_whatsapp_number'            => $configMap['company_whatsapp_number'] ?? $adminWhatsapp,
        'sms_enable'                         => $configMap['sms_enable'] ?? ($configMap['enable_sms'] ?? '0'),
        'sms_default'                        => $configMap['sms_default'] ?? '',
        'enable_push_notification'           => $configMap['enable_push_notification'] ?? '1',
        'enable_inapp_notification'          => $configMap['enable_inapp_notification'] ?? '1',
        'company_whatsapp_enquiry_thank_you' => $configMap['company_whatsapp_enquiry_thank_you'] ?? '1',
        'company_whatsapp_payment_request'   => $configMap['company_whatsapp_payment_request'] ?? '1',
        'company_whatsapp_itinerary_sent'    => $configMap['company_whatsapp_itinerary_sent'] ?? '1',
        'enable_sound_alert'                 => $configMap['enable_sound_alert'] ?? '1',
        'notify_new_lead'                    => $configMap['notify_new_lead'] ?? '1',
        'notify_booking_paid'                => $configMap['notify_booking_paid'] ?? '1',
        'notify_lead_assigned'               => $configMap['notify_lead_assigned'] ?? '1',
        'notify_invoice_generated'           => $configMap['notify_invoice_generated'] ?? '1'
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
    foreach ($data as $key => $value) {
        $val = trim((string)$value);
        $escVal = mysqli_real_escape_string($connection, $val);
        $escKey = mysqli_real_escape_string($connection, $key);

        $upsertSql = "
            INSERT INTO {$tblPrefix}site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES 
                ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ON DUPLICATE KEY UPDATE 
                site_configuration_value = '{$escVal}', 
                site_configuration_status = 1
        ";
        mysqli_query($connection, $upsertSql);
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
