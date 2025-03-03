<?php

if (!isset($_COOKIE['user_data'])) {

	header("Location: /logout/?redirect_url=" . ACTIVE_URL);
	exit();
} else {

	$user_salt_key = base64_decode($_COOKIE['user_data']);

	if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id, user_email, user_salt_key, user_status, site_domain, site_url, site_name, site_icon, site_status, site_app_id, site_access_site_id, site_access_type, site_dashboard_version, profile_display_name as logginUserName FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "user ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_access_site_id=site_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_salt_key='{$user_salt_key}' AND site_access_site_id=" . SITE_ID)) {

		if (mysqli_num_rows($checkUserIsReal) != 0) {

			extract(mysqli_fetch_assoc($checkUserIsReal));

			$encryptEmail = base64_encode($user_email);

			if ($user_status != 1 && SITE_APP_ID!=3) {

				header("Location: /verify-account/?email={$encryptEmail}");
				exit();
			} else {

				/** Define new constant variable */

				define('USER_ID', $user_id);

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

						/** User is for current website */

						if ($site_access_site_id != SITE_ID) {

							if ($site_domain != '') {

								/** Redirect user to its own custom domain website */

								header("Location: http://{$site_domain}/dashboard/", TRUE, 301);
								exit();
							} else {

								/** Redirect user to its own sub domain website */

								header("Location: http://{$site_url}/dashboard/", TRUE, 301);
								exit();
							}
						}
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

			header("Location: /logout/");
			exit();
		}
	} else {

		echo 'something is wrong while checking user session';
		exit();
	}
}