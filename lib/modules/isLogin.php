<?php

if (!isset($connection)) {
	$dbConnectionFile = dirname(__DIR__) . '/../dbConfig.php';
	if (!file_exists($dbConnectionFile)) {
		exit("Configuration file not found\n{$dbConnectionFile}");
	} else {
		if (!$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
			exit("Database connection failed\n");
		}
	}
}

$isMobile = ((isset($redirectToMobileAuth)) ? 'authby=mobile' : '');

if (!isset($_COOKIE['user_data'])) {
	$regType = ((isset($regType)) ? $regType : 'subscriber');
	// echo "invalid data cookie";
	header("Location: /logout/?redirect_url=" . ACTIVE_URL . "&{$isMobile}&regType={$regType}");
	exit();
} else {

	$user_salt_key = base64_decode($_COOKIE['user_data']);

	$activeSiteID = ((SITE_ID == 234 || SITE_ID == 49) ? " IN (49, 234)" : "=" . SITE_ID);

	$checkUserAccessQuery = "SELECT user_id, user_email, user_mobile, user_salt_key, user_status, site_domain, site_url, site_name, site_icon, site_status, site_app_id, site_access_site_id, site_access_type, site_dashboard_version, profile_display_name as logginUserName, company_id, vendor_id FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "user ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_access_site_id=site_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id LEFT JOIN " . TABLE_PREFIX . "company ON company_user_id=user_id LEFT JOIN " . TABLE_PREFIX . "vendor ON vendor_user_id=user_id WHERE user_salt_key='{$user_salt_key}' AND site_access_site_id{$activeSiteID}";

	if ($checkUserIsReal = mysqli_query($connection, $checkUserAccessQuery)) {

		if (mysqli_num_rows($checkUserIsReal) != 0) {

			extract(mysqli_fetch_assoc($checkUserIsReal));

			$encryptEmail = base64_encode($user_email);

			if (isset($_COOKIE['device_id'])) {
				if ($checkSession = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$user_id}' AND us_active_device='{$_COOKIE['device_id']}' AND us_site_id{$activeSiteID}")) {
					if (mysqli_num_rows($checkSession) == 0) {
						header("Location: /logout/?msg=invalid+session+token+or+expire", true, 301);
						exit();
					}
				}
			} else {
				// echo "Invalid deviceid";
				header("Location: /logout/?msg=session_token%20not%20found", true, 301);
				exit();
			}

			if ($user_status != 1 && SITE_APP_ID != 3) {

				header("Location: /verify-account/?email={$encryptEmail}", true, 301);
				exit();
			} else {

				/** Define new constant variable */

				if (!defined('USER_ID')) {
					define('USER_ID', $user_id);
				}
				if (!defined('COMPANY_ID')) {
					define('COMPANY_ID', (($company_id != '') ? $company_id : '0'));
				}
				if (!defined('VENDOR_ID')) {
					define('VENDOR_ID', (($vendor_id != '') ? $vendor_id : '0'));
				}

				/** Define new cookie for logged in user */

				if (!isset($_COOKIE['site_app'])) {

					setcookie('site_app', base64_encode($site_app), time() + 30 * 24 * 60 * 60, '/', SITE_DOMAIN);
					header("Location: /login/");
					exit();
				} else {

					/** Check website status */

					if ($site_status != 1) {

						if ($site_access_type == 'administrator') {

							/** if user type is Administrator Redirect to website setup page */

							header("Location: /website-setup/?setup_id={$encryptEmail}");
							exit();
						} else {

							/** Show Access Forbidden page to the use */

							header("Location: " . ERROR_403, TRUE, 301);
							exit();
						}
					} else {

						// /** User is for current website */

						// if (SITE_ID != 49 && SITE_ID != 234) {
						// 	if ($site_access_site_id != $activeSiteID) {

						// 		if ($site_domain != '') {

						// 			/** Redirect user to its own custom domain website */

						// 			header("Location: http://{$site_domain}/dashboard/", TRUE, 301);
						// 			exit();
						// 		} else {

						// 			/** Redirect user to its own sub domain website */

						// 			header("Location: http://{$site_url}/dashboard/", TRUE, 301);
						// 			exit();
						// 		}
						// 	}
						// }
					}

					define('SITE_DASHBOARD_VERSION', $site_dashboard_version);

					/** Redirect to complete profile page */

					if ($site_dashboard_version == 1) {
						if ($site_name == '' && $site_icon == '' && ACTIVE_URL != '/dashboard/setting/?type=configuration') {

							header("Location: /dashboard/setting/?type=configuration", TRUE, 301);
							exit();
						}
					} else {

						if ($site_name == '' && $site_icon == '' && ACTIVE_URL != '/dashboard/setting/configuration/') {

							header("Location: /dashboard/setting/configuration/", TRUE, 301);
							exit();
						}
					}


					/** Set login is valid */

					setcookie('isLogin', 1, time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
				}
			}
		} else {
			// echo "invalid user found";
			header("Location: /logout/?{$isMobile}");
			exit();
		}
	} else {

		echo 'something is wrong while checking user session';
		exit();
	}
}
