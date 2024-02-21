<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

    extract($data);

    if ($first_name == '') {

        $msg = 'Please enter a first name';
    }

    if ($mobile == '') {

        $msg = 'Please enter a mobile number';
    }

    if ($password == '') {

        $msg = 'Password cannot be blank';
    }

    if ($password != $confirm_password) {

        $msg = 'Password is not matched';
    }

    // User registraion process

    if ($checkEmail = mysqli_query($connection, "SELECT user_email FROM " . TABLE_PREFIX . "user WHERE user_id='{$user_id}'")) {

        if (mysqli_num_rows($checkEmail) == 0) {

            /** Check mobile number is available or not */

            if ($checkMobile = mysqli_query($connection, "SELECT profile_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_mobile='{$mobile}'")) {

                if (mysqli_num_rows($checkMobile) == 0) {

                    /** Start the user registration process */

                    mysqli_query($connection, "START TRANSACTION");

                    /* Insert data into user table */

                    $updateUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$mobile}', '{$enc_password}', '$saltKey', 1, NOW(), '" . IP . "')");
                    $userID = mysqli_insert_id($connection);

                    $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$profile_display_name}')");

                    if ($insertUser && $insertProfile) {

                        $status = 'success';
                        $msg = 'User update successfully';
                        mysqli_query($connection, 'COMMIT');

                    } else {

                        $status = 'error';
                        $msg = 'Something is wrong while trying to update user';
                        mysqli_query($connection, 'ROLLBACK');
                    }
                } else {

                    $status = 'warning';
                    $msg = 'This mobile number is already in use';
                }
            }
        } else {

            $status = 'warning';
            $msg = 'This email is already in use.';
        }
    }


    $msg=urlencode($msg);

    if ($status === 'success') {
        header("Location: /dashboard/users/edit/?id=" . base64_encode($userID) . "&responseMsg={$msg}", TRUE, 301);
    } else {
        header("Location: /dashboard/users/edit/?responseMsg={$msg}&status={$status}", TRUE, 301);
    }

    exit();
}