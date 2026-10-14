<?php
/**
 * WhatsApp Device & Outbound Messaging Helper
 * Path: api/v3/whatsapp/device_helper.php
 * Handles device resolution priority (assigned user device > default device > single connected device)
 * and direct WhatsApp outbound messaging across Itinerary, Quotation, and Chat features.
 */

if (!defined('TABLE_PREFIX')) {
    define('TABLE_PREFIX', 'isw_');
}

/**
 * Resolves the appropriate WhatsApp device for an outbound message or action.
 * Priority rules:
 * 1. User Assigned Device: If a WhatsApp device is assigned to the sender user and is connected,
 *    USE THIS DEVICE (do NOT use default device).
 * 2. Default Device: If sender user has no assigned device, use the site's default device (device_is_default = 1).
 * 3. Site Configuration Default: whatsapp_default_device_id in isw_site_configuration.
 * 4. Single Connected Device: If only 1 device is connected on the site, use this device.
 * 5. Any Connected Device: Earliest connected device on the site.
 *
 * @param mysqli $connection
 * @param int $siteId
 * @param int $senderUserId
 * @return array|null Device associative array or null if no connected device exists
 */
function resolveSenderWhatsAppDevice($connection, $siteId, $senderUserId = 0) {
    if (!$connection || $siteId <= 0) {
        return null;
    }

    $siteId = (int)$siteId;
    $senderUserId = (int)$senderUserId;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Check if sender user has an assigned connected device
    if ($senderUserId > 0) {
        $agQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
            WHERE device_site_id = {$siteId} 
              AND device_assigned_user_id = {$senderUserId} 
              AND device_status = 'connected' 
            LIMIT 1");
        if ($agQ && mysqli_num_rows($agQ) > 0) {
            $dev = mysqli_fetch_assoc($agQ);
            $dev['resolved_reason'] = 'user_assigned';
            return $dev;
        }
    }

    // 2. Check site's explicit default device
    $defQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId} 
          AND device_is_default = 1 
          AND device_status = 'connected' 
        LIMIT 1");
    if ($defQ && mysqli_num_rows($defQ) > 0) {
        $dev = mysqli_fetch_assoc($defQ);
        $dev['resolved_reason'] = 'site_default';
        return $dev;
    }

    // 3. Check site_configuration whatsapp_default_device_id
    $cfgDefQ = mysqli_query($connection, "SELECT site_configuration_value FROM {$prefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key = 'whatsapp_default_device_id' 
        LIMIT 1");
    if ($cfgDefQ && mysqli_num_rows($cfgDefQ) > 0) {
        $cfgRow = mysqli_fetch_assoc($cfgDefQ);
        $cfgDevId = (int)($cfgRow['site_configuration_value'] ?? 0);
        if ($cfgDevId > 0) {
            $chkQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
                WHERE device_site_id = {$siteId} 
                  AND device_id = {$cfgDevId} 
                  AND device_status = 'connected' 
                LIMIT 1");
            if ($chkQ && mysqli_num_rows($chkQ) > 0) {
                $dev = mysqli_fetch_assoc($chkQ);
                $dev['resolved_reason'] = 'site_configuration';
                return $dev;
            }
        }
    }

    // 4. Count connected devices on the site
    $cntQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId} AND device_status = 'connected'");
    $connectedCount = ($cntQ && $cr = mysqli_fetch_assoc($cntQ)) ? (int)$cr['cnt'] : 0;

    if ($connectedCount === 1) {
        $singleQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
            WHERE device_site_id = {$siteId} AND device_status = 'connected' 
            LIMIT 1");
        if ($singleQ && mysqli_num_rows($singleQ) > 0) {
            $dev = mysqli_fetch_assoc($singleQ);
            $dev['resolved_reason'] = 'single_connected_device';
            return $dev;
        }
    }

    // 5. Fallback: Any connected device on this site
    $anyQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId} AND device_status = 'connected' 
        ORDER BY device_id ASC 
        LIMIT 1");
    if ($anyQ && mysqli_num_rows($anyQ) > 0) {
        $dev = mysqli_fetch_assoc($anyQ);
        $dev['resolved_reason'] = 'any_connected';
        return $dev;
    }

    return null;
}

/**
 * Ensures that if the site has only 1 device, or no default device is configured yet,
 * the newly added/connected device is automatically marked as default.
 *
 * @param mysqli $connection
 * @param int $siteId
 * @param string|int|null $sessionIdOrDevId
 */
function ensureFirstDeviceIsDefault($connection, $siteId, $sessionIdOrDevId = null) {
    if (!$connection || $siteId <= 0) {
        return;
    }

    $siteId = (int)$siteId;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // Check if any device is currently marked as default
    $hasDefQ = mysqli_query($connection, "SELECT device_id FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId} AND device_is_default = 1 LIMIT 1");
    $hasDefault = ($hasDefQ && mysqli_num_rows($hasDefQ) > 0);

    // Total device count
    $totQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId}");
    $totalCount = ($totQ && $tr = mysqli_fetch_assoc($totQ)) ? (int)$tr['cnt'] : 0;

    // If no default exists, or only 1 device is on site, mark this device as default!
    if (!$hasDefault || $totalCount <= 1) {
        $targetDevId = 0;
        if (!empty($sessionIdOrDevId)) {
            if (is_numeric($sessionIdOrDevId) && (int)$sessionIdOrDevId > 0) {
                $targetDevId = (int)$sessionIdOrDevId;
            } else {
                $escSess = mysqli_real_escape_string($connection, (string)$sessionIdOrDevId);
                $devQ = mysqli_query($connection, "SELECT device_id FROM {$prefix}whatsapp_device 
                    WHERE device_site_id = {$siteId} AND device_session_id = '{$escSess}' LIMIT 1");
                if ($devQ && $dr = mysqli_fetch_assoc($devQ)) {
                    $targetDevId = (int)$dr['device_id'];
                }
            }
        }

        if ($targetDevId <= 0) {
            $firstDevQ = mysqli_query($connection, "SELECT device_id FROM {$prefix}whatsapp_device 
                WHERE device_site_id = {$siteId} ORDER BY device_id ASC LIMIT 1");
            if ($firstDevQ && $fr = mysqli_fetch_assoc($firstDevQ)) {
                $targetDevId = (int)$fr['device_id'];
            }
        }

        if ($targetDevId > 0) {
            mysqli_query($connection, "UPDATE {$prefix}whatsapp_device 
                SET device_is_default = 0 
                WHERE device_site_id = {$siteId}");
            mysqli_query($connection, "UPDATE {$prefix}whatsapp_device 
                SET device_is_default = 1 
                WHERE device_site_id = {$siteId} AND device_id = {$targetDevId}");

            // Persist to site_configuration
            $escKey = mysqli_real_escape_string($connection, 'whatsapp_default_device_id');
            $escVal = mysqli_real_escape_string($connection, (string)$targetDevId);
            mysqli_query($connection, "INSERT INTO {$prefix}site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
                ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}'");
        }
    }
}

/**
 * Dispatches an outbound WhatsApp message directly via the appropriate WhatsApp device.
 * Enforces sender assigned device > default device > single connected device.
 * Updates chat history and timeline in isw_whatsapp_chat and isw_whatsapp_message.
 *
 * @param mysqli $connection
 * @param int $siteId
 * @param int $senderUserId
 * @param string $recipientPhone
 * @param string $messageText
 * @param array $options [lead_id, type, media_url, file_name, device_id]
 * @return array ['status' => 'success'|'error', 'msg' => '...', 'device' => array]
 */
function sendWhatsAppDirectMessage($connection, $siteId, $senderUserId, $recipientPhone, $messageText, array $options = []) {
    if (!$connection || $siteId <= 0) {
        return ['status' => 'error', 'msg' => 'Invalid database connection or site identifier.'];
    }

    $siteId = (int)$siteId;
    $senderUserId = (int)$senderUserId;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Resolve Device
    // Rule: If user has an assigned device, use it. Else use default (or single connected device).
    $device = null;
    $forcedDeviceId = isset($options['device_id']) && (int)$options['device_id'] > 0 ? (int)$options['device_id'] : 0;

    // Check if user has an assigned device first
    if ($senderUserId > 0) {
        $agQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
            WHERE device_site_id = {$siteId} 
              AND device_assigned_user_id = {$senderUserId} 
              AND device_status = 'connected' 
            LIMIT 1");
        if ($agQ && mysqli_num_rows($agQ) > 0) {
            $device = mysqli_fetch_assoc($agQ);
        }
    }

    // If no assigned device, use forcedDeviceId (if valid) or standard resolution
    if (!$device && $forcedDeviceId > 0) {
        $fdQ = mysqli_query($connection, "SELECT * FROM {$prefix}whatsapp_device 
            WHERE device_site_id = {$siteId} AND device_id = {$forcedDeviceId} AND device_status = 'connected' LIMIT 1");
        if ($fdQ && mysqli_num_rows($fdQ) > 0) {
            $device = mysqli_fetch_assoc($fdQ);
        }
    }

    if (!$device) {
        $device = resolveSenderWhatsAppDevice($connection, $siteId, $senderUserId);
    }

    if (!$device || empty($device['device_session_id'])) {
        return [
            'status' => 'error',
            'msg'    => 'No active or connected WhatsApp device found. Please connect or assign a device first.'
        ];
    }

    $targetSessionId = $device['device_session_id'];
    $senderPhone = !empty($device['device_phone']) ? $device['device_phone'] : 'CRM';

    // 2. Clean and format recipient phone
    $rDigits = preg_replace('/[^0-9]/', '', (string)$recipientPhone);
    if (str_starts_with($rDigits, '0')) {
        $rDigits = ltrim($rDigits, '0');
    }
    if (strlen($rDigits) === 10) {
        $rDigits = '91' . $rDigits;
    }
    if (strlen($rDigits) < 8) {
        return [
            'status' => 'error',
            'msg'    => 'Recipient phone number is invalid (' . $recipientPhone . ').'
        ];
    }
    $cleanRecipientPhone = '+' . $rDigits;
    $escRecipientPhone = mysqli_real_escape_string($connection, $cleanRecipientPhone);

    // 3. Resolve or create chat conversation
    $leadId = isset($options['lead_id']) ? (int)$options['lead_id'] : (isset($options['enquiry_id']) ? (int)$options['enquiry_id'] : 0);
    $chatId = isset($options['chat_id']) ? (int)$options['chat_id'] : 0;

    if ($chatId <= 0) {
        $chatQ = mysqli_query($connection, "SELECT chat_id FROM {$prefix}whatsapp_chat 
            WHERE chat_site_id = {$siteId} AND chat_phone = '{$escRecipientPhone}' LIMIT 1");
        if ($chatQ && mysqli_num_rows($chatQ) > 0) {
            $cRow = mysqli_fetch_assoc($chatQ);
            $chatId = (int)$cRow['chat_id'];
        } else {
            $escSenderPhone = mysqli_real_escape_string($connection, $senderPhone);
            $leadSql = $leadId > 0 ? $leadId : "NULL";
            mysqli_query($connection, "INSERT INTO {$prefix}whatsapp_chat 
                (chat_site_id, chat_phone, chat_name, chat_device_phone, chat_lead_id) 
                VALUES ({$siteId}, '{$escRecipientPhone}', '{$escRecipientPhone}', '{$escSenderPhone}', {$leadSql})");
            $chatId = (int)mysqli_insert_id($connection);
        }
    }

    // Associate lead if not already linked
    if ($leadId > 0 && $chatId > 0) {
        mysqli_query($connection, "UPDATE {$prefix}whatsapp_chat 
            SET chat_lead_id = {$leadId} 
            WHERE chat_id = {$chatId} AND (chat_lead_id IS NULL OR chat_lead_id = 0)");
    }

    $type = trim($options['type'] ?? 'text');
    $mediaUrl = trim($options['media_url'] ?? ($options['media'] ?? ''));
    $fileName = trim($options['file_name'] ?? ($options['fileName'] ?? 'Document.pdf'));
    $now = date('Y-m-d H:i:s');
    $waMsgId = 'crm_out_' . uniqid('', true);

    $escText = mysqli_real_escape_string($connection, $messageText);
    $escMedia = mysqli_real_escape_string($connection, $mediaUrl);
    $escSender = mysqli_real_escape_string($connection, $senderPhone);
    $escReceiver = mysqli_real_escape_string($connection, $cleanRecipientPhone);
    $escType = mysqli_real_escape_string($connection, $type);

    // 4. Record outgoing message in database
    $msgSql = "INSERT INTO {$prefix}whatsapp_message 
               (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
               VALUES ({$siteId}, {$chatId}, '{$waMsgId}', 'outgoing', '{$escSender}', '{$escReceiver}', '{$escType}', '{$escText}', '{$escMedia}', 'sent', '{$now}')";
    mysqli_query($connection, $msgSql);
    $msgId = (int)mysqli_insert_id($connection);

    // 5. Update chat thread summary
    $previewText = !empty($messageText) ? $messageText : (!empty($mediaUrl) ? ($type === 'document' ? '📄 Document' : '📷 Photo') : '[Attachment]');
    $escPreview = mysqli_real_escape_string($connection, $previewText);
    mysqli_query($connection, "UPDATE {$prefix}whatsapp_chat 
        SET chat_last_message = '{$escPreview}', 
            chat_last_message_time = '{$now}',
            chat_updated_at = '{$now}',
            chat_device_phone = IF(chat_device_phone IS NULL OR chat_device_phone = '' OR chat_device_phone = 'CRM', '{$escSender}', chat_device_phone)
        WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");

    // 6. Forward payload to WhatsApp Gateway daemon
    $gwUrl = 'http://127.0.0.1:3001';
    $gwQ = mysqli_query($connection, "SELECT site_configuration_value FROM {$prefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_web_gateway_url' LIMIT 1");
    if ($gwQ && mysqli_num_rows($gwQ) > 0) {
        $gwRow = mysqli_fetch_assoc($gwQ);
        if (!empty($gwRow['site_configuration_value'])) {
            $gwUrl = trim($gwRow['site_configuration_value']);
        }
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
    $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook/';

    $gwPayload = json_encode([
        'site_id'     => $siteId,
        'session_id'  => $targetSessionId,
        'webhook_url' => $dynamicWebhookUrl,
        'to'          => $cleanRecipientPhone,
        'message'     => $messageText,
        'media'       => ($type === 'text' ? '' : $mediaUrl),
        'type'        => $type,
        'file_name'   => $fileName,
        'fileName'    => $fileName,
        'msg_id'      => $waMsgId
    ]);

    $gwEndpoint = rtrim($gwUrl, '/') . '/send-message';
    $gwRawRes = false;

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gwEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $gwPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($gwPayload)
        ]);
        $gwRawRes = curl_exec($ch);
        curl_close($ch);
    }

    if ($gwRawRes === false && ini_get('allow_url_fopen')) {
        $opts = [
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\n",
                'content'       => $gwPayload,
                'timeout'       => 10,
                'ignore_errors' => true
            ]
        ];
        $ctx = stream_context_create($opts);
        $gwRawRes = @file_get_contents($gwEndpoint, false, $ctx);
    }

    $gwSuccess = false;
    $gwErrorMsg = '';

    if ($gwRawRes !== false) {
        $gwResData = json_decode($gwRawRes, true);
        if (isset($gwResData['status']) && $gwResData['status'] === 'success') {
            $gwSuccess = true;
            if (!empty($gwResData['msg_id'])) {
                $realWaId = mysqli_real_escape_string($connection, $gwResData['msg_id']);
                mysqli_query($connection, "UPDATE {$prefix}whatsapp_message 
                    SET message_wa_id = '{$realWaId}', message_status = 'delivered' 
                    WHERE message_id = {$msgId}");
            }
        } else {
            $gwErrorMsg = $gwResData['message'] ?? 'WhatsApp device was unable to deliver message.';
        }
    } else {
        $gwErrorMsg = 'Could not communicate with WhatsApp gateway daemon on port 3001.';
    }

    if (!$gwSuccess) {
        mysqli_query($connection, "UPDATE {$prefix}whatsapp_message 
            SET message_status = 'failed' 
            WHERE message_id = {$msgId}");
        return [
            'status'  => 'error',
            'msg'     => $gwErrorMsg ?: 'Failed to send WhatsApp message.',
            'device'  => $device,
            'chat_id' => $chatId
        ];
    }

    return [
        'status'     => 'success',
        'msg'        => 'Message sent successfully via WhatsApp.',
        'device'     => [
            'device_id'   => (int)$device['device_id'],
            'phone'       => $senderPhone,
            'name'        => $device['device_name'] ?? 'WhatsApp Line',
            'session_id'  => $targetSessionId
        ],
        'chat_id'    => $chatId,
        'message_id' => $msgId
    ];
}
