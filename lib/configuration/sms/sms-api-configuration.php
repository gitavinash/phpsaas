<?php

    // require_once 'sms-api-configuration.php';

    if(SMS_DEFAULT==1) {

        define('SMS_AUTH_KEY', '');
        define('SMS_SENDER', '');
        define('SMS_ROUTE', '');

    }

    switch(SMS_GATEWAY) {

        case 'kap' :
            $smsURL="http://103.16.101.52:8080/sendsms/bulksms?username=".SMS_USERNAME."&password=".SMS_PASSWORD."&type=".SMS_TYPE."&dlr=".SMS_DELIVERY_REPORT."&destination=91{$mobile}&source=".SMS_SENDER."&message={$message}&tempid";
            break;

        case 'rock2connect' :
            $smsURL="http://login.rock2connect.com/MOBILE_APPS_API/sms_api.php?type=".SMS_TYPE."&user=".SMS_USERNAME."&pass=".SMS_PASSWORD."&sender=".SMS_SENDER."&to_mobileno={$mobile}&sms_text={$message}";
            break;

        case 'textmysms' :
            $smsURL="http://sms.textmysms.com/app/smsapi/index.php?key=".SMS_KEY."&campaign=".SMS_CAMPAIGN."&routeid=".SMS_ROUTEID."&type=".SMS_TYPE."&contacts={$mobile}&senderid=".SMS_SENDER."&msg={$message}&template_id={$dlt_sms_temp_id}";
            break;

        default :
            $smsURL="https://api.msg91.com/api/sendhttp.php?authkey=".SMS_AUTH_KEY."&mobiles={$mobile}&country=91&message={$message}&sender=".SMS_SENDER."&route=".SMS_ROUTE."&DLT_TE_ID={$sms_dlt_sms_id}";
            break;

    }