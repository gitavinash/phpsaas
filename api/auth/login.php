<?php

	/* login module */

	function login($connection, $data) {

		extract($data);

		if($checkUser=mysqli_query($connection, "SELECT user_id, user_password, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_status FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."profile ON user_id=profile_user_id INNER JOIN ".TABLE_PREFIX."site_access ON site_access_user_id=user_id INNER JOIN ".TABLE_PREFIX."site ON site_id=site_access_site_id INNER JOIN ".TABLE_PREFIX."user_type ON user_type_code=site_access_type WHERE user_email='{$email}' LIMIT 1")) {

			if(mysqli_num_rows($checkUser)!=0) {

				extract(mysqli_fetch_assoc($checkUser));

				if($user_password==hash('sha256', $user_salt_key.$pass)) {

					/** User has access of this site */
					
					if($site_access_status==1) {

						ob_start();

						setcookie('user_name', base64_encode($profile_display_name), time()+30*24*60*60, '/', MAIN_DOMAIN);
						setcookie('user_image', base64_encode($profile_image), time()+30*24*60*60, '/', MAIN_DOMAIN);
						setcookie('user_data', base64_encode($user_salt_key), time()+30*24*60*60, '/', MAIN_DOMAIN);
						setcookie('user_type', base64_encode($user_type), time()+30*24*60*60, '/', MAIN_DOMAIN);
						setcookie('site_app', base64_encode(SITE_APP), time()+30*24*60*60, '/', MAIN_DOMAIN);

						ob_end_flush();

						$status='success';
						$msg='Login Successfully';
						$url='/dashboard/';

					} else {

						if($user_status==0) {

							include_once EMAIL_TEMPLATE.'user-verification.php';

							$msg='Please verify your account';

						}

						$msg='Invalid Access';

					}

				} else {

					$status='error';
					$msg='Invalid Password';

				}

			}
	

        } else {
			$status='error';
			$msg='Technical error';
		}
        
		$return=array(
			'status' => ((isset($status)) ? $status : 'error' ),
			'msg' => ((isset($msg)) ? $msg : 'Invalid Username' ),
			'url' => ((isset($url)) ? $url : '#' )
		);

		echo json_encode($return, TRUE);
    
    }