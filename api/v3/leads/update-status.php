<?php

require_once LOGIN_CHECK;

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

// 1. Support both form-data/url-encoded and raw JSON body
$rawInput = file_get_contents('php://input');
$jsonData = !empty($rawInput) ? json_decode($rawInput, true) : null;
if (!is_array($jsonData)) {
    $jsonData = [];
}

// Retrieve lead identifier from all common aliases
$rawLeadId = $_POST['leadId']
    ?? ($_POST['lead_id']
        ?? ($_POST['id']
            ?? ($_POST['enquiry_id']
                ?? ($jsonData['leadId']
                    ?? ($jsonData['lead_id']
                        ?? ($jsonData['id']
                            ?? ($jsonData['enquiry_id']
                                ?? ($_GET['leadId']
                                    ?? ($_GET['lead_id']
                                        ?? ($_GET['id']
                                            ?? ($_REQUEST['leadId']
                                                ?? ($_REQUEST['lead_id']
                                                    ?? ($_REQUEST['id']
                                                        ?? ($leadId ?? ''))))))))))))));

// Retrieve target status from all common aliases
$rawStatus = $_POST['status']
    ?? ($_POST['leadStatus']
        ?? ($_POST['new_status']
            ?? ($jsonData['status']
                ?? ($jsonData['leadStatus']
                    ?? ($jsonData['new_status']
                        ?? ($_GET['status']
                            ?? ($_GET['leadStatus']
                                ?? ($_REQUEST['status']
                                    ?? ($_REQUEST['leadStatus']
                                        ?? ($status ?? null))))))))));

if ($rawLeadId !== '' && $rawLeadId !== null && $rawStatus !== null && is_numeric($rawStatus)) {
    $newStatus = intval($rawStatus);
    $decoded   = base64_decode(trim((string)$rawLeadId), true);
    $realId    = ($decoded !== false && is_numeric($decoded)) ? intval($decoded) : intval($rawLeadId);

    if ($realId > 0 && isset($connection) && $connection) {
        $siteClause = ($siteId > 0) ? " AND e.enquiry_site_id = {$siteId}" : "";

        // 2. Fetch existing lead details
        $leadSql = "SELECT e.*,
                           u.user_id AS agent_user_id,
                           u.user_username AS agent_username,
                           u.user_email AS agent_email,
                           u.user_mobile AS agent_mobile,
                           p.profile_first_name AS agent_first_name,
                           p.profile_last_name AS agent_last_name,
                           p.profile_display_name AS agent_display_name
                    FROM {$tblPrefix}enquiry e
                    LEFT JOIN {$tblPrefix}user u ON u.user_id = e.enquiry_agent
                    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = e.enquiry_agent
                    WHERE e.enquiry_id = {$realId}{$siteClause}
                    LIMIT 1";
        $leadRes = mysqli_query($connection, $leadSql);

        if (!$leadRes || mysqli_num_rows($leadRes) === 0) {
            http_response_code(404);
            echo json_encode([
                'status'  => 404,
                'msg'     => 'Lead not found or access denied.',
                'lead_id' => $realId
            ], JSON_UNESCAPED_SLASHES);
            exit();
        }

        $leadData = mysqli_fetch_assoc($leadRes);
        $oldStatus = (int)($leadData['enquiry_status'] ?? 0);

        // 3. Update enquiry status
        $updateSiteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
        $updateSql = "UPDATE {$tblPrefix}enquiry
                      SET enquiry_status = {$newStatus},
                          enquiry_update = NOW()
                      WHERE enquiry_id = {$realId}{$updateSiteClause}";

        if (!mysqli_query($connection, $updateSql)) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'msg'    => 'Database error during status update: ' . mysqli_error($connection)
            ], JSON_UNESCAPED_SLASHES);
            exit();
        }

        // 4. Resolve current operating user
        $currentUserId = USER_ID;
        $currentUserName = 'System';
        if ($currentUserId > 0) {
            $userQ = mysqli_query($connection, "SELECT p.profile_display_name, p.profile_first_name, p.profile_last_name, u.user_username 
                                                FROM {$tblPrefix}user u 
                                                LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id 
                                                WHERE u.user_id = {$currentUserId} LIMIT 1");
            if ($userQ && $userR = mysqli_fetch_assoc($userQ)) {
                $currentUserName = trim(($userR['profile_display_name'] ?? '') ?: (($userR['profile_first_name'] ?? '') . ' ' . ($userR['profile_last_name'] ?? '')));
                if (empty($currentUserName)) {
                    $currentUserName = $userR['user_username'] ?? ('User #' . $currentUserId);
                }
            }
        }

        // 5. Log activity into enquiry_notes (Omit for status 9 as itinerary generation/dispatch handles its own rich log)
        if ($newStatus !== 9) {
            require_once __DIR__ . '/status-config.php';
            $statusMeta = getLeadStatusMeta($newStatus);
            $statusLabel = $statusMeta['label'];

            $noteText = ($newStatus === 1)
                ? "Lead converted to Confirmed Sale (Success) by {$currentUserName}"
                : "Lead status updated to {$statusLabel} by {$currentUserName}";

            $metaTypeEsc = 'status';
            $metaValEsc  = mysqli_real_escape_string($connection, $noteText);
            $insertNoteSql = "INSERT INTO {$tblPrefix}enquiry_notes 
                              (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) 
                              VALUES ('{$realId}', '{$currentUserId}', '{$metaTypeEsc}', '{$metaValEsc}')";
            @mysqli_query($connection, $insertNoteSql);
        }

        // 6. Lead details for notifications
        $custName = trim(($leadData['enquiry_first_name'] ?? '') . ' ' . ($leadData['enquiry_last_name'] ?? ''));
        if (empty($custName)) {
            $custName = 'Valued Customer';
        }
        $custMobile     = !empty($leadData['enquiry_mobile']) ? $leadData['enquiry_mobile'] : 'Not provided';
        $custEmail      = !empty($leadData['enquiry_email']) ? $leadData['enquiry_email'] : 'Not provided';
        $leadSubject    = !empty($leadData['enquiry_subject']) ? $leadData['enquiry_subject'] : 'Tour Package Booking';
        $agentName      = trim($leadData['agent_display_name'] ?? (($leadData['agent_first_name'] ?? '') . ' ' . ($leadData['agent_last_name'] ?? '')));
        if (empty($agentName)) {
            $agentName = $leadData['agent_username'] ?? 'Unassigned';
        }

        $siteDomain = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? 'aleya.in'));
        $leadEncodedId = base64_encode((string)$realId);
        $leadViewUrl = "https://{$siteDomain}/dashboard/leads/view/?id={$leadEncodedId}";

        $alerts = [
            'push_sent'    => false,
            'push_count'   => 0,
            'email_sent'   => false,
            'admin_emails' => []
        ];

        // 7. If status is transitioning to CONFIRMED SALE ($newStatus === 1 and was not already 1)
        // Guard to send single alert only on initial conversion
        if ($newStatus === 1 && $oldStatus !== 1) {
            // Load site configuration for notification toggles & SMTP
            $siteCfg = [];
            $cfgSql = "SELECT site_configuration_key, site_configuration_value 
                       FROM {$tblPrefix}site_configuration 
                       WHERE site_configuration_site_id = {$siteId}";
            if ($resCfg = mysqli_query($connection, $cfgSql)) {
                while ($cRow = mysqli_fetch_assoc($resCfg)) {
                    $siteCfg[$cRow['site_configuration_key']] = $cRow['site_configuration_value'];
                }
            }

            // Fetch true Site Name from site table
            $siteName = '';
            if ($siteId > 0) {
                $sRes = mysqli_query($connection, "SELECT site_name FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
                if ($sRes && $sRow = mysqli_fetch_assoc($sRes)) {
                    $siteName = trim($sRow['site_name'] ?? '');
                }
            }
            if (empty($siteName)) {
                $siteName = !empty($siteCfg['company_name']) ? $siteCfg['company_name'] : (defined('SITE_NAME') && SITE_NAME !== 'CRM Platform' ? SITE_NAME : 'Aleya');
            }
            if (!defined('SITE_NAME')) {
                define('SITE_NAME', $siteName);
            }

            // --- A. Push Notification to All Admin Users (FCM) ---
            $enablePush = ($siteCfg['notification_enable_push_notification'] ?? ($siteCfg['enable_push_notification'] ?? '1')) !== '0';
            if ($enablePush) {
                $deviceTokens = [];
                // Query devices of all active admin users on this site or registered under this site
                $devSql = "SELECT DISTINCT d.device_token 
                           FROM {$tblPrefix}device d
                           WHERE d.device_status = 1 
                             AND d.device_token IS NOT NULL 
                             AND d.device_token != ''
                             AND (
                                 d.device_user_id IN (
                                     SELECT sa.site_access_user_id 
                                     FROM {$tblPrefix}site_access sa 
                                     WHERE sa.site_access_site_id = {$siteId} 
                                       AND sa.site_access_type = 'administrator' 
                                       AND sa.site_access_status = 1
                                 )
                                 OR d.device_site_id = {$siteId}
                             )";
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
                    $notifIcon  = defined('SITE_ICON') && !empty(SITE_ICON) ? SITE_ICON : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png";
                    $notifImage = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.png";
                    $notifTitle = "🎉 Lead Confirmed: {$custName}";
                    $notifBody  = "Lead #{$realId} ({$custName}) for {$leadSubject} has been converted to confirmed sale!";

                    $fcmPayload = [
                        "registration_ids" => array_values(array_unique($deviceTokens)),
                        "notification" => [
                            "title"        => $notifTitle,
                            "body"         => $notifBody,
                            "icon"         => $notifIcon,
                            "image"        => $notifImage,
                            "click_action" => $leadViewUrl,
                            "sound"        => "default"
                        ],
                        "data" => [
                            "title"        => $notifTitle,
                            "body"         => $notifBody,
                            "icon"         => $notifIcon,
                            "image"        => $notifImage,
                            "click_action" => $leadViewUrl,
                            "url"          => $leadViewUrl,
                            "lead_id"      => (string)$realId,
                            "status"       => "confirmed"
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
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    $fcmRes = @curl_exec($ch);
                    $fcmHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    @curl_close($ch);

                    if ($fcmHttp === 200) {
                        $alerts['push_sent']  = true;
                        $alerts['push_count'] = count(array_unique($deviceTokens));
                    }
                }
            }

            // --- B. Single Email Alert to Primary Administrator ---
            $enableEmail = ($siteCfg['notification_admin_email_notification']
                ?? ($siteCfg['admin_email_notification']
                    ?? ($siteCfg['notification_email_notification']
                        ?? ($siteCfg['email_notification'] ?? '1')))) !== '0';

            if ($enableEmail) {
                // Determine the single primary administrator email
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
                        $firstCandidate = trim(explode(',', $siteCfg[$cKey])[0]);
                        if (filter_var($firstCandidate, FILTER_VALIDATE_EMAIL)) {
                            $primaryAdminEmail = strtolower($firstCandidate);
                            break;
                        }
                    }
                }

                // If not found in config, fetch the site owner / primary administrator email
                if (empty($primaryAdminEmail)) {
                    $adminUserSql = "SELECT u.user_email 
                                     FROM {$tblPrefix}site_access sa
                                     JOIN {$tblPrefix}user u ON u.user_id = sa.site_access_user_id
                                     WHERE sa.site_access_site_id = {$siteId}
                                       AND sa.site_access_type = 'administrator'
                                       AND sa.site_access_status = 1
                                       AND u.user_email IS NOT NULL 
                                       AND u.user_email != ''
                                     ORDER BY sa.site_access_id ASC
                                     LIMIT 1";
                    if ($resAdmin = mysqli_query($connection, $adminUserSql)) {
                        if ($aRow = mysqli_fetch_assoc($resAdmin)) {
                            $cadm = trim($aRow['user_email']);
                            if (filter_var($cadm, FILTER_VALIDATE_EMAIL)) {
                                $primaryAdminEmail = strtolower($cadm);
                            }
                        }
                    }
                }

                if (!empty($primaryAdminEmail)) {
                    $alerts['admin_emails'] = [$primaryAdminEmail];

                    // Prepare Email HTML Template
                    $customerName    = $custName;
                    $customerMobile  = $custMobile;
                    $customerEmail   = $custEmail;
                    $leadSubject     = $leadSubject;
                    $realLeadId      = $realId;

                    $templatePath = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)) . '/lib/resource/email/lead-confirmed.php';
                    if (file_exists($templatePath)) {
                        require_once $templatePath;
                        $htmlContent = $email_body ?? '';

                        $fromEmail = !empty($siteCfg['email_smtp_email']) ? $siteCfg['email_smtp_email'] : 'donotreply@musafirindiatrip.com';
                        $fromName  = !empty($siteCfg['email_smtp_name']) ? $siteCfg['email_smtp_name'] : $siteName;
                        $emailSubject = "🎉 Lead Confirmed: {$custName} (Lead #{$realId}) - {$siteName}";

                        $mailDispatched = false;

                        // Method 1: PHPMailer via site SMTP credentials (single recipient)
                        $phpMailerDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)) . '/lib/configuration/mail/';
                        if (file_exists($phpMailerDir . 'php_mailer.php')) {
                            require_once $phpMailerDir . 'exception.php';
                            require_once $phpMailerDir . 'php_mailer.php';
                            require_once $phpMailerDir . 'smtp.php';

                            try {
                                $mail = new PHPMailer(true);
                                $mail->isSMTP();
                                $mail->Host = !empty($siteCfg['email_smtp_host']) ? $siteCfg['email_smtp_host'] : 'smtp.zeptomail.in';
                                $mail->SMTPAuth = true;
                                $mail->Username = !empty($siteCfg['email_smtp_username']) ? $siteCfg['email_smtp_username'] : 'emailapikey';
                                $mail->Password = !empty($siteCfg['email_smtp_password'])
                                    ? $siteCfg['email_smtp_password']
                                    : 'PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = !empty($siteCfg['email_smtp_port']) ? (int)$siteCfg['email_smtp_port'] : 587;
                                $mail->setFrom($fromEmail, $fromName);

                                // Single recipient only
                                $mail->addAddress($primaryAdminEmail, $siteName . ' Administrator');

                                $mail->isHTML(true);
                                $mail->CharSet = 'UTF-8';
                                $mail->Subject = $emailSubject;
                                $mail->Body    = $htmlContent;
                                $mail->AltBody = strip_tags($htmlContent);
                                $mail->Timeout = 5;

                                if ($mail->send()) {
                                    $mailDispatched = true;
                                    $alerts['email_sent'] = true;
                                }
                            } catch (\Throwable $e) {
                                // Fallback to direct Zepto API
                            }
                        }

                        // Method 2: Zeptomail API Fallback (single recipient)
                        if (!$mailDispatched) {
                            $zeptoKey = !empty($siteCfg['email_smtp_password'])
                                ? 'Zoho-enczapikey ' . $siteCfg['email_smtp_password']
                                : 'Zoho-enczapikey PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';

                            $emailPayload = [
                                "from" => [
                                    "address" => $fromEmail,
                                    "name"    => $fromName
                                ],
                                "to" => [[
                                    "email_address" => [
                                        "address" => $primaryAdminEmail,
                                        "name"    => $siteName . ' Administrator'
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
                                $mailDispatched = true;
                                $alerts['email_sent'] = true;
                            }
                        }
                    }
                }
            }
        }

        echo json_encode([
            'status'      => 200,
            'status_code' => 200,
            'success'     => true,
            'msg'         => ($newStatus === 1) ? 'Lead converted to Confirmed Sale (Success) successfully!' : 'Lead status updated successfully',
            'message'     => ($newStatus === 1) ? 'Lead converted to Confirmed Sale (Success) successfully!' : 'Lead status updated successfully',
            'lead_id'     => $realId,
            'new_status'  => $newStatus,
            'status_meta' => $statusMeta,
            'alerts'      => $alerts,
            'notification' => [
                'title' => ($newStatus === 1) ? "🎉 Lead Confirmed: {$custName}" : "Status Updated",
                'body'  => ($newStatus === 1) ? "Lead #{$realId} has been confirmed as a Sale." : "Lead #{$realId} status changed to {$statusLabel}.",
                'icon'  => defined('SITE_ICON') && !empty(SITE_ICON) ? SITE_ICON : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                'url'   => $leadViewUrl
            ]
        ], JSON_UNESCAPED_SLASHES);
        exit();
    }
}

http_response_code(400);
echo json_encode([
    'status' => 400,
    'msg'    => 'Invalid lead request parameters',
    'debug'  => [
        'rawLeadId' => $rawLeadId,
        'rawStatus' => $rawStatus,
    ],
], JSON_UNESCAPED_SLASHES);
exit();
