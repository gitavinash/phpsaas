<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($id)) {
        $userId = base64_decode($id);
        if ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, site_access_meta, profile_first_name, profile_last_name, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_id = {$userId}")) {
            if (mysqli_num_rows($checkUser) != 0) {
                extract(mysqli_fetch_assoc($checkUser));
                require_once EMAIL_TEMPLATE . 'request-kyc.php';
                $status = 'success';
                $msg = 'KYC request sent successfully';
                $httpStatus = 201;
            } else {
                $httpStatus = 204;
                $msg = 'No user found';
            }
        }
    } else {
        $httpStatus = 400;
        $msg = 'User id is required';
    }
}
$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);