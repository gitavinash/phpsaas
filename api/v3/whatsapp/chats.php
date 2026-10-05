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

    $whereClauses = ["c.chat_site_id = {$siteId}"];

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
                   e.enquiry_id, TRIM(CONCAT(COALESCE(e.enquiry_first_name, ''), ' ', COALESCE(e.enquiry_last_name, ''))) as enquiry_name, e.enquiry_subject as enquiry_destination, e.enquiry_status
            FROM " . TABLE_PREFIX . "whatsapp_chat c
            LEFT JOIN " . TABLE_PREFIX . "enquiry e ON (e.enquiry_id = c.chat_lead_id AND e.enquiry_site_id = {$siteId})
            WHERE {$whereSql}
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

            $chats[] = [
                'chat_id'           => (int)$row['chat_id'],
                'phone'             => $phone,
                'name'              => $displayName,
                'raw_name'          => $chatName,
                'pushname'          => $pushName,
                'last_message'      => $row['chat_last_message'] ?? '',
                'last_message_time' => $row['chat_last_message_time'] ?? '',
                'time_display'      => $formattedTime,
                'unread_count'      => (int)$row['chat_unread_count'],
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
        'status' => 'success',
        'chats'  => $chats,
        'count'  => count($chats)
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

    $sql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
            (chat_site_id, chat_phone, chat_name, chat_lead_id)
            VALUES ({$siteId}, '{$escPhone}', '{$escName}', {$leadVal})
            ON DUPLICATE KEY UPDATE 
                chat_name = IF('{$escName}' != '', '{$escName}', chat_name),
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
