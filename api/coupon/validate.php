<?php

    function validate($connection, $data) {

        extract($data);
        
        require_once MODULES.'validateCoupon.php';

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'discount' => ((isset($discountValue)) ? $discountValue : 0 ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }