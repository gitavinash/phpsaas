<?php

    function sendOTP($connection, $data) {

        if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

            if(mysqli_num_rows($checkUser)!=0) {

                extract(mysqli_fetch_assoc($checkUser));

                $mode=((isset($method)) ? $method : 'sms' );
                
                $expireDuration=((isset($setting_otp_expire)) ? $setting_otp_expire : 10 );
                $expireTime=date('Y-m-d H:i:s', strtotime("{$expireDuration} minutes"));
                
                $otpCode=$saltKey=substr(str_shuffle('0123456789'), 0, 6);
                $message=urlencode("Dear user, you verification code is {$otp}");

                if($sendOTP=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."otp(otp_user_id, otp_site_id, otp_mode, otp_code, otp_status, otp_expire) VALUES('{$user_id}', '".SITE_ID."', '{$mode}', '{$otpCode}', 0, '{$expireTime}')")) {

                    $status=200;
                    $msg='OTP send successfully';

                    $url="http://103.16.101.52:8080/sendsms/bulksms?username=".SMS_USER."&password=".SMS_PASS."&type=0&dlr=1&destination=91{$mobile}&source=".SMS_SENDER_ID."&message={$message}";
                    $smsStatus=file_get_contents($url);

                } else {

                    $msg='Technical errro while sending otp';

                }

            } else {

                $msg='No user found';

            }

        } else {

            $msg='Technical error';

        }

		$result = array(
			'status' => $status,
			'msg' => ((isset($msg)) ? $msg : '')
		);

        echo json_encode($result, true);

    }
