<?php

    $dlt_sms_temp_id='1107162486494502411';

    $message=urlencode("Name: {$enquiry_display_name}, Mob No: {$mobile}, Email: {$email}, BKG DT: ".date('d M Y').", BKG TMEI: ".date('h:i:s A').", TYPE: {$booking_time}, PNGT DT: ".date('d M Y', strtotime($booking_date)).", AMT: {$transaction_currency} {$transaction_amount} S S AUTOMOTIVE PRIVATE LIMITED NARAINA");
    $mobile='9810115828';
    require SEND_SMS;