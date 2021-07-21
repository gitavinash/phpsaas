<?php

    $dlt_sms_temp_id='1107161484362007629';

    $message=urlencode("Hello {$first_name}, your request for ".date('d M Y', strtotime($bookingDate))." {$bookingTime} pangat has been received.");
    
    require SEND_SMS;