<?php

unset($_REQUEST['type']);
unset($_REQUEST['action']);
unset($_REQUEST['recaptcha_token']);

// Define action parameter as form type

$type = $action;
$isAttachment = false;

if (isset($_FILES['upload'])) {

    require_once API_MODULES . './singleFileUpload.php';
    $isAttachment = true;
    $uploadFileURL = SITE_URL . $fileUrl;
    $enquiryMeta['file_url'] = $uploadFileURL;
}


if (isset($disableEmailNotification)) {
    $doNotSendEmail = true;
    unset($_REQUEST['disableEmailNotification']);
}

// Detect prefix enquiry table fields and remove from array

if (isset($name)) {

    // Remove name varible from array

    unset($_REQUEST['name']);
} elseif (isset($full_name)) {

    // Support full_name from landing page web forms
    $name = $full_name;
    unset($_REQUEST['full_name']);
} else {

    if (isset($first_name)) {

        // Remove first name varible from array

        unset($_REQUEST['first_name']);
    }

    if (isset($last_name)) {

        // Remove last name varible from array

        unset($_REQUEST['last_name']);
    }

    $name = $first_name . ' ' . $last_name;
}

unset($_REQUEST['submit']);
unset($_REQUEST['enquiry']);


$name = explode(' ', $name);
$first_name = $name[0];
unset($name[0]);
$last_name = implode(' ', $name);


if (isset($email)) {

    // Remove email varible from array

    unset($_REQUEST['email']);
}

if (isset($mobile)) {

    // Remove mobile varible from array

    unset($_REQUEST['mobile']);
}

if (isset($phone)) {

    // Remove mobile varible from array

    $mobile = $phone;
    unset($_REQUEST['phone']);
}

if (substr($mobile, 0, 1) === "0") {
    // If it is, remove the first character
    $mobile = substr($mobile, 1);
}

if (substr($mobile, 0, 3) === "+91") {
    // If it is, remove the first character
    $mobile = substr($mobile, 3);
}

if (isset($source)) {

    // Remove enquiry source varible from array

    unset($_REQUEST['source']);
} else {

    $source = '';
}


if (isset($medium)) {

    // Remove enquiry medium varible from array

    unset($_REQUEST['medium']);
} else {

    $medium = '';
}

if (isset($campaign)) {

    // Remove enquiry campaign varible from array

    unset($_REQUEST['campaign']);
} else {

    $campaign = '';
}

if (isset($sender)) {

    // Admin email id or where email will be send

    $toAddress = base64_decode($sender);

    // Remove enquiry campaign varible from array

    unset($_REQUEST['sender']);
}

if (isset($cc)) {

    // Admin email id or where email will be send

    $ccAddress = base64_decode($cc);

    // Remove enquiry campaign varible from array

    unset($_REQUEST['cc']);
}

if (isset($bcc)) {

    // Admin email id or where email will be send

    $bccAddress = base64_decode($bcc);

    // Remove enquiry campaign varible from array

    unset($_REQUEST['bcc']);
}

unset($_REQUEST['request_id']);

if (sizeof($_REQUEST) != 0) {

    foreach ($_REQUEST as $key => $value) {

        $enquiryMeta[$key] = $value;
    }
}

$redirect_url = $redirect_url;
$subject = $subject;

unset($enquiryMeta['redirect_url']);
unset($enquiryMeta['subject']);

$messageForm = '';

foreach ($enquiryMeta as $key => $value) {
    $messageForm .= "<tr>
    <td style='padding: 8px; text-align: left; border: 1px solid #ddd;'><strong>" . ucwords(str_replace('_', ' ', $key))
        . ":</strong></td>
    <td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>$value</td>
</tr>";
}

$messageDB = json_encode($enquiryMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($mobile != '') {


    $cleaned = str_replace(['+', '-'], '', $mobile);
    $mobile = trim(preg_replace('/\s+/', ' ', $cleaned)); // remove extra spaces

    if (is_numeric($mobile)) {

        $escaped_subject = mysqli_real_escape_string($connection, (string)$subject);
        $escaped_first_name = mysqli_real_escape_string($connection, (string)$first_name);

        if ($checkUserEnquiry = mysqli_query($connection, "SELECT enquiry_time FROM " . TABLE_PREFIX . "enquiry WHERE
enquiry_subject='{$escaped_subject}' AND enquiry_mobile='{$mobile}' AND enquiry_first_name='{$escaped_first_name}' AND enquiry_site_id="
            . SITE_ID . " ORDER BY enquiry_time DESC LIMIT 0, 1")) {
            if (mysqli_num_rows($checkUserEnquiry) != 0) {
                extract(mysqli_fetch_assoc($checkUserEnquiry));
                $time_difference = time() - strtotime($enquiry_time);
                $time_difference_hours = $time_difference / (60 * 60);
                if ($time_difference_hours > 2) {
                    $freshEnquiry = true;
                } else {
                    $freshEnquiry = false;
                }
            } else {
                $freshEnquiry = true;
            }
        }

        $saveEnquiry = TRUE;

        if (SITE_APP_ID == 20) {
            if ($freshEnquiry != TRUE) {
                $saveEnquiry = FALSE;
            }
        }

        if ($saveEnquiry == TRUE) {
            // Start record enquiry data into table

            mysqli_query($connection, "START TRANSACTION");

            // Check user is exiting or not

            $escaped_email = mysqli_real_escape_string($connection, (string)$email);
            $findUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$escaped_email}' OR
user_mobile='{$mobile}' LIMIT 1");

            $userId = 0;

            if (mysqli_num_rows($findUser) != 0) {
                $userInfo = mysqli_fetch_assoc($findUser);
                $userId = (int)$userInfo['user_id'];
            }

            // track traffic resource

            $trafficSource = ((isset($_GET['utm_source']) ? $_GET['utm_source'] : ((isset($_GET['fbclid'])) ? 'facebook' : ((isset($_GET['gad_source'])) ? 'Google Adwords' : (isset($_GET['gclid']) ? 'Google Adwords' : ((isset($_GET['gad_source']) ? 'Google Adwords' : ((isset($_GET['wbraid'])) ? 'Google Adwords' : $trafficDomain =
                parse_url($_SERVER['HTTP_REFERER'])))))))));
            $trafficSource = mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host'] !=
                SITE_DOMAIN) ? $trafficDomain['host'] : '') : $trafficSource));

            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .
                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            // Append query string if available
            if (!empty($_SERVER['QUERY_STRING'])) {
                $currentUrl .= '?' . $_SERVER['QUERY_STRING'];
            }

            $alternate_mobile = ((isset($alternate_mobile)) ? $alternate_mobile : '');

            $trafficMedium = mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource == 'facebook') ? 'social' : ''))));
            $trafficCampaign = mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] :
                '')));

            $escaped_last_name = mysqli_real_escape_string($connection, (string)$last_name);
            $escaped_alt_mobile = mysqli_real_escape_string($connection, (string)$alternate_mobile);
            $escaped_messageDB = mysqli_real_escape_string($connection, (string)$messageDB);
            $escaped_currentUrl = mysqli_real_escape_string($connection, (string)$currentUrl);
            $escaped_ip = mysqli_real_escape_string($connection, (string)IP);

            $enquiryStatus = (SITE_ID == 35) ? 1 : 0;

            $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id,
enquiry_user_ip, enquiry_url, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_alt_mobile,
enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status,
enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '{$escaped_ip}', '{$escaped_currentUrl}', '{$escaped_first_name}', '{$escaped_last_name}',
'{$escaped_email}', '{$mobile}', '{$escaped_alt_mobile}', '{$escaped_messageDB}', '{$escaped_subject}', 'enquiry', '{$trafficSource}',
'{$trafficMedium}', '{$trafficCampaign}', {$enquiryStatus}, NOW())");
            $leadId = mysqli_insert_id($connection);

            if (!empty($form_uid)) {
                $escFormUid = mysqli_real_escape_string($connection, (string)$form_uid);
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "web_form SET form_submissions_count = form_submissions_count + 1 WHERE form_uid = '{$escFormUid}'");

                // Check custom email notification config and redirect URL for this web form
                $formMailRes = mysqli_query($connection, "SELECT form_email_to, form_email_cc, form_email_bcc, form_redirect_url FROM " . TABLE_PREFIX . "web_form WHERE form_uid = '{$escFormUid}' LIMIT 1");
                if ($formMailRes && $fMailRow = mysqli_fetch_assoc($formMailRes)) {
                    if (!empty($fMailRow['form_email_to'])) {
                        $toAddress = trim($fMailRow['form_email_to']);
                        $sender = base64_encode($toAddress);
                    }
                    if (!empty($fMailRow['form_email_cc'])) {
                        $ccAddress = trim($fMailRow['form_email_cc']);
                        $cc = base64_encode($ccAddress);
                    }
                    if (!empty($fMailRow['form_email_bcc'])) {
                        $bccAddress = trim($fMailRow['form_email_bcc']);
                        $bcc = base64_encode($bccAddress);
                    }
                    if (empty($redirect_url) && !empty($fMailRow['form_redirect_url'])) {
                        $redirect_url = trim($fMailRow['form_redirect_url']);
                    }
                }
            }

            if ($addNewEnquiry) {
                mysqli_query($connection, "COMMIT");
            } else {
                mysqli_query($connection, "ROLLBACK");
            }

            if (!isset($sender) && empty($toAddress)) {
                $siteMailQ = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = " . (int)SITE_ID . " AND site_configuration_key = 'enquiry_email' AND site_configuration_status = 1 LIMIT 1");
                if ($siteMailQ && $sm = mysqli_fetch_assoc($siteMailQ)) {
                    if (!empty($sm['site_configuration_value'])) {
                        $toAddress = trim($sm['site_configuration_value']);
                        $sender = base64_encode($toAddress);
                    }
                } elseif (defined('SITE_EMAIL') && filter_var(SITE_EMAIL, FILTER_VALIDATE_EMAIL)) {
                    $toAddress = SITE_EMAIL;
                    $sender = base64_encode($toAddress);
                }
            }

            $cleanEnquirySubject = $subject;

            if (isset($sender)) {
                if (!isset($doNotSendEmail)) {

                    if (file_exists(APP_EMAIL_TEMPLATE . 'default-enquiry.php')) {
                        require_once APP_EMAIL_TEMPLATE . 'default-enquiry.php';
                    } else {
                        require_once EMAIL_TEMPLATE . 'default-enquiry.php';
                    }

                    if (SITE_APP == 'aleya') {
                        if (isset($email)) {
                            unset($ccAddress);
                            unset($bccAddress);
                            $toAddress = $email;
                            if (file_exists(APP_EMAIL_TEMPLATE . 'enquiry-thank-you.php')) {
                                require_once APP_EMAIL_TEMPLATE . 'enquiry-thank-you.php';
                            }
                        }
                    }
                }
            }

            $isWhatsAppEnabled = false;
            if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                $cVal = strtolower(trim((string)ENABLE_WHATSAPP_BUSINESS_API));
                if (in_array($cVal, ['yes', '1', 'true', 'on'], true)) {
                    $isWhatsAppEnabled = true;
                }
            }
            if (!$isWhatsAppEnabled && defined('NOTIFICATION_ENABLE_WHATSAPP_BUSINESS_API')) {
                $cVal = strtolower(trim((string)NOTIFICATION_ENABLE_WHATSAPP_BUSINESS_API));
                if (in_array($cVal, ['yes', '1', 'true', 'on'], true)) {
                    $isWhatsAppEnabled = true;
                }
            }

            if (!$isWhatsAppEnabled && isset($connection)) {
                $targetSiteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($site_id) ? (int)$site_id : 0);
                $ownerId = isset($site_owner_id) ? (int)$site_owner_id : 0;
                $parentSiteId = defined('APP_SITE_ID') ? (int)APP_SITE_ID : 0;
                $siteIds = array_unique(array_filter([$targetSiteId, $ownerId, $parentSiteId]));
                if (!empty($siteIds)) {
                    $siteIdList = implode(',', $siteIds);
                    $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
                    $cfgQ = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id IN ({$siteIdList}) AND site_configuration_key IN ('notification_enable_whatsapp_business_api', 'enable_whatsapp_business_api', 'notification_enable_whatsapp', 'enable_whatsapp') AND site_configuration_status = 1");
                    if ($cfgQ) {
                        while ($cfgR = mysqli_fetch_assoc($cfgQ)) {
                            $v = strtolower(trim((string)$cfgR['site_configuration_value']));
                            if (in_array($v, ['yes', '1', 'true', 'on'], true)) {
                                $isWhatsAppEnabled = true;
                                break;
                            }
                        }
                    }
                }
            }

            if ($isWhatsAppEnabled) {
                $subject = $cleanEnquirySubject;
                if (defined('WHATSAPP_TEMPLATE_ADMIN') && file_exists(WHATSAPP_TEMPLATE_ADMIN . 'new_enquiry.php')) {
                    require WHATSAPP_TEMPLATE_ADMIN . 'new_enquiry.php';
                }
                // Visitor thank-you WhatsApp message disabled: handled exclusively via CRM Workflow Automation
                // require_once WHATSAPP_TEMPLATE_VISITOR . 'enquiry_thank_you.php';
            }
        }

        $isJsonRequest = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json')
        );

        if (!empty($redirect_url) && strpos($redirect_url, '/shipping/') !== false) {

            $postUrlValue = "&name=" . urlencode($first_name) . "&email=" . urlencode($email) . "&mobile=" . urlencode($mobile) .
                "&pickup_date=" . urlencode($pickup_date) . "&pickup_time=" . urlencode($pickup_time) . "&address=" .
                urlencode($address);
            $targetUrl = "{$redirect_url}{$postUrlValue}";
            if ($isJsonRequest) {
                echo json_encode([
                    'status' => 'success',
                    'msg' => 'Enquiry submitted successfully',
                    'leadId' => $leadId ?? 0,
                    'redirect_url' => $targetUrl
                ]);
                exit();
            }
            header("Location: {$targetUrl}", TRUE, 303);
            exit();
        } else {
            $isWebForm = !empty($form_uid);
            $hasCustomRedirect = !empty($redirect_url) && trim($redirect_url) !== '';

            if ($isWebForm) {
                // Public Web Form: 301 Redirect ONLY if Thank You URL is defined by user
                if ($hasCustomRedirect) {
                    $targetUrl = trim($redirect_url);
                    if ($isJsonRequest) {
                        echo json_encode([
                            'status' => 'success',
                            'msg' => 'Enquiry submitted successfully',
                            'leadId' => $leadId ?? 0,
                            'redirect_url' => $targetUrl
                        ]);
                        exit();
                    }
                    header("Location: " . $targetUrl, TRUE, 301);
                    exit();
                }

                // If user has NOT defined the Thank You URL: Show thank you message (no dashboard/login redirect)
                if ($isJsonRequest) {
                    echo json_encode([
                        'status' => 'success',
                        'msg' => 'Enquiry submitted successfully',
                        'leadId' => $leadId ?? 0,
                        'redirect_url' => null
                    ]);
                    exit();
                }

                // Direct non-AJAX browser view fallback
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Thank You</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #1e293b; text-align: center; }
                        .card { padding: 40px 32px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; max-width: 440px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06); }
                        .icon { width: 56px; height: 56px; margin: 0 auto 16px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; }
                        h2 { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #0f172a; }
                        p { font-size: 14.5px; margin: 0; line-height: 1.5; color: #64748b; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <div class="icon">&#10003;</div>
                        <h2>Request Received!</h2>
                        <p>Thank you! We have received your inquiry and will contact you shortly.</p>
                    </div>
                </body>
                </html>
                <?php
                exit();
            } else {
                // Internal / Admin Dashboard Enquiry creation
                if (!$hasCustomRedirect) {
                    $redirect_url = '/dashboard/leads/view/?id=' . base64_encode($leadId);
                }

                if ($isJsonRequest) {
                    echo json_encode([
                        'status' => 'success',
                        'msg' => 'Enquiry submitted successfully',
                        'leadId' => $leadId ?? 0,
                        'redirect_url' => $redirect_url
                    ]);
                    exit();
                }

                header("Location: " . $redirect_url, TRUE, 303);
                exit();
            }
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) || (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json')) {
            echo json_encode(['status' => 'error', 'msg' => 'Enter valid mobile number']);
            exit();
        }
        echo "<h2>Enter valid mobile number</h2>";
    }
} else {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) || (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json')) {
        echo json_encode(['status' => 'error', 'msg' => 'Enter mobile number']);
        exit();
    }
    echo "<h2>Enter mobile number</h2>";
}

exit();