<?php


$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userKey = ((isset($_COOKIE['user_data'])) ? mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data'])) : '');
    if ($userKey != '') {
        if ($checkMobile = mysqli_query($connection, "SELECT user_id, user_salt_key as saltKey, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_salt_key='{$userKey}'")) {
            if (mysqli_num_rows($checkMobile) == 0) {
                $status = 'error';
                $msg = 'Invalid user token found';
                $httpStatus = 400;
            } else {
                extract(mysqli_fetch_assoc($checkMobile));

                $updateProfile = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='" . mysqli_real_escape_string($connection, $name) . "' WHERE profile_user_id={$user_id}");
                if ($updateProfile) {
                    $status = 'success';
                    $msg = 'OTP sent successfully';
                    $httpStatus = 200;

                    ob_start();


                    $cleanDomain = preg_replace('#^https?://#', '', MAIN_DOMAIN);
                    $cleanDomain = explode(':', $cleanDomain)[0];
                    $cleanDomain = rtrim($cleanDomain, '/');

                    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

                    $cookieOptions = [
                        'expires'  => time() + (30 * 24 * 60 * 60),
                        'path'     => '/',
                        'domain'   => '.' . $cleanDomain,
                        'secure'   => $isSecure,
                        'httponly' => true,
                        'samesite' => 'None',
                    ];

                    setcookie('user_name', base64_encode($name), $cookieOptions);

                    if (headers_sent($file, $line)) {
                        error_log("Headers already sent in $file on line $line. Cookies may not work.");
                    }

                    require_once API_MODULES . 'analytics/session-record.php';
                    ob_end_flush();
                }
            }
        } else {
            $status = 'error';
            $msg = 'Something is wrong while trying to register';
            mysqli_query($connection, 'ROLLBACK');
        }
    } else {
        $status = 'error';
        $msg = 'User token is missing';
        $httpStatus = 400;
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