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

$messageDB = json_encode($enquiryMeta, TRUE);

if ($mobile != '') {


    $cleaned = str_replace(['+', '-'], '', $mobile);
    $mobile = trim(preg_replace('/\s+/', ' ', $cleaned)); // remove extra spaces

    if (is_numeric($mobile)) {

        if ($checkUserEnquiry = mysqli_query($connection, "SELECT enquiry_time FROM " . TABLE_PREFIX . "enquiry WHERE
enquiry_subject='{$subject}' AND enquiry_mobile='{$mobile}' AND enquiry_first_name='{$first_name}' AND enquiry_site_id="
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
                $saveEnquiry == FALSE;
            }
        }

        if ($saveEnquiry == TRUE) {
            // Start record enquiry data into table

            mysqli_query($connection, "START TRANSACTION");

            // Check user is exiting or not

            $findUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' OR
user_mobile='{$mobile}' LIMIT 1");

            $userId = 0;

            if (mysqli_num_rows($findUser) != 0) {
                $userInfo = mysqli_fetch_assoc($findUser);
                $userId = $userInfo['user_id'];
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

            if (SITE_ID == 35) {
                $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id,
enquiry_user_ip, enquiry_url, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_alt_mobile,
enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status,
enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '" . IP . "', '{$currentUrl}', '{$first_name}', '{$last_name}',
'{$email}', '{$mobile}', '{$alternate_mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}',
'{$trafficMedium}', '{$trafficCampaign}', 1, NOW())");
                $leadId = mysqli_insert_id($connection);
            } else {
                $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id,
enquiry_user_ip, enquiry_url, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_alt_mobile,
enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status,
enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '" . IP . "', '{$currentUrl}', '{$first_name}', '{$last_name}',
'{$email}', '{$mobile}', '{$alternate_mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}',
'{$trafficMedium}', '{$trafficCampaign}', 0, NOW())");
                $leadId = mysqli_insert_id($connection);
            }

            if ($addNewEnquiry) {
                mysqli_query($connection, "COMMIT");
            } else {
                mysqli_query($connection, "ROLLBACK");
            }

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

            if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                if (ENABLE_WHATSAPP_BUSINESS_API == 'yes') {
                    require_once WHATSAPP_TEMPLATE_ADMIN . 'new_enquiry.php';
                    require_once WHATSAPP_TEMPLATE_VISITOR . 'enquiry_thank_you.php';
                    exit();
                }
            }
        }

        if (strpos($redirect_url, '/shipping/') !== false) {

            $postUrlValue = "&name=" . urlencode($first_name) . "&email=" . urlencode($email) . "&mobile=" . urlencode($mobile) .
                "&pickup_date=" . urlencode($pickup_date) . "&pickup_time=" . urlencode($pickup_time) . "&address=" .
                urlencode($address);
            header("Location: {$redirect_url}{$postUrlValue}", TRUE, 301);
        } else {

            if (!isset($redirect_url)) {
                $redirect_url = '/dashboard/leads/view/?id=' . base64_encode($leadId);
            }

            header("Location: " . $redirect_url, TRUE, 301);
        }
    } else {
        echo "<h2>Enter valid mobile number</h2>";
    }
} else {
    echo "<h2>Enter mobile number</h2>";
}

exit();