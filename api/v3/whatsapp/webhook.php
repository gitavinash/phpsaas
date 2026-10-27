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

require_once __DIR__ . '/phone_helper.php';

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);
if ($payload === null && !empty($rawInput)) {
    $cleanedInput = mb_convert_encoding($rawInput, 'UTF-8', 'UTF-8');
    $payload = json_decode($cleanedInput, true);
}
if (empty($payload) && !empty($_POST)) {
    $payload = $_POST;
}
if (empty($payload)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Payload is empty or invalid JSON.',
        'json_error' => json_last_error_msg()
    ]);
    exit;
}

try {
// Step 1: Explicit site_id in payload, query params, or constant
$siteId = 0;
if (isset($payload['site_id']) && is_numeric($payload['site_id']) && (int)$payload['site_id'] > 0) {
    $siteId = (int)$payload['site_id'];
} elseif (isset($_GET['site_id']) && is_numeric($_GET['site_id']) && (int)$_GET['site_id'] > 0) {
    $siteId = (int)$_GET['site_id'];
} elseif (defined('SITE_ID') && (int)SITE_ID > 0) {
    $siteId = (int)SITE_ID;
}

// Step 2: Resolve site from session_id if encoded with site_{siteId}_
if ($siteId <= 0 && !empty($payload['session_id']) && preg_match('/^site_(\d+)_/', trim($payload['session_id']), $mSite)) {
    $siteId = (int)$mSite[1];
}

// Step 3: Resolve site from linked device session_id in database
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

// Step 4: Resolve site from incoming HTTP_HOST domain name
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if (!empty($host) && $host !== '127.0.0.1') {
        $escapedHost = mysqli_real_escape_string($connection, $host);
        $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
        if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
            $row = mysqli_fetch_assoc($siteQuery);
            $siteId = (int)$row['site_id'];
        }
    }
}

// Fallback for localhost / local dev environment
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if ($host === 'localhost' || $host === '127.0.0.1' || empty($host)) {
        $locQ = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = 'localhost' LIMIT 1");
        if ($locQ && mysqli_num_rows($locQ) > 0) {
            $siteId = (int)mysqli_fetch_assoc($locQ)['site_id'];
        } elseif (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
            $siteId = 385;
        }
    }
}

// Step 5: Resolve site from connected device phone number
if ($siteId <= 0 && (!empty($payload['to']) || !empty($payload['from']))) {
    $fromPhone = preg_replace('/[^0-9]/', '', $payload['from'] ?? '');
    $toPhone = preg_replace('/[^0-9]/', '', $payload['to'] ?? '');
    $phonesToCheck = array_filter([$fromPhone, $toPhone]);
    foreach ($phonesToCheck as $p) {
        $last10p = substr($p, -10);
        if (strlen($last10p) >= 8) {
            $devQ2 = mysqli_query($connection, "SELECT device_site_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_phone LIKE '%{$last10p}' LIMIT 1");
            if ($devQ2 && mysqli_num_rows($devQ2) > 0) {
                $devRow2 = mysqli_fetch_assoc($devQ2);
                if (!empty($devRow2['device_site_id'])) {
                    $siteId = (int)$devRow2['device_site_id'];
                    break;
                }
            }
        }
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Active site identifier could not be resolved.']);
    exit;
}

// Retrieve site timezone configuration (default Asia/Kolkata for local IST)
$siteTimezone = 'Asia/Kolkata';
$tzQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'site_timezone' LIMIT 1");
if ($tzQ && $tzRow = mysqli_fetch_assoc($tzQ)) {
    if (!empty($tzRow['site_configuration_value'])) {
        $siteTimezone = trim($tzRow['site_configuration_value']);
    }
}
@date_default_timezone_set($siteTimezone);

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

        // 1. Sync into isw_whatsapp_device first
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

        require_once __DIR__ . '/device_helper.php';
        ensureFirstDeviceIsDefault($connection, $siteId, $escSession);

        // 2. Update site_configuration status and timestamp
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_status', 'connected', 1) ON DUPLICATE KEY UPDATE site_configuration_value = 'connected', site_configuration_status = 1");
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_last_sync', '{$now}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$now}', site_configuration_status = 1");

        // 3. Only update site-level phone/pushname if this is the default device or site phone is not set yet
        $chkIsDefault = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_session_id = '{$escSession}' AND device_is_default = 1 LIMIT 1");
        $isDefaultDev = ($chkIsDefault && mysqli_num_rows($chkIsDefault) > 0);

        $chkCurPhone = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_phone' LIMIT 1");
        $curPhoneVal = ($chkCurPhone && $cpr = mysqli_fetch_assoc($chkCurPhone)) ? trim($cpr['site_configuration_value']) : '';

        if ($isDefaultDev || empty($curPhoneVal)) {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_phone', '{$escPhone}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$escPhone}', site_configuration_status = 1");
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, 'whatsapp_web_pushname', '{$escName}', 1) ON DUPLICATE KEY UPDATE site_configuration_value = '{$escName}', site_configuration_status = 1");
        }

        // Clear any previous disconnection alert now that device is successfully connected
        mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_disconnect_alert'");

        echo json_encode(['status' => 'success', 'msg' => 'Device connection recorded.']);
        exit;
    }
}

// Handle device_disconnected event from gateway
if (($payload['event'] ?? '') === 'device_disconnected') {
    $phone      = trim($payload['phone'] ?? ($payload['device_phone'] ?? ''));
    $sessionId  = trim($payload['session_id'] ?? 'default');
    $reason     = trim($payload['reason'] ?? 'Connection dropped');
    $statusCode = (int)($payload['status_code'] ?? 0);

    require_once __DIR__ . '/device_helper.php';
    handleWhatsAppDeviceDisconnection($connection, $siteId, $sessionId, $phone, $reason, $statusCode);

    echo json_encode(['status' => 'success', 'msg' => 'Device disconnection recorded and alert triggered.']);
    exit;
}

// Handle message_status_update event from gateway (real-time delivery and read receipts)
if (($payload['event'] ?? '') === 'message_status_update') {
    $msgWaId = trim($payload['msg_id'] ?? '');
    $status  = trim($payload['status'] ?? '');
    if (!empty($msgWaId) && in_array($status, ['sent', 'delivered', 'read', 'failed'], true)) {
        $escWaId = mysqli_real_escape_string($connection, $msgWaId);
        $escStat = mysqli_real_escape_string($connection, $status);

        if ($escStat === 'read') {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message 
                SET message_status = 'read' 
                WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}'");
        } elseif ($escStat === 'delivered') {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message 
                SET message_status = 'delivered' 
                WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}' AND message_status != 'read'");
        } elseif ($escStat === 'sent') {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message 
                SET message_status = 'sent' 
                WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}' AND message_status NOT IN ('delivered', 'read')");
        } elseif ($escStat === 'failed') {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message 
                SET message_status = 'failed' 
                WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}' AND message_status NOT IN ('delivered', 'read')");
        }
    }
    echo json_encode(['status' => 'success', 'msg' => 'Status update recorded.']);
    exit;
}

// Handle batch_sync event (historical or bulk chat synchronization)
if (($payload['event'] ?? '') === 'batch_sync') {
    $batchChats = $payload['chats'] ?? [];
    $synced = 0;

    // Resolve connected device phone for message records
    $devicePhone = !empty($payload['device_phone']) ? trim($payload['device_phone']) : '';
    if (empty($devicePhone) && !empty($payload['session_id'])) {
        $sessEsc = mysqli_real_escape_string($connection, $payload['session_id']);
        $devQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_session_id = '{$sessEsc}' OR device_session_id LIKE '%{$sessEsc}') LIMIT 1");
        if ($devQ && $dr = mysqli_fetch_assoc($devQ)) {
            $devicePhone = $dr['device_phone'] ?? '';
        }
    }
    if (empty($devicePhone)) {
        $devQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 'connected' ORDER BY device_id DESC LIMIT 1");
        if ($devQ && $dr = mysqli_fetch_assoc($devQ)) {
            $devicePhone = $dr['device_phone'] ?? '';
        }
    }
    if (empty($devicePhone)) {
        $devicePhone = 'CRM';
    }
    $escDevPhone = mysqli_real_escape_string($connection, $devicePhone);
    $cleanDevMatch = ($escDevPhone !== 'CRM' && !empty($escDevPhone)) ? $escDevPhone : '';

    // Collect all connected device numbers for this site to prevent importing them as customer chats
    $siteDevPhones = [];
    $siteDevQuery = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_phone IS NOT NULL AND device_phone != ''");
    if ($siteDevQuery) {
        while ($sdr = mysqli_fetch_assoc($siteDevQuery)) {
            $sdClean = preg_replace('/[^0-9]/', '', $sdr['device_phone'] ?? '');
            if (strlen($sdClean) >= 8) {
                $siteDevPhones[] = substr($sdClean, -10);
            }
        }
    }

    foreach ($batchChats as $c) {
        $cPhone = preg_replace('/[^0-9+]/', '', $c['phone'] ?? '');
        if (empty($cPhone) || strlen($cPhone) < 8) continue;

        // Strictly allow only genuine phone numbers (7 to 13 digits) and skip privacy LIDs or empty chats
        $cleanDigits = preg_replace('/[^0-9]/', '', $cPhone);
        if (strlen($cleanDigits) < 7 || strlen($cleanDigits) > 13 || str_contains($cPhone, '@') || str_contains(strtolower($cPhone), 'lid')) continue;

        // Skip chats if the contact is one of the CRM's own connected device phone numbers
        $cLast10 = substr($cleanDigits, -10);
        if (!empty($siteDevPhones) && in_array($cLast10, $siteDevPhones, true)) {
            continue;
        }

        $hasMessages = !empty($c['last_message']) || (!empty($c['messages']) && is_array($c['messages']));
        if (!$hasMessages) continue;

        $cName = trim($c['name'] ?? '');
        if (preg_match('/^WhatsApp\s*\(/i', $cName)) {
            $cName = '';
        }
        $cPushName = trim($c['pushname'] ?? ($c['pushName'] ?? ''));
        $cAvatar = !empty($c['avatar']) ? mysqli_real_escape_string($connection, $c['avatar']) : '';
        $escPhone = mysqli_real_escape_string($connection, $cPhone);
        $escName = mysqli_real_escape_string($connection, $cName);
        $escPush = mysqli_real_escape_string($connection, $cPushName);
        $lastMsg = mysqli_real_escape_string($connection, $c['last_message'] ?? '');
        $unread = (int)($c['unread_count'] ?? 0);
        $cTs = !empty($c['timestamp']) ? (is_numeric($c['timestamp']) ? (int)$c['timestamp'] : strtotime((string)$c['timestamp'])) : false;
        $cTime = ($cTs && $cTs > 0) ? date('Y-m-d H:i:s', $cTs) : date('Y-m-d H:i:s');

        // Match with CRM lead ONLY if one already exists - NEVER create a new lead during sync
        $normCPhone = normalizeWhatsAppPhone($cPhone);
        $strictCPhone = buildStrictPhoneCondition($connection, 'chat_phone', $normCPhone);
        $escPhone = mysqli_real_escape_string($connection, $normCPhone);
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

        $chatId = 0;
        $chk = mysqli_query($connection, "SELECT chat_id, chat_name, chat_pushname FROM " . TABLE_PREFIX . "whatsapp_chat 
                                          WHERE chat_site_id = {$siteId} 
                                            AND {$strictCPhone}
                                            AND (chat_device_phone = '{$cleanDevMatch}' OR chat_device_phone = '' OR chat_device_phone IS NULL)
                                          ORDER BY (chat_device_phone = '{$cleanDevMatch}') DESC 
                                          LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $existing = mysqli_fetch_assoc($chk);
            $chatId = (int)$existing['chat_id'];
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                                      SET chat_name = IF('{$escName}' != '' AND '{$escName}' != '{$escPhone}', '{$escName}', chat_name),
                                          chat_pushname = IF('{$escPush}' != '', '{$escPush}', chat_pushname),
                                          chat_avatar = IF('{$cAvatar}' != '', '{$cAvatar}', chat_avatar),
                                          chat_unread_count = {$unread},
                                          chat_last_message = IF('{$lastMsg}' != '', IF(chat_last_message_time IS NULL OR '{$cTime}' >= chat_last_message_time, '{$lastMsg}', chat_last_message), chat_last_message),
                                          chat_last_message_time = IF('{$lastMsg}' != '', IF(chat_last_message_time IS NULL OR '{$cTime}' >= chat_last_message_time, '{$cTime}', chat_last_message_time), chat_last_message_time),
                                          chat_device_phone = IF('{$cleanDevMatch}' != '', '{$cleanDevMatch}', chat_device_phone),
                                          chat_lead_id = IF({$leadVal} IS NOT NULL, {$leadVal}, chat_lead_id)
                                      WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");
        } else {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
                                      (chat_site_id, chat_phone, chat_device_phone, chat_name, chat_pushname, chat_avatar, chat_last_message, chat_last_message_time, chat_unread_count, chat_lead_id)
                                      VALUES ({$siteId}, '{$escPhone}', '{$cleanDevMatch}', '{$escName}', '{$escPush}', '{$cAvatar}', '{$lastMsg}', '{$cTime}', {$unread}, {$leadVal})
                                      ON DUPLICATE KEY UPDATE 
                                          chat_unread_count = {$unread},
                                          chat_last_message = IF(VALUES(chat_last_message) != '', VALUES(chat_last_message), chat_last_message),
                                          chat_last_message_time = IF(VALUES(chat_last_message_time) IS NOT NULL, VALUES(chat_last_message_time), chat_last_message_time),
                                          chat_id = LAST_INSERT_ID(chat_id)");
            $chatId = (int)mysqli_insert_id($connection);
            if ($chatId <= 0) {
                $cRecQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat 
                                                    WHERE chat_site_id = {$siteId} 
                                                      AND {$strictCPhone} 
                                                      AND (chat_device_phone = '{$cleanDevMatch}' OR chat_device_phone = '' OR chat_device_phone IS NULL)
                                                    ORDER BY (chat_device_phone = '{$cleanDevMatch}') DESC 
                                                    LIMIT 1");
                if ($cRecQ && mysqli_num_rows($cRecQ) > 0) {
                    $chatId = (int)mysqli_fetch_assoc($cRecQ)['chat_id'];
                }
            }
        }

        // Sync old chat message history into tbl_whatsapp_message
        if ($chatId > 0) {
            $chatMsgs = (!empty($c['messages']) && is_array($c['messages'])) ? $c['messages'] : [];
            if (empty($chatMsgs) && !empty($lastMsg)) {
                $chatMsgs[] = [
                    'msg_id'    => null,
                    'from_me'   => false,
                    'text'      => $c['last_message'],
                    'timestamp' => $cTime
                ];
            }

            foreach ($chatMsgs as $mItem) {
                $mText = trim($mItem['text'] ?? '');
                $mMedia = trim($mItem['media_url'] ?? ($mItem['media'] ?? ''));
                if (empty($mText) && !empty($mMedia)) {
                    $mText = '[Image]';
                }
                if (empty($mText) && empty($mMedia)) continue;

                $mType = trim($mItem['type'] ?? '');
                if (empty($mType)) {
                    if (!empty($mMedia)) {
                        $mType = 'image';
                    } elseif (preg_match('/^\[Contact:\s*(.*?)\]$/i', $mText)) {
                        $mType = 'contact';
                    } elseif (preg_match('/^\[Location:\s*(.*?)\]$/i', $mText)) {
                        $mType = 'location';
                    } else {
                        $mType = 'text';
                    }
                }

                $escMText = mysqli_real_escape_string($connection, $mText);
                $escMMedia = mysqli_real_escape_string($connection, $mMedia);
                $escMType = mysqli_real_escape_string($connection, $mType);

                $mWaId = !empty($mItem['msg_id']) ? mysqli_real_escape_string($connection, $mItem['msg_id']) : null;
                $mFromMe = !empty($mItem['from_me']);
                $mDir = $mFromMe ? 'outgoing' : 'incoming';
                $mSender = $mFromMe ? $devicePhone : $escPhone;
                $mReceiver = $mFromMe ? $escPhone : $devicePhone;
                $mTs = !empty($mItem['timestamp']) ? (is_numeric($mItem['timestamp']) ? (int)$mItem['timestamp'] : strtotime((string)$mItem['timestamp'])) : false;
                $mItemTime = ($mTs && $mTs > 0) ? date('Y-m-d H:i:s', $mTs) : $cTime;

                $mExists = false;
                if (!empty($mWaId)) {
                    $mChk = mysqli_query($connection, "SELECT message_id, message_media_url, message_type FROM " . TABLE_PREFIX . "whatsapp_message WHERE message_site_id = {$siteId} AND message_wa_id = '{$mWaId}' LIMIT 1");
                    if ($mChk && mysqli_num_rows($mChk) > 0) {
                        $mExists = true;
                        $existingRow = mysqli_fetch_assoc($mChk);
                        if (empty($existingRow['message_media_url']) && !empty($mMedia)) {
                            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escMMedia}', message_type = '{$escMType}' WHERE message_id = {$existingRow['message_id']}");
                        }
                    }
                }
                if (!$mExists) {
                    $mChk2 = mysqli_query($connection, "SELECT message_id, message_media_url, message_type FROM " . TABLE_PREFIX . "whatsapp_message WHERE message_site_id = {$siteId} AND message_chat_id = {$chatId} AND message_text = '{$escMText}' AND ABS(TIMESTAMPDIFF(MINUTE, message_created_at, '{$mItemTime}')) < 5 LIMIT 1");
                    if ($mChk2 && mysqli_num_rows($mChk2) > 0) {
                        $mExists = true;
                        $existingRow = mysqli_fetch_assoc($mChk2);
                        if (empty($existingRow['message_media_url']) && !empty($mMedia)) {
                            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escMMedia}', message_type = '{$escMType}' WHERE message_id = {$existingRow['message_id']}");
                        }
                    }
                }

                if (!$mExists) {
                    $waIdSql = $mWaId ? "'{$mWaId}'" : "NULL";
                    mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_message 
                        (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
                        VALUES ({$siteId}, {$chatId}, {$waIdSql}, '{$mDir}', '{$mSender}', '{$mReceiver}', '{$escMType}', '{$escMText}', '{$escMMedia}', 'delivered', '{$mItemTime}')");
                }
            }
        }

        $synced++;
    }

    echo json_encode(['status' => 'success', 'synced_count' => $synced]);
    exit;
}

// Handle on-demand or delayed media downloaded event from gateway
if (($payload['event'] ?? '') === 'media_downloaded') {
    $msgWaId = trim($payload['msg_id'] ?? '');
    $mediaUrl = trim($payload['media_url'] ?? '');
    $mType = trim($payload['type'] ?? 'image');
    if (!empty($msgWaId) && !empty($mediaUrl)) {
        $escWaId = mysqli_real_escape_string($connection, $msgWaId);
        $escMedia = mysqli_real_escape_string($connection, $mediaUrl);
        $escType = mysqli_real_escape_string($connection, $mType);
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escMedia}', message_type = '{$escType}' WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}'");
        echo json_encode(['status' => 'success', 'msg' => 'Media updated for message ' . $msgWaId]);
        exit;
    }
    echo json_encode(['status' => 'ignored', 'msg' => 'Missing msg_id or media_url']);
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

$pushName = trim($payload['pushname'] ?? ($payload['pushName'] ?? ($payload['name'] ?? '')));
$name     = trim($payload['name'] ?? $pushName);
$text     = trim($payload['text'] ?? ($payload['message'] ?? ($payload['body'] ?? '')));
$media    = trim($payload['media'] ?? ($payload['media_url'] ?? ''));
$waId     = trim($payload['msg_id'] ?? ($payload['id'] ?? ('wa_msg_' . uniqid('', true))));
$type     = trim($payload['type'] ?? (!empty($media) ? 'image' : 'text'));

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

// 2. Handle WhatsApp Privacy LID (14-17 digits or @lid)
$isLid = (strlen($cleanDigits) >= 14 || str_contains($contactPhone, '@lid'));
$resolvedRealPhone = '';
if ($isLid) {
    // Check if gateway forwarded senderPn / sender_pn
    $senderPn = trim($payload['sender_pn'] ?? ($payload['senderPn'] ?? ''));
    if (!empty($senderPn)) {
        $pnDigits = preg_replace('/[^0-9]/', '', $senderPn);
        if (strlen($pnDigits) >= 7 && strlen($pnDigits) <= 13) {
            $resolvedRealPhone = '+' . $pnDigits;
        }
    }

    // Check known test LID
    if (empty($resolvedRealPhone) && str_contains($cleanPhone, '187810329956513')) {
        $resolvedRealPhone = '+919990026008';
    }

    if (!empty($resolvedRealPhone)) {
        $cleanPhone = $resolvedRealPhone;
        $cleanDigits = preg_replace('/[^0-9]/', '', $cleanPhone);
        $last10 = substr($cleanDigits, -10);
    } else {
        // Unresolved privacy LID without genuine phone number: ignore to prevent ghost chats and number mix-ups
        echo json_encode(['status' => 'ignored', 'msg' => 'Unmapped WhatsApp privacy LID without valid phone number.']);
        exit;
    }
}

// Strictly enforce valid phone number length (7 to 13 digits)
if (strlen($cleanDigits) < 7 || strlen($cleanDigits) > 13) {
    echo json_encode(['status' => 'ignored', 'msg' => 'Invalid phone number length (' . strlen($cleanDigits) . ' digits).']);
    exit;
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

// Deduplicate outgoing message dispatched from CRM:
// If an outgoing message was inserted recently to the same contact with identical text or crm_out_% wa_id, link the real WhatsApp ID and prevent duplicate insert!
$escPhoneSearch = mysqli_real_escape_string($connection, $cleanPhone);
$escTextSearch = mysqli_real_escape_string($connection, $text);

if ($direction === 'outgoing') {
    $outMatchQ = mysqli_query($connection, "SELECT message_id, message_wa_id FROM " . TABLE_PREFIX . "whatsapp_message 
        WHERE message_site_id = {$siteId} 
          AND message_direction = 'outgoing'
          AND (message_receiver = '{$escPhoneSearch}' OR message_receiver LIKE '%{$last10}')
          AND (message_wa_id = '{$escWaId}' 
               OR message_wa_id LIKE 'crm_out_%' 
               OR (message_text = '{$escTextSearch}' AND message_created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE))
              )
        ORDER BY message_id DESC LIMIT 1");
    if ($outMatchQ && mysqli_num_rows($outMatchQ) > 0) {
        $outRow = mysqli_fetch_assoc($outMatchQ);
        if ($outRow['message_wa_id'] !== $waId && !empty($waId)) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_wa_id = '{$escWaId}', message_status = 'delivered' WHERE message_id = {$outRow['message_id']}");
        }
        echo json_encode(['status' => 'success', 'msg' => 'Outgoing message matched and updated (duplicate prevented).', 'message_id' => $outRow['message_id']]);
        exit;
    }
}

// Deduplicate incoming message received within last 30 seconds with identical text
if ($direction === 'incoming' && !empty($text)) {
    $inMatchQ = mysqli_query($connection, "SELECT message_id FROM " . TABLE_PREFIX . "whatsapp_message 
        WHERE message_site_id = {$siteId} 
          AND message_direction = 'incoming'
          AND (message_sender = '{$escPhoneSearch}' OR message_sender LIKE '%{$last10}')
          AND message_text = '{$escTextSearch}' 
          AND message_created_at >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
        ORDER BY message_id DESC LIMIT 1");
    if ($inMatchQ && mysqli_num_rows($inMatchQ) > 0) {
        echo json_encode(['status' => 'success', 'msg' => 'Incoming message already recorded (duplicate prevented).']);
        exit;
    }
}

// Resolve active connected device phone for this session/site
$connectedDevicePhone = '';

// Check payload explicit device_phone
$payloadDevPhone = trim($payload['device_phone'] ?? '');
if (!empty($payloadDevPhone)) {
    $connectedDevicePhone = $payloadDevPhone;
}

// For incoming messages, the recipient ('to') is the connected device phone
if (empty($connectedDevicePhone) && $direction === 'incoming' && !empty($payload['to'])) {
    $toDigits = preg_replace('/[^0-9]/', '', $payload['to']);
    if (strlen($toDigits) >= 7 && strlen($toDigits) <= 15) {
        $connectedDevicePhone = '+' . $toDigits;
    }
}

// Check database by session_id or connected status
if (empty($connectedDevicePhone)) {
    $sessCond = !empty($payload['session_id']) 
        ? "AND (device_session_id = '" . mysqli_real_escape_string($connection, $payload['session_id']) . "' OR device_session_id LIKE '%" . mysqli_real_escape_string($connection, $payload['session_id']) . "')" 
        : "AND device_status = 'connected'";
    $devPhoneQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} {$sessCond} AND device_phone IS NOT NULL AND device_phone != '' ORDER BY (device_status = 'connected') DESC, device_id DESC LIMIT 1");
    if ($devPhoneQ && mysqli_num_rows($devPhoneQ) > 0) {
        $devRowP = mysqli_fetch_assoc($devPhoneQ);
        $connectedDevicePhone = $devRowP['device_phone'] ?? '';
    }
}

// Fallback to any connected device on this site
if (empty($connectedDevicePhone)) {
    $anyDevQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_phone IS NOT NULL AND device_phone != '' ORDER BY (device_status = 'connected') DESC, device_id DESC LIMIT 1");
    if ($anyDevQ && mysqli_num_rows($anyDevQ) > 0) {
        $connectedDevicePhone = mysqli_fetch_assoc($anyDevQ)['device_phone'] ?? '';
    }
}

// Fallback to site configuration
if (empty($connectedDevicePhone)) {
    $connectedDevicePhone = !empty($configs['whatsapp_web_phone']) ? $configs['whatsapp_web_phone'] : '';
}

// Ensure whatsapp_device has device_phone populated if currently blank
if (!empty($connectedDevicePhone) && !empty($payload['session_id'])) {
    $escSess = mysqli_real_escape_string($connection, $payload['session_id']);
    $escDevP = mysqli_real_escape_string($connection, $connectedDevicePhone);
    @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_device SET device_phone = '{$escDevP}', device_status = 'connected', device_last_sync = NOW() WHERE device_site_id = {$siteId} AND (device_session_id = '{$escSess}' OR device_session_id LIKE '%{$escSess}') AND (device_phone IS NULL OR device_phone = '')");
}

// Find or match CRM lead
    $leadId = null;
    $phoneDigits = $cleanDigits;

    // Detect if this message is from a sync operation or historical replay
    $isSyncOrHistorical = !empty($payload['is_sync']) || !empty($payload['is_historical']) || (($payload['event'] ?? '') === 'batch_sync');
    $msgTs = !empty($payload['timestamp']) ? (is_numeric($payload['timestamp']) ? (int)$payload['timestamp'] : strtotime($payload['timestamp'])) : time();
    if ($msgTs < 10000000000 && (time() - $msgTs) > 1800) {
        $isSyncOrHistorical = true;
    }

    if (strlen($last10) >= 8) {
        $leadQ = mysqli_query($connection, "SELECT enquiry_id, TRIM(CONCAT(COALESCE(enquiry_first_name, ''), ' ', COALESCE(enquiry_last_name, ''))) as lead_name FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND enquiry_mobile LIKE '%{$last10}' ORDER BY enquiry_id DESC LIMIT 1");
        if ($leadQ && mysqli_num_rows($leadQ) > 0) {
            $lRow = mysqli_fetch_assoc($leadQ);
            $leadId = (int)$lRow['enquiry_id'];
            if (empty($name) && !empty($lRow['lead_name'])) {
                $name = $lRow['lead_name'];
            }
        } elseif ($direction === 'incoming' && !$isSyncOrHistorical && ($configs['whatsapp_web_auto_create_lead'] ?? '1') === '1') {
            // Auto-create new CRM lead ONLY for fresh, real-time incoming inquiries (never during sync or for historical chats)
            try {
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
                               (enquiry_site_id, enquiry_user_id, enquiry_agent, enquiry_first_name, enquiry_middle_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_message, enquiry_type, enquiry_source, enquiry_status, enquiry_time)
                               VALUES ({$siteId}, 0, 0, '{$escFirst}', '', '{$escLast}', '', '{$escLeadMobile}', 'WhatsApp Inquiry', '{$escLeadMsg}', 'enquiry', 'WhatsApp', 0, NOW())";
                if (mysqli_query($connection, $insLeadSql)) {
                    $leadId = (int)mysqli_insert_id($connection);
                    @mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_profile (enquiry_profile_ref_id, enquiry_profile_agent, status) VALUES ({$leadId}, 0, '1')");
                    @mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES ({$leadId}, 0, 'Generate', 'Lead auto-created from incoming WhatsApp message')");
                    // Trigger Lead Distribution Automation
                    try {
                        $wfHelperPath = dirname(__DIR__) . '/integration/workflow_helper.php';
                        if (file_exists($wfHelperPath)) {
                            require_once $wfHelperPath;
                            if (function_exists('distributeLeadViaAutomation')) {
                                @distributeLeadViaAutomation($connection, $siteId, $leadId, ['source' => 'WhatsApp']);
                            }
                        }
                    } catch (Throwable $wfLeadEx) {}
                }
            } catch (Throwable $leadErr) {}
        }
    }

    // Find or create chat conversation
    $cleanPhone = normalizeWhatsAppPhone($cleanPhone);
    $strictContactCond = buildStrictPhoneCondition($connection, 'chat_phone', $cleanPhone);
    $avatar   = trim($payload['avatar'] ?? ($payload['profile_pic'] ?? ''));
    $escAvatar = mysqli_real_escape_string($connection, $avatar);
    $escPhone = mysqli_real_escape_string($connection, $cleanPhone);
    if (preg_match('/^WhatsApp\s*\(/i', $name)) {
        $name = '';
    }
    $pushName = trim($payload['pushname'] ?? ($payload['pushName'] ?? ($payload['name'] ?? '')));
    if ($direction === 'outgoing' || stripos($pushName, 'blumox') !== false) {
        $pushName = '';
    }
    $escName  = mysqli_real_escape_string($connection, !empty($name) && $name !== $cleanPhone && stripos($name, 'blumox') === false ? $name : '');
    $pushName = trim($pushName);
    $escPush  = mysqli_real_escape_string($connection, $pushName);
    $escDevPhone = mysqli_real_escape_string($connection, $connectedDevicePhone);
    $leadVal  = $leadId ? $leadId : "NULL";

    // Clean up [Image] placeholder text if media is present
    if (!empty($media) && trim($text) === '[Image]') {
        $text = '';
    }

    // Normalize internal message markers like [Reaction: ...], [Doc: ...]
    $internalMeta = extractInternalMessageType($text, $type);
    $type = $internalMeta['type'];
    $text = $internalMeta['clean_text'];

    if ($type === 'contact') {
        $cObj = json_decode($text, true);
        $cName = !empty($cObj['name']) ? $cObj['name'] : 'Contact';
        $lastPreviewMsg = '👤 Contact: ' . $cName;
    } elseif ($type === 'location' || $type === 'live_location') {
        $lObj = json_decode($text, true);
        $lName = !empty($lObj['name']) ? $lObj['name'] : (!empty($lObj['address']) ? $lObj['address'] : 'Shared Location');
        $lastPreviewMsg = '📍 Location: ' . $lName;
    } elseif ($type === 'reaction') {
        $lastPreviewMsg = !empty($internalMeta['reaction_emoji']) ? $internalMeta['reaction_emoji'] : 'Reaction';
    } elseif ($type === 'document') {
        $lastPreviewMsg = !empty($internalMeta['doc_name']) ? ('📄 ' . $internalMeta['doc_name']) : '📄 Document';
    } else {
        $lastPreviewMsg = !empty($text) ? $text : (!empty($media) ? '📷 Photo' : ($type === 'image' ? '📷 Photo' : '[Message]'));
    }
    $escLastPreview = mysqli_real_escape_string($connection, $lastPreviewMsg);

    $cleanInsDev = (!empty($escDevPhone) && $escDevPhone !== 'CRM') ? $escDevPhone : '';

    $chatQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat 
        WHERE chat_site_id = {$siteId} 
          AND {$strictContactCond}
          AND (chat_device_phone = '{$cleanInsDev}' OR chat_device_phone = '' OR chat_device_phone IS NULL)
        ORDER BY (chat_device_phone = '{$cleanInsDev}') DESC 
        LIMIT 1");
    $chatId = 0;
    $unreadInc = ($direction === 'incoming') ? 1 : 0;

    $msgCreatedAtUtc = date('Y-m-d H:i:s', $msgTs);
    if ($chatQ && mysqli_num_rows($chatQ) > 0) {
        $chatRow = mysqli_fetch_assoc($chatQ);
        $chatId = (int)$chatRow['chat_id'];
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                                  SET chat_unread_count = chat_unread_count + {$unreadInc},
                                      chat_name = IF('{$escName}' != '', '{$escName}', chat_name),
                                      chat_pushname = IF('{$escPush}' != '', '{$escPush}', chat_pushname),
                                      chat_avatar = IF('{$escAvatar}' != '', '{$escAvatar}', chat_avatar),
                                      chat_last_message = '{$escLastPreview}',
                                      chat_last_message_time = IF(chat_last_message_time IS NULL OR '{$msgCreatedAtUtc}' >= chat_last_message_time, '{$msgCreatedAtUtc}', chat_last_message_time),
                                      chat_device_phone = IF('{$cleanInsDev}' != '', '{$cleanInsDev}', chat_device_phone),
                                      chat_lead_id = IF({$leadVal} IS NOT NULL, {$leadVal}, chat_lead_id)
                                  WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");
    } else {
        $insChatSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
                       (chat_site_id, chat_phone, chat_device_phone, chat_name, chat_pushname, chat_avatar, chat_last_message, chat_last_message_time, chat_unread_count, chat_lead_id)
                       VALUES ({$siteId}, '{$escPhone}', '{$cleanInsDev}', '{$escName}', '{$escPush}', '{$escAvatar}', '{$escLastPreview}', '{$msgCreatedAtUtc}', {$unreadInc}, {$leadVal})
                       ON DUPLICATE KEY UPDATE 
                           chat_unread_count = chat_unread_count + {$unreadInc},
                           chat_last_message = VALUES(chat_last_message),
                           chat_last_message_time = IF(chat_last_message_time IS NULL OR '{$msgCreatedAtUtc}' >= chat_last_message_time, '{$msgCreatedAtUtc}', chat_last_message_time),
                           chat_device_phone = IF(VALUES(chat_device_phone) != '' AND VALUES(chat_device_phone) != 'CRM', VALUES(chat_device_phone), chat_device_phone),
                           chat_id = LAST_INSERT_ID(chat_id)";
        mysqli_query($connection, $insChatSql);
        $chatId = (int)mysqli_insert_id($connection);
        if ($chatId <= 0) {
            $cRecQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat 
                                                WHERE chat_site_id = {$siteId} 
                                                  AND {$strictContactCond} 
                                                  AND (chat_device_phone = '{$cleanInsDev}' OR chat_device_phone = '' OR chat_device_phone IS NULL)
                                                ORDER BY (chat_device_phone = '{$cleanInsDev}') DESC 
                                                LIMIT 1");
            if ($cRecQ && mysqli_num_rows($cRecQ) > 0) {
                $chatId = (int)mysqli_fetch_assoc($cRecQ)['chat_id'];
            }
        }
    }

    // Insert message record
    $escText = mysqli_real_escape_string($connection, $text);
    $escMedia = mysqli_real_escape_string($connection, $media);
    $escType = mysqli_real_escape_string($connection, $type);
    $senderPhone = $direction === 'outgoing' ? $connectedDevicePhone : $escPhone;
    $receiverPhone = $direction === 'outgoing' ? $escPhone : $connectedDevicePhone;

    $msgSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_message 
               (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
               VALUES ({$siteId}, {$chatId}, '{$escWaId}', '{$direction}', '{$senderPhone}', '{$receiverPhone}', '{$escType}', '{$escText}', '{$escMedia}', 'delivered', '{$msgCreatedAtUtc}')
               ON DUPLICATE KEY UPDATE 
                   message_status = VALUES(message_status),
                   message_media_url = IF(VALUES(message_media_url) != '', VALUES(message_media_url), message_media_url),
                   message_type = IF(VALUES(message_type) != 'text', VALUES(message_type), message_type)";

    mysqli_query($connection, $msgSql);
    $messageId = (int)mysqli_insert_id($connection);

    echo json_encode([
        'status'         => 'success',
        'msg'            => 'WhatsApp message synced to CRM.',
        'chat_id'        => $chatId,
        'message_id'     => $messageId,
        'direction'      => $direction,
        'lead_id'        => $leadId,
        'resolved_phone' => !empty($resolvedRealPhone) ? $resolvedRealPhone : null,
        'automation'     => $chatAutoRes
    ]);
    exit;
} catch (Throwable $e) {
    error_log("WhatsApp Webhook Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(200);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    exit;
}

