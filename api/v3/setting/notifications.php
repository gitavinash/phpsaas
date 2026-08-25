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
    echo json_encode(['status' => 'error', 'msg' => 'Active site identifier could not be resolved.']);
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
// 1. GET: Fetch Current Notification Routing Settings
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $configKeys = [
        'email_notification',
        'admin_email_notification',
        'enable_sms',
        'enable_whatsapp',
        'company_whatsapp',
        'company_mobile',
        'enable_push_notification',
        'enable_sound_alert',
        'notify_new_lead',
        'notify_booking_paid',
        'notify_lead_assigned',
        'notify_invoice_generated'
    ];

    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) {
        return mysqli_real_escape_string($connection, $k);
    }, $configKeys)) . "'";

    $cfgRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
                                         FROM " . TABLE_PREFIX . "site_configuration 
                                         WHERE site_configuration_site_id = {$siteId} 
                                           AND site_configuration_key IN ({$escKeys}) 
                                           AND site_configuration_status = 1");

    $configMap = [];
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $configMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $siteRes = mysqli_query($connection, "SELECT site_email, site_mobile FROM " . TABLE_PREFIX . "site WHERE site_id = {$siteId} LIMIT 1");
    $siteRow = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    $data = [
        'email_notification'        => $configMap['email_notification'] ?? ($configMap['admin_email_notification'] ?? '1'),
        'admin_email_notification'  => $configMap['admin_email_notification'] ?? ($configMap['email_notification'] ?? ($siteRow['site_email'] ?? '')),
        'enable_sms'                => $configMap['enable_sms'] ?? '0',
        'company_mobile'            => $configMap['company_mobile'] ?? ($siteRow['site_mobile'] ?? ''),
        'enable_whatsapp'           => $configMap['enable_whatsapp'] ?? '1',
        'company_whatsapp'          => $configMap['company_whatsapp'] ?? ($configMap['company_mobile'] ?? ($siteRow['site_mobile'] ?? '')),
        'enable_push_notification'  => $configMap['enable_push_notification'] ?? '1',
        'enable_sound_alert'        => $configMap['enable_sound_alert'] ?? '1',
        'notify_new_lead'           => $configMap['notify_new_lead'] ?? '1',
        'notify_booking_paid'       => $configMap['notify_booking_paid'] ?? '1',
        'notify_lead_assigned'      => $configMap['notify_lead_assigned'] ?? '1',
        'notify_invoice_generated'  => $configMap['notify_invoice_generated'] ?? '1'
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

    $fields = [
        'email_notification',
        'admin_email_notification',
        'enable_sms',
        'company_mobile',
        'enable_whatsapp',
        'company_whatsapp',
        'enable_push_notification',
        'enable_sound_alert',
        'notify_new_lead',
        'notify_booking_paid',
        'notify_lead_assigned',
        'notify_invoice_generated'
    ];

    $updatedKeys = [];
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $val = trim((string)$data[$field]);
            $escVal = mysqli_real_escape_string($connection, $val);
            $escKey = mysqli_real_escape_string($connection, $field);

            $upsertSql = "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                          (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                          VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
                          ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}', site_configuration_status = 1";
            mysqli_query($connection, $upsertSql);
            $updatedKeys[] = $field;
        }
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
