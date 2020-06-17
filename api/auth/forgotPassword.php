<?php

    /** Forgot Password module */

	function forgotPassword($connection, $data) {

		extract($data);
		global $table_prefix;

		if($checkUser=mysqli_query($connection, "SELECT user_salt_key, user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

			if(mysqli_num_rows($checkUser)!=0) {

				extract(mysqli_fetch_assoc($checkUser));

				$passwordResetURL=SITE_DOMAIN.'/verify/password.php?transaction='.$user_salt_key.'&email='.base64_encode($email);

				require_once EMAIL_TEMPLATE.'forgot-password.php';

				$status='success';
				$msg='Email has been sent to your register email.';

			} else {

                $status='warning';
				$msg='No User Found';

			}

		}

		$result=array(
			'status' => ((isset($status)) ? $status : 'error' ),
			'msg' => ((isset($msg)) ? $msg : 'Unable to find any user' )
		);

		echo json_encode($result, TRUE);

	}