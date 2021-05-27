<?php

    $dlt_sms_temp_id='1107161484353124853';

    // $message=urlencode("You have successfully booked Pangat for {$bookingDate} {$bookingTime} {$guestNumbers}. Please call 011-23670506 in case of any issue.");
    $message=urlencode("Hello {$enquiry_display_name},\nYour pangat for ".date('d M Y', strtotime($booking_date))." {$booking_time} has been successfully booked.");
    
    require_once SEND_SMS;