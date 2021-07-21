<?php

    $dlt_sms_temp_id='1107161484357821673';

    $message=urlencode("Hello {$enquiry_first_name},\nYour payment has been failed. Please try again.");
    
    require SEND_SMS;