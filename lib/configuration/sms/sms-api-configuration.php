<?php

    require_once 'sms-api-configuration.php';

    if($sms_default==1) {

        $sms_auth_key='';
        $sms_sender='';
        $sms_route='';
        $sms_gateway='msg91';

    }

    switch($sms_gateway) {

        case 'kap' :
            $smsURL="http://103.16.101.52:8080/sendsms/bulksms?username={$sms_username}&password={$sms_password}&type={$sms_type}&dlr={$sms_delivery_report}&destination=91{$mobile}&source={$sms_sender}&message={$message}";
            break;

        case 'rock2connect' :
            $smsURL="http://login.rock2connect.com/MOBILE_APPS_API/sms_api.php?type={$sms_type}&user={$sms_username}&pass={$sms_password}&sender={$sms_sender}&to_mobileno={$mobile}&sms_text={$message}";
            break;

        case 'textmysms' :
            $smsURL="http://sms.textmysms.com/app/smsapi/index.php?key={$sms_key}&campaign={$sms_campaign}&routeid={$sms_routeid}&type={$sms_type}&contacts={$mobile}senderid={$sms_senderid}&msg={$message}&template_id={$dlt_sms_temp_id}";
            break;

        default :
            $smsURL="https://api.msg91.com/api/sendhttp.php?authkey={$sms_auth_key}&mobiles={$mobile}&country=91&message={$message}&sender={$sms_sender}&route={$sms_route}&DLT_TE_ID={$sms_dlt_sms_id}";
            break;

    }