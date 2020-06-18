<?php

    /** Sending verification email module */

    function emailVerification($connection, $data) {

        extract($data);

        if($checkEmail=mysqli_query($connection, "SELECT user_salt_key, user_status FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

            if(mysqli_num_rows($checkEmail)!=0) {

                extract(mysqli_fetch_assoc($checkEmail));

                /** Send User's Email Verification Mail */

                $verifyURL=SITE_DOMAIN.'/verify/email.php?user_id='.$user_salt_key.'&email='.base64_encode($email);

                require_once EMAIL_TEMPLATE.'email-verification.php';

                $status='success';
                $msg='New verification mail has been sent';

            } else {

                $status='warning';
                $msg='No Email Found';

            }

        } else {

            $status='error';
            // $msg='Something is wrong while trying to connect to server';
            $msg=mysqli_error($connection);

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Unknown Error' )
        );

        echo json_encode($result, TRUE);

    }