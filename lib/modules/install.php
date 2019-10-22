<?php

	/* step up website */

	function website ($connection, $data) {

		extract($data);

		$siteExprireDate=date('Y-m-d', strtotime("+5 Years"));

		if($checkUser=mysqli_query($connection, "SELECT user_email FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {
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

		$saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
		$dbPassword=hash('sha256', $saltKey.$password);
		$index=((isset($site_index)) ? $site_index : 0 );

		/* insert site profile into database */

		mysqli_query($connection, "START TRANSACTION");

		/* Insert data into user table */

		$insertUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_password, user_salt_key, user_app, user_type, user_reg_time, user_reg_ip) VALUES('administrator', '{$email}', '{$dbPassword}', '$saltKey', 'system', 'administrator', NOW(), '{$_SERVER['REMOTE_ADDR']}')");
		$userID=mysqli_insert_id($connection);

		$insertSite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_domain, site_name, site_description, site_app, site_theme, site_indexing, site_status, site_expire_date) VALUES('{$userID}', '".SITE_DOMAIN."', '{$site_name}', '{$site_desc}', 'system', 'default', '{$index}', 1, '{$siteExprireDate}')");

		$insertProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '', '', '', '{$site_name}', '{$email}')");

		if($insertUser && $insertSite && $insertProfile) {

			mysqli_query($connection, 'COMMIT');
			
			/** Generate Verification Key and send email */
			
			$keyType='key';
			$keyMedium='email';
			$verifyURL=SITE_URL.'/verify/email.php?user_id='.$saltKey.'&email='.base64_encode($email);
			
			require_once 'generateKey.php';

			header("Location: /install/?step=2");
			exit();

		} else {
			
			mysqli_query($connection, 'ROLLBACK');
			exit();

		}

	}