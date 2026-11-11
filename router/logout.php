<?php

require_once UTIL_FUNCTIONS;
require_once MODULES . 'sso.php';

// Capture user & device before clearing cookies so sessions are revoked across the entire parent group
$rawUserCookie = isset($_COOKIE['user_data']) ? trim((string) $_COOKIE['user_data']) : '';
$deviceID = isset($_COOKIE['device_id']) ? trim((string) $_COOKIE['device_id']) : '';

if ($rawUserCookie !== '' && $deviceID !== '') {
	$user_salt_key = base64_decode($rawUserCookie, true);
	if ($user_salt_key === false || $user_salt_key === '') {
		$user_salt_key = base64_decode($rawUserCookie);
	}
	$user_salt_key = trim((string) $user_salt_key);

	if ($user_salt_key !== '') {
		$escSalt = mysqli_real_escape_string($connection, $user_salt_key);
		if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$escSalt}' LIMIT 1")) {
			if (mysqli_num_rows($checkUserIsReal) != 0) {
				$userRow = mysqli_fetch_assoc($checkUserIsReal);
				iswRevokeGroupUserSessions($connection, (int) $userRow['user_id'], $deviceID, (int) SITE_ID);
			}
		}
	}
}

clearAllCookies();

if (session_status() === PHP_SESSION_ACTIVE) {
	@session_destroy();
}

if (file_exists(USER_PATH . 'logout.php')) {

	include_once USER_PATH . 'logout.php';
} else {

	$safeRedirect = iswNormalizeRedirectPath($redirect_url ?? ($_GET['redirect_url'] ?? '/dashboard/'), '/dashboard/');

	$authByParam = (isset($authby)) ? '&authby=mobile' : '';
	$regTypeVal = ((isset($regType)) ? $regType : 'subscriber');
	header("Location: /login/?logout=true&sso_done=1{$authByParam}&regType=" . urlencode((string) $regTypeVal) . "&redirect_url=" . urlencode($safeRedirect) . "&timestamp=" . time());
	exit();
}