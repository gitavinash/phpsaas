<?php

/**
 * V3 WhatsApp Cloud API Integration API Endpoint
 * Path: /api/v3/integration/whatsapp.php
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

$keys = [
    'enable_whatsapp_business_api',
    'whatsapp_phone_number_id',
    'whatsapp_waba_id',
    'whatsapp_access_token',
    'whatsapp_webhook_verify_token',
    'company_whatsapp_number',
    'company_whatsapp_enquiry_thank_you',
    'company_whatsapp_payment_request',
    'company_whatsapp_itinerary_sent'
];

if ($method === 'GET') {
    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) { return mysqli_real_escape_string($connection, $k); }, $keys)) . "'";
    $sql = "SELECT site_configuration_key, site_configuration_value 
            FROM " . TABLE_PREFIX . "site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key IN ({$escKeys}) 
              AND site_configuration_status = 1";
    
    $res = mysqli_query($connection, $sql);
    $data = [
        'enable_whatsapp_business_api'        => '1',
        'whatsapp_phone_number_id'           => '',
        'whatsapp_waba_id'                   => '',
        'whatsapp_access_token'              => '',
        'whatsapp_webhook_verify_token'      => 'isw_wa_' . substr(md5((string)$siteId), 0, 10),
        'company_whatsapp_number'            => '',
        'company_whatsapp_enquiry_thank_you' => '1',
        'company_whatsapp_payment_request'   => '1',
        'company_whatsapp_itinerary_sent'    => '1'
    ];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    foreach ($keys as $k) {
        if (isset($data[$k])) {
            $val = trim((string)$data[$k]);
            $escVal = mysqli_real_escape_string($connection, $val);
            $escKey = mysqli_real_escape_string($connection, $k);
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
                ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}', site_configuration_status = 1");
        }
    }

    echo json_encode(['status' => 'success', 'msg' => 'WhatsApp Cloud API settings saved successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
