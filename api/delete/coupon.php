<?php

    require_once LOGIN_CHECK;

    function coupon($connection, $data) {

        extract($data);

        if(isset($id)) {

            $id=base64_decode($id);

            if($checkCoupon=mysqli_query($connection, "SELECT coupon_id FROM ".TABLE_PREFIX."coupon WHERE coupon_id='{$id}' AND coupon_site_id=".APP_SITE_ID)) {

                if(mysqli_num_rows($checkCoupon)!=0) {

                    extract(mysqli_fetch_assoc($checkCoupon));

                    if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."coupon SET coupon_status=3 WHERE coupon_id='{$coupon_id}' AND coupon_site_id=".APP_SITE_ID)) {

                        $status=200;
                        $msg='Coupon delete successfully';

                    } else {

                        $msg='Something is wrong while deleting coupon';

                    }

                }

            }

        }

        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }