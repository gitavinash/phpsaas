<?php

	if(!isset($_COOKIE['user_data'])) {

		header("Location: /login/");
		exit();

	} else {

		$user_salt_key=base64_decode($_COOKIE['user_data']);

		if($checkUserIsReal=mysqli_query($connection, "SELECT site_id, site_name, site_description, user_id, user_status, user_type, user_app FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."site WHERE user_salt_key='{$user_salt_key}' AND (site_owner_id=user_id OR site_owner_id=user_parent_id) LIMIT 1")) {
			if(mysqli_num_rows($checkUserIsReal)!=0) {
				extract(mysqli_fetch_assoc($checkUserIsReal));

				/** Define new constant variable */
				
				define('USER_ID', $user_id);

				/** Define new cookie for logged in user */

				if(!isset($_COOKIE['user_app'])) {
					setcookie('user_app', base64_encode($user_type.CREDIT.$user_app), time()+30*24*60*60, '/');
					header("Location: /login/");
					exit();
				}

				if($user_status!=1) {
					header("Location: /logout/");
					exit();
				}

			} else {
				header("Location: /logout/");
				exit();
			}
		}

	}

?>