<?php

    require_once LOGIN_CHECK;

    if($checkOrder=mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_id, order_product_qty, inventory_currency_symbol, order_product_price, order_delivery_charges, order_currency, order_total_price, order_payment_mode, order_generate, order_status, address_mobile FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."address ON address_id=order_address_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=order_user_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=order_product_id WHERE order_site_id=".APP_SITE_ID." AND order_product_id!='' AND inventory_site_id=".APP_SITE_ID." ORDER BY order_id DESC LIMIT 0, 1000")) {

        

    }
    
    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
    );

    echo json_encode($result, TRUE);