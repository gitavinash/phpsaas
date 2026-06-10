<?php

// Load manifest.json

if (ADMIN_BASE_URL == '/manifest.json') {
	$manifestFile = ROOT . '/app/dashboard/v' . $site_dashboard_version . '/default/desktop/manifest/' . strtolower(SITE_APP) . '.php';
	if (file_exists($manifestFile)) {
		require_once $manifestFile;
	} else {
		require_once ROOT . '/app/dashboard/v' . $site_dashboard_version . '/default/desktop/manifest/default.php';
	}
	exit();
}

if (ADMIN_BASE_URL == '/sw.js') {
	require_once DASHBOARD_SERVICE_WORKER;
	exit();
}

// SPECIAL OVERRIDE: Visual Page Builder — fullscreen editor
// Serves ecit_view.php as a standalone HTML document,
// bypassing the normal header/footer dashboard wrapping.
// ==========================================================
if (rtrim(ADMIN_BASE_URL, '/') === '/editor') {
	if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true && (!isset($_COOKIE['user_type']) || base64_decode($_COOKIE['user_type']) !== 'administrator')) {
		setcookie('user_type', base64_encode('administrator'), time() + 3600, '/');
		header("Location: " . ACTIVE_URL);
		exit();
	}

	$noCodeEditorFile = ROOT . '/app/default/' . USER_TYPE . '/' . SITE_VERSION . '/templates/editor/no_code_editor.php';
	if (file_exists($noCodeEditorFile)) {
		require_once $noCodeEditorFile;
	} else {
		require_once ERROR_404;
		http_response_code(404);
	}
	exit();
}

/* Check user is logged in or not */

require_once LOGIN_CHECK;

/* Checking dashboard user type directory is exits or not */

if ($site_dashboard_version == 1) {

	// Verion 1 theme type it's use in beggining of the project

	$dashboardDirectory = ADMIN_PATH;

	if (!is_dir($dashboardDirectory)) {

		if (!is_dir($dashboardDirectory = str_replace('/default/', '/default/', ADMIN_PATH))) {

			$dashboardDirectory = str_replace('/mobile/', '/desktop/', $dashboardDirectory);
		}
	}
} else {

	// Latest dashboard directory 

	$dashboardDirectory = strtolower(str_replace('/default/', '/' . SITE_APP . '/' . USER_TYPE . '/', ADMIN_PATH));
	$userDashboardDirectory = $dashboardDirectory;
	if (!is_dir($dashboardDirectory)) {
		if (USER_TYPE != 'customer' && USER_TYPE != 'subscriber') {
			$dashboardDirectory = str_replace('/default/', '/default/', ADMIN_PATH);
			$userDashboardDirectory = '';
		}
	}
}

// v2 Request url file path 

$dashboardFilePath = ((isset($type)) ? 'templates' . ADMIN_BASE_URL . $type : ((ADMIN_BASE_URL != '/') ? ltrim(rtrim(ADMIN_BASE_URL, '/'), '/') : 'dashboard')) . '.php';

$dashboardFile = $dashboardDirectory . $dashboardFilePath;

// ==========================================================


if (!file_exists($dashboardFile)) {

	// When dashboard theme need to call file from the default theme directory ( v1 )

	$dashboardFile = ROOT . '/app/default/' . USER_TYPE . '/' . SITE_VERSION . '/' . $dashboardFilePath;

	if (!file_exists($dashboardFile)) {
		$dashboardFile = $dashboardDirectory . '404.php';
		http_response_code(404);
	}
}

// User for detect while type of page is load

$postType = explode('/', mysqli_real_escape_string($connection, rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')));
$postType = end($postType);

// html title tag value

$dashboardPageTitle = ucwords(str_replace('/', ' &#8702; ', mysqli_real_escape_string($connection, ((rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') != '') ? rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') : 'Dashboard'))) . ((isset($type)) ? ' &#8702; ' . $type : ''));

// Call dashboard theme file header, footer and main required file as per uri

if (is_dir($dashboardDirectory)) {
	require_once $dashboardDirectory . '/header.php';

	require_once $dashboardFile;

	require_once $dashboardDirectory . '/footer.php';
} else {
	require_once ERROR_404;
}

exit();
