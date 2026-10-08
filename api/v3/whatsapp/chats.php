<?php

/**
 * V3 WhatsApp CRM Conversations API
 * Path: /api/v3/whatsapp/chats.php
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

require_once __DIR__ . '/db_init.php';
initWhatsAppDatabase($connection);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// --------------------------------------------------------------------------
// 1. GET: Fetch List of WhatsApp Chat Conversations
// --------------------------------------------------------------------------
if ($method === 'GET') {
    if ($action === 'sync') {
        $currentUserId = defined('USER_ID') ? (int)USER_ID : 0;
        $sessParam = '';
        if ($currentUserId > 0) {
            $devQ = mysqli_query($connection, "SELECT device_session_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
            if ($devQ && mysqli_num_rows($devQ) > 0) {
                $devR = mysqli_fetch_assoc($devQ);
                $sessParam = '?session_id=' . urlencode($devR['device_session_id']);
            }
        }

        if (empty($sessParam)) {
            $anyDevQ = mysqli_query($connection, "SELECT device_session_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_id DESC LIMIT 1");
            if ($anyDevQ && mysqli_num_rows($anyDevQ) > 0) {
                $anyDevR = mysqli_fetch_assoc($anyDevQ);
                $sessParam = '?session_id=' . urlencode($anyDevR['device_session_id']);
            }
        }

        $sep = empty($sessParam) ? '?' : '&';
        $sessParam .= $sep . 'site_id=' . $siteId;

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $scheme = $isHttps ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook/';
        $sessParam .= '&webhook_url=' . urlencode($dynamicWebhookUrl);

        $gwUrl = 'http://127.0.0.1:3001/sync-chats' . $sessParam;
        $gwQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_gateway_url' LIMIT 1");
        if ($gwQ && mysqli_num_rows($gwQ) > 0) {
            $gwRow = mysqli_fetch_assoc($gwQ);
            if (!empty($gwRow['site_configuration_value'])) {
                $gwUrl = rtrim($gwRow['site_configuration_value'], '/') . '/sync-chats' . $sessParam;
            }
        }
        $ctx = stream_context_create(['http' => ['method' => 'POST', 'timeout' => 5, 'ignore_errors' => true]]);
        @file_get_contents($gwUrl, false, $ctx);
    }

    $q = trim($_GET['q'] ?? '');
    $filter = trim($_GET['filter'] ?? 'all'); // 'all', 'unread', 'leads'

    // Determine current user and role
    $currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);
    $userRole = isset($site_access_type) ? strtolower($site_access_type) : '';
    $isAdmin = in_array($userRole, ['administrator', 'admin', 'owner', 'superadmin']);

    if (!$isAdmin && $currentUserId > 0) {
        $saQ = mysqli_query($connection, "SELECT site_access_type FROM " . TABLE_PREFIX . "site_access WHERE site_access_site_id = {$siteId} AND site_access_user_id = {$currentUserId} AND site_access_status = 1 LIMIT 1");
        if ($saQ && mysqli_num_rows($saQ) > 0) {
            $saR = mysqli_fetch_assoc($saQ);
            if (in_array(strtolower($saR['site_access_type'] ?? ''), ['administrator', 'admin', 'owner', 'superadmin'])) {
                $isAdmin = true;
            }
        }
    }

    $isDeviceLinked = false;
    $linkedPhone = '';
    $agentDevicePhone = '';

    // Check device connectivity based on role
    if (!$isAdmin) {
        // Sales Agent: Check their assigned connected device
        if ($currentUserId > 0) {
            $devAssigned = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
            if ($devAssigned && mysqli_num_rows($devAssigned) > 0) {
                $devRow = mysqli_fetch_assoc($devAssigned);
                $isDeviceLinked = true;
                $linkedPhone = $devRow['device_phone'] ?? '';
                $agentDevicePhone = $linkedPhone;
            }
        }

        // Rule: When current number is disconnected, remove the current number chat from chatbox
        if (!$isDeviceLinked) {
            echo json_encode([
                'status'           => 'success',
                'is_device_linked' => false,
                'linked_phone'     => '',
                'is_admin'         => false,
                'chats'            => [],
                'count'            => 0
            ]);
            exit;
        }
    } else {
        // Administrator: Check if ANY device on this site is connected
        $devAny = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_id DESC LIMIT 1");
        if ($devAny && mysqli_num_rows($devAny) > 0) {
            $devRow = mysqli_fetch_assoc($devAny);
            $isDeviceLinked = true;
            $linkedPhone = $devRow['device_phone'] ?? 'All Devices';
        }

        // Fallback: Check site_configuration if whatsapp_web_status = 'connected'
        if (!$isDeviceLinked) {
            $cfgCheck = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key IN ('whatsapp_web_status', 'whatsapp_web_phone')");
            $cfgs = [];
            if ($cfgCheck) {
                while ($cRow = mysqli_fetch_assoc($cfgCheck)) {
                    $cfgs[$cRow['site_configuration_key']] = $cRow['site_configuration_value'];
                }
            }
            if (($cfgs['whatsapp_web_status'] ?? '') === 'connected' && !empty($cfgs['whatsapp_web_phone'])) {
                $isDeviceLinked = true;
                $linkedPhone = $cfgs['whatsapp_web_phone'];
            }
        }

        if (!$isDeviceLinked) {
            echo json_encode([
                'status'           => 'success',
                'is_device_linked' => false,
                'linked_phone'     => '',
                'is_admin'         => true,
                'chats'            => [],
                'count'            => 0
            ]);
            exit;
        }
    }

    // Clean up connected device phone numbers from appearing in chat inbox
    $devPhoneQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_phone IS NOT NULL AND device_phone != ''");
    $devPhones = [];
    $allActiveDevPhones = [];
    if ($devPhoneQ) {
        while ($dpRow = mysqli_fetch_assoc($devPhoneQ)) {
            $rawP = preg_replace('/[^0-9]/', '', $dpRow['device_phone'] ?? '');
            if (!empty($rawP)) {
                $devPhones[] = "'" . mysqli_real_escape_string($connection, '+' . $rawP) . "'";
                $devPhones[] = "'" . mysqli_real_escape_string($connection, $rawP) . "'";
                $allActiveDevPhones[] = '+' . $rawP;
            }
        }
    }

    // Auto-heal: Purge connected device's own chat rows if they were previously created
    if (!empty($devPhones)) {
        mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND chat_phone IN (" . implode(',', array_unique($devPhones)) . ")");
    }

    // Auto-heal: Purge ghost LID chats with no messages or empty text
    mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "whatsapp_chat 
        WHERE chat_site_id = {$siteId} 
          AND (chat_phone REGEXP '^\\\\+?[0-9]{13,17}$' OR chat_last_message IS NULL OR chat_last_message = '')
          AND (chat_lead_id IS NULL OR chat_lead_id = 0)
          AND chat_id NOT IN (SELECT DISTINCT message_chat_id FROM " . TABLE_PREFIX . "whatsapp_message WHERE message_site_id = {$siteId})");

    // Auto-heal: Populate chat_device_phone from messages for existing chats where missing
    @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat c
        JOIN (
            SELECT message_chat_id, 
                   MAX(IF(message_direction = 'outgoing', message_sender, message_receiver)) as dev_phone
            FROM " . TABLE_PREFIX . "whatsapp_message
            WHERE message_site_id = {$siteId}
            GROUP BY message_chat_id
        ) m_dev ON m_dev.message_chat_id = c.chat_id
        SET c.chat_device_phone = m_dev.dev_phone
        WHERE c.chat_site_id = {$siteId} AND (c.chat_device_phone IS NULL OR c.chat_device_phone = '') AND m_dev.dev_phone IS NOT NULL AND m_dev.dev_phone != ''");

    // Auto-heal: If chat_device_phone is still empty, populate with primary connected device
    if (!empty($allActiveDevPhones[0])) {
        $escPrimaryDev = mysqli_real_escape_string($connection, $allActiveDevPhones[0]);
        @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat SET chat_device_phone = '{$escPrimaryDev}' WHERE chat_site_id = {$siteId} AND (chat_device_phone IS NULL OR chat_device_phone = '')");
    }

    // Auto-heal: Synchronize chat_last_message and chat_last_message_time with the latest message in message table
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat c
        JOIN (
            SELECT message_chat_id, message_text, message_created_at
            FROM " . TABLE_PREFIX . "whatsapp_message
            WHERE message_id IN (
                SELECT MAX(message_id)
                FROM " . TABLE_PREFIX . "whatsapp_message
                WHERE message_site_id = {$siteId}
                GROUP BY message_chat_id
            )
        ) latest ON latest.message_chat_id = c.chat_id
        SET c.chat_last_message = IF(latest.message_text != '', latest.message_text, c.chat_last_message),
            c.chat_last_message_time = latest.message_created_at
        WHERE c.chat_site_id = {$siteId}
          AND (c.chat_last_message_time IS NULL OR c.chat_last_message_time < latest.message_created_at OR c.chat_last_message IS NULL OR c.chat_last_message = '')");

    $whereClauses = ["c.chat_site_id = {$siteId}"];

    // Role-based Chat Filtering:
    // Sales Agent sees only current number chats or leads assigned to them.
    // Admin sees all devices' chats.
    if (!$isAdmin && !empty($agentDevicePhone)) {
        $cleanAgentDigits = preg_replace('/[^0-9]/', '', $agentDevicePhone);
        $last10Agent = substr($cleanAgentDigits, -10);
        $escAgentDev = mysqli_real_escape_string($connection, $agentDevicePhone);
        $whereClauses[] = "(c.chat_device_phone = '{$escAgentDev}' OR c.chat_device_phone LIKE '%{$last10Agent}' OR c.chat_lead_id IN (SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND (enquiry_agent = {$currentUserId} OR enquiry_user_id = {$currentUserId})))";
    }

    if (!empty($devPhones)) {
        $whereClauses[] = "c.chat_phone NOT IN (" . implode(',', array_unique($devPhones)) . ")";
    }

    // Exclude unmapped ghost LID numbers from chat list only if they have no messages
    $whereClauses[] = "(c.chat_phone NOT REGEXP '^\\\\+?[0-9]{13,17}$' OR (c.chat_last_message IS NOT NULL AND c.chat_last_message != ''))";
    $whereClauses[] = "((c.chat_last_message IS NOT NULL AND c.chat_last_message != '') OR (c.chat_lead_id IS NOT NULL AND c.chat_lead_id > 0))";

    if (!empty($q)) {
        $escQ = mysqli_real_escape_string($connection, $q);
        $whereClauses[] = "(c.chat_phone LIKE '%{$escQ}%' OR c.chat_name LIKE '%{$escQ}%' OR c.chat_last_message LIKE '%{$escQ}%')";
    }

    if ($filter === 'unread') {
        $whereClauses[] = "c.chat_unread_count > 0";
    } elseif ($filter === 'leads') {
        $whereClauses[] = "c.chat_lead_id IS NOT NULL AND c.chat_lead_id > 0";
    }

    $whereSql = implode(' AND ', $whereClauses);

    $sql = "SELECT c.*, 
                   e.enquiry_id, TRIM(CONCAT(COALESCE(e.enquiry_first_name, ''), ' ', COALESCE(e.enquiry_last_name, ''))) as enquiry_name, e.enquiry_subject as enquiry_destination, e.enquiry_status,
                   d.device_name, d.device_phone,
                   TRIM(CONCAT(COALESCE(p.profile_first_name, ''), ' ', COALESCE(p.profile_last_name, ''))) as agent_name,
                   u.user_username as agent_username
            FROM " . TABLE_PREFIX . "whatsapp_chat c
            LEFT JOIN " . TABLE_PREFIX . "enquiry e ON (e.enquiry_id = c.chat_lead_id AND e.enquiry_site_id = {$siteId})
            LEFT JOIN " . TABLE_PREFIX . "whatsapp_device d ON (d.device_site_id = {$siteId} AND (
                (c.chat_device_phone IS NOT NULL AND c.chat_device_phone != '' AND (d.device_phone = c.chat_device_phone OR d.device_phone LIKE CONCAT('%', RIGHT(REPLACE(c.chat_device_phone, '+', ''), 10))))
            ))
            LEFT JOIN " . TABLE_PREFIX . "user u ON (u.user_id = d.device_assigned_user_id OR (d.device_assigned_user_id IS NULL AND e.enquiry_agent = u.user_id))
            LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
            WHERE {$whereSql}
            GROUP BY c.chat_id
            ORDER BY c.chat_last_message_time DESC, c.chat_updated_at DESC
            LIMIT 100";

    $res = mysqli_query($connection, $sql);
    $chats = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $formattedTime = '';
            if (!empty($row['chat_last_message_time'])) {
                $ts = strtotime($row['chat_last_message_time']);
                if (date('Y-m-d') === date('Y-m-d', $ts)) {
                    $formattedTime = date('h:i A', $ts);
                } elseif (date('Y') === date('Y', $ts)) {
                    $formattedTime = date('M d', $ts);
                } else {
                    $formattedTime = date('d/m/Y', $ts);
                }
            }

            $leadName = trim($row['enquiry_name'] ?? '');
            $chatName = trim($row['chat_name'] ?? '');
            $pushName = trim($row['chat_pushname'] ?? '');
            $phone    = trim($row['chat_phone'] ?? '');

            // Rule: Show Name if exists, else show WhatsApp username + contact number
            $displayName = '';
            if (!empty($leadName)) {
                $displayName = $leadName;
            } elseif (!empty($chatName) && $chatName !== $phone && !str_starts_with($chatName, '+')) {
                $displayName = $chatName;
            } elseif (!empty($pushName)) {
                $displayName = $pushName . ' (' . $phone . ')';
            } else {
                $displayName = $phone;
            }

            $agentDisplay = !empty($row['agent_name']) ? trim($row['agent_name']) : (!empty($row['agent_username']) ? $row['agent_username'] : 'Unassigned');
            $devicePhone = !empty($row['chat_device_phone']) ? $row['chat_device_phone'] : (!empty($row['device_phone']) ? $row['device_phone'] : '');
            $deviceName = !empty($row['device_name']) ? $row['device_name'] : 'WhatsApp Line';

            $chats[] = [
                'chat_id'           => (int)$row['chat_id'],
                'phone'             => $phone,
                'name'              => $displayName,
                'raw_name'          => $chatName,
                'pushname'          => $pushName,
                'avatar'            => !empty($row['chat_avatar']) ? $row['chat_avatar'] : '',
                'last_message'      => $row['chat_last_message'] ?? '',
                'last_message_time' => $row['chat_last_message_time'] ?? '',
                'time_display'      => $formattedTime,
                'unread_count'      => (int)$row['chat_unread_count'],
                'device'            => [
                    'phone'      => $devicePhone,
                    'name'       => $deviceName,
                    'agent_name' => $agentDisplay
                ],
                'lead'              => !empty($row['chat_lead_id']) ? [
                    'id'          => (int)$row['chat_lead_id'],
                    'name'        => $row['enquiry_name'] ?? '',
                    'destination' => $row['enquiry_destination'] ?? '',
                    'status'      => $row['enquiry_status'] ?? ''
                ] : null
            ];
        }
    }

    echo json_encode([
        'status'           => 'success',
        'is_device_linked' => $isDeviceLinked,
        'linked_phone'     => $linkedPhone,
        'is_admin'         => $isAdmin,
        'chats'            => $chats,
        'count'            => count($chats)
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST: Create New Chat Conversation or Mark Read
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    // Action: Mark chat as read
    if ($action === 'mark_read' || ($data['action'] ?? '') === 'mark_read') {
        $chatId = (int)($data['chat_id'] ?? 0);
        if ($chatId > 0) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat SET chat_unread_count = 0 WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Action: Start new conversation
    $phone = trim($data['phone'] ?? '');
    $name  = trim($data['name'] ?? '');
    $leadId = isset($data['lead_id']) && is_numeric($data['lead_id']) ? (int)$data['lead_id'] : null;

    $digits = preg_replace('/[^0-9]/', '', $phone);
    if (str_starts_with($digits, '0')) {
        $digits = ltrim($digits, '0');
    }
    if (strlen($digits) === 10) {
        $digits = '91' . $digits;
    }
    if (strlen($digits) < 8) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid phone number is required (at least 8 digits).']);
        exit;
    }
    $cleanPhone = '+' . $digits;

    // Match existing lead if lead_id not passed
    if (!$leadId) {
        $phoneDigits = preg_replace('/[^0-9]/', '', $cleanPhone);
        $last10 = substr($phoneDigits, -10);
        $leadQ = mysqli_query($connection, "SELECT enquiry_id, TRIM(CONCAT(COALESCE(enquiry_first_name, ''), ' ', COALESCE(enquiry_last_name, ''))) as enquiry_name FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND enquiry_mobile LIKE '%{$last10}' LIMIT 1");
        if ($leadQ && mysqli_num_rows($leadQ) > 0) {
            $lRow = mysqli_fetch_assoc($leadQ);
            $leadId = (int)$lRow['enquiry_id'];
            if (empty($name) && !empty($lRow['enquiry_name'])) {
                $name = $lRow['enquiry_name'];
            }
        }
    }

    $escPhone = mysqli_real_escape_string($connection, $cleanPhone);
    $escName  = mysqli_real_escape_string($connection, $name);
    $leadVal  = $leadId ? $leadId : "NULL";

    // Bind new chat to agent's assigned device phone or primary connected device on site
    $currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);
    $agentDevPhone = '';
    if ($currentUserId > 0) {
        $devAssigned = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
        if ($devAssigned && mysqli_num_rows($devAssigned) > 0) {
            $agentDevPhone = mysqli_fetch_assoc($devAssigned)['device_phone'] ?? '';
        }
    }
    if (empty($agentDevPhone)) {
        $anyDev = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_id DESC LIMIT 1");
        if ($anyDev && mysqli_num_rows($anyDev) > 0) {
            $agentDevPhone = mysqli_fetch_assoc($anyDev)['device_phone'] ?? '';
        }
    }
    $escDevPhone = !empty($agentDevPhone) ? mysqli_real_escape_string($connection, $agentDevPhone) : '';

    $sql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
            (chat_site_id, chat_phone, chat_device_phone, chat_name, chat_lead_id)
            VALUES ({$siteId}, '{$escPhone}', " . (!empty($escDevPhone) ? "'{$escDevPhone}'" : "NULL") . ", '{$escName}', {$leadVal})
            ON DUPLICATE KEY UPDATE 
                chat_name = IF('{$escName}' != '', '{$escName}', chat_name),
                chat_device_phone = IF(" . (!empty($escDevPhone) ? "'{$escDevPhone}'" : "chat_device_phone") . " != '', " . (!empty($escDevPhone) ? "'{$escDevPhone}'" : "chat_device_phone") . ", chat_device_phone),
                chat_lead_id = IF({$leadVal} IS NOT NULL, {$leadVal}, chat_lead_id)";

    mysqli_query($connection, $sql);

    // Fetch created/existing chat row
    $cRes = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND chat_phone = '{$escPhone}' LIMIT 1");
    $chatRow = $cRes ? mysqli_fetch_assoc($cRes) : null;

    echo json_encode([
        'status' => 'success',
        'chat'   => $chatRow
    ]);
    exit;
}
