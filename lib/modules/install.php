<?php

	/* step up website */

	function website ($connection, $data) {

		global $table_prefix;

		if($checkUser=mysqli_query($connection, "SELECT user_email FROM ".TABLE_PREFIX."user WHERE user_email='{$data['email']}'")) {
			if(mysqli_num_rows($checkUser)!=0) {

				header("Location /?msg=email is already register.");
				exit();

			}

			if($checkDomain=mysqli_query($connection, "SELECT site_domain FROM ".TABLE_PREFIX."site WHERE site_domain='".SITE_DOMAIN."'")) {

				if(mysqli_num_rows($checkDomain)!=0) {

					header("Location /?msg=domain is already register.");
					exit();

				}

			}

		}

		/* Encrypt password with salt key */

		$saltKey=hash('sha256', uniqid(mt_rand(), true).$data['email'].substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
		$password=hash('sha256', $saltKey.$data['password']);
		$index=((isset($data['site_index'])) ? $data['site_index'] : 0 );

		/* insert site profile into database */

		mysqli_query($connection, "START TRANSACTION");

		/* Insert data into user table */

		$insertUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_password, user_salt_key, user_type, user_reg_time, user_reg_ip) VALUES('administrator', '{$data['email']}', '{$password}', '$saltKey', 'administrator', NOW(), '{$_SERVER['REMOTE_ADDR']}')");
		$userID=mysqli_insert_id($connection);

		$insertSite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_domain, site_name, site_description, site_indexing, site_status) VALUES('{$userID}', '".SITE_DOMAIN."', '{$data['site_name']}', '{$data['site_desc']}', '{$index}', 0)");

		$insertProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '', '', '', '{$data['site_name']}', '{$data['email']}')");

		if($insertUser && $insertSite && $insertProfile) {

			mysqli_query($connection, 'COMMIT');
			header("Location: /install/?step=2");
			exit();

		} else {
			echo mysqli_error($connection);
			mysqli_query($connection, 'ROLLBACK');
			exit();

		}

	}

	/* Install Application */

	function application($connection, $data) {

		$expire_date=date('Y-m-d', strtotime('+5 years'));

		global $table_prefix;

		$theme=((isset($theme)) ? $theme : 'default' );

			mysqli_query($connection, "START TRANSACTION");

			$updateUser=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."user SET user_app='{$data['site_app']}', user_status=1, user_act_time=NOW() WHERE user_id='{$data['user_id']}'");
			$updateSite=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site SET site_status=1, site_lang='{$data['site_lang']}', site_app='{$data['site_app']}', site_theme='{$theme}', site_expire_date='{$expire_date}' WHERE site_owner_id='{$data['user_id']}'");

			if($updateUser && $updateSite) {

				/* Query Run*/

				mysqli_query($connection, "COMMIT");

				header("Location: /login/");
				exit();

			} else {
				mysqli_query($connection, "ROLLBACK");
				die("Something is wrong while finishing the installing process.");
				exit();

			}

	}

?>