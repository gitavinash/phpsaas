<?php

/**
 * V3 WhatsApp Web API Integration & Multi-Device Session Controller
 * Path: /api/v3/integration/whatsapp_web.php
 * Handles multi-device listing, agent assignment, permissions, pairing & sync.
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

require_once dirname(__DIR__) . '/whatsapp/db_init.php';
require_once dirname(__DIR__) . '/whatsapp/qr_helper.php';

initWhatsAppDatabase($connection);

$currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);
$userRole = isset($site_access_type) ? $site_access_type : '';
$isAdmin = ($userRole === 'administrator');

if (!$isAdmin && $currentUserId > 0) {
    $saQ = mysqli_query($connection, "SELECT site_access_type FROM " . TABLE_PREFIX . "site_access WHERE site_access_site_id = {$siteId} AND site_access_user_id = {$currentUserId} AND site_access_status = 1 LIMIT 1");
    if ($saQ && mysqli_num_rows($saQ) > 0) {
        $saR = mysqli_fetch_assoc($saQ);
        if ($saR['site_access_type'] === 'administrator') {
            $isAdmin = true;
        }
        $userRole = $saR['site_access_type'];
    }
}

// Check if current user already has an assigned WhatsApp device
$userAssignedDevice = null;
$hasAssignedDevice = false;
if ($currentUserId > 0) {
    $uadQ = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} LIMIT 1");
    if ($uadQ && mysqli_num_rows($uadQ) > 0) {
        $userAssignedDevice = mysqli_fetch_assoc($uadQ);
        $hasAssignedDevice = true;
    }
}
$canLink = $isAdmin ? true : (!$hasAssignedDevice);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$configKeys = [
    'enable_whatsapp_web_api',
    'whatsapp_web_status',
    'whatsapp_web_phone',
    'whatsapp_web_pushname',
    'whatsapp_web_device',
    'whatsapp_web_session_id',
    'whatsapp_web_sync_incoming',
    'whatsapp_web_auto_create_lead',
    'whatsapp_web_gateway_url',
    'whatsapp_web_last_sync'
];

function getWaWebConfigs($connection, $siteId, $keys) {
    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) { return mysqli_real_escape_string($connection, $k); }, $keys)) . "'";
    $sql = "SELECT site_configuration_key, site_configuration_value 
            FROM " . TABLE_PREFIX . "site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key IN ({$escKeys}) 
              AND site_configuration_status = 1";
    
    $res = mysqli_query($connection, $sql);
    $data = [
        'enable_whatsapp_web_api'       => '0',
        'whatsapp_web_status'           => 'disconnected',
        'whatsapp_web_phone'            => '',
        'whatsapp_web_pushname'         => '',
        'whatsapp_web_device'           => 'WhatsApp Web / Multi-Device',
        'whatsapp_web_session_id'       => '',
        'whatsapp_web_sync_incoming'    => '1',
        'whatsapp_web_auto_create_lead' => '1',
        'whatsapp_web_gateway_url'      => '',
        'whatsapp_web_last_sync'        => ''
    ];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }
    return $data;
}

function saveWaWebConfig($connection, $siteId, $key, $value) {
    $escKey = mysqli_real_escape_string($connection, $key);
    $escVal = mysqli_real_escape_string($connection, $value);
    $sql = "INSERT INTO " . TABLE_PREFIX . "site_configuration 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}', site_configuration_status = 1";
    return mysqli_query($connection, $sql);
}

function callWaGateway($customUrl, $path, $method = 'GET', $body = null) {
    $gwUrl = !empty($customUrl) ? rtrim($customUrl, '/') : 'http://127.0.0.1:3001';
    $target = $gwUrl . '/' . ltrim($path, '/');

    // Dynamic tenant domain resolution
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
    $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook.php';
    $siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

    $separator = (strpos($target, '?') !== false) ? '&' : '?';
    $target .= $separator . 'webhook_url=' . urlencode($dynamicWebhookUrl) . '&site_id=' . $siteId;

    if ($body !== null && is_array($body)) {
        $body['webhook_url'] = $dynamicWebhookUrl;
        $body['site_id'] = $siteId;
    }

    $res = false;

    // Method 1: cURL (most reliable in hardened Apache/PHP production environments)
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($body !== null) {
            $jsonPayload = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonPayload)
            ]);
        }
        $res = curl_exec($ch);
        curl_close($ch);
    }

    // Method 2: stream context fallback if allow_url_fopen is enabled
    if ($res === false && ini_get('allow_url_fopen')) {
        $opts = [
            'http' => [
                'method'  => $method,
                'timeout' => 6,
                'ignore_errors' => true
            ]
        ];
        if ($body !== null) {
            $opts['http']['header'] = "Content-Type: application/json\r\n";
            $opts['http']['content'] = json_encode($body);
        }
        $ctx = stream_context_create($opts);
        $res = @file_get_contents($target, false, $ctx);
    }

    if ($res !== false && $res !== '') {
        return json_decode($res, true);
    }
    return null;
}

// --------------------------------------------------------------------------
// 1. ACTION: Get Devices & Role Context (Multi-Device Hub)
// --------------------------------------------------------------------------
if ($action === 'get_devices' || $action === 'devices') {
    $configs = getWaWebConfigs($connection, $siteId, $configKeys);

    // Sync live device statuses from gateway in the background
    $gwDevices = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'devices');
    if ($gwDevices && ($gwDevices['status'] ?? '') === 'success' && is_array($gwDevices['devices'] ?? null)) {
        foreach ($gwDevices['devices'] as $gDev) {
            $sess = mysqli_real_escape_string($connection, $gDev['session_id']);
            $stat = ($gDev['is_connected'] || $gDev['connection_status'] === 'connected') ? 'connected' : ($gDev['has_qr'] ? 'qr_ready' : 'disconnected');
            $ph   = mysqli_real_escape_string($connection, $gDev['user']['phone'] ?? '');
            $nm   = mysqli_real_escape_string($connection, $gDev['user']['name'] ?? '');
            $sync = !empty($gDev['last_sync']) ? date('Y-m-d H:i:s', strtotime($gDev['last_sync'])) : date('Y-m-d H:i:s');

            if (!empty($ph)) {
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device 
                                          SET device_phone = IF(device_phone = '' OR device_phone IS NULL, '{$ph}', device_phone),
                                              device_name = IF(device_name = '' OR device_name IS NULL, '{$nm}', device_name),
                                              device_status = '{$stat}',
                                              device_last_sync = '{$sync}'
                                          WHERE device_site_id = {$siteId} AND device_session_id = '{$sess}'");
            }
        }
    }

    $devices = [];
    $agents = [];

    if ($isAdmin) {
        // Admins can see all site devices
        $devsQ = mysqli_query($connection, "SELECT d.*, 
                                                  u.user_username, u.user_email,
                                                  TRIM(CONCAT(COALESCE(p.profile_first_name, ''), ' ', COALESCE(p.profile_last_name, ''))) as assigned_agent_name,
                                                  sa.site_access_type as assigned_agent_role
                                           FROM " . TABLE_PREFIX . "whatsapp_device d
                                           LEFT JOIN " . TABLE_PREFIX . "user u ON u.user_id = d.device_assigned_user_id
                                           LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                                           LEFT JOIN " . TABLE_PREFIX . "site_access sa ON (sa.site_access_user_id = d.device_assigned_user_id AND sa.site_access_site_id = {$siteId})
                                           WHERE d.device_site_id = {$siteId}
                                           ORDER BY d.device_id ASC");
        if ($devsQ) {
            while ($r = mysqli_fetch_assoc($devsQ)) {
                $devices[] = [
                    'device_id'           => (int)$r['device_id'],
                    'session_id'          => $r['device_session_id'],
                    'phone'               => $r['device_phone'] ?: '',
                    'name'                => $r['device_name'] ?: 'WhatsApp Device',
                    'assigned_user_id'    => $r['device_assigned_user_id'] ? (int)$r['device_assigned_user_id'] : null,
                    'assigned_agent_name' => $r['assigned_agent_name'] ?: ($r['user_username'] ?: 'Unassigned'),
                    'assigned_agent_role' => $r['assigned_agent_role'] ?: '',
                    'status'              => $r['device_status'],
                    'last_sync'           => $r['device_last_sync'] ? date('M d, Y h:i A', strtotime($r['device_last_sync'])) : 'Not yet',
                    'created_at'          => $r['device_created_at']
                ];
            }
        }

        // Fetch all sales agents / team members on this site
        $agQ = mysqli_query($connection, "SELECT sa.site_access_user_id as user_id, sa.site_access_type as role,
                                                u.user_username, u.user_email,
                                                TRIM(CONCAT(COALESCE(p.profile_first_name, ''), ' ', COALESCE(p.profile_last_name, ''))) as full_name
                                         FROM " . TABLE_PREFIX . "site_access sa
                                         INNER JOIN " . TABLE_PREFIX . "user u ON u.user_id = sa.site_access_user_id
                                         LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                                         WHERE sa.site_access_site_id = {$siteId} AND sa.site_access_status = 1
                                         ORDER BY p.profile_first_name ASC, u.user_username ASC");
        if ($agQ) {
            while ($ar = mysqli_fetch_assoc($agQ)) {
                $agents[] = [
                    'user_id' => (int)$ar['user_id'],
                    'name'    => !empty($ar['full_name']) ? $ar['full_name'] : $ar['user_username'],
                    'email'   => $ar['user_email'],
                    'role'    => $ar['role']
                ];
            }
        }
    } else {
        // Sales Agent view: Only return their single assigned device if one exists
        if ($userAssignedDevice) {
            $devices[] = [
                'device_id'           => (int)$userAssignedDevice['device_id'],
                'session_id'          => $userAssignedDevice['device_session_id'],
                'phone'               => $userAssignedDevice['device_phone'] ?: '',
                'name'                => $userAssignedDevice['device_name'] ?: 'WhatsApp Web',
                'assigned_user_id'    => (int)$userAssignedDevice['device_assigned_user_id'],
                'assigned_agent_name' => 'Your Assigned Device',
                'status'              => $userAssignedDevice['device_status'],
                'last_sync'           => $userAssignedDevice['device_last_sync'] ? date('M d, Y h:i A', strtotime($userAssignedDevice['device_last_sync'])) : 'Not yet'
            ];
        }
    }

    echo json_encode([
        'status'              => 'success',
        'is_admin'            => $isAdmin,
        'user_role'           => $userRole,
        'current_user_id'     => $currentUserId,
        'has_assigned_device' => $hasAssignedDevice,
        'can_link_device'     => $canLink,
        'assigned_device'     => $userAssignedDevice ? [
            'device_id'  => (int)$userAssignedDevice['device_id'],
            'session_id' => $userAssignedDevice['device_session_id'],
            'phone'      => $userAssignedDevice['device_phone'],
            'name'       => $userAssignedDevice['device_name'],
            'status'     => $userAssignedDevice['device_status'],
            'last_sync'  => $userAssignedDevice['device_last_sync']
        ] : null,
        'devices'             => $devices,
        'agents'              => $agents,
        'is_active'           => $configs['enable_whatsapp_web_api'] === '1'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. ACTION: Assign WhatsApp Device to Sales Agent (Admin Only)
// --------------------------------------------------------------------------
if ($action === 'assign_device') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Access denied. Only administrators have permission to assign WhatsApp devices to sales agents.'
        ]);
        exit;
    }

    $rawInput = file_get_contents('php://input');
    $postData = json_decode($rawInput, true) ?? [];
    $deviceId = (int)($_POST['device_id'] ?? $postData['device_id'] ?? 0);
    $targetUserId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : (isset($postData['user_id']) ? (int)$postData['user_id'] : 0);

    if ($deviceId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid device ID is required.']);
        exit;
    }

    // Verify device exists on site
    $devCheck = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_id = {$deviceId} AND device_site_id = {$siteId} LIMIT 1");
    if (!$devCheck || mysqli_num_rows($devCheck) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Device not found on this site.']);
        exit;
    }

    if ($targetUserId > 0) {
        // Enforce: "CRM sales agent can add/have only 1 whatsapp number"
        $chkOther = mysqli_query($connection, "SELECT device_id, device_phone, device_name FROM " . TABLE_PREFIX . "whatsapp_device 
                                              WHERE device_site_id = {$siteId} 
                                                AND device_assigned_user_id = {$targetUserId} 
                                                AND device_id != {$deviceId} LIMIT 1");
        if ($chkOther && mysqli_num_rows($chkOther) > 0) {
            $otherRow = mysqli_fetch_assoc($chkOther);
            $existingPhone = !empty($otherRow['device_phone']) ? $otherRow['device_phone'] : ('Device #' . $otherRow['device_id']);
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'msg'    => "This sales agent already has an assigned WhatsApp device ({$existingPhone}). A sales agent can have only 1 assigned WhatsApp number."
            ]);
            exit;
        }

        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device SET device_assigned_user_id = {$targetUserId} WHERE device_id = {$deviceId} AND device_site_id = {$siteId}");
        echo json_encode([
            'status' => 'success',
            'msg'    => 'WhatsApp device assigned to sales agent successfully.'
        ]);
        exit;
    } else {
        // Unassign device
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device SET device_assigned_user_id = NULL WHERE device_id = {$deviceId} AND device_site_id = {$siteId}");
        echo json_encode([
            'status' => 'success',
            'msg'    => 'WhatsApp device unassigned successfully.'
        ]);
        exit;
    }
}

// --------------------------------------------------------------------------
// 3. ACTION: Generate QR Code for WhatsApp Web pairing
// --------------------------------------------------------------------------
if ($action === 'generate_qr' || $action === 'qr') {
    // Check sales agent constraint
    if (!$isAdmin && $hasAssignedDevice) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'A WhatsApp number is already assigned to your account. Sales agents can link only 1 device.'
        ]);
        exit;
    }

    $configs = getWaWebConfigs($connection, $siteId, $configKeys);

    // Resolve target session ID
    $rawInput = file_get_contents('php://input');
    $postData = json_decode($rawInput, true) ?? [];
    $sessionId = trim($_GET['session_id'] ?? $_POST['session_id'] ?? $postData['session_id'] ?? '');

    if (empty($sessionId)) {
        if (!$isAdmin) {
            $sessionId = 'agent_' . $currentUserId;
        } else {
            $isNewDevice = (!empty($_GET['new_device']) || !empty($_POST['new_device']) || !empty($postData['new_device']));
            $sessionId = $isNewDevice ? ('device_' . time()) : 'default';
        }
    }

    $forceParam = (!empty($_GET['force']) && $_GET['force'] === '1') ? '&force=1' : '';
    $gwRes = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'qr?session_id=' . urlencode($sessionId) . $forceParam);

    if ($gwRes && !empty($gwRes['qr_svg'])) {
        // Pre-register or update device record
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $assignedSql = (!$isAdmin && $currentUserId > 0) ? $currentUserId : "NULL";
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_device 
                                  (device_site_id, device_session_id, device_assigned_user_id, device_status, device_created_by)
                                  VALUES ({$siteId}, '{$escSession}', {$assignedSql}, 'qr_ready', {$currentUserId})
                                  ON DUPLICATE KEY UPDATE device_status = 'qr_ready'");

        echo json_encode([
            'status'     => 'success',
            'session_id' => $sessionId,
            'qr_data'    => $gwRes['qr_string'] ?? '',
            'qr_svg'     => $gwRes['qr_svg'],
            'expires_in' => $gwRes['expires_in'] ?? 30,
            'is_live'    => true,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    if ($gwRes && ($gwRes['status'] ?? '') === 'connected') {
        echo json_encode([
            'status'     => 'connected',
            'session_id' => $sessionId,
            'msg'        => 'Device is already connected.'
        ]);
        exit;
    }

    echo json_encode([
        'status'     => 'initializing',
        'session_id' => $sessionId,
        'msg'        => 'Generating live WhatsApp QR code, please wait...'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 4. ACTION: Request 8-Digit Pairing Code (Phone Number Linking)
// --------------------------------------------------------------------------
if ($action === 'pairing_code') {
    // Check sales agent constraint
    if (!$isAdmin && $hasAssignedDevice) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'A WhatsApp number is already assigned to your account. Sales agents can link only 1 device.'
        ]);
        exit;
    }

    $configs = getWaWebConfigs($connection, $siteId, $configKeys);
    $rawInput = file_get_contents('php://input');
    $postData = json_decode($rawInput, true) ?? [];
    $phone = $_POST['phone'] ?? $postData['phone'] ?? $_GET['phone'] ?? '';
    $sessionId = trim($_POST['session_id'] ?? $postData['session_id'] ?? $_GET['session_id'] ?? '');

    if (empty($sessionId)) {
        if (!$isAdmin) {
            $sessionId = 'agent_' . $currentUserId;
        } else {
            $isNewDevice = (!empty($_POST['new_device']) || !empty($postData['new_device']));
            $sessionId = $isNewDevice ? ('device_' . time()) : 'default';
        }
    }

    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Please enter your WhatsApp phone number with country code (e.g. +91 98765 43210).']);
        exit;
    }

    $gwRes = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'pairing-code', 'POST', [
        'session_id' => $sessionId,
        'phone'      => $phone
    ]);

    if ($gwRes && !empty($gwRes['pairing_code'])) {
        // Pre-register device in isw_whatsapp_device
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $assignedSql = (!$isAdmin && $currentUserId > 0) ? $currentUserId : "NULL";
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_device 
                                  (device_site_id, device_session_id, device_assigned_user_id, device_status, device_created_by)
                                  VALUES ({$siteId}, '{$escSession}', {$assignedSql}, 'qr_ready', {$currentUserId})
                                  ON DUPLICATE KEY UPDATE device_status = 'qr_ready'");

        echo json_encode([
            'status'       => 'success',
            'session_id'   => $sessionId,
            'pairing_code' => $gwRes['pairing_code'],
            'phone'        => $gwRes['phone'] ?? $phone,
            'msg'          => 'Pairing code generated successfully.'
        ]);
        exit;
    } else {
        $errMsg = $gwRes['message'] ?? 'Failed to request pairing code from WhatsApp gateway. Make sure daemon is running.';
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => $errMsg]);
        exit;
    }
}

// --------------------------------------------------------------------------
// 5. ACTION: Check Status / Polling for Device Connection
// --------------------------------------------------------------------------
if ($action === 'check_status' || $action === 'status') {
    $configs = getWaWebConfigs($connection, $siteId, $configKeys);
    $sessionId = trim($_GET['session_id'] ?? $_POST['session_id'] ?? '');

    if (empty($sessionId)) {
        if (!$isAdmin && $userAssignedDevice) {
            $sessionId = $userAssignedDevice['device_session_id'];
        } elseif (!$isAdmin) {
            $sessionId = 'agent_' . $currentUserId;
        } else {
            $sessionId = 'default';
        }
    }

    $gwStatus = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'status?session_id=' . urlencode($sessionId));
    $isConnected = false;
    $userPhone = '';
    $userName = '';

    if ($gwStatus && !empty($gwStatus['is_connected']) && !empty($gwStatus['user'])) {
        $isConnected = true;
        $userPhone = $gwStatus['user']['phone'] ?? '';
        $userName = $gwStatus['user']['name'] ?? '';
        $now = date('Y-m-d H:i:s');
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $escPhone = mysqli_real_escape_string($connection, $userPhone);
        $escName = mysqli_real_escape_string($connection, $userName);

        if ($sessionId === 'default') {
            saveWaWebConfig($connection, $siteId, 'whatsapp_web_status', 'connected');
            saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', $userPhone);
            saveWaWebConfig($connection, $siteId, 'whatsapp_web_pushname', $userName);
            saveWaWebConfig($connection, $siteId, 'whatsapp_web_last_sync', $now);
        }

        $assignedSql = (!$isAdmin && $currentUserId > 0) ? $currentUserId : "NULL";
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_device 
                                  (device_site_id, device_session_id, device_phone, device_name, device_assigned_user_id, device_status, device_last_sync)
                                  VALUES ({$siteId}, '{$escSession}', '{$escPhone}', '{$escName}', {$assignedSql}, 'connected', '{$now}')
                                  ON DUPLICATE KEY UPDATE device_phone = '{$escPhone}', device_name = '{$escName}', device_status = 'connected', device_last_sync = '{$now}'");
    }

    echo json_encode([
        'status'            => 'success',
        'session_id'        => $sessionId,
        'connection_status' => $isConnected ? 'connected' : ($gwStatus['connection_status'] ?? 'disconnected'),
        'phone'             => $userPhone ?: ($configs['whatsapp_web_phone'] ?? ''),
        'pushname'          => $userName ?: ($configs['whatsapp_web_pushname'] ?? ''),
        'last_sync'         => $now ?? date('Y-m-d H:i:s'),
        'can_link_device'   => $isAdmin ? true : (!$hasAssignedDevice && !$isConnected),
        'has_assigned_device' => $isAdmin ? false : ($hasAssignedDevice || $isConnected)
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 6. ACTION: Disconnect / Log Out Device
// --------------------------------------------------------------------------
if ($action === 'disconnect' || $action === 'delete_device') {
    $configs = getWaWebConfigs($connection, $siteId, $configKeys);
    $sessionId = trim($_GET['session_id'] ?? $_POST['session_id'] ?? '');

    if (empty($sessionId)) {
        if (!$isAdmin && $userAssignedDevice) {
            $sessionId = $userAssignedDevice['device_session_id'];
        } else {
            $sessionId = 'default';
        }
    }

    // Agent can only disconnect their own assigned device
    if (!$isAdmin && $userAssignedDevice && $userAssignedDevice['device_session_id'] !== $sessionId) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'msg' => 'Access denied. You can only disconnect your own assigned device.']);
        exit;
    }

    @callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'logout?session_id=' . urlencode($sessionId));

    $escSession = mysqli_real_escape_string($connection, $sessionId);
    mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}'");

    if ($sessionId === 'default') {
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_status', 'disconnected');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', '');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_pushname', '');
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'WhatsApp device disconnected successfully.'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 7. ACTION: Manual Sync Chats
// --------------------------------------------------------------------------
if ($action === 'sync_chats' || $action === 'sync') {
    $configs = getWaWebConfigs($connection, $siteId, $configKeys);
    $sessionId = trim($_GET['session_id'] ?? $_POST['session_id'] ?? 'default');

    if (!$isAdmin && $userAssignedDevice) {
        $sessionId = $userAssignedDevice['device_session_id'];
    }

    $gwRes = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'sync-chats?session_id=' . urlencode($sessionId));
    if ($gwRes && ($gwRes['status'] ?? '') === 'success') {
        $now = date('Y-m-d H:i:s');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_last_sync', $now);
        echo json_encode([
            'status'       => 'success',
            'session_id'   => $sessionId,
            'synced_chats' => $gwRes['synced_chats'] ?? 0,
            'msg'          => $gwRes['message'] ?? 'Chats synchronized successfully.',
            'last_sync'    => $now
        ]);
        exit;
    } else {
        $errMsg = $gwRes['message'] ?? 'Could not synchronize chats from gateway. Ensure WhatsApp is connected.';
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => $errMsg]);
        exit;
    }
}

// --------------------------------------------------------------------------
// 8. GET: Fetch Complete Plugin Configuration & Hub State
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $data = getWaWebConfigs($connection, $siteId, $configKeys);

    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $webhookUrl = "{$scheme}://{$host}/api/v3/whatsapp/webhook.php";

    echo json_encode([
        'status'              => 'success',
        'is_admin'            => $isAdmin,
        'user_role'           => $userRole,
        'current_user_id'     => $currentUserId,
        'has_assigned_device' => $hasAssignedDevice,
        'can_link_device'     => $canLink,
        'assigned_device'     => $userAssignedDevice,
        'data'                => $data,
        'webhook_url'         => $webhookUrl
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 9. POST / PUT: Save Plugin Settings & Toggles
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    foreach ($configKeys as $k) {
        if (isset($data[$k])) {
            $val = trim((string)$data[$k]);
            saveWaWebConfig($connection, $siteId, $k, $val);
        }
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'WhatsApp Web API configuration saved successfully.'
    ]);
    exit;
}
