<?php

    $dlt_sms_temp_id='1107161263366953935';
    $guestNumbers=(($guest==1) ? "with {$noOfGuest} guests" : "" ); 

    $message=urlencode("You have successfully booked Pangat for {$bookingDate} {$bookingTime} {$guestNumbers}. Please call 011-23670506 in case of any issue.");
    
    require_once SEND_SMS;