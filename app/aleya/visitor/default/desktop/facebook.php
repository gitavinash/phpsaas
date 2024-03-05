<?php

    if(isset($_REQUEST['hub_challenge'])) {
        echo $_REQUEST['hub_challenge'];
        exit();
    }

    $subject='Test email from facebook webhook';
    $toAddress = 'avinashsharma114@gmail.com';

    if(count($_REQUEST)!=0) {
        $email_body = $_REQUEST;
    } else {
        $email_body='test body';
    }

    require_once SEND_EMAIL;

?>