<?php

/**
 * V3 Executive Lead Assign API Endpoint
 * Handles Lead Assignment, Status Transition, Note Log, Push Notification, and Email Alerts
 * Path: /api/v3/leads/assign.php
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once LOGIN_CHECK;

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')  ? (int) USER_ID : 0;
$siteDomain = SITE_URL;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Blumox Technologies';

// Flexible parameter extraction
$rawLeadId = '';
if (isset($_POST['lead_id']))       $rawLeadId = $_POST['lead_id'];
elseif (isset($_POST['leadId']))    $rawLeadId = $_POST['leadId'];
elseif (isset($_POST['id']))        $rawLeadId = $_POST['id'];
elseif (isset($_GET['lead_id']))    $rawLeadId = $_GET['lead_id'];
elseif (isset($_GET['leadId']))     $rawLeadId = $_GET['leadId'];
elseif (isset($_REQUEST['lead_id'])) $rawLeadId = $_REQUEST['lead_id'];
elseif (isset($_REQUEST['leadId']))  $rawLeadId = $_REQUEST['leadId'];
elseif (isset($leadId))              $rawLeadId = $leadId;

$rawAgentId = null;
if (isset($_POST['agent_id']))       $rawAgentId = $_POST['agent_id'];
elseif (isset($_POST['agentId']))    $rawAgentId = $_POST['agentId'];
elseif (isset($_POST['user_id']))    $rawAgentId = $_POST['user_id'];
elseif (isset($_POST['userId']))     $rawAgentId = $_POST['userId'];
elseif (isset($_GET['agent_id']))    $rawAgentId = $_GET['agent_id'];
elseif (isset($_GET['agentId']))     $rawAgentId = $_GET['agentId'];
elseif (isset($_REQUEST['agent_id'])) $rawAgentId = $_REQUEST['agent_id'];
elseif (isset($_REQUEST['agentId']))  $rawAgentId = $_REQUEST['agentId'];
elseif (isset($agentId))              $rawAgentId = $agentId;

if ($rawLeadId !== '' && $rawLeadId !== null && $rawAgentId !== null) {
    $decodedLead = base64_decode(trim($rawLeadId), true);
    $realLeadId  = ($decodedLead !== false && is_numeric($decodedLead)) ? intval($decodedLead) : intval($rawLeadId);

    $cleanAgentId = trim((string)$rawAgentId);
    $decodedAgent = base64_decode($cleanAgentId, true);
    if ($decodedAgent !== false && is_numeric($decodedAgent)) {
        $realAgentId = intval($decodedAgent);
    } elseif ($decodedAgent !== false && strpos($decodedAgent, '_') !== false) {
        $parts = explode('_', $decodedAgent);
        $realAgentId = intval($parts[0]);
    } else {
        $realAgentId = intval($cleanAgentId);
    }

    if ($realLeadId > 0 && isset($connection) && $connection) {
        $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";

        // Check if lead exists and fetch lead details
        $leadData = [];
        $enquirySubject = 'Lead';
        $leadSql = "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$realLeadId}{$siteClause} LIMIT 1";
        if ($resLead = mysqli_query($connection, $leadSql)) {
            if ($lRow = mysqli_fetch_assoc($resLead)) {
                $leadData = $lRow;
                if ($siteId <= 0 && !empty($lRow['enquiry_site_id'])) {
                    $siteId = (int)$lRow['enquiry_site_id'];
                }
                $enquirySubject = !empty($lRow['enquiry_subject']) ? $lRow['enquiry_subject'] : 'Customer Enquiry';
            }
        }

        if (empty($leadData)) {
            // Fallback check without site constraint
            $leadSql2 = "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$realLeadId} LIMIT 1";
            if ($resLead2 = mysqli_query($connection, $leadSql2)) {
                if ($lRow2 = mysqli_fetch_assoc($resLead2)) {
                    $leadData = $lRow2;
                    $siteId = (int)$lRow2['enquiry_site_id'];
                    $enquirySubject = !empty($lRow2['enquiry_subject']) ? $lRow2['enquiry_subject'] : 'Customer Enquiry';
                }
            }
        }

        if (empty($leadData)) {
            echo json_encode(['status' => 404, 'status_code' => 404, 'msg' => 'Lead not found in this workspace']);
            exit();
        }

        if ($realAgentId > 0) {
            // Check if agent/user exists
            $agentName = '';
            $agentEmail = '';
            $userCheckSql = "SELECT profile_display_name, profile_first_name, profile_last_name, user_email 
                             FROM {$tblPrefix}user 
                             LEFT JOIN {$tblPrefix}profile ON profile_user_id = user_id 
                             WHERE user_id = {$realAgentId} LIMIT 1";
            if ($resUser = mysqli_query($connection, $userCheckSql)) {
                if ($uRow = mysqli_fetch_assoc($resUser)) {
                    $agentName = trim($uRow['profile_display_name'] ?? '');
                    if (empty($agentName)) {
                        $agentName = trim(($uRow['profile_first_name'] ?? '') . ' ' . ($uRow['profile_last_name'] ?? ''));
                    }
                    if (empty($agentName)) {
                        $agentName = $uRow['user_email'];
                    }
                    $agentEmail = trim($uRow['user_email'] ?? '');
                }
            }

            if (empty($agentName)) {
                $agentName = "Agent #{$realAgentId}";
            }

            // Update lead agent and set status to 2 (Assigned) if it was unassigned (status 0)
            $currentStatus = isset($leadData['enquiry_status'])
                ? (int)$leadData['enquiry_status']
                : 0;

            // Flow: New Lead (0) -> Assigned (2) -> Follow Up (3) -> Itinerary Sent (9) -> Payment Requested (6) -> Confirmed (1)
            // When an unassigned new lead (0) is assigned to a calling agent, it moves into Stage 2: Assigned
            $newStatus = ($currentStatus === 0) ? 2 : $currentStatus;

            $sql = "UPDATE {$tblPrefix}enquiry
                    SET enquiry_agent = '{$realAgentId}', enquiry_status = {$newStatus}
                    WHERE enquiry_id = {$realLeadId}";

            if (mysqli_query($connection, $sql)) {
                // Record in enquiry notes
                $noteText = ($newStatus === 2 && $currentStatus === 0)
                    ? "Lead assigned to {$agentName} (Assigned)"
                    : "Lead assigned to {$agentName}";
                $noteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value)
                            VALUES ({$realLeadId}, '{$currentUserId}', 'assign', '" . mysqli_real_escape_string($connection, $noteText) . "')";
                @mysqli_query($connection, $noteSql);

                $custName = trim(($leadData['enquiry_first_name'] ?? '') . ' ' . ($leadData['enquiry_last_name'] ?? ''));
                if (empty($custName)) {
                    $custName = $leadData['enquiry_name'] ?? 'Customer Enquiry';
                }
                $custMobile = $leadData['enquiry_mobile'] ?? '';
                $custEmail = $leadData['enquiry_email'] ?? '';
                $leadEncodedId = base64_encode((string)$realLeadId);
                $leadViewUrl = "{$siteDomain}/dashboard/leads/view/?id={$leadEncodedId}";

                // Load site configuration for SMTP
                $siteSmtp = [];
                $cfgSql = "SELECT site_configuration_key, site_configuration_value 
                           FROM {$tblPrefix}site_configuration 
                           WHERE site_configuration_site_id = {$siteId}";
                if ($resCfg = mysqli_query($connection, $cfgSql)) {
                    while ($cRow = mysqli_fetch_assoc($resCfg)) {
                        $siteSmtp[$cRow['site_configuration_key']] = $cRow['site_configuration_value'];
                    }
                }

                // 1. Send Push Notification to Agent's active devices (FCM)
                $deviceTokens = [];
                $agentUserIdEsc = mysqli_real_escape_string($connection, (string)$realAgentId);
                $devSql = "SELECT DISTINCT device_token FROM {$tblPrefix}device 
                           WHERE (device_user_id = '{$agentUserIdEsc}' OR device_user_id = " . (int)$realAgentId . ") 
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
                    $notifIcon = defined('SITE_ICON') && !empty(SITE_ICON) ? SITE_ICON : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png";
                    $notifImage = "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.png";
                    $notifTitle = "New Lead Assigned: {$custName}";
                    $notifBody  = "Hi {$agentName}, lead #{$realLeadId} ({$enquirySubject}) has been assigned to you.";

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
                            "lead_id" => (string)$realLeadId
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
                    @curl_exec($ch);
                    @curl_close($ch);
                }

                // 2. Send HTML Email Notification to Assigned Agent
                $emailSent = false;
                if (!empty($agentEmail) && filter_var($agentEmail, FILTER_VALIDATE_EMAIL)) {
                    $customerName = $custName;
                    $customerMobile = $custMobile;
                    $customerEmail = $custEmail;
                    $leadSubject = $enquirySubject;

                    $templatePath = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)) . '/lib/resource/email/lead-assignment.php';
                    if (file_exists($templatePath)) {
                        require_once $templatePath;
                        $htmlContent = $email_body ?? '';

                        $fromEmail = !empty($siteSmtp['email_smtp_email']) ? $siteSmtp['email_smtp_email'] : 'donotreply@graciousworld.com';
                        $fromName  = !empty($siteSmtp['email_smtp_name']) ? $siteSmtp['email_smtp_name'] : $siteName;
                        // $emailSubject = "New Lead Assigned: {$customerName} ({$leadSubject}) - {$siteName}";
                        $emailSubject = "New Lead Assigned - {$siteName}";

                        // Method A: Dispatch via PHPMailer using site SMTP credentials
                        $phpMailerDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)) . '/lib/configuration/mail/';
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
                                // Fallback to direct Zepto API if PHPMailer had a network issue
                            }
                        }

                        // Method B: Direct Zeptomail API fallback if not already sent
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
                                $emailSent = true;
                            }
                        }
                    }
                }

                echo json_encode([
                    'status'      => 200,
                    'status_code' => 200,
                    'success'     => true,
                    'msg'         => 'Lead assigned to ' . $agentName . ' successfully',
                    'message'     => 'Lead assigned to ' . $agentName . ' successfully',
                    'lead_id'     => $realLeadId,
                    'agent_id'    => $realAgentId,
                    'agent'       => $agentName,
                    'new_status'  => $newStatus,
                    'status_id'   => $newStatus,
                    'email_sent'  => $emailSent,
                    'notification' => [
                        'title' => "Lead Assigned: {$custName}",
                        'body'  => "Lead #{$realLeadId} ({$enquirySubject}) has been assigned to {$agentName}.",
                        'icon'  => "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                        'url'   => $leadViewUrl
                    ]
                ]);
                exit();
            }
        } else {
            // Unassign lead (agent = 0)
            $currentStatus = isset($leadData['enquiry_status']) ? (int)$leadData['enquiry_status'] : 0;
            $unassignStatus = ($currentStatus === 2) ? 0 : $currentStatus;
            $sql = "UPDATE {$tblPrefix}enquiry
                    SET enquiry_agent = '0', enquiry_status = {$unassignStatus}
                    WHERE enquiry_id = {$realLeadId}";

            if (mysqli_query($connection, $sql)) {
                $noteText = "Lead unassigned";
                $noteSql = "INSERT INTO {$tblPrefix}enquiry_notes (enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value)
                            VALUES ({$realLeadId}, '{$currentUserId}', 'assign', '" . mysqli_real_escape_string($connection, $noteText) . "')";
                @mysqli_query($connection, $noteSql);

                echo json_encode([
                    'status'      => 200,
                    'status_code' => 200,
                    'success'     => true,
                    'msg'         => 'Lead unassigned successfully',
                    'message'     => 'Lead unassigned successfully',
                    'lead_id'     => $realLeadId,
                    'agent_id'    => 0,
                    'agent'       => 'Unassigned',
                    'new_status'  => $unassignStatus,
                    'status_id'   => $unassignStatus,
                ]);
                exit();
            }
        }

        echo json_encode(['status' => 500, 'msg' => 'Failed to update agent: ' . mysqli_error($connection)]);
        exit();
    }
}

echo json_encode([
    'status' => 400,
    'msg' => 'Invalid lead assignment parameters',
    'debug' => [
        'rawLeadId'  => $rawLeadId,
        'rawAgentId' => $rawAgentId,
    ]
]);
