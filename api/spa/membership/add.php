<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($checkUser = mysqli_query($connection, "SELECT user_id as userID FROM " . TABLE_PREFIX . "user WHERE user_mobile='{$mobile}'")) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
        } else {
            registerUserByMobile($connection, $mobile, $name);
        }
    }

    if ($userId != '') {

        // Create a membership



    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}


http_response_code($httpStatus);
echo json_encode($result, TRUE);