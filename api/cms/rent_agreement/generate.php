<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';

    if ($name == '') {
        $msg = 'Kindly enter your full name';
    } else {
        $name = explode(' ', $name);
        $first_name = $name[0];
        unset($name[0]);
        $last_name = implode(' ', $name);
    }

    if ($email == '') {
        $msg = 'Kindly enter your email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Kindly enter valid email address';
    }

    if ($mobile == '') {
        $msg = 'Kindly enter your mobile number';
    } else if (!preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        $msg = "Kindly enter a valid mobile number";
    }

    $messageDB = '';
    $subject = 'New request for Rent Agreement';

    if ($msg == "") {

        // Start record enquiry data into table

        mysqli_query($connection, "START TRANSACTION");

        // Check user is exiting or not

        $findUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

        $userId = 0;

        if (mysqli_num_rows($findUser) != 0) {
            $userInfo = mysqli_fetch_assoc($findUser);
            $userId = $userInfo['user_id'];
        }

        // track traffic resource

        $trafficSource = ((isset($_GET['utm_source']) ? $_GET['utm_source'] : ((isset($_GET['fbclid'])) ? 'facebook' : ((isset($_GET['gad_source'])) ? 'Google Adwords' : (isset($_GET['gclid']) ? 'Google Adwords' : ((isset($_GET['gad_source']) ? 'Google Adwords' : ((isset($_GET['wbraid'])) ? 'Google Adwords' : $trafficDomain = parse_url($_SERVER['HTTP_REFERER'])))))))));
        $trafficSource = mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host'] != SITE_DOMAIN) ? $trafficDomain['host'] : '') : $trafficSource));

        $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Append query string if available
        if (!empty($_SERVER['QUERY_STRING'])) {
            $currentUrl .= '?' . $_SERVER['QUERY_STRING'];
        }


        $trafficMedium = mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource == 'facebook') ? 'social' : ''))));
        $trafficCampaign = mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '')));

        if (SITE_ID == 35) {
            $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_user_ip, enquiry_url, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status, enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '" . IP . "', '{$currentUrl}', '{$first_name}', '{$last_name}', '{$email}', '{$mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 1, NOW())");
        } else {
            $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_user_ip, enquiry_url, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status, enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '" . IP . "', '{$currentUrl}', '{$first_name}', '{$last_name}', '{$email}', '{$mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 0, NOW())");
            $leadId = mysqli_insert_id($connection);
        }

        $enquiryID = mysqli_insert_id($connection);


        $email_body = "
    <h2>{$subject}</h2>
    <table>
        <tr>
            <td>Name: </td>
            <td>{$first_name} {$last_name}</td>
        </tr>
        <tr>
            <td>Email: </td>
            <td>{$email}</td>
        </tr>
        <tr>
            <td>Mobile: </td>
            <td>{$mobile}</td>
        </tr>
    </table>
    ";

        if (isset($sender)) {
            if (!isset($doNotSendEmail)) {
                require SEND_EMAIL;
                require_once APP_EMAIL_TEMPLATE . 'enquiry-thank-you.php';
            }
        }

        if ($addNewEnquiry) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Process to next step';
            mysqli_query($connection, "COMMIT");
        } else {
            $msg = 'Something went wrong while trying to store information';
            mysqli_query($connection, "ROLLBACK");
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'enquiryId' => ((isset($enquiryID)) ? $enquiryID : ''),
    );

    http_response_code($httpStatus);
    echo json_encode($result, TRUE);
}
