<?php

require_once USER_FUNCTIONS;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Trim inputs
    $mobile    = trim($_POST['mobile'] ?? '');
    $isValid = true;

    // List of required fields
    $required = [
        'mobile'    => "Kindly enter mobile number",
    ];

    // Generic empty checks
    foreach ($required as $field => $message) {
        if ($$field === '') {
            $status = 'warning';
            $httpStatus = 400;
            $msg = $message;
            $isValid = false;
            break;
        }
    }

    // Extra validations if not already failed
    if ($isValid) {
        if (!preg_match('/^(\+91[\-\s]?)?[0]?(91)?[6-9]\d{9}$/', $mobile)) {
            $status = 'warning';
            $httpStatus = 400;
            $msg = "Kindly enter a valid mobile number";
            $isValid = false;
        }
    }

    // Check site access

    if ($isValid) {
        $checkUserQuery = "SELECT site_domain FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id = site_access_site_id WHERE user_mobile = '$mobile' AND user_status = 1";

        if ($checkUser = mysqli_query($connection, $checkUserQuery)) {
            if (mysqli_num_rows($checkUser) > 0) {
                $site = mysqli_fetch_assoc($checkUser);
                $data['site_domain'] = $site['site_domain'];
                $httpStatus = 200;
                $status = 'success';
                $msg = 'Site found';
            } else {
                $httpStatus = 404;
                $msg = 'No site access found for this mobile number';
            }
        } else {
            $httpStatus = 500;
            $msg = 'Database error: ' . mysqli_error($connection);
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
    'data' => ((isset($data)) ? $data : '')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);