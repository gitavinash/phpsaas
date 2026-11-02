<?php

/**
 * Cron Job: Multi-Stage Site Subscription Expiration Email Reminders
 * Path: /cron/subscriptionReminders.php
 *
 * Description:
 * Checks isw_site joined with isw_site_installation to determine site subscription
 * expiration dates. Automatically delivers renewal reminder emails to site owners
 * across key milestones:
 *   - 1 Week Before Expiration (7 days before)
 *   - 3 Days Before Expiration (3 days before)
 *   - On Expiration Day (0 days)
 *   - 1 Day After Expiration (grace period notice)
 *   - 7 Days After Expiration (final warning before suspension)
 *
 * Requirements:
 *   - Morning delivery window enforcement (10:00 AM to 11:50 AM IST)
 *   - Idempotency & deduplication via isw_site_subscription_reminder table
 *   - Dual email delivery (PHPMailer SMTP + ZeptoMail API fallback)
 *   - Comprehensive execution logging to cron/cron.log
 *   - CLI arguments: --dry-run, --site_id=N, --date=YYYY-MM-DD, --force-window, --force, --test-email=xyz
 */

$logFile = __DIR__ . '/cron.log';
$logTimezone = new DateTimeZone('Asia/Kolkata');
$logRunId = (new DateTime('now', $logTimezone))->format('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

if (!function_exists('formatSchedulerContext')) {
    function formatSchedulerContext(array $context): string
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
}

if (!function_exists('writeSchedulerLog')) {
    function writeSchedulerLog(string $level, string $event, array $context = []): void
    {
        global $logFile, $logRunId, $logTimezone;
        $filename = basename(__FILE__);
        $timestamp = (new DateTime('now', $logTimezone))->format('Y-m-d H:i:s') . ' IST';
        $prefix = '[' . $timestamp . "] [" . strtoupper($level) . "] [file={$filename}] [run={$logRunId}] " . $event;
        $contextText = formatSchedulerContext($context);
        $line = $prefix . (($contextText !== '') ? ' | ' . $contextText : '') . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND);
    }
}

// --------------------------------------------------------------------------
// 1. Parse Command-Line / Web Query Arguments
// --------------------------------------------------------------------------
$isCli = (php_sapi_name() === 'cli');
$cliArgs = [];

if ($isCli && isset($argv) && is_array($argv)) {
    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', substr($arg, 2), 2);
            $key = $parts[0];
            $val = isset($parts[1]) ? $parts[1] : true;
            $cliArgs[$key] = $val;
        }
    }
}

$isDryRun      = isset($cliArgs['dry-run']) || !empty($_GET['dry_run']);
$forceWindow   = isset($cliArgs['force-window']) || !empty($_GET['force_window']) || isset($cliArgs['force']) || !empty($_GET['force']);
$forceSend     = isset($cliArgs['force']) || !empty($_GET['force']);
$filterSiteId  = isset($cliArgs['site_id']) ? (int)$cliArgs['site_id'] : (!empty($_GET['site_id']) ? (int)$_GET['site_id'] : 0);
$filterStage   = isset($cliArgs['stage']) ? trim($cliArgs['stage']) : (!empty($_GET['stage']) ? trim($_GET['stage']) : '');
$testEmail     = isset($cliArgs['test-email']) ? trim($cliArgs['test-email']) : (!empty($_GET['test_email']) ? trim($_GET['test_email']) : '');
$evalDateStr   = isset($cliArgs['date']) ? trim($cliArgs['date']) : (!empty($_GET['date']) ? trim($_GET['date']) : '');

$evalDateTime = !empty($evalDateStr) ? DateTime::createFromFormat('Y-m-d', $evalDateStr, $logTimezone) : new DateTime('now', $logTimezone);
if (!$evalDateTime) {
    $evalDateTime = new DateTime('now', $logTimezone);
}
$evalDateFormatted = $evalDateTime->format('Y-m-d');

// --------------------------------------------------------------------------
// 2. Enforce Morning Delivery Time Window (10:00 AM to 11:50 AM IST)
// --------------------------------------------------------------------------
$nowTimeStr = (new DateTime('now', $logTimezone))->format('H:i');
$isWithinWindow = ($nowTimeStr >= '10:00' && $nowTimeStr <= '11:50');

writeSchedulerLog('info', 'Started: subscriptionReminders.php', [
    'current_time_ist' => $nowTimeStr,
    'within_window'    => $isWithinWindow,
    'eval_date'        => $evalDateFormatted,
    'dry_run'          => $isDryRun,
    'force_window'     => $forceWindow,
    'filter_site_id'   => $filterSiteId,
    'test_email'       => $testEmail
]);

if (!$isWithinWindow && !$forceWindow) {
    $windowMsg = "Outside permitted morning delivery window (10:00 AM - 11:50 AM IST). Current time: {$nowTimeStr} IST. Halting execution.";
    writeSchedulerLog('notice', $windowMsg);
    if ($isCli) {
        echo "[SKIP] {$windowMsg}\nUse --force-window or --force to bypass this check during manual testing.\n";
    }
    return;
}

// --------------------------------------------------------------------------
// 3. Database Connection
// --------------------------------------------------------------------------
$dbConnectionFile = dirname(__DIR__) . '/dbConfig.php';
if (!file_exists($dbConnectionFile)) {
    writeSchedulerLog('error', 'Aborted: dbConfig.php not found', ['path' => $dbConnectionFile]);
    if ($isCli) echo "Error: dbConfig.php not found.\n";
    return;
}

require_once $dbConnectionFile;
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    writeSchedulerLog('error', 'Aborted: Database connection failed', ['error' => mysqli_connect_error()]);
    if ($isCli) echo "Error: Database connection failed.\n";
    return;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

// --------------------------------------------------------------------------
// 4. Ensure Idempotent Reminder Tracking Table Exists
// --------------------------------------------------------------------------
$createTableSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}site_subscription_reminder` (
  `reminder_id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `reminder_site_id` BIGINT NOT NULL,
  `reminder_owner_id` BIGINT NOT NULL,
  `reminder_site_domain` VARCHAR(255) NOT NULL,
  `reminder_expiry_date` DATE NOT NULL,
  `reminder_stage` VARCHAR(50) NOT NULL,
  `reminder_email` VARCHAR(255) NOT NULL,
  `reminder_email_sent` TINYINT(1) DEFAULT 0,
  `reminder_status` VARCHAR(20) DEFAULT 'sent',
  `reminder_error` TEXT NULL,
  `reminder_sent_at` DATETIME NULL,
  `reminder_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_site_expiry_stage` (`reminder_site_id`, `reminder_expiry_date`, `reminder_stage`),
  KEY `idx_reminder_expiry_date` (`reminder_expiry_date`),
  KEY `idx_reminder_owner_id` (`reminder_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_query($connection, $createTableSql)) {
    writeSchedulerLog('error', 'Failed to create reminder tracking table', ['error' => mysqli_error($connection)]);
}

// --------------------------------------------------------------------------
// 5. Query Sites Joined with Site Installation, User, and Profile
// --------------------------------------------------------------------------
$siteWhere = "s.site_status = 1 AND si.site_installation_expiry_date IS NOT NULL AND si.site_installation_expiry_date != '0000-00-00'";
if ($filterSiteId > 0) {
    $siteWhere .= " AND s.site_id = {$filterSiteId}";
}

$sitesSql = "SELECT 
    s.site_id,
    s.site_name,
    s.site_domain,
    s.site_owner_id,
    s.site_status,
    s.site_app_id,
    a.app_title,
    a.app_path,
    MAX(si.site_installation_expiry_date) AS site_installation_expiry_date,
    p.profile_first_name,
    p.profile_last_name,
    p.profile_display_name,
    p.profile_email,
    u.user_email,
    u.user_username
FROM {$tblPrefix}site s
INNER JOIN {$tblPrefix}site_installation si ON si.site_installation_site_id = s.site_id
LEFT JOIN {$tblPrefix}app a ON a.app_id = s.site_app_id
LEFT JOIN {$tblPrefix}user u ON u.user_id = s.site_owner_id
LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = s.site_owner_id
WHERE {$siteWhere}
GROUP BY s.site_id
ORDER BY s.site_id ASC";

$sitesResult = mysqli_query($connection, $sitesSql);
if (!$sitesResult) {
    writeSchedulerLog('error', 'SQL Query failed', ['error' => mysqli_error($connection)]);
    mysqli_close($connection);
    if ($isCli) echo "Error: SQL Query failed.\n";
    return;
}

// --------------------------------------------------------------------------
// 6. Map Expiration Milestones / Stages
// --------------------------------------------------------------------------
$stageDefinitions = [
    7  => '7_days_before',
    3  => '3_days_before',
    0  => 'same_day',
    -1 => '1_day_after',
    -7 => '7_days_after'
];

$summary = [
    'total_sites_checked' => 0,
    'eligible_reminders'  => 0,
    'sent_success'        => 0,
    'skipped_already_sent'=> 0,
    'skipped_invalid_email' => 0,
    'failed_delivery'     => 0,
    'dry_run_count'       => 0,
];

$processedItems = [];

// Prepare Email Dispatch Helper
$emailTemplatePath = dirname(__DIR__) . '/lib/resource/email/site-subscription-reminder.php';
$phpMailerDir      = dirname(__DIR__) . '/lib/configuration/mail/';

while ($row = mysqli_fetch_assoc($sitesResult)) {
    $summary['total_sites_checked']++;

    $siteId         = (int)$row['site_id'];
    $ownerId        = (int)$row['site_owner_id'];
    $siteNameText   = trim($row['site_name'] ?? '');
    $siteDomainText = trim($row['site_domain'] ?? '');
    if (empty($siteDomainText)) {
        $siteDomainText = "site-{$siteId}.isw.one";
    }
    if (empty($siteNameText)) {
        $siteNameText = $siteDomainText;
    }

    $expiryDateRaw  = $row['site_installation_expiry_date'];
    $expiryDateTime = DateTime::createFromFormat('Y-m-d', $expiryDateRaw, $logTimezone);
    if (!$expiryDateTime) {
        continue;
    }

    // Calculate exact difference in days between expiryDate and evalDate
    // Positive = days remaining until expiry, Negative = days past expiry
    $evalDayMidnight   = new DateTime($evalDateFormatted, $logTimezone);
    $expiryDayMidnight = new DateTime($expiryDateRaw, $logTimezone);
    $diffInterval      = $evalDayMidnight->diff($expiryDayMidnight);
    $daysRemaining     = (int)$diffInterval->format('%r%a');

    if (!isset($stageDefinitions[$daysRemaining])) {
        // Not a target reminder milestone
        continue;
    }

    $stage = $stageDefinitions[$daysRemaining];

    if (!empty($filterStage) && $filterStage !== $stage) {
        continue;
    }

    $summary['eligible_reminders']++;

    // Determine Owner Name & Email
    $ownerDisplayName = trim($row['profile_display_name'] ?? '');
    if (empty($ownerDisplayName)) {
        $first = trim($row['profile_first_name'] ?? '');
        $last  = trim($row['profile_last_name'] ?? '');
        $ownerDisplayName = trim("{$first} {$last}");
    }
    if (empty($ownerDisplayName)) {
        $ownerDisplayName = trim($row['user_username'] ?? 'Site Owner');
    }

    $ownerEmail = trim($row['profile_email'] ?? '');
    if (!filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
        $ownerEmail = trim($row['user_email'] ?? '');
    }

    // Apply Test Email Override if provided (e.g. avinash@blumox.com for QA)
    $actualRecipient = $ownerEmail;
    $isTestRedirect = false;
    if (!empty($testEmail) && filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        $actualRecipient = $testEmail;
        $isTestRedirect  = true;
    }

    if (!filter_var($actualRecipient, FILTER_VALIDATE_EMAIL)) {
        $summary['skipped_invalid_email']++;
        writeSchedulerLog('warning', "Invalid email for site #{$siteId} ({$siteDomainText})", [
            'site_id'       => $siteId,
            'profile_email' => $row['profile_email'] ?? '',
            'user_email'    => $row['user_email'] ?? ''
        ]);
        continue;
    }

    // Check Deduplication (has this stage already been sent for this expiry date?)
    $checkSql = "SELECT reminder_id, reminder_email_sent FROM {$tblPrefix}site_subscription_reminder 
                 WHERE reminder_site_id = {$siteId} 
                   AND reminder_expiry_date = '{$expiryDateRaw}' 
                   AND reminder_stage = '{$stage}' 
                 LIMIT 1";
    $checkRes = mysqli_query($connection, $checkSql);
    $alreadySent = false;
    if ($checkRes && $existing = mysqli_fetch_assoc($checkRes)) {
        if ((int)$existing['reminder_email_sent'] === 1 && !$forceSend) {
            $alreadySent = true;
        }
    }

    if ($alreadySent) {
        $summary['skipped_already_sent']++;
        continue;
    }

    // ----------------------------------------------------------------------
    // 7. Determine Brand Identity, Plan Tier & Sender Details
    // ----------------------------------------------------------------------
    $appPath   = strtolower(trim($row['app_path'] ?? ''));
    $siteAppId = (int)($row['site_app_id'] ?? 0);
    $isAleya   = ($siteAppId === 20 || $appPath === 'aleya' || stripos($siteDomainText, 'aleya.in') !== false);

    if ($isAleya) {
        $brandName    = 'Aleya';
        $brandLogo    = 'https://crm.blumox.com/upload/others/aleya-logo.png';
        $brandWebsite = 'https://aleya.in';
        $fromName     = 'Aleya Accounts';
    } else {
        $brandName    = 'Blumox Technologies';
        $brandLogo    = 'https://blumox.com/assets/images/logo.png';
        $brandWebsite = 'https://blumox.com';
        $fromName     = 'Blumox Accounts';
    }

    // Query site configuration for specific subscription plan tier
    $subscriptionPlan = 'Annual Professional SaaS';
    $planQ = mysqli_query($connection, "SELECT site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'subscription_plan' LIMIT 1");
    if ($planQ && $planRow = mysqli_fetch_assoc($planQ)) {
        if (!empty($planRow['site_configuration_value'])) {
            $subscriptionPlan = trim($planRow['site_configuration_value']);
        }
    }

    // Prepare Email Content Variables
    $expiryDateFormatted = $expiryDateTime->format('d M Y');
    $dashboardUrl        = "https://{$siteDomainText}/dashboard/";
    $whatsappMsg         = "Hi, I want to renew my website subscription for {$siteDomainText} (Site ID: {$siteId}, Plan: {$subscriptionPlan}, Expiry: {$expiryDateFormatted})";
    $whatsappRenewUrl    = "https://api.whatsapp.com/send?phone=919990026008&text=" . urlencode($whatsappMsg);

    if ($isDryRun) {
        $summary['dry_run_count']++;
        $processedItems[] = [
            'site_id'          => $siteId,
            'site_domain'      => $siteDomainText,
            'brand'            => $brandName,
            'plan'             => $subscriptionPlan,
            'stage'            => $stage,
            'days_remaining'   => $daysRemaining,
            'expiry_date'      => $expiryDateRaw,
            'recipient_email'  => $actualRecipient,
            'status'           => 'dry_run_simulated'
        ];
        continue;
    }

    // ----------------------------------------------------------------------
    // 8. Render Email Template
    // ----------------------------------------------------------------------
    $email_body = '';
    $subject    = '';
    if (file_exists($emailTemplatePath)) {
        // Variables for template: $stage, $siteNameText, $siteDomainText, $expiryDateFormatted,
        // $daysRemaining, $ownerDisplayName, $dashboardUrl, $whatsappRenewUrl,
        // $brandName, $brandLogo, $brandWebsite, $subscriptionPlan
        ob_start();
        include $emailTemplatePath;
        ob_get_clean();
        $renderedHtml = $email_body ?? '';
    } else {
        $renderedHtml = "Reminder: Your website subscription for {$siteNameText} ({$siteDomainText}) expires on {$expiryDateFormatted}.";
        $subject      = "Subscription Renewal Notice for {$siteDomainText}";
    }

    // Subject line is generated cleanly inside the template without any test prefixes


    // ----------------------------------------------------------------------
    // 9. Deliver Email (Primary: accounts@blumox.com | Fallback: alerts@blumox.com)
    // ----------------------------------------------------------------------
    $fromEmail    = 'accounts@blumox.com';
    $replyToEmail = 'accounts@blumox.com';
    $emailSent    = 0;
    $deliveryError = '';

    // Method A: PHPMailer SMTP
    if (file_exists($phpMailerDir . 'php_mailer.php')) {
        require_once $phpMailerDir . 'exception.php';
        require_once $phpMailerDir . 'php_mailer.php';
        require_once $phpMailerDir . 'smtp.php';

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host     = 'smtp.zeptomail.in';
            $mail->SMTPAuth = true;
            $mail->Username = 'emailapikey';
            $mail->Password = 'PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port     = 587;
            $mail->setFrom($fromEmail, $fromName);
            $mail->addReplyTo($replyToEmail, $fromName);
            $mail->addAddress($actualRecipient, $ownerDisplayName);
            $mail->isHTML(true);
            $mail->CharSet  = 'UTF-8';
            $mail->Subject  = $subject;
            $mail->Body     = $renderedHtml;
            $mail->AltBody  = strip_tags($renderedHtml);
            $mail->Timeout  = 8;

            if ($mail->send()) {
                $emailSent = 1;
            } else {
                $deliveryError = 'PHPMailer failed: ' . $mail->ErrorInfo;
            }
        } catch (\Throwable $e) {
            $deliveryError = 'PHPMailer Exception: ' . $e->getMessage();
        }
    }

    // Method B: ZeptoMail HTTP API (Direct API with Automatic Sender Address Fallback)
    if (!$emailSent) {
        $zeptoKey = 'Zoho-enczapikey PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
        
        $dispatchViaZepto = function($senderAddr) use ($actualRecipient, $ownerDisplayName, $replyToEmail, $fromName, $subject, $renderedHtml, $zeptoKey) {
            $payload = [
                "from" => [
                    "address" => $senderAddr,
                    "name"    => $fromName
                ],
                "to" => [[
                    "email_address" => [
                        "address" => $actualRecipient,
                        "name"    => $ownerDisplayName
                    ]
                ]],
                "reply_to" => [
                    "address" => $replyToEmail,
                    "name"    => $fromName
                ],
                "subject"  => $subject,
                "htmlbody" => $renderedHtml
            ];

            $ch = curl_init('https://api.zeptomail.in/v1.1/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'content-type: application/json',
                'authorization: ' . $zeptoKey,
                'cache-control: no-cache'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            $zRes  = @curl_exec($ch);
            $zCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            return ['code' => $zCode, 'response' => $zRes];
        };

        // Attempt 1: accounts@blumox.com
        $zResData = $dispatchViaZepto($fromEmail);
        if ($zResData['code'] === 200 || $zResData['code'] === 201) {
            $emailSent = 1;
            $deliveryError = '';
        } else {
            // Attempt 2: Fallback to verified sender alerts@blumox.com with Reply-To accounts@blumox.com
            $zFallback = $dispatchViaZepto('alerts@blumox.com');
            if ($zFallback['code'] === 200 || $zFallback['code'] === 201) {
                $emailSent = 1;
                $deliveryError = '';
            } else {
                $deliveryError .= ' | Zepto HTTP ' . $zResData['code'] . ' & Fallback ' . $zFallback['code'] . ': ' . substr((string)$zFallback['response'], 0, 150);
            }
        }
    }

    // ----------------------------------------------------------------------
    // 9. Record Tracking in Database
    // ----------------------------------------------------------------------
    $domainEsc    = mysqli_real_escape_string($connection, $siteDomainText);
    $recipientEsc = mysqli_real_escape_string($connection, $actualRecipient);
    $errorEsc     = mysqli_real_escape_string($connection, $deliveryError);
    $statusText   = ($emailSent === 1) ? 'sent' : 'failed';

    $upsertSql = "INSERT INTO {$tblPrefix}site_subscription_reminder
        (reminder_site_id, reminder_owner_id, reminder_site_domain, reminder_expiry_date, reminder_stage, reminder_email, reminder_email_sent, reminder_status, reminder_error, reminder_sent_at)
        VALUES ({$siteId}, {$ownerId}, '{$domainEsc}', '{$expiryDateRaw}', '{$stage}', '{$recipientEsc}', {$emailSent}, '{$statusText}', '{$errorEsc}', NOW())
        ON DUPLICATE KEY UPDATE 
            reminder_email = VALUES(reminder_email),
            reminder_email_sent = VALUES(reminder_email_sent),
            reminder_status = VALUES(reminder_status),
            reminder_error = VALUES(reminder_error),
            reminder_sent_at = NOW()";
    mysqli_query($connection, $upsertSql);

    if ($emailSent === 1) {
        $summary['sent_success']++;
        writeSchedulerLog('info', "Sent {$stage} reminder to {$actualRecipient} for site #{$siteId} ({$siteDomainText})", [
            'site_id'      => $siteId,
            'stage'        => $stage,
            'expiry_date'  => $expiryDateRaw,
            'email'        => $actualRecipient,
            'is_redirect'  => $isTestRedirect
        ]);
    } else {
        $summary['failed_delivery']++;
        writeSchedulerLog('error', "Failed delivering {$stage} reminder to {$actualRecipient} for site #{$siteId}", [
            'site_id' => $siteId,
            'error'   => $deliveryError
        ]);
    }

    $processedItems[] = [
        'site_id'         => $siteId,
        'site_domain'     => $siteDomainText,
        'stage'           => $stage,
        'expiry_date'     => $expiryDateRaw,
        'recipient_email' => $actualRecipient,
        'status'          => $statusText,
        'error'           => $deliveryError
    ];
}

mysqli_close($connection);

writeSchedulerLog('info', 'Finished: subscriptionReminders.php', $summary);

// --------------------------------------------------------------------------
// 10. Output Console Summary
// --------------------------------------------------------------------------
$outputStr = sprintf(
    "Completed: Checked %d sites | Eligible: %d | Sent: %d | Skipped (Already Sent): %d | Skipped (Invalid Email): %d | Failed: %d | Dry Run: %d\n",
    $summary['total_sites_checked'],
    $summary['eligible_reminders'],
    $summary['sent_success'],
    $summary['skipped_already_sent'],
    $summary['skipped_invalid_email'],
    $summary['failed_delivery'],
    $summary['dry_run_count']
);

if ($isCli) {
    echo $outputStr;
}

$GLOBALS['subscriptionReminderOutput'] = [
    'summary'   => $summary,
    'processed' => $processedItems,
    'output'    => trim($outputStr)
];

