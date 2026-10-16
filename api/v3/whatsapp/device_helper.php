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
                    SET message_wa_id = '{$realWaId}', message_status = 'sent' 
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

/**
 * Handles device disconnection notification from WhatsApp gateway daemon.
 * Updates device status, records dashboard alert in isw_site_configuration,
 * and sends a throttled email alert to the site administrator.
 *
 * @param mysqli $connection
 * @param int $siteId
 * @param string $sessionId
 * @param string $phone
 * @param string $reason
 * @param int $statusCode
 * @return bool
 */
function handleWhatsAppDeviceDisconnection($connection, $siteId, $sessionId, $phone = '', $reason = '', $statusCode = 0) {
    if (!$connection || (int)$siteId <= 0) {
        return false;
    }

    $siteId = (int)$siteId;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    $escSession = mysqli_real_escape_string($connection, trim($sessionId));
    $escReason = mysqli_real_escape_string($connection, trim(substr($reason ?: 'Connection dropped', 0, 250)));
    $now = date('Y-m-d H:i:s');

    // 1. Mark device as disconnected in isw_whatsapp_device
    if (!empty($escSession)) {
        mysqli_query($connection, "UPDATE {$prefix}whatsapp_device 
            SET device_status = 'disconnected',
                device_error_reason = '{$escReason}',
                device_disconnected_at = '{$now}'
            WHERE device_site_id = {$siteId} AND (device_session_id = '{$escSession}' OR device_session_id LIKE '%{$escSession}')");
    }

    // 2. Count active connected devices remaining on this site
    $remQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM {$prefix}whatsapp_device 
        WHERE device_site_id = {$siteId} AND device_status = 'connected'");
    $remCount = ($remQ && $r = mysqli_fetch_assoc($remQ)) ? (int)$r['cnt'] : 0;

    // If no active devices remain connected, update site configuration status
    if ($remCount === 0) {
        mysqli_query($connection, "INSERT INTO {$prefix}site_configuration 
            (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES ({$siteId}, 'whatsapp_web_status', 'disconnected', 1)
            ON DUPLICATE KEY UPDATE site_configuration_value = 'disconnected'");
    }

    // 3. Save In-App Dashboard Alert flag in isw_site_configuration
    $alertData = [
        'status'                   => 'disconnected',
        'session_id'               => $sessionId,
        'phone'                    => $phone,
        'reason'                   => $reason ?: 'Connection dropped',
        'status_code'              => (int)$statusCode,
        'disconnected_at'          => $now,
        'active_devices_remaining' => $remCount,
        'dismissed'                => 0
    ];
    $escAlertJson = mysqli_real_escape_string($connection, json_encode($alertData));
    mysqli_query($connection, "INSERT INTO {$prefix}site_configuration 
        (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
        VALUES ({$siteId}, 'whatsapp_disconnect_alert', '{$escAlertJson}', 1)
        ON DUPLICATE KEY UPDATE site_configuration_value = '{$escAlertJson}'");

    // 4. Send email alert to Site Administrator (throttled to at most 1 email per 30 minutes)
    sendWhatsAppDisconnectionEmail($connection, $siteId, $sessionId, $phone, $reason, $statusCode, $remCount);

    return true;
}

/**
 * Sends a throttled email notification to the site administrator when a WhatsApp connection drops.
 * Throttled to at most 1 alert per 30 minutes (1800s) to prevent spamming admin inbox during reconnection retries.
 *
 * @param mysqli $connection
 * @param int $siteId
 * @param string $sessionId
 * @param string $phone
 * @param string $reason
 * @param int $statusCode
 * @param int $remCount
 * @return bool
 */
function sendWhatsAppDisconnectionEmail($connection, $siteId, $sessionId, $phone = '', $reason = '', $statusCode = 0, $remCount = 0) {
    if (!$connection || (int)$siteId <= 0) {
        return false;
    }

    $siteId = (int)$siteId;
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Throttling check: max 1 email per 30 minutes (1800 seconds)
    $throtQ = mysqli_query($connection, "SELECT site_configuration_value FROM {$prefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'whatsapp_disconnect_last_email_time' LIMIT 1");
    if ($throtQ && $tr = mysqli_fetch_assoc($throtQ)) {
        $lastSent = (int)($tr['site_configuration_value'] ?? 0);
        if ($lastSent > 0 && (time() - $lastSent) < 1800) {
            // Suppress duplicate email notification within 30 minutes
            return false;
        }
    }

    // 2. Fetch site configurations for candidate emails
    $cfgQ = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
        FROM {$prefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key IN ('notification_admin_email', 'admin_email', 'notification_enquiry_email', 'enquiry_email', 'site_email_alert', 'site_email', 'site_name')");
    $siteCfg = [];
    if ($cfgQ) {
        while ($r = mysqli_fetch_assoc($cfgQ)) {
            $siteCfg[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $primaryAdminEmail = '';
    $candidateKeys = [
        'notification_admin_email',
        'admin_email',
        'notification_enquiry_email',
        'enquiry_email',
        'site_email_alert',
        'site_email'
    ];
    foreach ($candidateKeys as $cKey) {
        if (!empty($siteCfg[$cKey])) {
            $firstCand = trim(explode(',', $siteCfg[$cKey])[0]);
            if (filter_var($firstCand, FILTER_VALIDATE_EMAIL)) {
                $primaryAdminEmail = strtolower($firstCand);
                break;
            }
        }
    }

    // Fallback: fetch primary administrator from site_access & user table
    if (empty($primaryAdminEmail)) {
        $admQ = mysqli_query($connection, "SELECT u.user_email 
            FROM {$prefix}site_access sa
            JOIN {$prefix}user u ON u.user_id = sa.site_access_user_id
            WHERE sa.site_access_site_id = {$siteId}
              AND sa.site_access_type = 'administrator'
            ORDER BY sa.site_access_id ASC LIMIT 1");
        if ($admQ && $ar = mysqli_fetch_assoc($admQ)) {
            if (filter_var($ar['user_email'], FILTER_VALIDATE_EMAIL)) {
                $primaryAdminEmail = strtolower(trim($ar['user_email']));
            }
        }
    }

    if (empty($primaryAdminEmail)) {
        return false;
    }

    // 3. Resolve site domain and name
    $siteName = $siteCfg['site_name'] ?? '';
    $siteDomain = '';
    $siteQ = mysqli_query($connection, "SELECT site_name, site_domain, site_url FROM {$prefix}site WHERE site_id = {$siteId} LIMIT 1");
    if ($siteQ && $sr = mysqli_fetch_assoc($siteQ)) {
        if (empty($siteName) && !empty($sr['site_name'])) {
            $siteName = $sr['site_name'];
        }
        $siteDomain = $sr['site_domain'] ?? '';
    }
    if (empty($siteName)) {
        $siteName = !empty($siteDomain) ? $siteDomain : 'CRM Portal';
    }
    $loginUrl = !empty($siteDomain) ? "https://{$siteDomain}/app/dashboard/v3/default/desktop/integration/whatsapp_web.php" : "https://crm.musafirindiatrip.com/";

    // 4. Update throttling timestamp in database immediately
    $currentTime = time();
    mysqli_query($connection, "INSERT INTO {$prefix}site_configuration 
        (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
        VALUES ({$siteId}, 'whatsapp_disconnect_last_email_time', '{$currentTime}', 1)
        ON DUPLICATE KEY UPDATE site_configuration_value = '{$currentTime}'");

    // 5. Build HTML Email Body
    $safePhone = htmlspecialchars($phone ?: 'Linked WhatsApp Line');
    $safeReason = htmlspecialchars($reason ?: 'Connection was terminated by WhatsApp or companion session conflict.');
    $safeTime = date('d M Y, h:i A');
    $statusText = ($statusCode > 0) ? " (Code: {$statusCode})" : "";

    $emailSubject = "[Urgent Alert] WhatsApp Connection Dropped - {$siteName}";
    $htmlContent = "
    <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background-color: #dc2626; color: #ffffff; padding: 20px 24px; text-align: left;'>
            <h2 style='margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -0.02em;'>⚠️ WhatsApp Disconnected Alert</h2>
            <p style='margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;'>Action required: WhatsApp automation has paused for {$siteName}.</p>
        </div>
        <div style='padding: 24px; color: #1e293b; font-size: 14px; line-height: 1.6;'>
            <p style='margin-top: 0;'>Hello Administrator,</p>
            <p>Your WhatsApp companion device has lost connection to the CRM gateway. Automated WhatsApp messages (itineraries, quotations, and chat synchronization) may not be delivered until the connection is restored.</p>
            
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f8fafc; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;'>
                <tr>
                    <td style='padding: 10px 14px; font-weight: 600; color: #64748b; width: 35%; border-bottom: 1px solid #e2e8f0;'>Site</td>
                    <td style='padding: 10px 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0;'>{$siteName} (" . htmlspecialchars($siteDomain) . ")</td>
                </tr>
                <tr>
                    <td style='padding: 10px 14px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0;'>Device Phone</td>
                    <td style='padding: 10px 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-weight: 600;'>{$safePhone}</td>
                </tr>
                <tr>
                    <td style='padding: 10px 14px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0;'>Disconnected At</td>
                    <td style='padding: 10px 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0;'>{$safeTime}</td>
                </tr>
                <tr>
                    <td style='padding: 10px 14px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0;'>Reason</td>
                    <td style='padding: 10px 14px; color: #b91c1c; border-bottom: 1px solid #e2e8f0;'>{$safeReason}{$statusText}</td>
                </tr>
                <tr>
                    <td style='padding: 10px 14px; font-weight: 600; color: #64748b;'>Active Devices Left</td>
                    <td style='padding: 10px 14px; color: #0f172a;'>{$remCount} connected</td>
                </tr>
            </table>

            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$loginUrl}' style='background-color: #2563eb; color: #ffffff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block; font-size: 15px;'>Reconnect WhatsApp on Dashboard</a>
            </div>

            <p style='color: #64748b; font-size: 12px; margin-bottom: 0;'>
                Note: This automated alert is throttled to at most 1 email every 30 minutes to prevent mailbox clutter. Please log in to your CRM dashboard to scan the QR code and re-establish the connection.
            </p>
        </div>
        <div style='background-color: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;'>
            CRM Automated Gateway Notification System &copy; " . date('Y') . "
        </div>
    </div>";

    // 6. Send Email using system mailer
    $sendMailPath = defined('SEND_EMAIL') && file_exists(SEND_EMAIL) 
        ? SEND_EMAIL 
        : (defined('CONFIG') ? CONFIG . 'mail/send-email.php' : (dirname(__DIR__, 3) . '/lib/configuration/mail/send-email.php'));

    if (file_exists($sendMailPath)) {
        $toAddress = $primaryAdminEmail;
        $subject = $emailSubject;
        $email_body = $htmlContent;

        if (!defined('SITE_NAME')) {
            define('SITE_NAME', $siteName);
        }
        if (!defined('SITE_ID')) {
            define('SITE_ID', $siteId);
        }

        ob_start();
        try {
            require $sendMailPath;
        } catch (\Throwable $e) {}
        ob_get_clean();
        return true;
    }

    return false;
}

/**
 * Headless Chrome / Chromium PDF Generator Helper
 * Renders an internal/public web page into a pixel-perfect, multi-page PDF on disk.
 *
 * @param string $targetUrl The URL to render (e.g. http://localhost/view-itinerary/MTc1MQ==/)
 * @param string $outputPdfPath Absolute disk path where PDF should be saved
 * @return bool True if PDF was created successfully with valid size
 */
if (!function_exists('generateHeadlessPdf')) {
    function generateHeadlessPdf(string $targetUrl, string $outputPdfPath): bool {
        $candidates = [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Chromium.app/Contents/MacOS/Chromium',
            '/Applications/Brave Browser.app/Contents/MacOS/Brave Browser',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/snap/bin/chromium',
            '/usr/local/bin/chrome',
            '/usr/local/bin/chromium'
        ];
        $chromeBin = null;
        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_executable($cand)) {
                $chromeBin = $cand;
                break;
            }
        }
        if (!$chromeBin) {
            $which = trim((string)@shell_exec('which google-chrome 2>/dev/null || which chromium 2>/dev/null || which chromium-browser 2>/dev/null'));
            if (!empty($which) && file_exists($which) && is_executable($which)) {
                $chromeBin = $which;
            }
        }
        if (!$chromeBin) {
            return false;
        }

        $outDir = dirname($outputPdfPath);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0777, true);
        }
        if (file_exists($outputPdfPath) && filesize($outputPdfPath) <= 500) {
            @unlink($outputPdfPath);
        }

        $cmd = escapeshellarg($chromeBin)
            . " --headless --disable-gpu --no-sandbox --disable-dev-shm-usage --ignore-certificate-errors --no-pdf-header-footer --print-to-pdf="
            . escapeshellarg($outputPdfPath) . " "
            . escapeshellarg($targetUrl) . " 2>&1";

        @exec($cmd, $output, $returnCode);

        if (file_exists($outputPdfPath) && filesize($outputPdfPath) > 500) {
            $fp = @fopen($outputPdfPath, 'rb');
            $head = $fp ? fread($fp, 5) : '';
            if ($fp) fclose($fp);
            if (strpos($head, '%PDF') === 0) {
                return true;
            }
        }

        return false;
    }
}

