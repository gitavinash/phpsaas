<?php

$ssoModuleFile = defined('MODULES') ? MODULES . 'sso.php' : __DIR__ . '/sso.php';
if (file_exists($ssoModuleFile)) {
	require_once $ssoModuleFile;
}

$actualUrl = (string) ($_SERVER['REQUEST_URI'] ?? (defined('ACTIVE_URL') ? ACTIVE_URL : ''));
$isApiV3 = (stripos($actualUrl, '/api/v3/') !== false)
	|| (isset($_SERVER['SCRIPT_NAME']) && stripos((string) $_SERVER['SCRIPT_NAME'], '/api/v3/') !== false)
	|| (isset($_SERVER['PHP_SELF']) && stripos((string) $_SERVER['PHP_SELF'], '/api/v3/') !== false);

$isApiPath = $isApiV3
	|| (stripos($actualUrl, '/api/') !== false)
	|| (isset($_SERVER['SCRIPT_NAME']) && stripos((string) $_SERVER['SCRIPT_NAME'], '/api/') !== false)
	|| (isset($_SERVER['PHP_SELF']) && stripos((string) $_SERVER['PHP_SELF'], '/api/') !== false);
$isAjaxReq = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$acceptHeader = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
$wantsJson = $isApiPath || $isAjaxReq || (strpos($acceptHeader, 'application/json') !== false && strpos($acceptHeader, 'text/html') === false);
$reqMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$isInteractiveGet = ($reqMethod === 'GET') && !$wantsJson;

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
$activeUrlStr = defined('ACTIVE_URL') ? ACTIVE_URL : ($_SERVER['REQUEST_URI'] ?? '/dashboard/');
$safeReturnPath = iswNormalizeRedirectPath($activeUrlStr, '/dashboard/');
$regType = ((isset($regType)) ? $regType : 'subscriber');
$ssoDone = isset($_GET['sso_done']);

$sessionValidation = iswValidateCurrentSiteSession($connection, (int) SITE_ID);

if (empty($sessionValidation['ok'])) {
	$reason = (string) ($sessionValidation['reason'] ?? 'missing_cookies');

	if ($reason === 'unverified_user') {
		$encryptEmail = base64_encode((string) ($sessionValidation['user']['user_email'] ?? ''));
		isLoginHandleExit($isApiV3, 403, 'Account verification required', "/verify-account/?email={$encryptEmail}", 301);
	}

	if (in_array($reason, ['disabled', 'no_access'], true)) {
		// Preserve any sibling session cookies on shared root domains while denying this workspace cleanly
		$denyMsg = ($reason === 'disabled')
			? 'Your workspace access has been deactivated.'
			: 'You do not have access to this workspace.';
		$denyUrl = '/login/?sso_done=1&redirect_url=' . urlencode($safeReturnPath) . '&alertMsg=' . urlencode($denyMsg);
		isLoginHandleExit($isApiV3, 403, $denyMsg, $denyUrl, 302);
	}

	// Clear stale local auth cookies that failed validation so /login/ does not bounce
	if (isset($_COOKIE['user_data']) || isset($_COOKIE['device_id']) || isset($_COOKIE['isLogin'])) {
		iswClearAllAuthCookies(false);
	}

	// Cross-site SSO discovery for interactive browser GET requests
	if ($isInteractiveGet && !$ssoDone) {
		$siblings = iswGetSiblingSitesWithActiveSessions($connection, (int) SITE_ID);
		if (!empty($siblings)) {
			header('Location: /sso/check/?redirect_url=' . urlencode($safeReturnPath), true, 302);
			exit();
		}
	}

	$mobileQuery = ($isMobile !== '') ? "&{$isMobile}" : '';
	$loginFallbackUrl = '/login/?sso_done=1&redirect_url=' . urlencode($safeReturnPath) . $mobileQuery . '&regType=' . urlencode((string) $regType);
	isLoginHandleExit($isApiV3, 401, 'Authentication required / Session expired', $loginFallbackUrl, 302);
}

// Valid authenticated session — export legacy globals and constants expected by downstream views
$userRow   = $sessionValidation['user'] ?? [];
$siteRow   = $sessionValidation['site'] ?? [];
$accessRow = $sessionValidation['access'] ?? [];

$user_id                = (int) ($userRow['user_id'] ?? 0);
$user_email             = (string) ($userRow['user_email'] ?? '');
$user_mobile            = (string) ($userRow['user_mobile'] ?? '');
$user_salt_key          = (string) ($userRow['user_salt_key'] ?? '');
$user_status            = (int) ($userRow['user_status'] ?? 0);
$site_domain            = (string) ($siteRow['site_domain'] ?? '');
$site_url               = (string) ($siteRow['site_url'] ?? '');
$site_name              = (string) ($siteRow['site_name'] ?? '');
$site_icon              = (string) ($siteRow['site_icon'] ?? '');
$site_status            = (int) ($siteRow['site_status'] ?? 0);
$site_app_id            = (int) ($siteRow['site_app_id'] ?? 0);
$site_access_site_id    = (int) ($accessRow['site_access_site_id'] ?? SITE_ID);
$site_access_type       = strtolower(trim((string) ($accessRow['site_access_type'] ?? 'visitor')));
$site_access_meta       = (string) ($accessRow['site_access_meta'] ?? '');
$site_dashboard_version = (int) ($siteRow['site_dashboard_version'] ?? 1);
$site_app               = trim((string) ($siteRow['site_app'] ?? (defined('SITE_APP') ? SITE_APP : 'system')));
$logginUserName         = trim((string) ($userRow['logginUserName'] ?? ''));
if ($logginUserName === '') {
	$logginUserName = trim(((string) ($userRow['profile_first_name'] ?? '')) . ' ' . ((string) ($userRow['profile_last_name'] ?? '')));
}
if ($logginUserName === '') {
	$logginUserName = trim((string) ($userRow['user_username'] ?? 'Team Member'));
}
$company_id             = $userRow['company_id'] ?? '';
$vendor_id              = $userRow['vendor_id'] ?? '';
$profile_image          = trim((string) ($userRow['profile_image'] ?? ''));
$encryptEmail           = base64_encode($user_email);

if (!defined('USER_ID')) {
	define('USER_ID', $user_id);
}
if (!defined('COMPANY_ID')) {
	define('COMPANY_ID', (($company_id != '') ? $company_id : '0'));
}
if (!defined('VENDOR_ID')) {
	define('VENDOR_ID', (($vendor_id != '') ? $vendor_id : '0'));
}

// Inspect existing cookies BEFORE syncing so shared-root subdomain role switches trigger a clean reboot
$cookieUserType = isset($_COOKIE['user_type']) ? base64_decode($_COOKIE['user_type']) : null;
$cookieSiteApp  = isset($_COOKIE['site_app']) ? base64_decode($_COOKIE['site_app']) : null;
$cookieSiteId   = isset($_COOKIE['user_site_id']) ? base64_decode($_COOKIE['user_site_id']) : null;
$cookieImg      = isset($_COOKIE['user_image']) ? base64_decode($_COOKIE['user_image']) : null;
$cookieName     = isset($_COOKIE['user_name']) ? base64_decode($_COOKIE['user_name']) : null;

$needsCookieSync = ($cookieUserType !== $site_access_type)
	|| ($cookieSiteApp !== $site_app)
	|| ($cookieSiteId !== (string) SITE_ID)
	|| ($cookieImg !== $profile_image)
	|| ($cookieName !== $logginUserName)
	|| !isset($_COOKIE['isLogin']);

$needsRoleReboot = $isInteractiveGet
	&& !isset($_GET['_sso_sync'])
	&& defined('USER_TYPE')
	&& (USER_TYPE !== $site_access_type);

if ($needsCookieSync) {
	iswSetSiteAuthCookies($sessionValidation, (string) $sessionValidation['device_id'], 30);
}

if ($needsRoleReboot) {
	$sep = (strpos($safeReturnPath, '?') !== false) ? '&' : '?';
	header("Location: {$safeReturnPath}{$sep}_sso_sync=1", true, 302);
	exit();
}

/** Check website status */
if ($site_status != 1) {
	if ($site_access_type == 'administrator') {
		isLoginHandleExit($isApiV3, 403, 'Site setup required', "/website-setup/?setup_id={$encryptEmail}");
	} else {
		isLoginHandleExit($isApiV3, 403, 'Access forbidden', (defined('ERROR_403') ? ERROR_403 : '/error-403/'), 301);
	}
}

if (!defined('SITE_DASHBOARD_VERSION')) {
	define('SITE_DASHBOARD_VERSION', $site_dashboard_version);
}

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


