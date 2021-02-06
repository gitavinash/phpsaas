<?php

    $dlt_sms_temp_id='1107161263361686617';

    $message=urlencode("We have received request for booking Pangat for {$bookingDate} {$bookingTime}. Please complete the payment to confirm.");
    
    require_once SEND_SMS;