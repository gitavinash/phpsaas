<?php

    $message=urlencode("Thanks for placing order with Vedic Vitals. Your order number OD{$order_id} will be processed within 24 hrs.");
    
    require_once SEND_SMS;