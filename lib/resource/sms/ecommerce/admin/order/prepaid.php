<?php

    $message=urlencode("Thanks for placing order with ".SITE_NAME.". Your order number OD{$order_id} will be processed within 24 hrs.");
    
    require SEND_SMS;