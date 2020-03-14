<?php

	/* login module */

	function login($connection, $data) {
		
		$status=404;
		$msg='Invalid Username';

		extract($data);

		if($checkUser=mysqli_query($connection, "SELECT user_id, user_password, user_salt_key, user_status, user_type, site_app, profile_display_name, profile_image, site_access_status FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."profile ON user_id=profile_owner_id INNER JOIN ".TABLE_PREFIX."site_access ON site_access_user_id=user_id INNER JOIN ".TABLE_PREFIX."site ON site_id=site_access_site_id WHERE user_email='{$email}' AND site_id=".SITE_ID." LIMIT 1")) {

			if(mysqli_num_rows($checkUser)!=0) {

				extract(mysqli_fetch_assoc($checkUser));

				if($user_password==hash('sha256', $user_salt_key.$pass)) {

					/** User has access of this site */
					
					if($site_access_status==1) {

						ob_start();

						setcookie('user_name', base64_encode($profile_display_name), time()+30*24*60*60, '/');
						setcookie('user_image', base64_encode($profile_image), time()+30*24*60*60, '/');
						setcookie('user_data', base64_encode($user_salt_key), time()+30*24*60*60, '/');
						setcookie('user_type', base64_encode($user_type), time()+30*24*60*60, '/');
						setcookie('site_app', base64_encode($site_app), time()+30*24*60*60, '/');

						ob_end_flush();

						$status=200;
						$msg='Login Successfully';

					} else {

						if($user_status==0) {

							include_once EMAIL_TEMPLATE.'user-verification.php';

							$status=403;
							$msg='Please verify your account';

						}


						$msg='Invalid Access';

					}

				} else {

					$status=403;
					$msg='Invalid Password';

				}

			}
	

		}

		$return=array(
			'status' => $status,
			'msg' => $msg,
			'url' => $url
		);

		echo json_encode($return);

	}

	/* Registration module */

	function register($connection, $data) {

		global $table_prefix;

		extract($data);
		$status=204;

		if(strlen($mobile)!=10 || !is_numeric($mobile)) {
			$msg='Enter valid mobile no.';
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$status=204;
			$msg='Enter valid email address';
		}

		if(strcmp($password, $confirm_password)) {

			$status=204;
			$msg='Password does not match';

		} else {

			$saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
			$password=hash('sha256', $saltKey.$password);

			mysqli_query($connection, "START TRANSACTION");

			/* Insert into user table */

			$createUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(email, pass, key, type, reg_time, reg_ip) VALUES('{$email}', '{$password}', '{$saltKey}', 'enquiry' ,NOW(), '{$_SERVER['REMOTE_ADDR']}')");
			$userID=mysqli_insert_id($connection);

			/* Create profile of user */

			$createProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_id, profile_first_name, profile_last_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$email}')");

			if($createUser && $createProfile) {

				mysqli_query($connection, "COMMIT");			

				$status=200;
				$msg=$password;
					
			} else {

				echo mysqli_error($connection);

				mysqli_query($connection, "ROLLBACK");
				$status=500;
				$msg='error while trying to register';

			}

		}

		

		$return=array(
			'status' => $status,
			'msg' => $msg,
			'url' => '/final-setup/'
		);

		echo json_encode($return);

	}

	function forgotPassword($connection, $data) {

		extract($data);
		global $table_prefix;

		if($checkUser=mysqli_query($connection, "SELECT user_salt_key, user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

			if(mysqli_num_rows($checkUser)!=0) {

				extract(mysqli_fetch_assoc($checkUser));

				$passwordResetURL=SITE_DOMAIN.'/verify/password.php?transaction='.$user_salt_key.'&email='.base64_encode($email);

				require_once EMAIL_TEMPLATE.'forgot-password.php';

				$status=200;
				$msg='Email has been sent to your register email.';

			} else {

				$msg='No User Found';

			}

		}

		$result=array(
			'status' => ((isset($status)) ? $status : 206 ),
			'msg' => ((isset($msg)) ? $msg : 'Unable to find any user' )
		);

		echo json_encode($result, TRUE);

	}

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

						$status=200;
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
			'status' => ((isset($status)) ? $status : 206 ),
			'msg' => ((isset($msg)) ? $msg : 'Unable to update your password' )
		);

		echo json_encode($result, TRUE);

	}

?>