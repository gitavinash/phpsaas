<?php

    $message=urlencode("You Pangat couldnot be booked for {$bookingDate} {$bookingTime} as payment failed. You can try again after some time.");
    
    require_once SEND_SMS;