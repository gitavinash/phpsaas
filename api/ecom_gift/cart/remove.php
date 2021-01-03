<?php

    function remove($connection, $data) {
        
        extract($data);

        if($addToCart=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."cart SET cart_status=4 WHERE cart_id='{$id}'")) {

            $status=200;
            $msg='Product remove from cart';

        } else {

            $msg='Something is wrong while performing remove to cart';

        }
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        );
    
        echo json_encode($result, TRUE);

    }