<?php

    if(isset($_REQUEST['hub_challenge'])) {
        echo $_REQUEST['hub_challenge'];
        exit();
    }

    $toAddress = 'avinashsharma114@gmail.com';
    $email_body = $_REQUEST;
    require_once SEND_EMAIL;

    echo 'im here';

?>