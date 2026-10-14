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

// Retrieve site timezone configuration (default Asia/Kolkata for local IST)
$siteTimezone = 'Asia/Kolkata';
$tzQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'site_timezone' LIMIT 1");
if ($tzQ && $tzRow = mysqli_fetch_assoc($tzQ)) {
    if (!empty($tzRow['site_configuration_value'])) {
        $siteTimezone = trim($tzRow['site_configuration_value']);
    }
}
@date_default_timezone_set($siteTimezone);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch Message History for a Chat / Phone
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // Handle retry media download for pending/historical attachments
    if ($action === 'retry_media') {
        $msgWaId = trim($_GET['msg_id'] ?? ($_POST['msg_id'] ?? ''));
        if (!empty($msgWaId)) {
            $gwUrl = "http://127.0.0.1:3001/retry-media?msg_id=" . urlencode($msgWaId);
            $ctx = stream_context_create(['http' => ['timeout' => 8]]);
            $gwRes = @file_get_contents($gwUrl, false, $ctx);
            echo $gwRes ?: json_encode(['status' => 'pending']);
            exit;
        }
        echo json_encode(['status' => 'error', 'msg' => 'Message ID required']);
        exit;
    }

    if ($action === 'check_default_device' || $action === 'default_device') {
        require_once __DIR__ . '/device_helper.php';

        $currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);

        // Count total devices
        $cntQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId}");
        $totalDevs = ($cntQ && $cr = mysqli_fetch_assoc($cntQ)) ? (int)$cr['cnt'] : 0;

        // Resolve device: sender assigned device > default device > single connected device
        $devFound = resolveSenderWhatsAppDevice($connection, $siteId, $currentUserId);

        echo json_encode([
            'status'       => 'success',
            'has_device'   => ($totalDevs > 0),
            'is_connected' => ($devFound !== null),
            'device'       => $devFound ? [
                'device_id'  => (int)$devFound['device_id'],
                'session_id' => $devFound['device_session_id'],
                'phone'      => $devFound['device_phone'] ?: '',
                'name'       => $devFound['device_name'] ?: 'WhatsApp Line',
                'status'     => $devFound['device_status']
            ] : null
        ]);
        exit;
    }

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

    // Role-based Access Control: Ensure sales agents can only access their own chats
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

    if (!$isAdmin) {
        $assignedDevPhones = [];
        if ($currentUserId > 0) {
            $agentDevQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId}");
            if ($agentDevQ && mysqli_num_rows($agentDevQ) > 0) {
                while ($dRow = mysqli_fetch_assoc($agentDevQ)) {
                    if (!empty($dRow['device_phone'])) {
                        $assignedDevPhones[] = $dRow['device_phone'];
                    }
                }
            }
        }

        $canAccess = false;
        if (!empty($assignedDevPhones)) {
            $devConds = [];
            foreach ($assignedDevPhones as $aph) {
                $cDigits = preg_replace('/[^0-9]/', '', $aph);
                $last10 = substr($cDigits, -10);
                $escAph = mysqli_real_escape_string($connection, $aph);
                $devConds[] = "(chat_device_phone = '{$escAph}' OR chat_device_phone LIKE '%{$last10}')";
            }
            $authCheckQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat 
                WHERE chat_id = {$chatId} AND chat_site_id = {$siteId} 
                  AND (" . implode(' OR ', $devConds) . ")
                LIMIT 1");
            if ($authCheckQ && mysqli_num_rows($authCheckQ) > 0) {
                $canAccess = true;
            }
        }

        if (!$canAccess && !empty($assignedDevPhones)) {
            $devCountQ = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId}");
            $totalDevs = ($devCountQ && $dCnt = mysqli_fetch_assoc($devCountQ)) ? (int)$dCnt['cnt'] : 0;
            if ($totalDevs <= 1) {
                $canAccess = true;
            } else {
                $leadAgentQ = mysqli_query($connection, "SELECT c.chat_id FROM " . TABLE_PREFIX . "whatsapp_chat c 
                    LEFT JOIN " . TABLE_PREFIX . "enquiry e ON e.enquiry_id = c.chat_lead_id 
                    WHERE c.chat_id = {$chatId} AND c.chat_site_id = {$siteId} AND (e.enquiry_agent = {$currentUserId} OR c.chat_device_phone IS NULL OR c.chat_device_phone = '' OR c.chat_device_phone = 'CRM') LIMIT 1");
                if ($leadAgentQ && mysqli_num_rows($leadAgentQ) > 0) {
                    $canAccess = true;
                }
            }
        }

        if (!$canAccess) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'Access denied. You can only view chats for your assigned WhatsApp device.']);
            exit;
        }
    }

    // Reset unread count
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat SET chat_unread_count = 0 WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");

    // Fetch conversation info
    $chatQ = mysqli_query($connection, "SELECT c.*, 
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
                                         WHERE c.chat_id = {$chatId} AND c.chat_site_id = {$siteId}
                                         GROUP BY c.chat_id LIMIT 1");
    $chat = $chatQ ? mysqli_fetch_assoc($chatQ) : null;
    if ($chat) {
        $chat['device'] = [
            'phone'      => !empty($chat['chat_device_phone']) ? $chat['chat_device_phone'] : (!empty($chat['device_phone']) ? $chat['device_phone'] : ''),
            'name'       => !empty($chat['device_name']) ? $chat['device_name'] : 'WhatsApp Line',
            'agent_name' => !empty($chat['agent_name']) ? trim($chat['agent_name']) : (!empty($chat['agent_username']) ? $chat['agent_username'] : 'Unassigned')
        ];
    }

    // Fetch messages strictly for this chat conversation
    $sql = "SELECT * FROM " . TABLE_PREFIX . "whatsapp_message 
            WHERE message_site_id = {$siteId} AND message_chat_id = {$chatId} 
            ORDER BY message_created_at ASC, message_id ASC 
            LIMIT 300";

    $res = mysqli_query($connection, $sql);
    $messages = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $timeDisplay = '';
            $dateDisplay = '';
            if (!empty($row['message_created_at'])) {
                try {
                    $dt = new DateTime($row['message_created_at'], new DateTimeZone('UTC'));
                    $dt->setTimezone(new DateTimeZone($siteTimezone));
                    $timeDisplay = $dt->format('h:i A');
                    $dateDisplay = $dt->format('M d, Y');
                } catch (Exception $e) {
                    $ts = strtotime($row['message_created_at']);
                    $timeDisplay = date('h:i A', $ts);
                    $dateDisplay = date('M d, Y', $ts);
                }
            }
            $messages[] = [
                'id'         => (int)$row['message_id'],
                'wa_id'      => $row['message_wa_id'] ?? '',
                'direction'  => $row['message_direction'],
                'sender'     => $row['message_sender'],
                'receiver'   => $row['message_receiver'],
                'type'       => $row['message_type'],
                'text'       => $row['message_text'] ?? '',
                'media_url'  => $row['message_media_url'] ?? '',
                'status'     => $row['message_status'],
                'created_at' => $row['message_created_at'],
                'time'       => $timeDisplay,
                'date'       => $dateDisplay
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

    // Handle image file or base64 upload
    if (!empty($_FILES['media']['tmp_name']) && is_uploaded_file($_FILES['media']['tmp_name'])) {
        $upDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/upload/whatsapp';
        if (!is_dir($upDir)) {
            @mkdir($upDir, 0777, true);
        }
        $origName = $_FILES['media']['name'] ?? 'image.jpg';
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION)) ?: 'jpg';
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'])) {
            $ext = 'jpg';
        }
        $newFileName = 'wa_' . time() . '_' . substr(md5(uniqid('', true)), 0, 8) . '.' . $ext;
        $destPath = $upDir . '/' . $newFileName;
        if (move_uploaded_file($_FILES['media']['tmp_name'], $destPath)) {
            $mediaUrl = '/upload/whatsapp/' . $newFileName;
            $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'document';
        }
    } elseif (!empty($data['image_base64']) && str_starts_with($data['image_base64'], 'data:image/')) {
        $upDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/upload/whatsapp';
        if (!is_dir($upDir)) {
            @mkdir($upDir, 0777, true);
        }
        $bParts = explode(',', $data['image_base64'], 2);
        if (count($bParts) === 2) {
            $bin = base64_decode($bParts[1]);
            $ext = 'jpg';
            if (str_contains($bParts[0], 'png')) $ext = 'png';
            elseif (str_contains($bParts[0], 'webp')) $ext = 'webp';
            $newFileName = 'wa_' . time() . '_' . substr(md5(uniqid('', true)), 0, 8) . '.' . $ext;
            $destPath = $upDir . '/' . $newFileName;
            if (file_put_contents($destPath, $bin) !== false) {
                $mediaUrl = '/upload/whatsapp/' . $newFileName;
                $type = 'image';
            }
        }
    }

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
    $now = gmdate('Y-m-d H:i:s');

    // Insert outgoing message
    $msgSql = "INSERT INTO " . TABLE_PREFIX . "whatsapp_message 
               (message_site_id, message_chat_id, message_wa_id, message_direction, message_sender, message_receiver, message_type, message_text, message_media_url, message_status, message_created_at)
               VALUES ({$siteId}, {$chatId}, '{$waMsgId}', 'outgoing', '{$escSender}', '{$escReceiver}', '{$escType}', '{$escText}', '{$escMedia}', 'sent', '{$now}')";
    mysqli_query($connection, $msgSql);
    $msgId = (int)mysqli_insert_id($connection);

    // Update conversation thread
    $lastMsgPreview = !empty($text) ? $text : (!empty($mediaUrl) ? ($type === 'document' ? '📄 Document' : '📷 Photo') : '[Media Attachment]');
    $escPreview = mysqli_real_escape_string($connection, $lastMsgPreview);
    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat 
                              SET chat_last_message = '{$escPreview}', 
                                  chat_last_message_time = '{$now}',
                                  chat_updated_at = '{$now}',
                                  chat_device_phone = IF(chat_device_phone IS NULL OR chat_device_phone = '' OR chat_device_phone = 'CRM', '{$escSender}', chat_device_phone)
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

    // Determine device session and role for current user / site
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

    $targetSessionId = 'site_' . $siteId . '_default';
    $selectedDeviceId = isset($data['device_id']) && is_numeric($data['device_id']) ? (int)$data['device_id'] : 0;
    $selectedDevicePhone = trim($data['device_phone'] ?? '');

    // Strict Priority Rule:
    // If sender user has an assigned connected device, ALWAYS use it (do not use default device)!
    $userAssignedDev = null;
    if ($currentUserId > 0) {
        $uadQ = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "whatsapp_device 
            WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
        if ($uadQ && mysqli_num_rows($uadQ) > 0) {
            $userAssignedDev = mysqli_fetch_assoc($uadQ);
        }
    }

    if ($userAssignedDev) {
        $targetSessionId = $userAssignedDev['device_session_id'];
        if (!empty($userAssignedDev['device_phone'])) {
            $senderPhone = $userAssignedDev['device_phone'];
        }
    } else {
        // User has NO assigned device. Resolve based on explicit selection or default device
        $foundDev = false;
        if ($selectedDeviceId > 0) {
            $specDevQ = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_id = {$selectedDeviceId} AND device_status = 'connected' LIMIT 1");
            if ($specDevQ && mysqli_num_rows($specDevQ) > 0) {
                $sdRow = mysqli_fetch_assoc($specDevQ);
                $targetSessionId = $sdRow['device_session_id'];
                if (!empty($sdRow['device_phone'])) $senderPhone = $sdRow['device_phone'];
                $foundDev = true;
            }
        }
        if (!$foundDev && !empty($selectedDevicePhone) && $selectedDevicePhone !== 'all') {
            $cleanSelDigits = preg_replace('/[^0-9]/', '', $selectedDevicePhone);
            $last10Sel = substr($cleanSelDigits, -10);
            $specDevQ = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_phone LIKE '%{$last10Sel}') AND device_status = 'connected' LIMIT 1");
            if ($specDevQ && mysqli_num_rows($specDevQ) > 0) {
                $sdRow = mysqli_fetch_assoc($specDevQ);
                $targetSessionId = $sdRow['device_session_id'];
                if (!empty($sdRow['device_phone'])) $senderPhone = $sdRow['device_phone'];
                $foundDev = true;
            }
        }
        if (!$foundDev && $chatId > 0) {
            $chatDevQ = mysqli_query($connection, "SELECT chat_device_phone FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_id = {$chatId} AND chat_site_id = {$siteId} LIMIT 1");
            if ($chatDevQ && mysqli_num_rows($chatDevQ) > 0) {
                $cdRow = mysqli_fetch_assoc($chatDevQ);
                if (!empty($cdRow['chat_device_phone'])) {
                    $cleanChatDev = preg_replace('/[^0-9]/', '', $cdRow['chat_device_phone']);
                    $last10Cd = substr($cleanChatDev, -10);
                    $specDevQ = mysqli_query($connection, "SELECT device_session_id, device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND (device_phone LIKE '%{$last10Cd}') AND device_status = 'connected' LIMIT 1");
                    if ($specDevQ && mysqli_num_rows($specDevQ) > 0) {
                        $sdRow = mysqli_fetch_assoc($specDevQ);
                        $targetSessionId = $sdRow['device_session_id'];
                        if (!empty($sdRow['device_phone'])) $senderPhone = $sdRow['device_phone'];
                        $foundDev = true;
                    }
                }
            }
        }
        if (!$foundDev) {
            // Fallback to default device (or single connected device)
            $resDev = resolveSenderWhatsAppDevice($connection, $siteId, $currentUserId);
            if ($resDev) {
                $targetSessionId = $resDev['device_session_id'];
                if (!empty($resDev['device_phone'])) {
                    $senderPhone = $resDev['device_phone'];
                }
                $foundDev = true;
            }
        }

        if (!$foundDev && !$isAdmin) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'No active WhatsApp device assigned to your account or configured as default.']);
            exit;
        }
    }

    // Verify chat access for non-admin
    if (!$isAdmin && !empty($senderPhone) && $chatId > 0) {
        $cleanSender = preg_replace('/[^0-9]/', '', $senderPhone);
        $last10Sender = substr($cleanSender, -10);
        $escSender = mysqli_real_escape_string($connection, $senderPhone);
        $chatDevCheck = mysqli_query($connection, "SELECT c.chat_id FROM " . TABLE_PREFIX . "whatsapp_chat c
            LEFT JOIN " . TABLE_PREFIX . "enquiry e ON e.enquiry_id = c.chat_lead_id
            WHERE c.chat_id = {$chatId} AND c.chat_site_id = {$siteId} 
              AND (c.chat_device_phone = '{$escSender}' 
                   OR c.chat_device_phone LIKE '%{$last10Sender}'
                   OR c.chat_device_phone IS NULL 
                   OR c.chat_device_phone = '' 
                   OR c.chat_device_phone = 'CRM'
                   OR e.enquiry_agent = {$currentUserId}
                   OR (SELECT COUNT(*) FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId}) <= 1
                  ) 
            LIMIT 1");
        if (!$chatDevCheck || mysqli_num_rows($chatDevCheck) === 0) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'Access denied. You can only send messages for conversations on your assigned WhatsApp device.']);
            exit;
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
    $dynamicWebhookUrl = $scheme . $host . '/api/v3/whatsapp/webhook/';

    $fileName = !empty($data['file_name']) ? $data['file_name'] : (!empty($data['fileName']) ? $data['fileName'] : 'Quotation.pdf');

    $payload = json_encode([
        'site_id'     => $siteId,
        'session_id'  => $targetSessionId,
        'webhook_url' => $dynamicWebhookUrl,
        'to'          => $recipientPhone,
        'message'     => $text,
        'media'       => $mediaUrl,
        'type'        => $type,
        'file_name'   => $fileName,
        'fileName'    => $fileName,
        'msg_id'      => $waMsgId
    ]);

    $targetGwEndpoint = rtrim($gwUrl, '/') . '/send-message';
    $gwRawRes = false;

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetGwEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        $gwRawRes = curl_exec($ch);
        curl_close($ch);
    }

    if ($gwRawRes === false && ini_get('allow_url_fopen')) {
        $opts = [
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\n",
                'content'       => $payload,
                'timeout'       => 8,
                'ignore_errors' => true
            ]
        ];
        $ctx = stream_context_create($opts);
        $gwRawRes = @file_get_contents($targetGwEndpoint, false, $ctx);
    }
    if ($gwRawRes !== false) {
        $gwResData = json_decode($gwRawRes, true);
        if (isset($gwResData['status']) && $gwResData['status'] === 'success') {
            $gwSuccess = true;
            if (!empty($gwResData['msg_id'])) {
                $realWaId = mysqli_real_escape_string($connection, $gwResData['msg_id']);
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_wa_id = '{$realWaId}', message_status = 'sent' WHERE message_id = {$msgId}");
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
            'time'       => (new DateTime('now', new DateTimeZone($siteTimezone)))->format('h:i A')
        ]
    ]);
    exit;
}
