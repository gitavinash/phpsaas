<?php

    require_once 'sms-api-configuration.php';

    switch($sms_gateway) {

        case 'kap' :
            $smsURL="http://103.16.101.52:8080/sendsms/bulksms?username={$sms_username}&password={$sms_password}&type={$sms_type}&dlr={$sms_delivery_report}&destination=91{$mobile}&source={$sms_sender}&message={$message}";
            break;

        case 'rock2connect' :
            $smsURL="http://login.rock2connect.com/MOBILE_APPS_API/sms_api.php?type={$sms_type}&user={$sms_username}&pass={$sms_password}&sender={$sms_sender}&to_mobileno={$mobile}&sms_text={$message}";
            break;

        default :
            $smsURL="https://api.msg91.com/api/sendhttp.php?authkey={$sms_auth_key}&mobiles={$mobile}&country=91&message={$message}&sender={$sms_sender}&route={$sms_route}";
            break;

    }