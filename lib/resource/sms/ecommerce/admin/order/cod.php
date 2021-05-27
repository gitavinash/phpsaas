<?php

    $message=urlencode("Hi {$profile_first_name} Thanks for placing order with ".SITE_NAME.". Your order number is OD{$order_id} You may receive a confirmation call shortly.");
    
    require_once SEND_SMS;