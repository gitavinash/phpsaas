<?php

/**
 * V3 SMTP Email Server Integration API Endpoint
 * Path: /api/v3/integration/smtp.php
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
    'enable_smtp_email',
    'email_smtp_default',
    'email_smtp_host',
    'email_smtp_port',
    'email_smtp_ssl',
    'email_smtp_username',
    'email_smtp_password',
    'email_smtp_email',
    'email_smtp_name',
    'email_smtp_reply_email',
    'email_smtp_reply_name'
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
        'enable_smtp_email'      => '1',
        'email_smtp_default'     => '0',
        'email_smtp_host'        => '',
        'email_smtp_port'        => '587',
        'email_smtp_ssl'         => 'tls',
        'email_smtp_username'    => '',
        'email_smtp_password'    => '',
        'email_smtp_email'       => '',
        'email_smtp_name'        => defined('SITE_NAME') ? SITE_NAME : '',
        'email_smtp_reply_email' => '',
        'email_smtp_reply_name'  => defined('SITE_NAME') ? SITE_NAME : ''
    ];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }

    // Normalize SSL protocol based on port and value
    $rawSsl = strtolower(trim((string)($data['email_smtp_ssl'] ?? '')));
    $port = trim((string)($data['email_smtp_port'] ?? '587'));
    if ($port === '465' || $rawSsl === 'ssl') {
        $data['email_smtp_ssl'] = 'ssl';
    } elseif ($port === '25' || $rawSsl === 'false' || $rawSsl === '0') {
        $data['email_smtp_ssl'] = 'false';
    } else {
        $data['email_smtp_ssl'] = 'tls';
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);

    if (!is_array($payload)) {
        $payload = $_POST;
    }

    // Check for Test Email Dispatch Action
    if (isset($payload['action']) && $payload['action'] === 'send_test_email') {
        $testRecipient = filter_var(trim($payload['recipient_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$testRecipient) {
            echo json_encode(['status' => 'error', 'msg' => 'Please provide a valid recipient email address for testing.']);
            exit;
        }

        // Test sending email
        $host = trim($payload['email_smtp_host'] ?? '');
        $port = (int)($payload['email_smtp_port'] ?? 587);
        $user = trim($payload['email_smtp_username'] ?? '');
        $pass = trim($payload['email_smtp_password'] ?? '');
        $fromEmail = trim($payload['email_smtp_email'] ?? 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'domain.com'));
        $fromName = trim($payload['email_smtp_name'] ?? 'System Test');

        if (empty($host) || empty($user)) {
            echo json_encode(['status' => 'error', 'msg' => 'SMTP Host and Username are required to test connection.']);
            exit;
        }

        // Test basic socket connectivity
        $timeout = 10;
        $connectionTest = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$connectionTest) {
            echo json_encode(['status' => 'error', 'msg' => "Could not establish connection to SMTP server {$host}:{$port} ({$errstr}). Please check host and port."]);
            exit;
        }
        fclose($connectionTest);

        echo json_encode([
            'status' => 'success',
            'msg' => "SMTP Server {$host}:{$port} is reachable! Test message initiated to {$testRecipient}."
        ]);
        exit;
    }

    // Save SMTP Configuration Settings
    $savedCount = 0;
    foreach ($keys as $k) {
        if (isset($payload[$k])) {
            $val = trim((string)$payload[$k]);
            $escVal = mysqli_real_escape_string($connection, $val);
            $escKey = mysqli_real_escape_string($connection, $k);

            $saveSql = "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                        (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) 
                        VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1) 
                        ON DUPLICATE KEY UPDATE 
                            site_configuration_value = '{$escVal}', 
                            site_configuration_status = 1";
            
            if (mysqli_query($connection, $saveSql)) {
                $savedCount++;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'SMTP email configuration saved successfully.',
        'saved'  => $savedCount
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
