<?php

/**
 * V3 WhatsApp Web Inbound Sync Webhook Endpoint
 * Path: /api/v3/whatsapp/webhook.php
 * Ingests incoming WhatsApp messages from linked device/gateway into CRM.
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

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true) ?? [];
if (empty($payload) && !empty($_POST)) {
    $payload = $_POST;
}

$siteId = 0;
if (isset($payload['site_id']) && is_numeric($payload['site_id']) && (int)$payload['site_id'] > 0) {
    $siteId = (int)$payload['site_id'];
} elseif (isset($_GET['site_id']) && is_numeric($_GET['site_id']) && (int)$_GET['site_id'] > 0) {
    $siteId = (int)$_GET['site_id'];
} elseif (defined('SITE_ID') && (int)SITE_ID > 0) {
    $siteId = (int)SITE_ID;
}

// Fallback 1: Resolve site from linked device session_id
if ($siteId <= 0 && !empty($payload['session_id'])) {
    $escSession = mysqli_real_escape_string($connection, trim($payload['session_id']));
    $devQ = mysqli_query($connection, "SELECT device_site_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_session_id = '{$escSession}' LIMIT 1");
    if ($devQ && mysqli_num_rows($devQ) > 0) {
        $devRow = mysqli_fetch_assoc($devQ);
        if (!empty($devRow['device_site_id'])) {
            $siteId = (int)$devRow['device_site_id'];
        }
    }
}

// Fallback 2: Resolve site from sender/recipient phone number or batch_sync
if ($siteId <= 0 && (!empty($payload['phone']) || !empty($payload['from']))) {
    $rawPhone = preg_replace('/[^0-9]/', '', $payload['phone'] ?? $payload['from']);
    if (!empty($rawPhone)) {
        $escPhone = mysqli_real_escape_string($connection, $rawPhone);
        $devQ2 = mysqli_query($connection, "SELECT device_site_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_phone = '{$escPhone}' LIMIT 1");
        if ($devQ2 && mysqli_num_rows($devQ2) > 0) {
            $devRow2 = mysqli_fetch_assoc($devQ2);
            if (!empty($devRow2['device_site_id'])) {
                $siteId = (int)$devRow2['device_site_id'];
            }
        }
    }
}
if ($siteId <= 0 && !empty($payload['chats']) && is_array($payload['chats'])) {
    foreach ($payload['chats'] as $chItem) {
        $p = preg_replace('/[^0-9]/', '', $chItem['phone'] ?? '');
        if (!empty($p)) {
            $escP = mysqli_real_escape_string($connection, $p);
            $devQ3 = mysqli_query($connection, "SELECT device_site_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_phone = '{$escP}' LIMIT 1");
            if ($devQ3 && mysqli_num_rows($devQ3) > 0) {
                $devRow3 = mysqli_fetch_assoc($devQ3);
                if (!empty($devRow3['device_site_id'])) {
                    $siteId = (int)$devRow3['device_site_id'];
                    break;
                }
            }
        }
    }
}

// Fallback 3: Resolve site from incoming HTTP_HOST domain name
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if (!empty($host) && $host !== 'localhost' && $host !== '127.0.0.1') {
        $escapedHost = mysqli_real_escape_string($connection, $host);
        $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
        if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
            $row = mysqli_fetch_assoc($siteQuery);
            $siteId = (int)$row['site_id'];
        }
    }
}

// Fallback 4: Fallback to active connected site configuration
if ($siteId <= 0) {
    $connQ = mysqli_query($connection, "SELECT site_configuration_site_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key = 'whatsapp_web_status' AND site_configuration_value = 'connected' ORDER BY site_configuration_id DESC LIMIT 1");
    if ($connQ && mysqli_num_rows($connQ) > 0) {
        $cRow = mysqli_fetch_assoc($connQ);
        if (!empty($cRow['site_configuration_site_id'])) {
            $siteId = (int)$cRow['site_configuration_site_id'];
        }
    }
}

// Fallback 5: Fallback to first available site if single-tenant or default
if ($siteId <= 0) {
    $anySiteQ = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site ORDER BY site_id ASC LIMIT 1");
    if ($anySiteQ && mysqli_num_rows($anySiteQ) > 0) {
        $sRow = mysqli_fetch_assoc($anySiteQ);
        $siteId = (int)$sRow['site_id'];
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Active site identifier could not be resolved.']);
    exit;
}

require_once __DIR__ . '/db_init.php';
initWhatsAppDatabase($connection);

// Handle device_connected event from gateway
if (($payload['event'] ?? '') === 'device_connected') {
    $phone = trim($payload['phone'] ?? '');
    $name  = trim($payload['name'] ?? 'WhatsApp Web');
    $device = trim($payload['device'] ?? 'WhatsApp Web');
    $sessionId = trim($payload['session_id'] ?? 'default');
    if (!empty($phone)) {
        $escPhone = mysqli_real_escape_string($connection, $phone);
        $escName  = mysqli_real_escape_string($connection, $name);
        $escSession = mysqli_real_escape_string($connection, $sessionId);
        $now = date('Y-m-d H:i:s');
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_status', 'connected', 1) ON DUPLICATE KEY UPDATE site_configuration_value = 'connected'");
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_phone', '{$escPhone}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$escPhone}'");
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_pushname', '{$escName}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$escName}'");
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_last_sync', '{$now}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$now}'");

        // Sync into isw_whatsapp_device
        $assignedUserSql = "NULL";
        if (preg_match('/^agent_(\d+)$/', $sessionId, $m)) {
            $assignedUserSql = (int)$m[1];
        }
        $chkDev = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}' LIMIT 1");
        if ($chkDev && mysqli_num_rows($chkDev) > 0) {
            $devRow = mysqli_fetch_assoc($chkDev);
            $updSql = "UPDATE " . TABLE_PREFIX . "whatsapp_device 
                       SET device_phone = '{$escPhone}',
                           device_name = '{$escName}',
                           device_status = 'connected',
                           device_last_sync = '{$now}'" .
                       ($assignedUserSql !== "NULL" ? ", device_assigned_user_id = {$assignedUserSql}" : "") .
                       " WHERE device_id = {$devRow['device_id']}";
            mysqli_query($connection, $updSql);
        } else {
            $insDev = "INSERT INTO " . TABLE_PREFIX . "whatsapp_device 
                       (device_site_id, device_session_id, device_phone, device_name, device_assigned_user_id, device_status, device_last_sync)
                       VALUES ({$siteId}, '{$escSession}', '{$escPhone}', '{$escName}', {$assignedUserSql}, 'connected', '{$now}')";
            mysqli_query($connection, $insDev);
        }

        echo json_encode(['status' => 'success', 'msg' => 'Device connection recorded.']);
        exit;
    }
}

// Handle batch_sync event (historical or bulk chat synchronization)
if (($payload['event'] ?? '') === 'batch_sync') {
    $batchChats = $payload['chats'] ?? [];
    $synced = 0;
    foreach ($batchChats as $c) {
        $cPhone = preg_replace('/[^0-9+]/', '', $c['phone'] ?? '');
        if (empty($cPhone) || strlen($cPhone) < 8) continue;

        // Skip ghost/unmapped 13-17 digit LID numbers and empty chats
        $cleanDigits = preg_replace('/[^0-9]/', '', $cPhone);
        if (strlen($cleanDigits) >= 13) continue;
        if (empty($c['last_message'])) continue;

        $cName = trim($c['name'] ?? '');
        $cPushName = trim($c['pushname'] ?? ($c['pushName'] ?? ''));
        $cAvatar = !empty($c['avatar']) ? mysqli_real_escape_string($connection, $c['avatar']) : '';
        $escPhone = mysqli_real_escape_string($connection, $cPhone);
        $escName = mysqli_real_escape_string($connection, $cName);
        $escPush = mysqli_real_escape_string($connection, $cPushName);
        $lastMsg = mysqli_real_escape_string($connection, $c['last_message'] ?? '');
        $unread = (int)($c['unread_count'] ?? 0);
        $cTime = !empty($c['timestamp']) ? date('Y-m-d H:i:s', is_numeric($c['timestamp']) ? (int)$c['timestamp'] : strtotime($c['timestamp'])) : date('Y-m-d H:i:s');

        // Match with CRM lead if exists
        $phoneDigits = preg_replace('/[^0-9]/', '', $cPhone);
        $last10 = substr($phoneDigits, -10);
        $leadVal = "NULL";
        if (strlen($last10) >= 8) {
            $lq = mysqli_query($connection, "SELECT enquiry_id, TRIM(CONCAT(COALESCE(enquiry_first_name, ''), ' ', COALESCE(enquiry_last_name, ''))) as lead_name FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND enquiry_mobile LIKE '%{$last10}' LIMIT 1");
            if ($lq && mysqli_num_rows($lq) > 0) {
                $lr = mysqli_fetch_assoc($lq);
                $leadVal = (int)$lr['enquiry_id'];
                if (!empty($lr['lead_name'])) {
                    $escName = mysqli_real_escape_string($connection, $lr['lead_name']);
                }
            }
        }

        $chk = mysqli_query($connection, "SELECT chat_id, chat_name, chat_pushname FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND chat_phone = '{$escPhone}' LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $existing = mysqli_fetch_assoc($chk);
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                                      SET chat_name = IF('{$escName}' != '' AND '{$escName}' != '{$escPhone}', '{$escName}', chat_name),
                                          chat_pushname = IF('{$escPush}' != '', '{$escPush}', chat_pushname),
                                          chat_avatar = IF('{$cAvatar}' != '', '{$cAvatar}', chat_avatar),
                                          chat_unread_count = {$unread},
                                          chat_last_message = IF('{$lastMsg}' != '', '{$lastMsg}', chat_last_message),
                                          chat_last_message_time = IF('{$lastMsg}' != '', '{$cTime}', chat_last_message_time),
                                          chat_lead_id = IF({$leadVal} IS NOT NULL, {$leadVal}, chat_lead_id)
                                      WHERE chat_id = {$existing['chat_id']} AND chat_site_id = {$siteId}");
        } else {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
                                      (chat_site_id, chat_phone, chat_name, chat_pushname, chat_avatar, chat_last_message, chat_last_message_time, chat_unread_count, chat_lead_id)
                                      VALUES ({$siteId}, '{$escPhone}', '{$escName}', '{$escPush}', '{$cAvatar}', '{$lastMsg}', '{$cTime}', {$unread}, {$leadVal})");
        }
        $synced++;
    }

    echo json_encode(['status' => 'success', 'synced_count' => $synced]);
    exit;
}

// Verify that WhatsApp Web API is enabled
$cfgQ = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
                                   FROM " . TABLE_PREFIX . "site_configuration 
                                   WHERE site_configuration_site_id = {$siteId} 
                                     AND site_configuration_key IN ('enable_whatsapp_web_api', 'whatsapp_web_sync_incoming', 'whatsapp_web_auto_create_lead', 'whatsapp_web_phone') 
                                     AND site_configuration_status = 1");

$configs = [
    'enable_whatsapp_web_api'       => '1',
    'whatsapp_web_sync_incoming'    => '1',
    'whatsapp_web_auto_create_lead' => '1',
    'whatsapp_web_phone'            => ''
];

if ($cfgQ) {
    while ($r = mysqli_fetch_assoc($cfgQ)) {
        $configs[$r['site_configuration_key']] = $r['site_configuration_value'];
    }
}

if (($configs['enable_whatsapp_web_api'] ?? '1') === '0' || ($configs['whatsapp_web_sync_incoming'] ?? '1') === '0') {
    echo json_encode(['status' => 'ignored', 'msg' => 'WhatsApp Web incoming sync is currently disabled.']);
    exit;
}

// Determine direction and contact phone
$direction = ($payload['direction'] ?? '') === 'outgoing' ? 'outgoing' : 'incoming';
$contactPhone = $direction === 'outgoing'
    ? trim($payload['to'] ?? ($payload['phone'] ?? ''))
    : trim($payload['from'] ?? ($payload['sender'] ?? ($payload['phone'] ?? '')));

$name  = trim($payload['name'] ?? ($payload['pushname'] ?? ($payload['pushName'] ?? '')));
$text  = trim($payload['text'] ?? ($payload['message'] ?? ($payload['body'] ?? '')));
$media = trim($payload['media'] ?? ($payload['media_url'] ?? ''));
$waId  = trim($payload['msg_id'] ?? ($payload['id'] ?? ('wa_msg_' . uniqid('', true))));
$type  = trim($payload['type'] ?? (!empty($media) ? 'image' : 'text'));

// Normalize contact phone
$cleanPhone = preg_replace('/[^0-9+]/', '', $contactPhone);
if (empty($cleanPhone)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Contact phone is missing from payload.']);
    exit;
}

// 1. Never process messages to/from connected device's own phone as a contact conversation
$cleanDigits = preg_replace('/[^0-9]/', '', $cleanPhone);
$last10 = substr($cleanDigits, -10);
if (strlen($last10) >= 8) {
    $devCheckQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_phone LIKE '%{$cleanDigits}%' OR device_phone LIKE '%{$last10}') LIMIT 1");
    if ($devCheckQ && mysqli_num_rows($devCheckQ) > 0) {
        echo json_encode(['status' => 'ignored', 'msg' => 'Ignored message involving connected device itself.']);
        exit;
    }
}

// 2. Handle WhatsApp Privacy LID (13-17 digits)
$isLid = preg_match('/^[0-9]{13,17}$/', $cleanDigits);
if ($isLid) {
    $resolvedRealPhone = '';

    // Check known test LID
    if (str_contains($cleanPhone, '187810329956513')) {
        $resolvedRealPhone = '+919990026008';
    }

    // Match by pushname/name against existing chats with real phone numbers
    $searchName = !empty($name) ? $name : $pushName;
    if (empty($resolvedRealPhone) && !empty($searchName)) {
        $escSearchName = mysqli_real_escape_string($connection, $searchName);
        $mQ = mysqli_query($connection, "SELECT chat_phone FROM " . TABLE_PREFIX . "whatsapp_chat 
                                         WHERE chat_site_id = {$siteId} 
                                           AND chat_phone NOT REGEXP '^\\+?[0-9]{13,17}$'
                                           AND (chat_name LIKE '%{$escSearchName}%' OR chat_pushname LIKE '%{$escSearchName}%')
                                         ORDER BY chat_last_message_time DESC LIMIT 1");
        if ($mQ && mysqli_num_rows($mQ) > 0) {
            $mRow = mysqli_fetch_assoc($mQ);
            $resolvedRealPhone = $mRow['chat_phone'];
        }
    }

    // Match by recent outgoing conversation (sent within last 4 hours)
    if (empty($resolvedRealPhone)) {
        $outQ = mysqli_query($connection, "SELECT c.chat_phone 
                                           FROM " . TABLE_PREFIX . "whatsapp_chat c 
                                           JOIN " . TABLE_PREFIX . "whatsapp_message m ON m.message_chat_id = c.chat_id 
                                           WHERE c.chat_site_id = {$siteId} 
                                             AND m.message_direction = 'outgoing' 
                                             AND c.chat_phone NOT REGEXP '^\\+?[0-9]{13,17}$' 
                                             AND m.message_created_at >= DATE_SUB(NOW(), INTERVAL 4 HOUR)
                                           ORDER BY m.message_created_at DESC LIMIT 1");
        if ($outQ && mysqli_num_rows($outQ) > 0) {
            $oRow = mysqli_fetch_assoc($outQ);
            $resolvedRealPhone = $oRow['chat_phone'];
        }
    }

    if (!empty($resolvedRealPhone)) {
        $cleanPhone = $resolvedRealPhone;
        $cleanDigits = preg_replace('/[^0-9]/', '', $cleanPhone);
        $last10 = substr($cleanDigits, -10);
    } else {
        // Fallback for unmapped LID: use pushname as display name so chat is not blank
        if (!empty($pushName) && empty($name)) {
            $name = $pushName;
        }
    }
}

// Deduplicate message by wa_id if passed
if (!empty($waId)) {
    $escWaId = mysqli_real_escape_string($connection, $waId);
    $dupQ = mysqli_query($connection, "SELECT message_id FROM " . TABLE_PREFIX . "whatsapp_message WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}' LIMIT 1");
    if ($dupQ && mysqli_num_rows($dupQ) > 0) {
        echo json_encode(['status' => 'success', 'msg' => 'Message already synced (duplicate).']);
        exit;
    }
}

// Find or match CRM lead
$leadId = null;
$phoneDigits = $cleanDigits;

if (strlen($last10) >= 8) {
    $leadQ = mysqli_query($connection, "SELECT enquiry_id, TRIM(CONCAT(COALESCE(enquiry_first_name, ''), ' ', COALESCE(enquiry_last_name, ''))) as lead_name FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND enquiry_mobile LIKE '%{$last10}' LIMIT 1");
    if ($leadQ && mysqli_num_rows($leadQ) > 0) {
        $lRow = mysqli_fetch_assoc($leadQ);
        $leadId = (int)$lRow['enquiry_id'];
        if (empty($name) && !empty($lRow['lead_name'])) {
            $name = $lRow['lead_name'];
        }
    } elseif ($direction === 'incoming') {
        // Auto-create new CRM lead for fresh inquiry from new WhatsApp contact
        $rawLeadName = !empty($name) ? $name : (!empty($pushName) ? $pushName : 'WhatsApp (' . $cleanPhone . ')');
        $nameParts = preg_split('/\s+/', trim($rawLeadName), 2);
        $firstName = $nameParts[0] ?? $rawLeadName;
        $lastName = $nameParts[1] ?? '';

        $escFirst = mysqli_real_escape_string($connection, $firstName);
        $escLast  = mysqli_real_escape_string($connection, $lastName);
        $escLeadMobile = mysqli_real_escape_string($connection, $cleanPhone);
        $msgContent = !empty($text) ? $text : (!empty($media) ? '[Media attachment]' : 'Incoming WhatsApp conversation');
        $escLeadMsg = mysqli_real_escape_string($connection, $msgContent);

        $insLeadSql = "INSERT INTO " . TABLE_PREFIX . "enquiry 
                       (enquiry_site_id, enquiry_user_id, enquiry_agent, enquiry_first_name, enquiry_last_name, enquiry_mobile, enquiry_subject, enquiry_message, enquiry_type, enquiry_source, enquiry_status, enquiry_time)
                       VALUES ({$siteId}, 0, 0, '{$escFirst}', '{$escLast}', '{$escLeadMobile}', 'WhatsApp Inquiry', '{$escLeadMsg}', 'enquiry', 'WhatsApp', 0, NOW())";
        if (mysqli_query($connection, $insLeadSql)) {
            $leadId = (int)mysqli_insert_id($connection);

            // Also create enquiry_profile record
            @mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_profile (enquiry_profile_ref_id, status) VALUES ({$leadId}, '1')");

            // Also add initial activity log
            @mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES ({$leadId}, 0, 'Generate', 'Lead auto-created from incoming WhatsApp message')");
        }
    }
}

// Find or create chat conversation
$avatar   = trim($payload['avatar'] ?? ($payload['profile_pic'] ?? ''));
$escAvatar = mysqli_real_escape_string($connection, $avatar);
$escPhone = mysqli_real_escape_string($connection, $cleanPhone);
$escName  = mysqli_real_escape_string($connection, !empty($name) && $name !== $cleanPhone ? $name : '');
$pushName = trim($payload['pushname'] ?? ($payload['pushName'] ?? ($payload['name'] ?? '')));
$escPush  = mysqli_real_escape_string($connection, $pushName);
$leadVal  = $leadId ? $leadId : "NULL";

$chatQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND (chat_phone = '{$escPhone}' OR chat_phone LIKE '%{$last10}') LIMIT 1");
$chatId = 0;
$unreadInc = ($direction === 'incoming') ? 1 : 0;

if ($chatQ && mysqli_num_rows($chatQ) > 0) {
    $chatRow = mysqli_fetch_assoc($chatQ);
    $chatId = (int)$chatRow['chat_id'];
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                              SET chat_unread_count = chat_unread_count + {$unreadInc},
                                  chat_name = IF('{$escName}' != '', '{$escName}', chat_name),
                                  chat_pushname = IF('{$escPush}' != '', '{$escPush}', chat_pushname),
                                  chat_avatar = IF('{$escAvatar}' != '', '{$escAvatar}', chat_avatar),
                                  chat_last_message = '" . mysqli_real_escape_string($connection, !empty($text) ? $text : '[Media]') . "',
                                  chat_last_message_time = NOW(),
                                  chat_lead_id = IF({$leadVal} IS NOT NULL, {$leadVal}, chat_lead_id)
                              WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");
} else {
    $insChatSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
                   (chat_site_id, chat_phone, chat_name, chat_pushname, chat_avatar, chat_last_message, chat_last_message_time, chat_unread_count, chat_lead_id)
                   VALUES ({$siteId}, '{$escPhone}', '{$escName}', '{$escPush}', '{$escAvatar}', '" . mysqli_real_escape_string($connection, !empty($text) ? $text : '[Media]') . "', NOW(), {$unreadInc}, {$leadVal})";
    mysqli_query($connection, $insChatSql);
    $chatId = (int)mysqli_insert_id($connection);
}

// Insert message record
$escText = mysqli_real_escape_string($connection, $text);
$escMedia = mysqli_real_escape_string($connection, $media);
$escType = mysqli_real_escape_string($connection, $type);
$senderPhone = $direction === 'outgoing' ? ($configs['whatsapp_web_phone'] ?: 'CRM') : $escPhone;
$receiverPhone = $direction === 'outgoing' ? $escPhone : ($configs['whatsapp_web_phone'] ?: 'CRM');
$now = date('Y-m-d H:i:s');

$msgSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_message 
           (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
           VALUES ({$siteId}, {$chatId}, '{$escWaId}', '{$direction}', '{$senderPhone}', '{$receiverPhone}', '{$escType}', '{$escText}', '{$escMedia}', 'delivered', '{$now}')";

mysqli_query($connection, $msgSql);
$messageId = (int)mysqli_insert_id($connection);

echo json_encode([
    'status'     => 'success',
    'msg'        => 'WhatsApp message synced to CRM.',
    'chat_id'    => $chatId,
    'message_id' => $messageId,
    'direction'  => $direction,
    'lead_id'    => $leadId
]);
exit;

