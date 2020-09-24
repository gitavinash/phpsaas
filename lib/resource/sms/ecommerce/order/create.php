<?php

    $message=urlencode("Hi {$profile_display_name} Thanks for placing order with Vedic Vitals. Your order number is {$order_id} You may receive a confirmation call shortly.");
    
    require_once SEND_SMS;