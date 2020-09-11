<?php

    function validate($connection, $data) {

        extract($data);

        if($checkCoupon=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."coupon WHERE coupon_code='{$coupon}'")) {

            if(mysqli_num_rows($checkCoupon)!=0) {

                $status=200;
                $msg='Valid coupon code';

            } else {

                $msg='Invalid coupon code';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }