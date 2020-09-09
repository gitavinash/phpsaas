<?php

    function add($connection, $data) {
        
        extract($data);

        if($addToCart=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."cart(cart_site_id, cart_product_id, cart_product_qty, cart_price, cart_status, cart_generated) VALUES(".SITE_ID.", '{$id}', '{$quantity}', '{$price}', 0, NOW())")) {

            $status=200;
            $msg='Product add to card is successful';

        } else {

            // $msg='Something is wrong while performing add to cart';
            $msg=mysqli_error($connection);

        }

        switch($id) {

            case 1 :
                $productName='Immunity Vitals';
                $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/immunity-vitals/1.jpg';
                break;

            case 2 :
                $productName='Tar Free';
                $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/tar-free/1.jpg';
                break;

            case 3 :
                $productName='Gyno Vitals';
                $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/gyno-vitals/1.jpg';
                break;

            case 4 :
                $productName='V Liver Vitals';
                $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/v-liver-vitals/1.jpg';
                break;

            default :
                $productName='';
                $productImage='';
                break;

        }
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'productName' => ((isset($productName)) ? $productName : '' ),
            'productImage' => ((isset($productImage)) ? $productImage : '' ),
            'productQty' => ((isset($quantity)) ? $quantity : 0 )
        );
    
        echo json_encode($result, TRUE);

    }