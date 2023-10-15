<?php

header('Content-Type: text/html; charset=utf-8');

/** Check website domain status */

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/siteStatus.php';

/** Check configuration file is exist or not */

if (file_exists('lib/configuration/index.php')) {

	/** Including configuration file file  */

	require_once 'lib/configuration/index.php';
} else {

	echo '<h1>Something is wrong while including configuration files</h1>';
	exit();
}

/** Convert all request method type parameter into strings */

if (sizeof($_REQUEST) != 0) {

	foreach ($_REQUEST as $key => $value) {

		if (!is_array($value)) {

			$secure[$key] = mysqli_real_escape_string($connection, $value);
		} else {

			/** Convert string array to string  */

			foreach ($value as $arrayKey => $arrayValue) {

				$secure[$arrayKey] = mysqli_real_escape_string($connection, $arrayValue);
			}
		}
	}

	extract($secure);
}

if ((isset($type))) {

	/** For all type of form process */

	require_once 'formManager.php';
}

/** Redirect all double slash url request to single slash */

if (strpos(BASE_URL, '//') !== FALSE) {

	$redirectURL = str_replace('//', '/', BASE_URL);
	header("Location: $redirectURL", TRUE, 301);
	exit();
} else {

	/** Redirect non end slash url to slash url */

	if (!strpos(BASE_URL, ".") !== false) {

		if (substr(BASE_URL, -1) != '/') {

			$redirectURL = BASE_URL . '/';
			header("Location: $redirectURL", TRUE, 301);
			exit();
		}
	}
}

/** Actual Base URL */

$url = BASE_URL;

switch ($urlStructure[0]) {

	case '':
		require_once 'homepage.php';
		break;

		/** For Dashboard activity process */

	case 'dashboard':
		require_once 'admin.php';
		break;

	case str_replace('/', '', LOGOUT_PATH):
		require_once 'logout.php';
		break;

	case str_replace('/', '', LOGIN_PATH):
		require_once 'login.php';
		break;

	case str_replace('/', '', FORGOT_PASSWORD_PATH):
		require_once 'forgot-password.php';
		break;

	case str_replace('/', '', REGISTRATION_PATH):
		require_once 'register.php';
		break;

	case 'api':
		require_once 'api.php';
		break;

	case 'status':
		require_once STATUS;
		break;

	case 'amp':
		require_once 'amp.php';
		break;

	case 'install':
		require_once 'install.php';
		break;

		/** For all type of verification process */

	case 'verify':
		require_once 'verify.php';
		break;

	case 'email':
		require_once 'email.php';
		break;

		/** For all type of search process */

	case 'search':
		require_once 'search/index.php';
		break;

	case 'tag':
		require_once 'tag.php';
		break;

	case 'category':
		require_once 'category.php';
		break;

	case 'feed':
		require_once 'feed.php';
		break;

	case 'sitemap':
		require_once 'sitemap.php';
		break;

		/** For all type of payment process */

	case 'payment':
		require_once 'payment.php';
		break;

	default:
		/** Check Cache is available or not */
		require_once ROUTER . 'cache.php';
		break;
}

if (DEVELOPMENT_MODE == TRUE && $urlStructure[0] != 'api') {

	// Display Script End time
	$scriptEndTime = microtime(true);

	//dividing with 60 will give the execution time in minutes other wise seconds
	$execution_time = $scriptEndTime - $scriptStartTime;

	//execution time of the script
	echo '<b>Total Execution Time:</b> ' . $execution_time . ' Seconds';
}

mysqli_close($connection);

exit();
