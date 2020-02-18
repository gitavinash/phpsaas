<?php

    /** User email verification */

    function email($connection, $data) {

        extract($data);
        global $table_prefix;

        $email=base64_decode($email); // decode email variable

        if($selectUser=mysqli_query($connection, "SELECT user_id, user_status FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' AND user_salt_key='{$user_id}'")) {
            if(mysqli_num_rows($selectUser)!=0) {

                extract(mysqli_fetch_assoc($selectUser));

                if($user_status==0) {

                    if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."user SET user_status=1, user_act_time=NOW() WHERE user_id='{$user_id}'")) {

                        $message='Thank you for verifying your email';

                    } else {

                        $message='Something went wrong while verification';

                    }

                } else {

                    $message='Email is already verified';

                }

            } else {

                $message='<h1>Invalid request</h1>';
            }

            if( file_exists(SYSTEM_PATH.'email.php')) {

                require_once SYSTEM_PATH.'email.php';

            } else {
                
                require_once USER_PATH.'email.php';

            }

        } else {

            $message='something went wrong';

        }

    }

    /** Reset Password */

    function password($connection, $data) {

        extract($data);
        global $table_prefix;

        $email=base64_decode($email);

        if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_salt_key='{$transaction}' AND user_email='{$email}'")) {

            if(mysqli_num_rows($checkUser)!=0) {

                require_once USER_PATH.'update-password.php';

            } else {

                $message='Something is wrong';

                require_once USER_PATH.'verify.php';

            }

        }

    }