<?php

    function sendOTP($connection, $data) {

        extract($data);

        if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

            if(mysqli_num_rows($checkUser)!=0) {

                $newuser='';
                extract(mysqli_fetch_assoc($checkUser));

            } else {

                $newuser='&redirect=/register/';

            }

            $mode=((isset($method)) ? $method : 'sms' );
                
            $expireDuration=((isset($setting_otp_expire)) ? $setting_otp_expire : 10 );
            $expireTime=date('Y-m-d H:i:s', strtotime("{$expireDuration} minutes"));
                
            $otpCode=$saltKey=substr(str_shuffle('0123456789'), 0, 6);

            $user_id=((isset($user_id)) ? $user_id : 0 );

            if($sendOTP=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."otp(otp_user_id, otp_site_id, otp_mode, otp_code, otp_status, otp_expire) VALUES('{$user_id}', '".SITE_ID."', '{$mode}', '{$otpCode}', 0, '{$expireTime}')")) {

                $otp_id=mysqli_insert_id($connection);
                $status=200;
                $msg='OTP send successfully';
                $url="/verify-otp/?id=".base64_encode($otp_id)."&mobile={$mobile}{$newuser}";

                require_once SMS_TEMPLATE.'otp.php';

            } else {

                $msg='Technical errro while sending otp';

            }

        } else {

            $msg='Technical error';

        }

		$result = array(
			'status' => ((isset($status)) ? $status : 'error'),
			'msg' => ((isset($msg)) ? $msg : ''),
			'url' => ((isset($url)) ? $url : '')
		);

        echo json_encode($result, true);

    }
