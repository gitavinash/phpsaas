<?php

    $dlt_sms_temp_id='1107161263371857897';

    $message=urlencode("You Pangat couldnot be booked for {$bookingDate} {$bookingTime} as payment failed. You can try again after some time.");
    
    require_once SEND_SMS;