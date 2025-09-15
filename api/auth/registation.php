<?php

require_once COMMUNICATION_FUNCTIONS;
header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];
$httpStatus = 400;

// ==== Validate GET Request ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $expiryDate = ((isset($expiryDate)) ? $expiryDate : date('Y-m-d', strtotime('+1 year')));

    if (isset($email)) {
        $findUserFilter = "user_mobile='{$user_id}'";
    }
    if (isset($mobile)) {
        $findUserFilter = "user_email='{$user_id}'";
    }

    $displayName = explode(' ', $full_name);
    $first_name = $displayName[0];
    $last_name = ((isset($displayName[1])) ? $displayName[1] : '');

    mysqli_query($connection, "START TRANSACTION");

    if ($checkUser = mysqli_query($connection, "SELECT user_email, user_id as userID, user_salt_key as saltKey, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE {$findUserFilter}")) {

        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));

            if (isset($email)) {
                $updateUserQuery = "UPDATE " . TABLE_PREFIX . "user SET user_email='{$user_id}', user_status=1 WHERE {$findUserFilter}";
                $updateProfileQuery = "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='{$full_name}', profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_email='{$email}', profile_email_status=1 WHERE profile_user_id='{$userID}'";
            }
            if (isset($mobile)) {
                $updateUserQuery = "UPDATE " . TABLE_PREFIX . "user SET user_mobile='{$user_id}', user_status=1 WHERE {$findUserFilter}";
                $updateProfileQuery = "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='{$full_name}', profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_mobile='{$mobile}', profile_mobile_status=1 WHERE profile_user_id='{$userID}'";
            }

            $updateUser = mysqli_query($connection, $updateUserQuery);
            $updateProfile = mysqli_query($connection, $updateProfileQuery);

            if ($updateProfile && $updateUser) {
                $httpStatus = 201;
                $response['status'] = 'success';
                $response['msg'] = "Registration completed successfully.";
                mysqli_query($connection, "COMMIT");
            } else {
                $response['msg'] = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong');
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $response['status'] = 204;
            $response['msg'] = "No user fond";
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
}

// ==== Output Response ====
if (isset($showOutput)) {
    if ($showOutput == true) {
        http_response_code($httpStatus);
        echo json_encode($response);
    }
} else {
    http_response_code($httpStatus);
    echo json_encode($response);
}