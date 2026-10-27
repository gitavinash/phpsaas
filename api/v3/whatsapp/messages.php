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

require_once __DIR__ . '/phone_helper.php';

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

if ($siteId <= 0 && !empty($_COOKIE['user_data'])) {
    $userSalt = mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data']));
    $userSiteQuery = mysqli_query($connection, "SELECT site_access_site_id FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "user ON site_access_user_id=user_id WHERE user_salt_key='{$userSalt}' LIMIT 1");
    if ($userSiteQuery && $userRow = mysqli_fetch_assoc($userSiteQuery)) {
        $siteId = (int)$userRow['site_access_site_id'];
    }
}

if ($siteId <= 0 && !empty($_GET['chat_id'])) {
    $cId = (int)$_GET['chat_id'];
    $cq = mysqli_query($connection, "SELECT chat_site_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_id = {$cId} LIMIT 1");
    if ($cq && $cr = mysqli_fetch_assoc($cq)) {
        $siteId = (int)$cr['chat_site_id'];
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
            $escWaId = mysqli_real_escape_string($connection, $msgWaId);

            $upDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/upload/whatsapp';
            $cacheDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/services/whatsapp-gateway/media_cache';
            if (!is_dir($upDir)) {
                @mkdir($upDir, 0777, true);
            }

            // 1. Check if DB already has media_url for this message AND file actually exists on disk
            $chkDb = mysqli_query($connection, "SELECT message_id, message_media_url, message_type FROM " . TABLE_PREFIX . "whatsapp_message WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}' AND message_media_url != '' LIMIT 1");
            if ($chkDb && mysqli_num_rows($chkDb) > 0) {
                $dbRow = mysqli_fetch_assoc($chkDb);
                $cleanUrl = preg_replace('#^https?://(?:127\.0\.0\.1|localhost):3001/media/#i', '/upload/whatsapp/', $dbRow['message_media_url']);
                $dbType = $dbRow['message_type'] ?? 'image';
                $localPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/' . ltrim($cleanUrl, '/');
                if (file_exists($localPath) && filesize($localPath) > 0) {
                    if ($cleanUrl !== $dbRow['message_media_url']) {
                        $escClean = mysqli_real_escape_string($connection, $cleanUrl);
                        @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escClean}' WHERE message_id = " . (int)$dbRow['message_id']);
                    }
                    echo json_encode([
                        'status'    => 'success',
                        'media_url' => $cleanUrl,
                        'type'      => $dbType
                    ]);
                    exit;
                }
            }

            // 2. Check if file is already on local disk or in gateway cache
            $allExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mov', 'webm', 'ogg', 'mp3', 'm4a', 'wav', 'aac', 'opus', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'];
            foreach ($allExts as $chkExt) {
                $chkFile = $upDir . '/wa_' . $msgWaId . '.' . $chkExt;
                $cacheFile = $cacheDir . '/wa_' . $msgWaId . '.' . $chkExt;
                if (!file_exists($chkFile) && file_exists($cacheFile) && filesize($cacheFile) > 0) {
                    @copy($cacheFile, $chkFile);
                }
                if (file_exists($chkFile) && filesize($chkFile) > 0) {
                    $foundUrl = '/upload/whatsapp/wa_' . $msgWaId . '.' . $chkExt;
                    $foundType = 'document';
                    if (in_array($chkExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $foundType = 'image';
                    } elseif (in_array($chkExt, ['mp4', 'mov', 'webm'])) {
                        $foundType = 'video';
                    } elseif (in_array($chkExt, ['ogg', 'mp3', 'm4a', 'wav', 'aac', 'opus'])) {
                        $foundType = 'audio';
                    }
                    $escU = mysqli_real_escape_string($connection, $foundUrl);
                    $escT = mysqli_real_escape_string($connection, $foundType);
                    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escU}', message_type = '{$escT}' WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}'");
                    echo json_encode([
                        'status'    => 'success',
                        'media_url' => $foundUrl,
                        'type'      => $foundType
                    ]);
                    exit;
                }
            }

            // 3. Request WhatsApp gateway to fetch media from device
            $gwUrl = "http://127.0.0.1:3001/retry-media?msg_id=" . urlencode($msgWaId);
            $ctx = stream_context_create(['http' => ['timeout' => 8]]);
            $gwRes = @file_get_contents($gwUrl, false, $ctx);
            if ($gwRes) {
                $gwJson = json_decode($gwRes, true);
                if (isset($gwJson['results']) && is_array($gwJson['results']) && !empty($gwJson['results'])) {
                    foreach ($gwJson['results'] as $r) {
                        if (!empty($r['url']) && ($r['msgId'] ?? '') === $msgWaId) {
                            $normUrl = preg_replace('#^https?://(?:127\.0\.0\.1|localhost):3001/media/#i', '/upload/whatsapp/', $r['url']);
                            $resType = $r['type'] ?? 'image';
                            if ($resType === 'media') {
                                if (preg_match('/\.(mp4|mov|webm)($|\?)/i', $normUrl)) $resType = 'video';
                                elseif (preg_match('/\.(ogg|mp3|m4a|wav|aac|opus)($|\?)/i', $normUrl)) $resType = 'audio';
                                elseif (preg_match('/\.(jpe?g|png|webp|gif)($|\?)/i', $normUrl)) $resType = 'image';
                                else $resType = 'document';
                            }
                            $escU = mysqli_real_escape_string($connection, $normUrl);
                            $escT = mysqli_real_escape_string($connection, $resType);
                            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escU}', message_type = '{$escT}' WHERE message_site_id = {$siteId} AND message_wa_id = '{$escWaId}'");
                            echo json_encode([
                                'status'    => 'success',
                                'media_url' => $normUrl,
                                'type'      => $resType
                            ]);
                            exit;
                        }
                    }
                }
                if (isset($gwJson['status']) && $gwJson['status'] === 'success' && empty($gwJson['results'])) {
                    echo json_encode(['status' => 'unavailable', 'msg' => 'Media file is no longer available on WhatsApp device.']);
                    exit;
                }
            }

            echo json_encode(['status' => 'unavailable', 'msg' => 'Media file is currently unavailable or gateway is offline.']);
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
        $cleanPhone = normalizeWhatsAppPhone($phone);
        $strictChatCond = buildStrictPhoneCondition($connection, 'chat_phone', $cleanPhone);

        $reqDevPhone = trim($_GET['device_phone'] ?? '');
        $devCond = "";
        if (!empty($reqDevPhone) && $reqDevPhone !== 'all') {
            $normReqDev = normalizeWhatsAppPhone($reqDevPhone);
            $strictDevCond = buildStrictPhoneCondition($connection, 'chat_device_phone', $normReqDev);
            $devCond = " AND {$strictDevCond}";
        }

        $cQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_site_id = {$siteId} AND {$strictChatCond} {$devCond} ORDER BY chat_id DESC LIMIT 1");
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
        $chatPhone = trim($chat['chat_phone'] ?? '');
        $cDigits = preg_replace('/[^0-9]/', '', $chatPhone);
        $last10 = substr($cDigits, -10);

        // If not linked to a lead, check if phone number exists in tbl_enquiry
        if (empty($chat['chat_lead_id']) && strlen($last10) >= 8) {
            $esc10 = mysqli_real_escape_string($connection, $last10);
            $lFindQ = mysqli_query($connection, "SELECT enquiry_id, TRIM(CONCAT(COALESCE(enquiry_first_name, ''), ' ', COALESCE(enquiry_last_name, ''))) as enquiry_name, enquiry_subject as enquiry_destination, enquiry_status FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND enquiry_mobile LIKE '%{$esc10}' ORDER BY enquiry_id DESC LIMIT 1");
            if ($lFindQ && mysqli_num_rows($lFindQ) > 0) {
                $lRow = mysqli_fetch_assoc($lFindQ);
                $chat['chat_lead_id'] = (int)$lRow['enquiry_id'];
                $chat['enquiry_id'] = (int)$lRow['enquiry_id'];
                $chat['enquiry_name'] = $lRow['enquiry_name'];
                $chat['enquiry_destination'] = $lRow['enquiry_destination'];
                $chat['enquiry_status'] = $lRow['enquiry_status'];
                @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_chat SET chat_lead_id = {$chat['chat_lead_id']} WHERE chat_id = {$chatId} AND chat_site_id = {$siteId}");
            }
        }

        $leadName = trim($chat['enquiry_name'] ?? '');
        $rawName = trim($chat['chat_name'] ?? '');

        if (preg_match('/^WhatsApp\s*\(/i', $leadName)) {
            $leadName = '';
        }
        if (preg_match('/^WhatsApp\s*\(/i', $rawName) || str_starts_with($rawName, '+') || stripos($rawName, 'blumox') !== false) {
            $rawName = '';
        }
        if (!empty($rawName)) {
            $cleanN = preg_replace('/[^0-9]/', '', $rawName);
            if ($cleanN === $cDigits && strlen($cDigits) >= 7) {
                $rawName = '';
            }
        }

        // Priority: 1. Lead Name, 2. Contact Name, 3. Contact Phone Number
        if (!empty($leadName)) {
            $chat['display_name'] = $leadName;
        } elseif (!empty($rawName)) {
            $chat['display_name'] = $rawName;
        } else {
            $chat['display_name'] = $chatPhone;
        }

        $chat['device'] = [
            'phone'      => !empty($chat['chat_device_phone']) ? $chat['chat_device_phone'] : (!empty($chat['device_phone']) ? $chat['device_phone'] : ''),
            'name'       => !empty($chat['device_name']) ? $chat['device_name'] : 'WhatsApp Line',
            'agent_name' => !empty($chat['agent_name']) ? trim($chat['agent_name']) : (!empty($chat['agent_username']) ? $chat['agent_username'] : 'Unassigned')
        ];
    }

    // Fetch latest messages chronologically (oldest at top, newest at bottom)
    // Strictly isolate messages to this contact conversation: sender or receiver must match chat_phone
    $normChatPhone = !empty($chatPhone) ? normalizeWhatsAppPhone($chatPhone) : '';
    $phoneCondSql = "1=1";
    if (!empty($normChatPhone)) {
        $senderStrict = buildStrictPhoneCondition($connection, 'message_sender', $normChatPhone);
        $receiverStrict = buildStrictPhoneCondition($connection, 'message_receiver', $normChatPhone);
        $phoneCondSql = "({$senderStrict} OR {$receiverStrict})";
    }

    $sql = "SELECT * FROM (
                SELECT * FROM " . TABLE_PREFIX . "whatsapp_message 
                WHERE message_site_id = {$siteId} 
                  AND message_chat_id = {$chatId} 
                  AND {$phoneCondSql}
                ORDER BY message_created_at DESC, message_id DESC 
                LIMIT 300
            ) sub 
            ORDER BY message_created_at ASC, message_id ASC";

    $res = mysqli_query($connection, $sql);
    $messages = [];

    if ($res) {
        $todayDt = new DateTime('now', new DateTimeZone($siteTimezone));
        $todayStr = $todayDt->format('Y-m-d');
        $yesterdayDt = (clone $todayDt)->modify('-1 day');
        $yesterdayStr = $yesterdayDt->format('Y-m-d');

        $rawRows = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $rawRows[] = $row;
        }

        $seenWaIds = [];
        $seenRecentMsgs = [];
        $rowCount = count($rawRows);
        foreach ($rawRows as $i => $row) {
            $waIdVal = trim($row['message_wa_id'] ?? '');
            $textVal = trim($row['message_text'] ?? '');
            $dirVal = $row['message_direction'] ?? '';
            $tsVal = !empty($row['message_created_at']) ? strtotime($row['message_created_at']) : 0;

            // 1. Filter exact wa_id duplicates
            if (!empty($waIdVal)) {
                if (isset($seenWaIds[$waIdVal])) {
                    continue;
                }
                $seenWaIds[$waIdVal] = true;
            }

            // 2. Filter near-instant identical message duplicates in same thread (within 10 seconds)
            $dupKey = $dirVal . '_' . $textVal;
            if ($textVal !== '' && isset($seenRecentMsgs[$dupKey]) && abs($tsVal - $seenRecentMsgs[$dupKey]) <= 10) {
                continue;
            }
            if ($textVal !== '') {
                $seenRecentMsgs[$dupKey] = $tsVal;
            }

            $timeDisplay = '';
            $dateDisplay = '';
            $dateLabel = '';
            if (!empty($row['message_created_at'])) {
                try {
                    $dt = new DateTime($row['message_created_at'], new DateTimeZone($siteTimezone));
                    $timeDisplay = $dt->format('h:i A');
                    $dateDisplay = $dt->format('M d, Y');
                    $msgDateStr = $dt->format('Y-m-d');

                    if ($msgDateStr === $todayStr) {
                        $dateLabel = 'Today';
                    } elseif ($msgDateStr === $yesterdayStr) {
                        $dateLabel = 'Yesterday';
                    } elseif ($dt->format('Y') === $todayDt->format('Y')) {
                        $dateLabel = $dt->format('d M');
                    } else {
                        $dateLabel = $dt->format('d M Y');
                    }
                } catch (Exception $e) {
                    $ts = strtotime($row['message_created_at']);
                    $timeDisplay = date('h:i A', $ts);
                    $dateDisplay = date('M d, Y', $ts);
                    $dateLabel = date('d M Y', $ts);
                }
            }
            $mediaUrl = $row['message_media_url'] ?? '';
            if (!empty($mediaUrl)) {
                $mediaUrl = preg_replace('#^https?://(?:127\.0\.0\.1|localhost):3001/media/#i', '/upload/whatsapp/', $mediaUrl);
                $diskPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/' . ltrim($mediaUrl, '/');
                if (!file_exists($diskPath) || filesize($diskPath) <= 0) {
                    $mediaUrl = '';
                }
            }
            $mType = strtolower(trim($row['message_type'] ?? 'text'));
            $mText = trim((string)($row['message_text'] ?? ''));

            if (empty($mediaUrl) && !empty($row['message_wa_id'])) {
                $upDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/upload/whatsapp';
                $cacheDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/services/whatsapp-gateway/media_cache';
                $allExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mov', 'webm', 'ogg', 'mp3', 'm4a', 'wav', 'aac', 'opus', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'];
                foreach ($allExts as $chkExt) {
                    $chkFile = $upDir . '/wa_' . $row['message_wa_id'] . '.' . $chkExt;
                    $cacheFile = $cacheDir . '/wa_' . $row['message_wa_id'] . '.' . $chkExt;
                    if (!file_exists($chkFile) && file_exists($cacheFile) && filesize($cacheFile) > 0) {
                        @copy($cacheFile, $chkFile);
                    }
                    if (file_exists($chkFile) && filesize($chkFile) > 0) {
                        $mediaUrl = '/upload/whatsapp/wa_' . $row['message_wa_id'] . '.' . $chkExt;
                        $foundType = 'document';
                        if (in_array($chkExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                            $foundType = 'image';
                        } elseif (in_array($chkExt, ['mp4', 'mov', 'webm'])) {
                            $foundType = 'video';
                        } elseif (in_array($chkExt, ['ogg', 'mp3', 'm4a', 'wav', 'aac', 'opus'])) {
                            $foundType = 'audio';
                        }
                        $mType = $foundType;
                        $escU = mysqli_real_escape_string($connection, $mediaUrl);
                        $escT = mysqli_real_escape_string($connection, $mType);
                        @mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "whatsapp_message SET message_media_url = '{$escU}', message_type = '{$escT}' WHERE message_id = " . (int)$row['message_id']);
                        break;
                    }
                }
            }

            // Infer correct media type if still marked as 'text' or generic 'media'
            if ($mType === 'text' || empty($mType) || $mType === 'media') {
                if (!empty($mediaUrl)) {
                    if (preg_match('/\.(jpe?g|png|webp|gif)($|\?)/i', $mediaUrl)) {
                        $mType = 'image';
                    } elseif (preg_match('/\.(mp4|mov|webm)($|\?)/i', $mediaUrl)) {
                        $mType = 'video';
                    } elseif (preg_match('/\.(ogg|mp3|m4a|wav|aac|opus)($|\?)/i', $mediaUrl)) {
                        $mType = 'audio';
                    } else {
                        $mType = 'document';
                    }
                } elseif ($mText === '[Voice Note]' || stripos($mText, 'voice note') !== false || $mText === '[Audio]') {
                    $mType = 'audio';
                } elseif ($mText === '[Video]') {
                    $mType = 'video';
                } elseif ($mText === '[Image]' || $mText === '[Photo]') {
                    $mType = 'image';
                } elseif ($mText === '[Document]' || $mText === '[Attachment]') {
                    $mType = 'document';
                }
            }

            // Parse Contact and Location payload
            $mText = trim((string)($row['message_text'] ?? ''));
            $contactData = null;
            $locationData = null;

            // 1. JSON-encoded structured payload
            if (str_starts_with($mText, '{') && str_ends_with($mText, '}')) {
                $json = json_decode($mText, true);
                if (is_array($json) && isset($json['type'])) {
                    if ($json['type'] === 'contact') {
                        $contactData = [
                            'name'  => trim($json['name'] ?? ''),
                            'phone' => trim($json['phone'] ?? ''),
                            'vcard' => $json['vcard'] ?? ''
                        ];
                        $mType = 'contact';
                    } elseif ($json['type'] === 'contacts') {
                        $contactData = [
                            'name'     => trim($json['name'] ?? 'Contacts'),
                            'phone'    => '',
                            'contacts' => $json['contacts'] ?? []
                        ];
                        $mType = 'contact';
                    } elseif ($json['type'] === 'location' || $json['type'] === 'live_location') {
                        $lat = (float)($json['latitude'] ?? 0);
                        $lng = (float)($json['longitude'] ?? 0);
                        $lName = trim($json['name'] ?? '');
                        $lAddr = trim($json['address'] ?? '');
                        $lUrl = !empty($json['url']) ? $json['url'] : ($lat && $lng ? "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}" : "");
                        $locationData = [
                            'name'      => $lName ?: ($lAddr ?: 'Shared Location'),
                            'address'   => $lAddr,
                            'latitude'  => $lat,
                            'longitude' => $lng,
                            'url'       => $lUrl,
                            'media_url' => $mediaUrl
                        ];
                        $mType = 'location';
                    }
                }
            }

            // 2. Legacy Contact message: [Contact: Display Name]
            if (!$contactData && preg_match('/^\[Contact:\s*(.*?)\]$/i', $mText, $cMatch)) {
                $cName = trim($cMatch[1]);
                $cPhone = '';
                $digitsOnly = preg_replace('/[^0-9]/', '', $cName);
                if (strlen($digitsOnly) >= 10 && strlen($digitsOnly) <= 13) {
                    $cPhone = '+' . $digitsOnly;
                } else {
                    // Check if adjacent message is a raw phone number
                    if ($i + 1 < $rowCount) {
                        $nextText = trim((string)($rawRows[$i + 1]['message_text'] ?? ''));
                        $nextDigits = preg_replace('/[^0-9]/', '', $nextText);
                        if (strlen($nextDigits) >= 10 && strlen($nextDigits) <= 13 && strlen($nextText) <= 20) {
                            $cPhone = (str_starts_with($nextText, '+') ? '+' : '+91') . (str_starts_with($nextDigits, '91') && strlen($nextDigits) == 12 ? substr($nextDigits, 2) : $nextDigits);
                        }
                    }
                }

                $contactData = [
                    'name'  => $cName ?: 'Contact',
                    'phone' => $cPhone,
                    'vcard' => ''
                ];
                $mType = 'contact';
            }

            // 3. Legacy Contacts message: [Contacts: X contacts]
            if (!$contactData && preg_match('/^\[Contacts:\s*(.*?)\]$/i', $mText, $cMatch)) {
                $contactData = [
                    'name'     => trim($cMatch[1]) ?: 'Contacts',
                    'phone'    => '',
                    'contacts' => []
                ];
                $mType = 'contact';
            }

            // 4. Legacy Location message: [Location: Place/Address] or [Location: ] or [Live Location]
            if (!$locationData && (preg_match('/^\[Location:\s*(.*?)\]$/i', $mText, $lMatch) || preg_match('/^\[Live Location\]$/i', $mText))) {
                $locStr = isset($lMatch[1]) ? trim($lMatch[1]) : '';
                $locUrl = '';
                if (!empty($locStr)) {
                    $locUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($locStr);
                }
                $locationData = [
                    'name'      => $locStr ?: 'Shared Location',
                    'address'   => $locStr,
                    'latitude'  => 0,
                    'longitude' => 0,
                    'url'       => $locUrl,
                    'media_url' => $mediaUrl
                ];
                $mType = 'location';
            }

            // Normalize internal markers: [Reaction: emoji], [Doc: title], placeholders
            $typeMeta = extractInternalMessageType((string)($row['message_text'] ?? ''), $mType);
            $finalType = $typeMeta['type'];
            $cleanText = $typeMeta['clean_text'];

            $messages[] = [
                'id'             => (int)$row['message_id'],
                'wa_id'          => $row['message_wa_id'] ?? '',
                'direction'      => $row['message_direction'],
                'sender'         => $row['message_sender'],
                'receiver'       => $row['message_receiver'],
                'type'           => $finalType,
                'text'           => $cleanText,
                'raw_text'       => $row['message_text'] ?? '',
                'reaction_emoji' => $typeMeta['reaction_emoji'] ?? null,
                'doc_name'       => $typeMeta['doc_name'] ?? null,
                'media_url'      => $mediaUrl,
                'contact_data'   => $contactData,
                'location_data'  => $locationData,
                'status'         => $row['message_status'],
                'created_at'     => $row['message_created_at'],
                'time'           => $timeDisplay,
                'date'           => $dateDisplay,
                'date_label'     => $dateLabel
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
        $normPhone = normalizeWhatsAppPhone($digits);
        $cleanPhone = $normPhone;
        $escPhone = mysqli_real_escape_string($connection, $cleanPhone);
        $strictChatPhoneCond = buildStrictPhoneCondition($connection, 'chat_phone', $cleanPhone);

        $selectedDevicePhone = trim($data['device_phone'] ?? '');
        $currentUserId = defined('USER_ID') ? (int)USER_ID : (isset($user_id) ? (int)$user_id : 0);
        $userDevPhone = '';
        if ($currentUserId > 0) {
            $uadQ = mysqli_query($connection, "SELECT device_phone FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} AND device_assigned_user_id = {$currentUserId} AND device_status = 'connected' LIMIT 1");
            if ($uadQ && $uadR = mysqli_fetch_assoc($uadQ)) {
                $userDevPhone = $uadR['device_phone'] ?? '';
            }
        }
        $targetDevPhone = !empty($userDevPhone) ? $userDevPhone : (!empty($selectedDevicePhone) && $selectedDevicePhone !== 'all' ? $selectedDevicePhone : '');
        $escTargetDev = mysqli_real_escape_string($connection, $targetDevPhone);

        if (!empty($cleanPhone)) {
            $cQ = mysqli_query($connection, "SELECT chat_id FROM " . TABLE_PREFIX . "whatsapp_chat 
                WHERE chat_site_id = {$siteId} 
                  AND {$strictChatPhoneCond}
                  AND (chat_device_phone = '{$escTargetDev}' OR chat_device_phone = '' OR chat_device_phone IS NULL)
                ORDER BY (chat_device_phone = '{$escTargetDev}') DESC 
                LIMIT 1");
            if ($cQ && mysqli_num_rows($cQ) > 0) {
                $cRow = mysqli_fetch_assoc($cQ);
                $chatId = (int)$cRow['chat_id'];
            } else {
                // Auto create chat
                mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "whatsapp_chat 
                    (chat_site_id, chat_phone, chat_device_phone, chat_name) 
                    VALUES ({$siteId}, '{$escPhone}', '{$escTargetDev}', '{$escPhone}')
                    ON DUPLICATE KEY UPDATE chat_id = LAST_INSERT_ID(chat_id)");
                $chatId = (int)mysqli_insert_id($connection);
            }
        }
    }

    if ($chatId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid recipient or chat ID is required.']);
        exit;
    }

    // Fetch chat phone and linked lead
    $cQ = mysqli_query($connection, "SELECT chat_phone, chat_lead_id FROM " . TABLE_PREFIX . "whatsapp_chat WHERE chat_id = {$chatId} AND chat_site_id = {$siteId} LIMIT 1");
    $cRow = $cQ ? mysqli_fetch_assoc($cQ) : null;
    $chatLeadId = (int)($cRow['chat_lead_id'] ?? 0);
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
            // Fallback: check assigned lead agent, established chat device, or site default
            $resDev = resolveSenderWhatsAppDevice($connection, $siteId, $currentUserId, [
                'lead_id' => $chatLeadId,
                'recipient_phone' => $recipientPhone
            ]);
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
