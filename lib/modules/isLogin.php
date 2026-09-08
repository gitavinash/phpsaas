<?php

$actualUrl = $_SERVER['REQUEST_URI'] ?? (defined('ACTIVE_URL') ? ACTIVE_URL : '');
$isApiV3 = (stripos($actualUrl, '/api/v3/') !== false)
	|| (isset($_SERVER['SCRIPT_NAME']) && stripos($_SERVER['SCRIPT_NAME'], '/api/v3/') !== false)
	|| (isset($_SERVER['PHP_SELF']) && stripos($_SERVER['PHP_SELF'], '/api/v3/') !== false);

if (!function_exists('isLoginHandleExit')) {
	function isLoginHandleExit($isApiV3, $apiStatusCode, $apiMessage, $redirectUrl = null, $redirectHttpCode = null) {
		if ($isApiV3) {
			if (!headers_sent()) {
				header('Content-Type: application/json; charset=utf-8');
				http_response_code($apiStatusCode);
			}
			echo json_encode([
				'status' => 'error',
				'status_code' => $apiStatusCode,
				'message' => $apiMessage,
				'msg' => $apiMessage
			]);
			exit();
		} else {
			if ($redirectUrl === null) {
				echo $apiMessage;
				exit();
			}
			if ($redirectHttpCode !== null) {
				header("Location: " . $redirectUrl, true, $redirectHttpCode);
			} else {
				header("Location: " . $redirectUrl);
			}
			exit();
		}
	}
}

if (!isset($connection)) {
	$dbConnectionFile = dirname(__DIR__) . '/../dbConfig.php';
	if (!file_exists($dbConnectionFile)) {
		isLoginHandleExit($isApiV3, 500, "Configuration file not found\n{$dbConnectionFile}");
	} else {
		if (!$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
			isLoginHandleExit($isApiV3, 500, "Database connection failed\n");
		}
	}
}

$isMobile = ((isset($redirectToMobileAuth)) ? 'authby=mobile' : '');
$activeUrlStr = defined('ACTIVE_URL') ? ACTIVE_URL : ($_SERVER['REQUEST_URI'] ?? '');

if (!isset($_COOKIE['user_data'])) {
	$regType = ((isset($regType)) ? $regType : 'subscriber');
	// echo "invalid data cookie";
	isLoginHandleExit($isApiV3, 401, 'Authentication required / Session expired', "/logout/?redirect_url=" . $activeUrlStr . "&{$isMobile}&regType={$regType}");
} else {

	$user_salt_key = base64_decode($_COOKIE['user_data']);

	$activeSiteID = ((SITE_ID == 234 || SITE_ID == 49) ? " IN (49, 234)" : "=" . SITE_ID);

	$checkUserAccessQuery = "SELECT user_id, user_email, user_mobile, user_salt_key, user_status, site_domain, site_url, site_name, site_icon, site_status, site_app_id, site_access_site_id, site_access_type, site_dashboard_version, profile_display_name as logginUserName, company_id, vendor_id, profile_image FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "user ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_access_site_id=site_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id LEFT JOIN " . TABLE_PREFIX . "company ON company_user_id=user_id LEFT JOIN " . TABLE_PREFIX . "vendor ON vendor_user_id=user_id WHERE user_salt_key='{$user_salt_key}' AND site_access_site_id{$activeSiteID}";

	if ($checkUserIsReal = mysqli_query($connection, $checkUserAccessQuery)) {

		if (mysqli_num_rows($checkUserIsReal) != 0) {

			extract(mysqli_fetch_assoc($checkUserIsReal));

			$encryptEmail = base64_encode($user_email);

			if (isset($_COOKIE['device_id'])) {
				if ($checkSession = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$user_id}' AND us_active_device='{$_COOKIE['device_id']}' AND us_site_id{$activeSiteID}")) {
					if (mysqli_num_rows($checkSession) == 0) {
						isLoginHandleExit($isApiV3, 401, 'Invalid session token or expired', "/logout/?msg=invalid+session+token+or+expire", 301);
					}
				}
			} else {
				// echo "Invalid deviceid";
				isLoginHandleExit($isApiV3, 401, 'Session token not found', "/logout/?msg=session_token%20not%20found", 301);
			}

			if ($user_status != 1 && SITE_APP_ID != 3) {
				isLoginHandleExit($isApiV3, 403, 'Account verification required', "/verify-account/?email={$encryptEmail}", 301);
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

				/** Sync profile image and display name cookies if needed */
				if (isset($profile_image)) {
					$cookieImg = isset($_COOKIE['user_image']) ? base64_decode($_COOKIE['user_image']) : null;
					if ($cookieImg !== $profile_image) {
						@setcookie('user_image', base64_encode($profile_image), time() + 30 * 24 * 60 * 60, '/');
						$_COOKIE['user_image'] = base64_encode($profile_image);
					}
				}
				if (isset($logginUserName)) {
					$cookieName = isset($_COOKIE['user_name']) ? base64_decode($_COOKIE['user_name']) : null;
					if ($cookieName !== $logginUserName) {
						@setcookie('user_name', base64_encode($logginUserName), time() + 30 * 24 * 60 * 60, '/');
						$_COOKIE['user_name'] = base64_encode($logginUserName);
					}
				}

				/** Define new cookie for logged in user */

				if (!isset($_COOKIE['site_app'])) {
					if ($isApiV3) {
						isLoginHandleExit($isApiV3, 401, 'Site application session not found');
					} else {
						setcookie('site_app', base64_encode($site_app), time() + 30 * 24 * 60 * 60, '/', SITE_DOMAIN);
						header("Location: /login/");
						exit();
					}
				} else {

					/** Check website status */

					if ($site_status != 1) {

						if ($site_access_type == 'administrator') {

							/** if user type is Administrator Redirect to website setup page */
							isLoginHandleExit($isApiV3, 403, 'Site setup required', "/website-setup/?setup_id={$encryptEmail}");
						} else {

							/** Show Access Forbidden page to the use */
							isLoginHandleExit($isApiV3, 403, 'Access forbidden', (defined('ERROR_403') ? ERROR_403 : '/error-403/'), 301);
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
						if ($site_name == '' && $site_icon == '' && $activeUrlStr != '/dashboard/setting/?type=configuration') {
							isLoginHandleExit($isApiV3, 403, 'Site configuration incomplete', "/dashboard/setting/?type=configuration", 301);
						}
					} else {
						if ($site_name == '' && $site_icon == '' && $activeUrlStr != '/dashboard/setting/configuration/') {
							isLoginHandleExit($isApiV3, 403, 'Site configuration incomplete', "/dashboard/setting/configuration/", 301);
						}
					}


					/** Set login is valid */

					setcookie('isLogin', 1, time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
				}
			}
		} else {
			// echo "invalid user found";
			isLoginHandleExit($isApiV3, 401, 'Invalid user credentials', "/logout/?{$isMobile}");
		}
	} else {
		isLoginHandleExit($isApiV3, 500, 'Something went wrong while checking user session');
	}
}

