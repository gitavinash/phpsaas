<?php

    $mobile = ltrim($mobile, '0');

    // if($checkMobile = mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

    //     if(mysqli_num_rows($checkMobile)!=0) {

    //         extract(mysqli_fetch_assoc($checkMobile));

    //     } else {

    //         echo "no user found";

    //     }

    // }

    // echo $user_id;

    // // echo require_once PAYMENT_GATEWAY.'generate.php';


    require_once API_MODULES.'payment/request.php';

    exit();