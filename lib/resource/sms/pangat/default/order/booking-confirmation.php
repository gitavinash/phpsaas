<?php

    $dlt_sms_temp_id='1107161484353124853';

    $message=urlencode("Hello {$enquiry_display_name},\nYour pangat for ".date('d M Y', strtotime($booking_date))." {$booking_time} has been successfully booked.");

    require_once SEND_SMS;