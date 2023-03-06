<?php

/* Set up default system application */

function website($connection, $data)
{

	extract($data);

	$expireDate = date('Y-m-d', strtotime('+10 years'));

	if ($checkUser = mysqli_query($connection, "SELECT user_email, user_status FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'")) {

		if (mysqli_num_rows($checkUser) != 0) {

			$status = 'warning';
			$msg = 'This email is already register.';
			exit();
		}

		if ($checkDomain = mysqli_query($connection, "SELECT site_domain FROM " . TABLE_PREFIX . "site WHERE site_domain='" . SITE_DOMAIN . "'")) {

			if (mysqli_num_rows($checkDomain) != 0) {

				$status = 'warning';
				$msg = 'domain is already register.';
				exit();
			}
		}
	}

	/* Encrypt password with salt key */

	$saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
	$enc_password = hash('sha256', $saltKey . $password);
	$index = ((isset($site_index)) ? $site_index : 0);

	/** Check is there any app in app table */

	if ($appDetails = $checkApp = mysqli_query($connection, "SELECT app_id as appID, theme_id as themeID, package_id as packageID FROM " . TABLE_PREFIX . "app INNER JOIN " . TABLE_PREFIX . "theme ON app_id=theme_app_id INNER JOIN " . TABLE_PREFIX . "package ON package_app_id=app_id WHERE app_path='system'")) {
		if (mysqli_num_rows($appDetails) != 0) {
			extract(mysqli_fetch_assoc($appDetails));
		}
	} else {
		echo mysqli_error($connection);
	}

	/* insert site profile into database */

	mysqli_query($connection, "START TRANSACTION");

	/* Insert data into user table */

	$insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_password, user_salt_key, user_reg_time, user_reg_ip) VALUES('administrator', '{$email}', '{$enc_password}', '$saltKey', NOW(), '{$_SERVER['REMOTE_ADDR']}')");
	$userID = mysqli_insert_id($connection);

	$insertSite = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site(site_owner_id, site_lang, site_domain, site_url, site_name, site_description, site_app_id, site_theme_id, site_index, site_expire_date, site_status) VALUES('{$userID}', '{$site_lang}', '" . SITE_DOMAIN . "', '" . SITE_DOMAIN . "', '{$site_name}', '{$site_desc}', '{$appID}', '{$themeID}', '{$index}', '{$expireDate}', 1)");
	$siteID = mysqli_insert_id($connection);

	$insertSiteApp = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_app(site_app_app_id, site_app_site_id, site_app_user_id, site_app_package_id, site_app_activation_time, site_app_expiry_date, site_app_status) VALUES('{$appID}', '{$siteID}', '{$userID}', '{$packageID}', NOW(), '{$expireDate}', 1)");

	$insertSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('{$siteID}', '{$userID}', 'administrator', 1)");

	$insertSiteSetting = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_setting(site_custom_domain, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_thumbnail_width, site_thumbnail_height, site_mainfest_json) VALUES(1, 10, 10, 10, 60, 223, 96, '" . json_encode(DEFAULT_MAINFEST_JSON, TRUE) . "', '" . DEFAULT_ROBOTS_TXT . "')");

	$insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '', '', '', '{$site_name}', '{$email}')");

	if ($insertUser && $insertSite && $insertSiteApp && $insertSiteAccess && $insertSiteSetting && $insertProfile) {

		mysqli_query($connection, 'COMMIT');

		/** Send User's Email Verification Mail */

		$verifyURL = SITE_DOMAIN . '/verify/email/?user_id=' . $saltKey . '&email=' . base64_encode($email);

		require_once EMAIL_TEMPLATE . 'add-new-user.php';

		$status = 'success';
		$msg = 'Website is now ready to use';
		$url = '/login/';
	} else {

		echo $insertUser . ' && ' . $insertSite . ' && ' . $insertSiteApp . ' && ' . $insertSiteAccess . ' && ' . $insertSiteSetting . ' && ' . $insertProfile;

		$status = 'error';
		$msg = 'Unable to process the installation request';
		mysqli_query($connection, 'ROLLBACK');
	}

	$result = array(
		'status' => ((isset($status)) ? $status : 'error'),
		'msg' => ((isset($msg)) ? $msg : 'Something is wrong while setup your website'),
		'url' => ((isset($url)) ? $url : '#')
	);

	echo json_encode($result, TRUE);
}
