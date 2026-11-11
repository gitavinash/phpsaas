<?php

/** Update Password Module */

function password($connection, $data)
{

    require_once LOGIN_CHECK;

    extract($data);

    if ($password == '' || $cnf_password == '') {

        $msg = 'Please enter your password';
    } elseif (strcmp($password, $cnf_password)) {

        /** Check both password is match or not */
        $msg = 'Your Confirm Password is not matched';
    } else {

        $email = $user_email;
        $escSaltKey = mysqli_real_escape_string($connection, (string) $user_salt_key);
        $escEmail   = mysqli_real_escape_string($connection, (string) $email);

        if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$escSaltKey}' AND user_email='{$escEmail}'")) {

            if (mysqli_num_rows($checkUser) != 0) {

                extract(mysqli_fetch_assoc($checkUser));
                $uidInt = (int) $user_id;

                $dbPassword = hash('sha256', $user_salt_key . $cnf_password);

                if ($updatePassword = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "user SET user_password='{$dbPassword}' WHERE user_id='{$uidInt}' AND user_email='{$escEmail}' AND user_salt_key='{$escSaltKey}'")) {

                    $status = 'success';
                    $msg = "Password updated successfully";

                    $passwordResetURL = SITE_URL . '/verify/password/?transaction=' . $user_salt_key . '&email=' . base64_encode($email);

                    require_once EMAIL_TEMPLATE . 'update-password-notification.php';
                } else {

                    $msg = 'Something is wrong while updating your password. Kindly contact to Admin';
                }
            } else {

                $msg = 'Invalid Action found';
            }
        } else {
            echo mysqli_error($connection);
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Unable to update your password')
    );

    echo json_encode($result, TRUE);
}
