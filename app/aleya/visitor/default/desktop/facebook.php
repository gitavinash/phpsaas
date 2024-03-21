<?php

    if(isset($_REQUEST['hub_challenge'])) {
        echo $_REQUEST['hub_challenge']='';
        exit();
    }

    $subject='Test email from facebook webhook';
    $toAddress = 'avinashsharma114@gmail.com';

    if(count($_REQUEST)!=0) {
        $email_body = $_REQUEST;
    } else {
        $email_body='test body ->'.SITE_DOMAIN;
    }

    // if(mysqli_query($connection, "INSERT INTO isw_enquiry(enquiry_site_id, enquiry_message) VALUES(".SITE_ID.", {$_REQUEST})"));

    require_once SEND_EMAIL;
    echo $_REQUEST['hub_challenge'];

?>