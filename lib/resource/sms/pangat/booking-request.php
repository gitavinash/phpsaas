<?php

    $message=urlencode("We have received request for booking Pangat for {$bookingDate} {$bookingTime}. Please complete the payment to confirm.");
    
    require_once SEND_SMS;