<?php

/**
 * V3 Razorpay Integration API Endpoint
 * Path: /api/v3/integration/razorpay.php
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

$otherGateways = [
    'enable_instamojo',
    'enable_sabpaisa',
    'enable_phonepe',
    'enable_worldline',
    'enable_payu',
    'enable_phi_commerce',
    'enable_cashfree'
];

$keys = [
    'enable_razorpay',
    'razorpay_mode',
    'razorpay_key_id',
    'razorpay_key_secret',
    'razorpay_webhook_secret',
    'razorpay_auto_receipt'
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
        'enable_razorpay'         => '1',
        'razorpay_mode'           => 'live',
        'razorpay_key_id'         => '',
        'razorpay_key_secret'     => '',
        'razorpay_webhook_secret' => '',
        'razorpay_auto_receipt'   => '1'
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

    $isEnabling = isset($data['enable_razorpay']) && (string)$data['enable_razorpay'] === '1';

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

    // Mutually exclusive single payment gateway activation
    if ($isEnabling) {
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES ({$siteId}, 'pg_name', 'razorpay', 1)
            ON DUPLICATE KEY UPDATE site_configuration_value = 'razorpay', site_configuration_status = 1");

        foreach ($otherGateways as $og) {
            $escOg = mysqli_real_escape_string($connection, $og);
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES ({$siteId}, '{$escOg}', '0', 1)
                ON DUPLICATE KEY UPDATE site_configuration_value = '0', site_configuration_status = 1");
        }
    }

    echo json_encode(['status' => 'success', 'msg' => 'Razorpay credentials and webhook preferences saved successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
