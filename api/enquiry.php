<?php

unset($_REQUEST['type']);
unset($_REQUEST['action']);

// Define action parameter as form type

$type = $action;

$isAttachment = false;

if (isset($_FILES['upload'])) {

    require_once API_MODULES . './singleFileUpload.php';
    $isAttachment = true;
    $uploadFileURL = SITE_URL . $fileUrl;
    $enquiryMeta['file_url'] = $uploadFileURL;
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

    // Remove enquiry campaign varible from array

    unset($_REQUEST['sender']);

    // Admin email id or where email will be send

    $toAddress = base64_decode($sender);
} else {

    echo $msg = 'Sender email is not found';
    exit();
}

if (isset($cc)) {

    // Remove enquiry campaign varible from array

    unset($_REQUEST['cc']);

    // Admin email id or where email will be send

    $ccAddress = base64_decode($cc);
}

if (isset($bcc)) {

    // Remove enquiry campaign varible from array

    unset($_REQUEST['bcc']);

    // Admin email id or where email will be send

    $bccAddress = base64_decode($bcc);
}

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
    $messageForm .= "<tr><td>{$key}</td><td>$value</td></tr>";
}

$messageDB = json_encode($enquiryMeta, TRUE);

// Start record enquiry data into table

mysqli_query($connection, "START TRANSACTION");

// Check user is exiting or not

$findUser = mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

$userId=0;

if(mysqli_num_rows($findUser)!=0)  {
    $userInfo = mysqli_fetch_assoc($findUser);
    $userId = $userInfo['user_id'];
}

// track traffic resource

$trafficSource=((isset($_GET['utm_source']) ? $_GET['utm_source'] : ((isset($_GET['fbclid'])) ? 'facebook' : ((isset($_GET['gclid'])) ? 'Google Adwords' : $trafficDomain=parse_url($_SERVER['HTTP_REFERER']))) ));
$trafficSource=mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host']!=SITE_DOMAIN) ? $trafficDomain['host'] : '' ) : $trafficSource ));

$trafficMedium=mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource=='facebook') ? 'social' : '' ) )));
$trafficCampaign=mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '' )));

if(SITE_ID==35)  {
    $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status) VALUES(" . SITE_ID . ", '{$userId}', '{$name}', '{$email}', '{$mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 1)");
} else {
    $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status) VALUES(" . SITE_ID . ", '{$userId}', '{$name}', '{$email}', '{$mobile}', '{$messageDB}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 0)");
}

$enquiryID = mysqli_insert_id($connection);

if ($addNewEnquiry) {
    mysqli_query($connection, "COMMIT");
} else {
    mysqli_query($connection, "ROLLBACK");
}

$email_body = "
    <h2>{$subject}</h2>
    <table>
        <tr>
            <td>Name: </td>
            <td>{$name}</td>
        </tr>
        <tr>
            <td>Email: </td>
            <td>{$email}</td>
        </tr>
        <tr>
            <td>Mobile: </td>
            <td>{$mobile}</td>
        </tr>
        {$messageForm}
    </table>
    ";

require SEND_EMAIL;

if (strpos($redirect_url, '/shipping/') !== false) {
    $postUrlValue = "&name=" . urlencode($name) . "&email=" . urlencode($email) . "&mobile=" . urlencode($mobile) . "&pickup_date=" . urlencode($pickup_date) . "&pickup_time=" . urlencode($pickup_time) . "&address=" . urlencode($address);
    header("Location: {$redirect_url}{$postUrlValue}", TRUE, 301);
} else {
    header("Location: " . $redirect_url, TRUE, 301);
}

exit();