<?php

header('Content-Type: application/json');
$input = file_get_contents('php://input');
require_once LOGIN_CHECK;

$requestBody = json_decode($input, TRUE);
extract($requestBody);
$httpStatus = 405;
$validRequest = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($name != '') {
        if ($mobile != '') {
            $mobile = ltrim($mobile, '0');
            if ($address != '') {
                if ($inventory != '') {
                    if ($warehouse != '') {
                        $validRequest = true;
                    } else {
                        $msg = 'Warehouse id is required';
                    }
                } else {
                    $msg = 'Inventory cannot be blank';
                }
            } else {
                $msg = 'Address cannot be blank';
            }
        } else {
            $msg = 'Mobile number cannot be blank';
        }
    } else {
        $msg = 'Name cannot be blank';
    }
}

if ($validRequest) {

    mysqli_query($connection, "START TRANSACTION");

    if ($checkEmail = mysqli_query($connection, "SELECT user_mobile, user_id as userID FROM " . TABLE_PREFIX . "user WHERE user_mobile='{$mobile}' OR user_email='{$email}'")) {

        if (mysqli_num_rows($checkEmail) == 0) {

            /** Start the user registration process */

            /* Encrypt password with salt key */

            $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
            $enc_password = hash('sha256', $saltKey . '12345678');

            $full_name = explode(' ', $name);
            $first_name = $full_name[0];
            $last_name = count($full_name) - 1;
            $profile_display_name = $first_name . ' ' . $last_name;

            /** Checking Username is avilable or not */

            $username = strtolower(str_replace(' ', '', $first_name . $last_name));

            if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                if (mysqli_num_rows($checkUsername) != 0) {
                    $username = strtolower($first_name) . time();
                }
            }

            $email = (($email != '') ? $email : time() . '@fakemail.com');

            mysqli_query($connection, "START TRANSACTION");

            /* Insert data into user table */

            $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '$saltKey', 1, NOW(), '" . IP . "')");
            $userID = mysqli_insert_id($connection);

            $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$name}', '{$email}')");
        } else {
            extract(mysqli_fetch_assoc($checkEmail));
            $insertUser = true;
            $insertProfile = true;
        }

        $insertSales = mysqli_query($connection, "INSERT INTO {$tableNamePrefix}sales(sales_site_id, sales_agent_id, sales_user_id, sales_warehouse_id, sales_name, sales_email, sales_mobile, sales_address, sales_data, sales_status, sales_created) VALUES(" . SITE_ID . ", " . USER_ID . ", {$userID}, '{$warehouse}', '{$name}', '{$email}', '{$mobile}', '{$address}', '{$inventory}', 0, NOW())");

        if ($insertUser && $insertProfile && $insertSales) {
            $salesId = mysqli_insert_id($connection);
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Sales request sent successfully';
            mysqli_query($connection, 'COMMIT');
        } else {
            $httpStatus = 500;
            $status = 'error';
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something is wrong while trying to sent sales request');
            mysqli_query($connection, 'ROLLBACK');
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'salesId' => ((isset($salesId)) ? $salesId : 0),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);