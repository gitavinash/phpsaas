<?php

    if(isset($sms_enable)) {

        if($sms_enable==1) {

            require_once 'sms-api-configuration.php';
            $smsStatus=file_get_contents($smsURL);
    
        }

    }