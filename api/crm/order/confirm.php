<?php

    function confirm($connection, $data) {

        extract($data);

        if(isset($order_id)) {

            if($checkOrder=mysqli_query($connection, "SELECT order_id FROM ".TABLE_PREFIX."order WHERE order_id='{$order_id}'")) {

                if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$order_id}'")) {

                    $status=200;
                    $msg='Order update successfully';

                } else {

                    $msg='Something is wrong while updating the order status';

                }

            }

        } else {

            $msg='Invalid order id found';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }