<?php

session_destroy();

if (isset($_SERVER['HTTP_COOKIE'])) {

	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);

	// Delete active session from database

	if (isset($_COOKIE['user_data'])) {
		$user_salt_key = base64_decode($_COOKIE['user_data']);
		if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}'")) {
			if (mysqli_num_rows($checkUserIsReal) != 0) {
				extract(mysqli_fetch_assoc($checkUserIsReal));
				$deviceID = $_COOKIE['device_id'];
				if ($checkUserSession = mysqli_query($connection, "SELECT us_id FROM " . TABLE_PREFIX . "user_sessions WHERE us_active_device='{$deviceID}' AND us_user_id='{$user_id}' AND us_site_id=" . SITE_ID)) {
					if (mysqli_num_rows($checkUserSession) != 0) {
						extract(mysqli_fetch_assoc($checkUserSession));
						if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user_sessions WHERE us_id='{$us_id}' AND us_site_id=" . SITE_ID));
					}
				}
			}
		}
	}

	foreach ($cookies as $cookie) {

		// Destroy all session and cache for logout the session

		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time() - 1000);
		setcookie($name, '', time() - 1000, '/', MAIN_DOMAIN);
	}
}

if (file_exists(USER_PATH . 'logout.php')) {

	include_once USER_PATH . 'logout.php';
} else {

	$redirect_url = urlencode((isset($redirect_url)) ? (($redirect_url != '') ? $redirect_url : '/dashboard/') : '/dashboard/');

	$authByParam = (isset($authby)) ? '&authby=mobile' : '';

	header("Location: /login/?logout=true{$authByParam}&redirect_url=" . urlencode($redirect_url) . "&timestamp=" . time());
	exit();
}