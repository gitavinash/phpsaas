<?php

require_once UTIL_FUNCTIONS;

$httpStatus = ((DEVELOPMENT_MODE == true) ? 200 : 500);
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isEmailValid($email)) {
        $status     = 'error';
        $msg        = 'Invalid email address';
        $httpStatus = 422;
    } else {
        if ($checkEmail = mysqli_query($connection, "SELECT user_id, user_salt_key as saltKey, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_email='{$email}'")) {
            if (mysqli_num_rows($checkEmail) == 0) {
                $newUser = true;
                $user_id = 0;
            } else {
                extract(mysqli_fetch_assoc($checkEmail));
            }

            // Send OTP
            $showOutput = false;
            $otpMode = 'email';
            $otpComment = ((isset($profile_display_name)) ? $profile_display_name : 'User');
            require_once 'send-otp.php';
            if ($response['status'] == 'success') {
                $status = 'success';
                $msg = 'OTP sent successfully';
                $httpStatus = 200;
            } else {
                $status = ((DEVELOPMENT_MODE == true)) ? 'success' : 'error';;
                $msg = ((DEVELOPMENT_MODE == true)) ? $response['msg'] : 'Failed to send OTP';
            }
        } else {
            $status = 'error';
            $msg = 'Something is wrong while trying to register';
            mysqli_query($connection, 'ROLLBACK');
        }
    }
} else {
    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something is wrong while trying to register');
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'otpRef' => ((isset($response['otpRef'])) ? base64_encode($response['otpRef']) : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
