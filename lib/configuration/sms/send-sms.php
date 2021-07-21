<?php

    // Send sms if sms service is enable

    if(defined('SMS_ENABLE')) {

        if(SMS_ENABLE==1) {

            require 'sms-api-configuration.php';
            $smsStatus=file_get_contents($smsURL);
    
        }
        
    }