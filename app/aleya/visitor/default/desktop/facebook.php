<?php

    if($_SERVER['REQUEST_METHOD']=='GET') {

        if(isset($_REQUEST['hub_challenge']) == '55254ddcf424ba06dd89df2449bd48fea06c2a26b768d73d6a01fc9d27233e2f') {
            echo $_REQUEST['hub_challenge'];
            exit();
        }
        
    }

    if($_SERVER['REQUEST_METHOD']=='POST') {
        $subject='Test email from facebook webhook';
        $toAddress = 'avinashsharma114@gmail.com';

        if(count($_REQUEST)!=0) {
            $email_body = $_REQUEST;
        } else {
            $email_body='test body ->'.SITE_DOMAIN;
        }

        // if(mysqli_query($connection, "INSERT INTO isw_enquiry(enquiry_site_id, enquiry_message) VALUES(".SITE_ID.", {$_REQUEST})"));

        require_once SEND_EMAIL;
        exit();
    }
    
?>