<?php

	if(!isset($_COOKIE['user_data'])) {

		header("Location: /login/");
		exit();

	} else {

		$user_salt_key=base64_decode($_COOKIE['user_data']);

		if($checkUserIsReal=mysqli_query($connection, "SELECT user_id, user_status, site_app_id, site_access_site_id, site_access_type FROM ".TABLE_PREFIX."site_access INNER JOIN ".TABLE_PREFIX."user ON site_access_user_id=user_id INNER JOIN ".TABLE_PREFIX."site ON site_access_site_id=site_id WHERE user_salt_key='{$user_salt_key}'")) {
			if(mysqli_num_rows($checkUserIsReal)!=0) {

				extract(mysqli_fetch_assoc($checkUserIsReal));

				/** Define new constant variable */
				
				define('USER_ID', $user_id);

				/** Define new cookie for logged in user */

				if(!isset($_COOKIE['site_app'])) {

					setcookie('site_app', base64_encode($site_app), time()+30*24*60*60, '/');
					header("Location: /login/");
					exit();

				} else {

					/** User is for current website */

					if($site_access_site_id!=SITE_ID) {

						/** Check user is related to site app */

						if($site_access_type='administrator') {
							
							header("Location: /website-setup.php");
							exit();

						} else {

							header("Location: ".ERROR_403);
							exit();

						}

						exit();

					}

				}

				if($user_status!=1) {
					header("Location: /install/?step=verify_account");
					exit();
				}

				/** Set login is valid */

				setcookie('isLogin', 1, time()+30*24*60*60, '/');

			} else {
				header("Location: /logout/");
				exit();
			}

		} else {

			echo 'something is wrong while checking user session';
			exit();

		}

	}

?>