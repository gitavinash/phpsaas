<?php

/**
 * Cron Job: 5-Minute Lead Follow-Up Reminder Processor
 * Path: /cron/followupReminders.php
 * 
 * Description:
 * Scans for upcoming lead follow-ups due in 5 minutes (enquiry_followup <= NOW() + 5 min),
 * dispatches FCM Web Push Notifications and HTML Email alerts to the assigned agent,
 * and records sent reminders in the database to prevent duplicate alerts.
 */

$logFile = __DIR__ . '/cron.log';
$logTimezone = new DateTimeZone('Asia/Kolkata');
$logRunId = (new DateTime('now', $logTimezone))->format('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

function formatSchedulerContext(array $context)
{
    if (empty($context)) {
        return '';
    }

    $parts = [];
    foreach ($context as $key => $value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $value = 'null';
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        $parts[] = $key . '=' . $value;
    }

    return implode(' | ', $parts);
}

function writeSchedulerLog($level, $event, $context = [])
{
    global $logFile, $logRunId, $logTimezone;
    $filename = basename(__FILE__);
    $timestamp = (new DateTime('now', $logTimezone))->format('Y-m-d H:i:s') . ' IST';
    $prefix = '[' . $timestamp . "] [" . strtoupper($level) . "] [file={$filename}] [run={$logRunId}] " . $event;
    $contextText = formatSchedulerContext((array) $context);
    $line = $prefix . (($contextText !== '') ? ' | ' . $contextText : '') . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND);
}

writeSchedulerLog('info', 'Started: followupReminders.php - checking for pending 5-minute follow-ups');

$dbConnectionFile = dirname(__DIR__) . '/dbConfig.php';

if (!file_exists($dbConnectionFile)) {
    writeSchedulerLog('error', 'Aborted: configuration file not found', ['path' => $dbConnectionFile]);
    exit("Configuration file not found\n");
}

require_once $dbConnectionFile;
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    writeSchedulerLog('error', 'Aborted: database connection failed', [
        'mysql_error' => mysqli_connect_error(),
    ]);
    exit("Database connection failed\n");
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'tbl_';

// 1. Ensure Reminder Tracking Table exists
$createTableSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}enquiry_reminder` (
  `reminder_id` INT AUTO_INCREMENT PRIMARY KEY,
  `reminder_enquiry_id` INT NOT NULL,
  `reminder_agent_id` INT NOT NULL,
  `reminder_followup_time` DATETIME NOT NULL,
  `reminder_push_sent` TINYINT(1) DEFAULT 0,
  `reminder_email_sent` TINYINT(1) DEFAULT 0,
  `reminder_sent_at` DATETIME NULL,
  `reminder_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`reminder_enquiry_id`),
  INDEX (`reminder_agent_id`),
  INDEX (`reminder_followup_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($connection, $createTableSql);

// 2. Query upcoming follow-ups that are due in the next 5-6 minutes (or overdue by <= 10 min during cron catch-up)
// and haven't been alerted yet for this exact followup timestamp
$query = "SELECT 
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
INNER JOIN {$tblPrefix}user u ON e.enquiry_agent = u.user_id
INNER JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
LEFT JOIN {$tblPrefix}site s ON e.enquiry_site_id = s.site_id
LEFT JOIN {$tblPrefix}enquiry_reminder r 
    ON r.reminder_enquiry_id = e.enquiry_id 
    AND r.reminder_followup_time = e.enquiry_followup
    AND (r.reminder_push_sent = 1 OR r.reminder_email_sent = 1)
WHERE e.enquiry_agent > 0
  AND e.enquiry_followup IS NOT NULL
  AND e.enquiry_followup != '0000-00-00 00:00:00'
  AND e.enquiry_followup <= (NOW() + INTERVAL 330 SECOND)
  AND e.enquiry_followup >= (NOW() - INTERVAL 15 MINUTE)
  AND e.enquiry_status NOT IN (1, 4, 8) -- Not Converted, Closed, or Cancelled
  AND r.reminder_id IS NULL
ORDER BY e.enquiry_followup ASC
LIMIT 50";

$res = mysqli_query($connection, $query);
if (!$res) {
    writeSchedulerLog('error', 'SQL query failed', ['error' => mysqli_error($connection)]);
    mysqli_close($connection);
    exit("Query failed\n");
}

$totalProcessed = 0;
$totalPushes = 0;
$totalEmails = 0;

while ($row = mysqli_fetch_assoc($res)) {
    $realLeadId = (int)$row['enquiry_id'];
    $agentId = (int)$row['enquiry_agent'];
    $agentName = trim($row['profile_display_name'] ?? 'Representative');
    $agentEmail = trim($row['profile_email'] ?? '');
    $customerName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
    if (empty($customerName)) {
        $customerName = 'Lead #' . $realLeadId;
    }
    $customerMobile = trim($row['enquiry_mobile'] ?? '');
    $leadSubject = trim($row['enquiry_subject'] ?? 'Tour Requirement');
    $followupRaw = $row['enquiry_followup'];
    $followupTimeFormatted = date('d M Y, h:i A', strtotime($followupRaw));
    
    $siteName = !empty($row['site_name']) ? $row['site_name'] : 'Aleya Workspace';
    $siteDomain = !empty($row['site_domain']) ? $row['site_domain'] : 'aleya.in';
    $leadViewUrl = "https://{$siteDomain}/dashboard/leads/view/?id=" . base64_encode((string)$realLeadId);

    // Fetch site SMTP settings
    $siteSmtp = [];
    $siteId = (int)$row['enquiry_site_id'];
    $smtpSql = "SELECT config_key, config_value FROM {$tblPrefix}configuration WHERE config_site_id = {$siteId} AND config_key LIKE 'email_smtp_%'";
    if ($smtpRes = mysqli_query($connection, $smtpSql)) {
        while ($cfg = mysqli_fetch_assoc($smtpRes)) {
            $siteSmtp[$cfg['config_key']] = $cfg['config_value'];
        }
    }

    $pushSent = 0;
    $emailSent = 0;

    // ----------------------------------------------------
    // 1. Send FCM Web Push Notification to Agent's Devices
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

    $fcmKey = defined('NOTIFICATION_KEY') && !empty(NOTIFICATION_KEY)
        ? NOTIFICATION_KEY
        : 'AAAA022yuIE:APA91bGmpLAcWlFEJB731Z-pTMjYvQu0xPBLQTtElWwsK83HM5mS-Ua0dnYaykR2QIhlxqRfH1vhgX2KHnsNpVZ_lQUbyHk-f7K9CGXcgFPkfIufQ5zbqp7mQfoF1qHoapHxyllk1U76';

    if (!empty($deviceTokens) && !empty($fcmKey)) {
        $notifIcon = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png";
        $notifImage = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.png";
        $notifTitle = "🔔 Follow-Up in 5 Min: {$customerName}";
        $notifBody  = "Follow-up at {$followupTimeFormatted} for lead #{$realLeadId} ({$leadSubject}).";

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
                "lead_id" => (string)$realLeadId,
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
            $pushSent = 1;
            $totalPushes++;
        }
    }

    // ----------------------------------------------------
    // 2. Send HTML Email Alert to Assigned Agent
    // ----------------------------------------------------
    if (!empty($agentEmail) && filter_var($agentEmail, FILTER_VALIDATE_EMAIL)) {
        $followupReason = "Follow-Up on " . $leadSubject;
        $templatePath = dirname(__DIR__) . '/lib/resource/email/followup-reminder.php';
        
        if (file_exists($templatePath)) {
            require $templatePath;
            $htmlContent = $email_body ?? '';

            $fromEmail = !empty($siteSmtp['email_smtp_email']) ? $siteSmtp['email_smtp_email'] : 'donotreply@graciousworld.com';
            $fromName  = !empty($siteSmtp['email_smtp_name']) ? $siteSmtp['email_smtp_name'] : $siteName;
            $emailSubject = "🔔 Follow-Up in 5 Minutes: {$customerName} - {$siteName}";

            // Method A: PHPMailer
            $phpMailerDir = dirname(__DIR__) . '/lib/configuration/mail/';
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
                        $emailSent = 1;
                        $totalEmails++;
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
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailPayload, JSON_UNESCAPED_SLASHES));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'accept: application/json',
                    'content-type: application/json',
                    'authorization: ' . $zeptoKey,
                    'cache-control: no-cache'
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                $zRes = @curl_exec($ch);
                $zCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                @curl_close($ch);

                if ($zCode === 200 || $zCode === 201) {
                    $emailSent = 1;
                    $totalEmails++;
                }
            }
        }
    }

    // ----------------------------------------------------
    // 3. Record Reminder in Database Log (Deduplication)
    // ----------------------------------------------------
    $followupEsc = mysqli_real_escape_string($connection, $followupRaw);
    $insSql = "INSERT INTO {$tblPrefix}enquiry_reminder 
               (reminder_enquiry_id, reminder_agent_id, reminder_followup_time, reminder_push_sent, reminder_email_sent, reminder_sent_at)
               VALUES ({$realLeadId}, {$agentId}, '{$followupEsc}', {$pushSent}, {$emailSent}, NOW())";
    mysqli_query($connection, $insSql);

    $totalProcessed++;
    writeSchedulerLog('info', "Sent reminder for lead #{$realLeadId} to agent {$agentName}", [
        'lead_id' => $realLeadId,
        'agent_id' => $agentId,
        'followup_time' => $followupRaw,
        'push_sent' => $pushSent,
        'email_sent' => $emailSent
    ]);
}

writeSchedulerLog('info', "Finished: followupReminders.php", [
    'total_leads_reminded' => $totalProcessed,
    'push_notifications' => $totalPushes,
    'emails_sent' => $totalEmails
]);

mysqli_close($connection);
echo "Completed: Processed {$totalProcessed} follow-up reminders ({$totalPushes} push, {$totalEmails} emails).\n";
