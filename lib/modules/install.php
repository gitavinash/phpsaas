<?php

	/* Set up default system application */

	function website ($connection, $data) {

		extract($data);

		$expireDate=date('Y-m-d', strtotime('+10 years'));

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
		$enc_password=hash('sha256', $saltKey.$password);
		$index=((isset($site_index)) ? $site_index : 0 );

		/* insert site profile into database */

		mysqli_query($connection, "START TRANSACTION");

		/* Insert data into user table */

		$insertUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_password, user_salt_key, user_type, user_reg_time, user_reg_ip) VALUES('administrator', '{$email}', '{$enc_password}', '$saltKey', 'administrator', NOW(), '{$_SERVER['REMOTE_ADDR']}')");
		$userID=mysqli_insert_id($connection);

		$insertSite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_domain, site_url, site_name, site_description, site_app, site_indexing, site_expire_date, site_status) VALUES('{$userID}', '".SITE_DOMAIN."', '".SITE_DOMAIN."', '{$site_name}', '{$site_desc}', 'system', '{$index}', '{$expireDate}', 1)");
		$siteID=mysqli_insert_id($connection);

		$insertSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_status) VALUES('{$siteID}', '{$userID}', 1)");

		$insertSiteSetting=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_setting(site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_thumbnail_width, site_thumbnail_height) VALUES(10, 10, 10, 60, 223, 96)");

		$insertProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '', '', '', '{$site_name}', '{$email}')");

		if($insertUser && $insertSite && $insertSiteAccess && $insertSiteSetting && $insertProfile) {

			mysqli_query($connection, 'COMMIT');

			/** Send User's Email Verification Mail */

			$verifyURL=SITE_DOMAIN.'/verify/email.php?user_id='.$saltKey.'&email='.base64_encode($email);

			require_once EMAIL_TEMPLATE.'add-new-user.php';

			header("Location: /install/?step=3");
			exit();

		} else {
			echo mysqli_error($connection);
			mysqli_query($connection, 'ROLLBACK');
			exit();

		}

	}