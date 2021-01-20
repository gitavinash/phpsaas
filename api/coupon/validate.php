<?php

    function validate($connection, $data) {

        extract($data);

        if($checkCoupon=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."coupon WHERE coupon_code='{$coupon}' AND coupon_site_id=".APP_SITE_ID)) {

            if(mysqli_num_rows($checkCoupon)!=0) {

                extract(mysqli_fetch_assoc($checkCoupon));

                if($coupon_status==1) {

                    if(date('Y-m-d') <= date('Y-m-d', strtotime($coupon_expire))) {

                        $status=200;
                        $msg='Valid coupon code';

                    } else {

                        $msg='Coupon code is expire';

                    }

                } else {

                    $msg='Coupon code is not active';

                }

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