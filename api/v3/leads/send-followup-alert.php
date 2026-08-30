<?php

/**
 * Send Follow-Up Alert to Assigned User (Push Notification + Email)
 * Path: /api/v3/leads/send-followup-alert.php
 */

header("Content-type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;

$rawLeadId = isset($_POST['lead_id']) ? $_POST['lead_id'] : (isset($_POST['leadId']) ? $_POST['leadId'] : '');
if (empty($rawLeadId)) {
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
    exit();
}

$decoded = base64_decode(trim($rawLeadId), true);
$leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;

if ($leadId <= 0 || !isset($connection) || !$connection) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid lead request or database error.']);
    exit();
}

// 1. Fetch Lead & Assigned Agent details
$leadSql = "SELECT 
    e.enquiry_id,
    e.enquiry_site_id,
    e.enquiry_agent,
    e.enquiry_first_name,
    e.enquiry_last_name,
    e.enquiry_mobile,
    e.enquiry_email,
    e.enquiry_subject,
    e.enquiry_followup,
    p.profile_display_name,
    p.profile_email,
    s.site_name,
    s.site_domain
FROM {$tblPrefix}enquiry e
LEFT JOIN {$tblPrefix}user u ON e.enquiry_agent = u.user_id
LEFT JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
LEFT JOIN {$tblPrefix}site s ON e.enquiry_site_id = s.site_id
WHERE e.enquiry_id = {$leadId} AND e.enquiry_site_id = {$siteId}
LIMIT 1";

$leadRes = mysqli_query($connection, $leadSql);
if (!$leadRes || mysqli_num_rows($leadRes) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Lead not found.']);
    exit();
}

$lead = mysqli_fetch_assoc($leadRes);
$agentId = (int)($lead['enquiry_agent'] ?? 0);
$agentName = trim($lead['profile_display_name'] ?? 'Assigned Representative');
$agentEmail = trim($lead['profile_email'] ?? '');

$customerName = trim(($lead['enquiry_first_name'] ?? '') . ' ' . ($lead['enquiry_last_name'] ?? ''));
if (empty($customerName)) {
    $customerName = 'Lead #' . $leadId;
}
$customerMobile = trim($lead['enquiry_mobile'] ?? '');
$leadSubject = trim($lead['enquiry_subject'] ?? 'Tour Requirement');
$followupRaw = !empty($_POST['followup_time']) ? $_POST['followup_time'] : $lead['enquiry_followup'];
$followupTimeFormatted = !empty($followupRaw) ? date('d M Y, h:i A', strtotime($followupRaw)) : 'In 5 Minutes';

$siteName = !empty($lead['site_name']) ? $lead['site_name'] : (defined('SITE_NAME') ? SITE_NAME : 'Aleya Workspace');
$siteDomain = !empty($lead['site_domain']) ? $lead['site_domain'] : (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'aleya.in');
$realLeadId = $leadId;
$leadViewUrl = "https://{$siteDomain}/dashboard/leads/view/?id=" . base64_encode((string)$realLeadId);

if ($agentId <= 0) {
    echo json_encode([
        'status' => 'skipped',
        'message' => 'No representative assigned to this lead.',
        'lead_id' => $leadId
    ]);
    exit();
}

// Fetch SMTP settings
$siteSmtp = [];
$smtpSql = "SELECT site_configuration_key, site_configuration_value 
            FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_status = 1 
              AND site_configuration_site_id = {$siteId} 
              AND (site_configuration_key LIKE 'email_smtp_%' OR site_configuration_key = 'enable_smtp_email')";
if ($smtpRes = mysqli_query($connection, $smtpSql)) {
    while ($cfg = mysqli_fetch_assoc($smtpRes)) {
        $siteSmtp[$cfg['site_configuration_key']] = $cfg['site_configuration_value'];
    }
}

$pushSent = false;
$emailSent = false;
$deviceCount = 0;

// ----------------------------------------------------
// 1. Send FCM Web Push Notification to Agent
// ----------------------------------------------------
$deviceTokens = [];
$agentUserIdEsc = mysqli_real_escape_string($connection, (string)$agentId);
$devSql = "SELECT DISTINCT device_token FROM {$tblPrefix}device 
           WHERE (device_user_id = '{$agentUserIdEsc}' OR device_user_id = {$agentId}) 
             AND device_status = 1";
if ($resDev = mysqli_query($connection, $devSql)) {
    while ($dRow = mysqli_fetch_assoc($resDev)) {
        if (!empty($dRow['device_token'])) {
            $deviceTokens[] = $dRow['device_token'];
        }
    }
}
$deviceCount = count($deviceTokens);

$fcmKey = defined('NOTIFICATION_KEY') && !empty(NOTIFICATION_KEY)
    ? NOTIFICATION_KEY
    : 'AAAA022yuIE:APA91bGmpLAcWlFEJB731Z-pTMjYvQu0xPBLQTtElWwsK83HM5mS-Ua0dnYaykR2QIhlxqRfH1vhgX2KHnsNpVZ_lQUbyHk-f7K9CGXcgFPkfIufQ5zbqp7mQfoF1qHoapHxyllk1U76';

if (!empty($deviceTokens)) {
    $notifIcon = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png";
    $notifImage = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.png";
    $notifTitle = "🔔 Follow-Up in 5 Min: {$customerName}";
    $notifBody  = "Scheduled follow-up at {$followupTimeFormatted} for lead #{$leadId} ({$leadSubject}).";

    // Try FCM HTTP v1 first
    $fcmV1File = __DIR__ . '/../../../lib/resource/notification/FcmV1.php';
    if (file_exists($fcmV1File)) {
        require_once $fcmV1File;
        if (class_exists('FcmV1')) {
            $v1Result = FcmV1::sendToMultipleTokens($deviceTokens, $notifTitle, $notifBody, $leadViewUrl, $notifIcon);
            if (!empty($v1Result['success']) && $v1Result['success'] > 0) {
                $pushSent = true;
            }
        }
    }

    // Fallback to legacy endpoint if v1 wasn't dispatched
    if (!$pushSent && !empty($fcmKey)) {
        $fcmPayload = [
            "registration_ids" => $deviceTokens,
            "notification" => [
                "title" => $notifTitle,
                "body" => $notifBody,
                "icon" => $notifIcon,
                "image" => $notifImage,
                "click_action" => $leadViewUrl,
                "sound" => "default"
            ],
            "data" => [
                "title" => $notifTitle,
                "body" => $notifBody,
                "icon" => $notifIcon,
                "image" => $notifImage,
                "click_action" => $leadViewUrl,
                "url" => $leadViewUrl,
                "lead_id" => (string)$leadId,
                "type" => "followup_reminder"
            ]
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmPayload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . $fcmKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        $pushResult = @curl_exec($ch);
        $pushHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($pushHttp === 200) {
            $pushSent = true;
        }
    }
}

// ----------------------------------------------------
// 2. Send HTML Email Alert to Assigned Agent
// ----------------------------------------------------
if (!empty($agentEmail) && filter_var($agentEmail, FILTER_VALIDATE_EMAIL)) {
    $followupReason = "Follow-Up on " . $leadSubject;
    $templatePath = dirname(__DIR__, 3) . '/lib/resource/email/followup-reminder.php';
    
    if (file_exists($templatePath)) {
        require $templatePath;
        $htmlContent = $email_body ?? '';

        $fromEmail = !empty($siteSmtp['email_smtp_email']) ? $siteSmtp['email_smtp_email'] : 'donotreply@graciousworld.com';
        $fromName  = !empty($siteSmtp['email_smtp_name']) ? $siteSmtp['email_smtp_name'] : $siteName;
        $emailSubject = "🔔 Follow-Up in 5 Minutes: {$customerName} - {$siteName}";

        // Method A: PHPMailer
        $phpMailerDir = dirname(__DIR__, 3) . '/lib/configuration/mail/';
        if (file_exists($phpMailerDir . 'php_mailer.php')) {
            require_once $phpMailerDir . 'exception.php';
            require_once $phpMailerDir . 'php_mailer.php';
            require_once $phpMailerDir . 'smtp.php';

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = !empty($siteSmtp['email_smtp_host']) ? $siteSmtp['email_smtp_host'] : 'smtp.zeptomail.in';
                $mail->SMTPAuth = true;
                $mail->Username = !empty($siteSmtp['email_smtp_username']) ? $siteSmtp['email_smtp_username'] : 'emailapikey';
                $mail->Password = !empty($siteSmtp['email_smtp_password'])
                    ? $siteSmtp['email_smtp_password']
                    : 'PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = !empty($siteSmtp['email_smtp_port']) ? (int)$siteSmtp['email_smtp_port'] : 587;
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($agentEmail, $agentName);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $emailSubject;
                $mail->Body    = $htmlContent;
                $mail->AltBody = strip_tags($htmlContent);
                $mail->Timeout = 5;
                if ($mail->send()) {
                    $emailSent = true;
                }
            } catch (\Throwable $e) {
                // Fallback to Zepto API
            }
        }

        // Method B: ZeptoMail API Direct Fallback
        if (!$emailSent) {
            $zeptoKey = !empty($siteSmtp['email_smtp_password'])
                ? 'Zoho-enczapikey ' . $siteSmtp['email_smtp_password']
                : 'Zoho-enczapikey PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';

            $emailPayload = [
                "from" => [
                    "address" => $fromEmail,
                    "name"    => $fromName
                ],
                "to" => [[
                    "email_address" => [
                        "address" => $agentEmail,
                        "name"    => $agentName
                    ]
                ]],
                "subject"  => $emailSubject,
                "htmlbody" => $htmlContent
            ];

            $ch = curl_init('https://api.zeptomail.in/v1.1/email');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailPayload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $zeptoKey,
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $zeptoRes = @curl_exec($ch);
            $zeptoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($zeptoHttp === 200 || $zeptoHttp === 201) {
                $emailSent = true;
            }
        }
    }
}

echo json_encode([
    'status' => 'success',
    'message' => 'Follow-up alert processed for assigned user.',
    'lead_id' => $leadId,
    'agent_id' => $agentId,
    'agent_name' => $agentName,
    'agent_email' => $agentEmail,
    'push_sent' => $pushSent,
    'device_count' => $deviceCount,
    'email_sent' => $emailSent,
    'followup_time' => $followupTimeFormatted
]);
exit();
