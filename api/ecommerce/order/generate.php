<?php

    function generate($connection, $data) {

        // extract($data);
        print_r($data);

        /** Get Product from cart */

        if($checkCart=mysqli_query($connection, "SELECT GROUP_CONCAT(cart_product_id SEPARATOR ', ') as product, GROUP_CONCAT(cart_product_qty SEPARATOR ', ') as quantity, GROUP_CONCAT(cart_price SEPARATOR ', ') as price, SUM(cart_price) as totalPrice, cart_currency FROM isw_cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=1 AND cart_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkCart)!=0) {

                print_r(mysqli_fetch_assoc($checkCart));

            } else {

                $msg='Cart is empty or something is wrong';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }