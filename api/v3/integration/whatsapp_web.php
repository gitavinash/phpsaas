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
require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

initWhatsAppDatabase($connection);

$currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);
$userRole = isset($site_access_type) ? strtolower($site_access_type) : '';
$isAdmin = in_array($userRole, ['administrator', 'admin', 'owner', 'superadmin']);

if (!$isAdmin && $currentUserId > 0) {
    $saQ = mysqli_query($connection, "SELECT site_access_type FROM " . TABLE_PREFIX . "site_access WHERE site_access_site_id = {$siteId} AND site_access_user_id = {$currentUserId} AND site_access_status = 1 LIMIT 1");
    if ($saQ && mysqli_num_rows($saQ) > 0) {
        $saR = mysqli_fetch_assoc($saQ);
        $roleCheck = strtolower($saR['site_access_type'] ?? '');
        if (in_array($roleCheck, ['administrator', 'admin', 'owner', 'superadmin'])) {
            $isAdmin = true;
        }
        $userRole = $roleCheck;
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

// Resolve WhatsApp Device Limit
// If WHATSAPP_DEVICE_LIMIT key is defined then use the value, else default to 1 device.
$deviceLimit = 1;
if (defined('WHATSAPP_DEVICE_LIMIT')) {
    $deviceLimit = (int)WHATSAPP_DEVICE_LIMIT;
} elseif (getenv('WHATSAPP_DEVICE_LIMIT') !== false && getenv('WHATSAPP_DEVICE_LIMIT') !== '') {
    $deviceLimit = (int)getenv('WHATSAPP_DEVICE_LIMIT');
} else {
    $chkLimitQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND (site_configuration_key = 'whatsapp_device_limit' OR site_configuration_key = 'WHATSAPP_DEVICE_LIMIT') AND site_configuration_status = 1 LIMIT 1");
    if ($chkLimitQ && mysqli_num_rows($chkLimitQ) > 0) {
        $devRow = mysqli_fetch_assoc($chkLimitQ);
        if (!empty($devRow['site_configuration_value']) && is_numeric($devRow['site_configuration_value'])) {
            $deviceLimit = (int)$devRow['site_configuration_value'];
        }
    }
}
if ($deviceLimit <= 0) {
    $deviceLimit = 1;
}
if (!defined('WHATSAPP_DEVICE_LIMIT')) {
    define('WHATSAPP_DEVICE_LIMIT', $deviceLimit);
}

// Current linked device count for this site
$devCntQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId}");
$currentDeviceCount = 0;
if ($devCntQ && $dcRow = mysqli_fetch_assoc($devCntQ)) {
    $currentDeviceCount = (int)$dcRow['cnt'];
}
$isDeviceLimitReached = ($currentDeviceCount >= $deviceLimit);

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
    'whatsapp_web_last_sync',
    'whatsapp_device_limit',
    'whatsapp_default_device_id',
    'whatsapp_disconnect_alert'
];

function getWaWebConfigs($connection, $siteId, $keys) {
    global $deviceLimit;
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
        'whatsapp_web_device'           => 'Aleya.in',
        'whatsapp_web_session_id'       => '',
        'whatsapp_web_sync_incoming'    => '1',
        'whatsapp_web_auto_create_lead' => '1',
        'whatsapp_web_gateway_url'      => '',
        'whatsapp_web_last_sync'        => '',
        'whatsapp_device_limit'         => (string)($deviceLimit ?? 1),
        'whatsapp_default_device_id'    => '0'
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
    $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook/';
    $siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

    $paramsToAdd = [];
    if (strpos($target, 'webhook_url=') === false) {
        $paramsToAdd[] = 'webhook_url=' . urlencode($dynamicWebhookUrl);
    }
    if ($siteId > 0 && strpos($target, 'site_id=') === false) {
        $paramsToAdd[] = 'site_id=' . $siteId;
    }
    if (!empty($paramsToAdd)) {
        $separator = (strpos($target, '?') !== false) ? '&' : '?';
        $target .= $separator . implode('&', $paramsToAdd);
    }

    if ($body !== null && is_array($body)) {
        if (!isset($body['webhook_url'])) $body['webhook_url'] = $dynamicWebhookUrl;
        if (!isset($body['site_id'])) $body['site_id'] = $siteId;
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

    // Purge accidental cross-site duplicated devices if any exist
    @mysqli_query($connection, "DELETE d2 FROM " . TABLE_PREFIX . "whatsapp_device d2
        INNER JOIN " . TABLE_PREFIX . "whatsapp_device d1 
            ON (d1.device_phone = d2.device_phone OR d1.device_session_id = d2.device_session_id)
            AND d1.device_site_id != d2.device_site_id
            AND d1.device_id < d2.device_id
        WHERE d2.device_site_id = {$siteId}");

    // Sync live device statuses from gateway in the background ONLY for devices that legitimately belong to this site
    $siteDevsQ = mysqli_query($connection, "SELECT device_id, device_session_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId}");
    $siteSessionIds = [];
    if ($siteDevsQ) {
        while ($sRow = mysqli_fetch_assoc($siteDevsQ)) {
            $siteSessionIds[] = $sRow['device_session_id'];
        }
    }

    if (!empty($siteSessionIds)) {
        $gwDevices = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'devices?site_id=' . $siteId);
        if ($gwDevices && ($gwDevices['status'] ?? '') === 'success' && is_array($gwDevices['devices'] ?? null)) {
            foreach ($gwDevices['devices'] as $gDev) {
                $sess = $gDev['session_id'] ?? '';
                if (in_array($sess, $siteSessionIds, true)) {
                    $escSess = mysqli_real_escape_string($connection, $sess);
                    $isConn = !empty($gDev['is_connected']) || ($gDev['connection_status'] ?? '') === 'connected';
                    $hasQr  = !empty($gDev['has_qr']) || ($gDev['connection_status'] ?? '') === 'qr_ready';
                    $stat   = $isConn ? 'connected' : ($hasQr ? 'qr_ready' : 'disconnected');
                    $ph     = mysqli_real_escape_string($connection, $gDev['user']['phone'] ?? '');
                    $nm   = mysqli_real_escape_string($connection, $gDev['user']['name'] ?? 'WhatsApp Device');
                    $sync = !empty($gDev['last_sync']) ? date('Y-m-d H:i:s', strtotime($gDev['last_sync'])) : date('Y-m-d H:i:s');

                    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device 
                                              SET device_phone = IF('{$ph}' != '', '{$ph}', device_phone),
                                                  device_name = IF('{$nm}' != '', '{$nm}', device_name),
                                                  device_status = '{$stat}',
                                                  device_last_sync = '{$sync}'
                                              WHERE device_site_id = {$siteId} AND device_session_id = '{$escSess}'");
                }
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
                $isExplicitDefault = !empty($r['device_is_default']) && (int)$r['device_is_default'] === 1;
                $isDefault = $isExplicitDefault || ($currentDeviceCount === 1);
                $devices[] = [
                    'device_id'           => (int)$r['device_id'],
                    'session_id'          => $r['device_session_id'],
                    'phone'               => $r['device_phone'] ?: '',
                    'name'                => $r['device_name'] ?: 'WhatsApp Device',
                    'assigned_user_id'    => $r['device_assigned_user_id'] ? (int)$r['device_assigned_user_id'] : null,
                    'assigned_agent_name' => $r['assigned_agent_name'] ?: ($r['user_username'] ?: 'Unassigned'),
                    'assigned_agent_role' => $r['assigned_agent_role'] ?: '',
                    'status'              => $r['device_status'],
                    'is_default'          => $isDefault,
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
            $isAgentExplicitDefault = !empty($userAssignedDevice['device_is_default']) && (int)$userAssignedDevice['device_is_default'] === 1;
            $devices[] = [
                'device_id'           => (int)$userAssignedDevice['device_id'],
                'session_id'          => $userAssignedDevice['device_session_id'],
                'phone'               => $userAssignedDevice['device_phone'] ?: '',
                'name'                => $userAssignedDevice['device_name'] ?: 'Aleya.in',
                'assigned_user_id'    => (int)$userAssignedDevice['device_assigned_user_id'],
                'assigned_agent_name' => 'Your Assigned Device',
                'status'              => $userAssignedDevice['device_status'],
                'is_default'          => $isAgentExplicitDefault || ($currentDeviceCount === 1),
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
        'device_limit'        => $deviceLimit,
        'device_count'        => $currentDeviceCount,
        'can_add_device'      => ($currentDeviceCount < $deviceLimit),
        'limit_reached'       => $isDeviceLimitReached,
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
        'disconnect_alert'    => !empty($configs['whatsapp_disconnect_alert']) ? json_decode($configs['whatsapp_disconnect_alert'], true) : null,
        'is_active'           => $configs['enable_whatsapp_web_api'] === '1'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 1.5 ACTION: Dismiss WhatsApp Disconnection Alert
// --------------------------------------------------------------------------
if ($action === 'dismiss_disconnect_alert') {
    mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_disconnect_alert'");
    echo json_encode([
        'status' => 'success',
        'msg'    => 'WhatsApp disconnection alert dismissed.'
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
// 2b. ACTION: Mark WhatsApp Device as Default Communication Line
// --------------------------------------------------------------------------
if ($action === 'set_default_device' || $action === 'set_default') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Access denied. Only administrators have permission to configure the default communication device.'
        ]);
        exit;
    }

    $rawInput = file_get_contents('php://input');
    $postData = json_decode($rawInput, true) ?? [];
    $deviceId = (int)($_POST['device_id'] ?? $postData['device_id'] ?? $_GET['device_id'] ?? 0);

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

    $targetDev = mysqli_fetch_assoc($devCheck);

    // Reset default on all devices for this site
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device SET device_is_default = 0 WHERE device_site_id = {$siteId}");

    // Set default on chosen device
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device SET device_is_default = 1 WHERE device_id = {$deviceId} AND device_site_id = {$siteId}");

    // Persist to site configuration
    saveWaWebConfig($connection, $siteId, 'whatsapp_default_device_id', (string)$deviceId);
    if (!empty($targetDev['device_phone'])) {
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', $targetDev['device_phone']);
    }
    if (!empty($targetDev['device_session_id'])) {
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_session_id', $targetDev['device_session_id']);
    }

    echo json_encode([
        'status'    => 'success',
        'msg'       => 'Device marked as default for customer alerts and notifications.',
        'device_id' => $deviceId
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2c. ACTION: Get Default Communication Device
// --------------------------------------------------------------------------
if ($action === 'get_default_device' || $action === 'default_device') {
    // Priority: Sender Assigned Device > Site Default Device > Single Connected Device
    $devFound = resolveSenderWhatsAppDevice($connection, $siteId, $currentUserId);
    $isConnected = $devFound && ($devFound['device_status'] === 'connected');

    echo json_encode([
        'status'       => 'success',
        'has_device'   => ($devFound !== null),
        'is_connected' => $isConnected,
        'device'       => $devFound ? [
            'device_id'   => (int)$devFound['device_id'],
            'session_id'  => $devFound['device_session_id'],
            'phone'       => $devFound['device_phone'] ?: '',
            'name'        => $devFound['device_name'] ?: 'WhatsApp Device',
            'status'      => $devFound['device_status'],
            'is_default'  => !empty($devFound['device_is_default']) || ($currentDeviceCount === 1),
            'last_sync'   => $devFound['device_last_sync']
        ] : null
    ]);
    exit;
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
            $sessionId = 'site_' . $siteId . '_agent_' . $currentUserId;
        } else {
            $isNewDevice = (!empty($_GET['new_device']) || !empty($_POST['new_device']) || !empty($postData['new_device']));
            if ($isNewDevice) {
                $sessionId = 'site_' . $siteId . '_device_' . time();
            } else {
                $existDef = mysqli_query($connection, "SELECT device_session_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_session_id = 'default' OR device_session_id = 'site_{$siteId}_default') LIMIT 1");
                if ($existDef && mysqli_num_rows($existDef) > 0) {
                    $sessionId = mysqli_fetch_assoc($existDef)['device_session_id'];
                } else {
                    $sessionId = 'site_' . $siteId . '_default';
                }
            }
        }
    } else {
        $chkSess = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '" . mysqli_real_escape_string($connection, $sessionId) . "' LIMIT 1");
        if ((!$chkSess || mysqli_num_rows($chkSess) === 0) && !str_starts_with($sessionId, 'site_' . $siteId . '_')) {
            $sessionId = 'site_' . $siteId . '_' . $sessionId;
        }
    }

    // Check device limit: if adding a new device when limit reached, block with "Buy more license" error
    $isExistingDevice = false;
    if (!empty($sessionId)) {
        $escSess = mysqli_real_escape_string($connection, $sessionId);
        $rawSess = preg_replace('/^site_\d+_/', '', $sessionId);
        $escRaw = mysqli_real_escape_string($connection, $rawSess);
        $chkExistQ = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_session_id = '{$escSess}' OR device_session_id = '{$escRaw}' OR device_session_id LIKE '%_{$escRaw}') LIMIT 1");
        if ($chkExistQ && mysqli_num_rows($chkExistQ) > 0) {
            $isExistingDevice = true;
        }
    }

    if (!$isExistingDevice && $isDeviceLimitReached) {
        http_response_code(403);
        echo json_encode([
            'status'        => 'error',
            'limit_reached' => true,
            'device_limit'  => $deviceLimit,
            'device_count'  => $currentDeviceCount,
            'msg'           => "WhatsApp device limit reached ({$currentDeviceCount}/{$deviceLimit}). Please buy more licenses to connect additional WhatsApp devices."
        ]);
        exit;
    }

    $forceParam = (!empty($_GET['force']) && $_GET['force'] === '1') ? '&force=1' : '';
    $gwRes = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'qr?session_id=' . urlencode($sessionId) . $forceParam);

    if ($gwRes && !empty($gwRes['qr_svg'])) {
        // Do not pre-insert into whatsapp_device until device is connected

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
            $sessionId = 'site_' . $siteId . '_agent_' . $currentUserId;
        } else {
            $isNewDevice = (!empty($_POST['new_device']) || !empty($postData['new_device']));
            if ($isNewDevice) {
                $sessionId = 'site_' . $siteId . '_device_' . time();
            } else {
                $existDef = mysqli_query($connection, "SELECT device_session_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_session_id = 'default' OR device_session_id = 'site_{$siteId}_default') LIMIT 1");
                if ($existDef && mysqli_num_rows($existDef) > 0) {
                    $sessionId = mysqli_fetch_assoc($existDef)['device_session_id'];
                } else {
                    $sessionId = 'site_' . $siteId . '_default';
                }
            }
        }
    } else {
        $chkSess = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '" . mysqli_real_escape_string($connection, $sessionId) . "' LIMIT 1");
        if ((!$chkSess || mysqli_num_rows($chkSess) === 0) && !str_starts_with($sessionId, 'site_' . $siteId . '_')) {
            $sessionId = 'site_' . $siteId . '_' . $sessionId;
        }
    }

    // Check device limit: if adding a new device when limit reached, block with "Buy more license" error
    $isExistingDevice = false;
    if (!empty($sessionId)) {
        $escSess = mysqli_real_escape_string($connection, $sessionId);
        $rawSess = preg_replace('/^site_\d+_/', '', $sessionId);
        $escRaw = mysqli_real_escape_string($connection, $rawSess);
        $chkExistQ = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_session_id = '{$escSess}' OR device_session_id = '{$escRaw}' OR device_session_id LIKE '%_{$escRaw}') LIMIT 1");
        if ($chkExistQ && mysqli_num_rows($chkExistQ) > 0) {
            $isExistingDevice = true;
        }
    }

    if (!$isExistingDevice && $isDeviceLimitReached) {
        http_response_code(403);
        echo json_encode([
            'status'        => 'error',
            'limit_reached' => true,
            'device_limit'  => $deviceLimit,
            'device_count'  => $currentDeviceCount,
            'msg'           => "WhatsApp device limit reached ({$currentDeviceCount}/{$deviceLimit}). Please buy more licenses to connect additional WhatsApp devices."
        ]);
        exit;
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
        // Do not pre-insert into whatsapp_device until device is connected

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

    $reqPhone = trim((string)($_GET['phone'] ?? $_POST['phone'] ?? ''));
    $reqName = trim((string)($_GET['name'] ?? $_POST['name'] ?? ''));
    $reqLeadId = trim((string)($_GET['lead_id'] ?? $_POST['lead_id'] ?? ''));
    $reqDest = trim((string)($_GET['destination'] ?? $_POST['destination'] ?? ''));
    $waAction = resolveWhatsAppLeadAction($connection, $siteId, $currentUserId, $site_access_type ?? '', $reqPhone, $reqName, $reqLeadId, $reqDest);

    // Case 1: General connection check (no specific session_id requested by client)
    if (empty($sessionId)) {
        $targetDev = null;
        if (!$isAdmin && $currentUserId > 0) {
            $uadQ = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} LIMIT 1");
            if ($uadQ && mysqli_num_rows($uadQ) > 0) {
                $targetDev = mysqli_fetch_assoc($uadQ);
            } else {
                // Non-admin has NO assigned WhatsApp device: Strictly return disconnected with notice
                echo json_encode(array_merge([
                    'status'              => 'success',
                    'session_id'          => '',
                    'connection_status'   => 'disconnected',
                    'is_device_linked'    => false,
                    'phone'               => '',
                    'pushname'            => '',
                    'last_sync'           => '',
                    'can_link_device'     => false,
                    'has_assigned_device' => false,
                    'is_admin'            => false,
                    'msg'                 => 'No WhatsApp device has been assigned to your user account. Please contact an administrator to assign a device to your profile.'
                ], $waAction));
                exit;
            }
        } else {
            $adQ = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_id DESC LIMIT 1");
            if ($adQ && mysqli_num_rows($adQ) > 0) {
                $targetDev = mysqli_fetch_assoc($adQ);
            }
            if (!$targetDev) {
                $instDevQ = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device WHERE (device_status = 'connected' OR (device_phone IS NOT NULL AND device_phone != '')) ORDER BY (device_status = 'connected') DESC, device_id DESC LIMIT 1");
                if ($instDevQ && mysqli_num_rows($instDevQ) > 0) {
                    $targetDev = mysqli_fetch_assoc($instDevQ);
                }
            }
        }

        if (!$targetDev) {
            // This site has NO device linked! Return disconnected. NEVER insert or adopt anything!
            echo json_encode(array_merge([
                'status'              => 'success',
                'session_id'          => '',
                'connection_status'   => 'disconnected',
                'is_device_linked'    => false,
                'phone'               => '',
                'pushname'            => '',
                'last_sync'           => '',
                'can_link_device'     => $canLink,
                'has_assigned_device' => $hasAssignedDevice,
                'is_admin'            => $isAdmin
            ], $waAction));
            exit;
        }

        $sessionId = $targetDev['device_session_id'];
        $gwStatus = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'status?session_id=' . urlencode($sessionId) . '&site_id=' . $siteId);
        $isLiveConn = $gwStatus && (!empty($gwStatus['is_connected']) || ($gwStatus['connection_status'] ?? '') === 'connected');
        $now = date('Y-m-d H:i:s');
        $statusStr = $isLiveConn ? 'connected' : 'disconnected';

        if ($isLiveConn) {
            $userPhone = $gwStatus['user']['phone'] ?? $targetDev['device_phone'];
            $userName = $gwStatus['user']['name'] ?? $targetDev['device_name'];
            $escPhone = mysqli_real_escape_string($connection, $userPhone);
            $escName = mysqli_real_escape_string($connection, $userName);
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device 
                                      SET device_phone = IF('{$escPhone}' != '', '{$escPhone}', device_phone),
                                          device_name = IF('{$escName}' != '', '{$escName}', device_name),
                                          device_status = 'connected',
                                          device_last_sync = '{$now}'
                                      WHERE device_id = {$targetDev['device_id']} AND device_site_id = {$siteId}");
        }

        echo json_encode(array_merge([
            'status'              => 'success',
            'session_id'          => $sessionId,
            'connection_status'   => $statusStr,
            'is_device_linked'    => ($statusStr === 'connected'),
            'phone'               => $targetDev['device_phone'] ?: '',
            'pushname'            => $targetDev['device_name'] ?: 'WhatsApp Web',
            'last_sync'           => $targetDev['device_last_sync'] ?: $now,
            'can_link_device'     => $canLink,
            'has_assigned_device' => $hasAssignedDevice,
            'is_admin'            => $isAdmin
        ], $waAction));
        exit;
    }

    // Case 2: Specific session_id polled (e.g. actively pairing/linking in the modal)
    // Strictly verify that this session belongs to this site
    $matchesSite = str_starts_with($sessionId, 'site_' . $siteId . '_');
    if (!$matchesSite) {
        $chkS = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '" . mysqli_real_escape_string($connection, $sessionId) . "' LIMIT 1");
        if ($chkS && mysqli_num_rows($chkS) > 0) {
            $matchesSite = true;
        }
    }

    if (!$matchesSite) {
        // If it starts with site_{OTHER_ID}_, it belongs to another site - reject!
        if (preg_match('/^site_(\d+)_/', $sessionId, $m) && (int)$m[1] !== (int)$siteId) {
            echo json_encode([
                'status'            => 'error',
                'session_id'        => $sessionId,
                'connection_status' => 'disconnected',
                'is_device_linked'  => false,
                'msg'               => 'Session does not belong to current site.'
            ]);
            exit;
        }
        // If it's a relative session id like device_xxx or agent_xxx, auto-prefix with current siteId
        if (!str_starts_with($sessionId, 'site_')) {
            $sessionId = 'site_' . $siteId . '_' . $sessionId;
            $matchesSite = true;
        } else {
            echo json_encode([
                'status'            => 'error',
                'session_id'        => $sessionId,
                'connection_status' => 'disconnected',
                'is_device_linked'  => false,
                'msg'               => 'Session does not belong to current site.'
            ]);
            exit;
        }
    }

    $gwStatus = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'status?session_id=' . urlencode($sessionId) . '&site_id=' . $siteId);
    $isConnected = $gwStatus && (!empty($gwStatus['is_connected']) || !empty($gwStatus['is_linked']) || ($gwStatus['connection_status'] ?? '') === 'connected');
    $userPhone = $gwStatus['user']['phone'] ?? '';
    $userName = $gwStatus['user']['name'] ?? '';

    // Check if database already recorded this session as connected on this site (e.g. via webhook or async open)
    if (!$isConnected) {
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $rawSession = preg_replace('/^site_\d+_/', '', $sessionId);
        $escRaw = mysqli_real_escape_string($connection, $rawSession);
        $dbCheckQ = mysqli_query($connection, "SELECT device_session_id, device_phone, device_name, device_last_sync, device_status 
                                               FROM " . TABLE_PREFIX . "whatsapp_device 
                                               WHERE device_site_id = {$siteId} 
                                                 AND (device_session_id = '{$escSession}' OR device_session_id = '{$escRaw}' OR device_session_id LIKE '%_{$escRaw}') 
                                                 AND device_status = 'connected' 
                                               LIMIT 1");
        if ($dbCheckQ && mysqli_num_rows($dbCheckQ) > 0) {
            $dbRow = mysqli_fetch_assoc($dbCheckQ);
            $isConnected = true;
            $sessionId = $dbRow['device_session_id'];
            $userPhone = !empty($dbRow['device_phone']) ? $dbRow['device_phone'] : $userPhone;
            $userName = !empty($dbRow['device_name']) ? $dbRow['device_name'] : $userName;
            $now = !empty($dbRow['device_last_sync']) ? $dbRow['device_last_sync'] : date('Y-m-d H:i:s');
        }
    }

    if ($isConnected) {
        $now = date('Y-m-d H:i:s');
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $escPhone = mysqli_real_escape_string($connection, $userPhone);
        $escName = mysqli_real_escape_string($connection, $userName ?: 'WhatsApp Device');
        $assignedSql = (!$isAdmin && $currentUserId > 0) ? $currentUserId : "NULL";

        // Enforce device limit before inserting new device
        $chkDevExists = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}' LIMIT 1");
        $devAlreadyInDb = ($chkDevExists && mysqli_num_rows($chkDevExists) > 0);

        if (!$devAlreadyInDb && $isDeviceLimitReached) {
            echo json_encode([
                'status'            => 'error',
                'limit_reached'     => true,
                'device_limit'      => $deviceLimit,
                'device_count'      => $currentDeviceCount,
                'connection_status' => 'disconnected',
                'is_device_linked'  => false,
                'msg'               => "WhatsApp device limit reached ({$currentDeviceCount}/{$deviceLimit}). Please buy more licenses to connect additional WhatsApp devices."
            ]);
            exit;
        }

        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_device 
                                  (device_site_id, device_session_id, device_phone, device_name, device_assigned_user_id, device_status, device_last_sync)
                                  VALUES ({$siteId}, '{$escSession}', '{$escPhone}', '{$escName}', {$assignedSql}, 'connected', '{$now}')
                                  ON DUPLICATE KEY UPDATE 
                                      device_phone = IF('{$escPhone}' != '', '{$escPhone}', device_phone),
                                      device_name = IF('{$escName}' != '', '{$escName}', device_name),
                                      device_status = 'connected',
                                      device_last_sync = '{$now}'");

        saveWaWebConfig($connection, $siteId, 'whatsapp_web_status', 'connected');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_last_sync', $now);
        ensureFirstDeviceIsDefault($connection, $siteId, $escSession);

        $chkIsDef = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}' AND device_is_default = 1 LIMIT 1");
        $isDefDev = ($chkIsDef && mysqli_num_rows($chkIsDef) > 0);

        $chkCurPh = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_phone' LIMIT 1");
        $curPhVal = ($chkCurPh && $cp = mysqli_fetch_assoc($chkCurPh)) ? trim($cp['site_configuration_value']) : '';

        if (($isDefDev || empty($curPhVal)) && !empty($userPhone)) {
            saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', $userPhone);
            if (!empty($userName)) {
                saveWaWebConfig($connection, $siteId, 'whatsapp_web_pushname', $userName);
            }
        }
    }

    echo json_encode([
        'status'            => 'success',
        'session_id'        => $sessionId,
        'connection_status' => $isConnected ? 'connected' : ($gwStatus['connection_status'] ?? 'disconnected'),
        'is_device_linked'  => $isConnected,
        'is_connected'      => $isConnected,
        'phone'             => $userPhone,
        'pushname'          => $userName,
        'last_sync'         => $now ?? date('Y-m-d H:i:s'),
        'can_link_device'   => $isAdmin ? true : (!$hasAssignedDevice && !$isConnected),
        'has_assigned_device' => $isAdmin ? false : ($hasAssignedDevice || $isConnected)
    ]);
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

    $devId = (int)($_GET['device_id'] ?? $_POST['device_id'] ?? 0);
    $escSession = mysqli_real_escape_string($connection, $sessionId);
    if ($devId > 0) {
        mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_id = {$devId}");
    }
    if (!empty($escSession)) {
        mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}'");
    }
    if (!$isAdmin && $currentUserId > 0) {
        mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId}");
    }

    // Update site-level config based on whether any connected device remains
    $remDevQ = mysqli_query($connection, "SELECT device_phone, device_name FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_is_default DESC, device_id ASC LIMIT 1");
    if ($remDevQ && mysqli_num_rows($remDevQ) > 0) {
        $remDev = mysqli_fetch_assoc($remDevQ);
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_status', 'connected');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', $remDev['device_phone']);
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_pushname', $remDev['device_name']);
    } else {
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_status', 'disconnected');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_phone', '');
        saveWaWebConfig($connection, $siteId, 'whatsapp_web_pushname', '');
    }

    // Clear any previous disconnect alert on intentional manual disconnect
    mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_disconnect_alert'");

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
// 7b. ACTION: Repair Corrupted Signal Ratchets (Bad MAC Fix)
// --------------------------------------------------------------------------
if ($action === 'repair_sessions' || $action === 'repair') {
    $configs = getWaWebConfigs($connection, $siteId, $configKeys);
    $gwRes = callWaGateway($configs['whatsapp_web_gateway_url'] ?? '', 'repair-sessions');
    if ($gwRes && ($gwRes['status'] ?? '') === 'success') {
        echo json_encode([
            'status'  => 'success',
            'msg'     => $gwRes['message'] ?? 'Signal session ratchets repaired successfully. WhatsApp encryption keys refreshed.',
            'details' => $gwRes
        ]);
        exit;
    } else {
        echo json_encode([
            'status'  => 'error',
            'msg'     => $gwRes['message'] ?? 'Could not contact WhatsApp gateway to repair sessions.'
        ]);
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
        'device_limit'        => $deviceLimit,
        'device_count'        => $currentDeviceCount,
        'can_add_device'      => ($currentDeviceCount < $deviceLimit),
        'limit_reached'       => $isDeviceLimitReached,
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
