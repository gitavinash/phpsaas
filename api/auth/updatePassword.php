<?php

    /** Update Password Module */

    function updatePassword($connection, $data) {

        extract($data);
        global $table_prefix;

        if($pass=='' || $cnf_pass=='') {

            $msg='Please enter your password';

        } else {

            /** Check both password is match or not */

            if(strcmp($pass, $cnf_pass)) {

                $msg='Your Confirm Password is not matched';

            }

            $email=base64_decode($email);

            if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_salt_key='{$transaction}' AND user_email='{$email}'")) {

                if(mysqli_num_rows($checkUser)!=0) {

                    extract(mysqli_fetch_assoc($checkUser));

                    $dbPassword=hash('sha256', $transaction.$cnf_pass);

                    if($updatePassword=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."user SET user_password='{$dbPassword}' WHERE user_id='{$user_id}' AND user_email='{$email}' AND user_salt_key='{$transaction}'")) {

                        $status='success';
                        $msg="Password updated successfully";

                        $passwordResetURL=SITE_DOMAIN.'/verify/password.php?transaction='.$transaction.'&email='.base64_encode($email);

                        require_once EMAIL_TEMPLATE.'update-password-notification.php';

                    } else {

                        $msg='Something is wrong while updating your password. Kindly contact to Admin';

                    }

                } else {

                    $msg='Invalid Action found';

                }

            }

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Unable to update your password' )
        );

        echo json_encode($result, TRUE);

    }