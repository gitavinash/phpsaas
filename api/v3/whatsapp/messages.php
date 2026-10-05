<?php

/**
 * V3 WhatsApp CRM Messages API (History & Outbound Dispatch)
 * Path: /api/v3/whatsapp/messages.php
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

// --------------------------------------------------------------------------
// 1. GET: Fetch Message History for a Chat / Phone
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $chatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
    $phone  = trim($_GET['phone'] ?? '');

    if ($chatId <= 0 && !empty($phone)) {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        $escPhone = mysqli_real_escape_string($connection, $cleanPhone);
        $cQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND chat_phone = '{$escPhone}' LIMIT 1");
        if ($cQ && mysqli_num_rows($cQ) > 0) {
            $cRow = mysqli_fetch_assoc($cQ);
            $chatId = (int)$cRow['chat_id'];
        }
    }

    if ($chatId <= 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Chat conversation not found.']);
        exit;
    }

    // Reset unread count
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat SET chat_unread_count = 0 WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");

    // Fetch conversation info
    $chatQ = mysqli_query($connection, "SELECT c.*, e.enquiry_id, TRIM(CONCAT(COALESCE(e.enquiry_first_name, ''), ' ', COALESCE(e.enquiry_last_name, ''))) as enquiry_name, e.enquiry_subject as enquiry_destination, e.enquiry_status 
                                        FROM " . TABLE_PREFIX . "whatsapp_chat c 
                                        LEFT JOIN " . TABLE_PREFIX . "enquiry e ON (e.enquiry_id = c.chat_lead_id AND e.enquiry_site_id = {$siteId})
                                        WHERE c.chat_id = {$chatId} AND c.chat_site_id = {$siteId} LIMIT 1");
    $chat = $chatQ ? mysqli_fetch_assoc($chatQ) : null;

    // Fetch messages
    $sql = "SELECT * FROM " . TABLE_PREFIX . "whatsapp_message 
            WHERE message_site_id = {$siteId} AND message_chat_id = {$chatId} 
            ORDER BY message_created_at ASC, message_id ASC 
            LIMIT 300";

    $res = mysqli_query($connection, $sql);
    $messages = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $ts = strtotime($row['message_created_at']);
            $messages[] = [
                'id'         => (int)$row['message_id'],
                'direction'  => $row['message_direction'],
                'sender'     => $row['message_sender'],
                'receiver'   => $row['message_receiver'],
                'type'       => $row['message_type'],
                'text'       => $row['message_text'] ?? '',
                'media_url'  => $row['message_media_url'] ?? '',
                'status'     => $row['message_status'],
                'created_at' => $row['message_created_at'],
                'time'       => date('h:i A', $ts),
                'date'       => date('M d, Y', $ts)
            ];
        }
    }

    echo json_encode([
        'status'   => 'success',
        'chat'     => $chat,
        'messages' => $messages,
        'count'    => count($messages)
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST: Send Outgoing Message from CRM
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $chatId = isset($data['chat_id']) ? (int)$data['chat_id'] : 0;
    $phone  = trim($data['phone'] ?? '');
    $text   = trim($data['text'] ?? ($data['message'] ?? ''));
    $type   = trim($data['type'] ?? 'text');
    $mediaUrl = trim($data['media_url'] ?? '');

    if (empty($text) && empty($mediaUrl)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Message text or media is required.']);
        exit;
    }

    // Resolve chat
    if ($chatId <= 0 && !empty($phone)) {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }
        $cleanPhone = strlen($digits) >= 8 ? ('+' . $digits) : '';
        $escPhone = mysqli_real_escape_string($connection, $cleanPhone);
        if (!empty($cleanPhone)) {
            $cQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND chat_phone = '{$escPhone}' LIMIT 1");
            if ($cQ && mysqli_num_rows($cQ) > 0) {
                $cRow = mysqli_fetch_assoc($cQ);
                $chatId = (int)$cRow['chat_id'];
            } else {
                // Auto create chat
                mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat (chat_site_id, chat_phone, chat_name) VALUES ({$siteId}, '{$escPhone}', '{$escPhone}')");
                $chatId = (int)mysqli_insert_id($connection);
            }
        }
    }

    if ($chatId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid recipient or chat ID is required.']);
        exit;
    }

    // Fetch chat phone
    $cQ = mysqli_query($connection, "SELECT chat_phone FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_id = {$chatId} AND chat_site_id = {$siteId} LIMIT 1");
    $cRow = $cQ ? mysqli_fetch_assoc($cQ) : null;
    $rawRecipient = !empty($cRow['chat_phone']) ? $cRow['chat_phone'] : $phone;
    $rDigits = preg_replace('/[^0-9]/', '', $rawRecipient);
    if (str_starts_with($rDigits, '0')) {
        $rDigits = ltrim($rDigits, '0');
    }
    if (strlen($rDigits) === 10) {
        $rDigits = '91' . $rDigits;
    }
    if (strlen($rDigits) < 8) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Recipient phone number is invalid (' . $rawRecipient . ').']);
        exit;
    }
    $recipientPhone = '+' . $rDigits;

    // Fetch site sender phone
    $senderPhone = 'CRM';
    $cfgQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_phone' LIMIT 1");
    if ($cfgQ && mysqli_num_rows($cfgQ) > 0) {
        $cfgRow = mysqli_fetch_assoc($cfgQ);
        if (!empty($cfgRow['site_configuration_value'])) {
            $senderPhone = $cfgRow['site_configuration_value'];
        }
    }

    $waMsgId = 'crm_out_' . uniqid('', true);
    $escText = mysqli_real_escape_string($connection, $text);
    $escMedia = mysqli_real_escape_string($connection, $mediaUrl);
    $escSender = mysqli_real_escape_string($connection, $senderPhone);
    $escReceiver = mysqli_real_escape_string($connection, $recipientPhone);
    $escType = mysqli_real_escape_string($connection, $type);
    $now = date('Y-m-d H:i:s');

    // Insert outgoing message
    $msgSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_message 
               (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
               VALUES ({$siteId}, {$chatId}, '{$waMsgId}', 'outgoing', '{$escSender}', '{$escReceiver}', '{$escType}', '{$escText}', '{$escMedia}', 'sent', '{$now}')";
    mysqli_query($connection, $msgSql);
    $msgId = (int)mysqli_insert_id($connection);

    // Update conversation thread
    $lastMsgPreview = !empty($text) ? $text : '[Media Attachment]';
    $escPreview = mysqli_real_escape_string($connection, $lastMsgPreview);
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                              SET chat_last_message = '{$escPreview}', 
                                  chat_last_message_time = '{$now}',
                                  chat_updated_at = '{$now}'
                              WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");

    // Forward dispatch to WhatsApp Web Gateway Daemon
    $gwUrl = 'http://127.0.0.1:3001';
    $gwQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_gateway_url' LIMIT 1");
    if ($gwQ && mysqli_num_rows($gwQ) > 0) {
        $gwRow = mysqli_fetch_assoc($gwQ);
        if (!empty($gwRow['site_configuration_value'])) {
            $gwUrl = trim($gwRow['site_configuration_value']);
        }
    }

    // Determine device session for current user / site
    $currentUserId = defined('USER_ID') ? (int)USER_ID : 0;
    $targetSessionId = 'default';
    if ($currentUserId > 0) {
        $devQ = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
        if ($devQ && mysqli_num_rows($devQ) > 0) {
            $devRow = mysqli_fetch_assoc($devQ);
            $targetSessionId = $devRow['device_session_id'];
            if (!empty($devRow['device_phone'])) {
                $senderPhone = $devRow['device_phone'];
            }
        }
    }

    $gwSuccess = false;
    $gwErrorMsg = '';
    $gwResData = null;

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
    $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook.php';

    $payload = json_encode([
        'site_id'     => $siteId,
        'session_id'  => $targetSessionId,
        'webhook_url' => $dynamicWebhookUrl,
        'to'          => $recipientPhone,
        'message'     => $text,
        'media'       => $mediaUrl,
        'msg_id'      => $waMsgId
    ]);

    $opts = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'content'       => $payload,
            'timeout'       => 6,
            'ignore_errors' => true
        ]
    ];
    $ctx = stream_context_create($opts);
    $gwRawRes = @file_get_contents(rtrim($gwUrl, '/') . '/send-message', false, $ctx);
    if ($gwRawRes !== false) {
        $gwResData = json_decode($gwRawRes, true);
        if (isset($gwResData['status']) && $gwResData['status'] === 'success') {
            $gwSuccess = true;
            if (!empty($gwResData['msg_id'])) {
                $realWaId = mysqli_real_escape_string($connection, $gwResData['msg_id']);
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_wa_id = '{$realWaId}', message_status = 'delivered' WHERE message_id = {$msgId}");
            }
        } else {
            $gwErrorMsg = $gwResData['message'] ?? 'WhatsApp device was unable to send this message.';
        }
    } else {
        $gwErrorMsg = 'Could not communicate with WhatsApp Web Gateway daemon on port 3001. Ensure daemon is running.';
    }

    if (!$gwSuccess) {
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_status = 'failed' WHERE message_id = {$msgId}");
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg'    => $gwErrorMsg ?: 'Failed to send WhatsApp message.',
            'details' => $gwResData
        ]);
        exit;
    }

    echo json_encode([
        'status'  => 'success',
        'msg'     => 'Message sent successfully.',
        'message' => [
            'id'         => $msgId,
            'chat_id'    => $chatId,
            'direction'  => 'outgoing',
            'sender'     => $senderPhone,
            'receiver'   => $recipientPhone,
            'text'       => $text,
            'status'     => 'delivered',
            'created_at' => $now,
            'time'       => date('h:i A', strtotime($now))
        ]
    ]);
    exit;
}
