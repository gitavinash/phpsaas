<?php

header('Content-Type: text/html; charset=utf-8');

// if ($_SERVER['HTTP_HOST'] == 'localhost') {
// 	$redirect = 'http://nms.local' . $_SERVER['REQUEST_URI'];
// 	header("Location: {$redirect}", TRUE, 301);
// 	exit();
// }

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
if (ACTIVE_URL == '/homepage/') {
	header("Location: /", TRUE, 301);
	exit();
}


// Site APP table name

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

function stripEditorToolkitMarkup($value)
{
	if (!is_string($value) || $value === '') {
		return $value;
	}

	if (
		stripos($value, 'rte-selection-tools') === false &&
		stripos($value, 'rte-selection-') === false &&
		stripos($value, 'toolbar-') === false &&
		stripos($value, 'contenteditable=') === false
	) {
		return $value;
	}

	// Remove accidental editor UI blocks/styles if they leak into submitted HTML.
	$value = preg_replace('/<div[^>]*class=["\'][^"\']*rte-selection-tools-floating[^"\']*["\'][^>]*>.*?<\/div>/is', '', $value);
	$value = preg_replace('/<style[^>]*id=["\']rte-selection-tools-style["\'][^>]*>.*?<\/style>/is', '', $value);
	$value = preg_replace('/<div[^>]*id=["\']toolbar-[^"\']+["\'][^>]*>.*?<\/div>/is', '', $value);
	$value = preg_replace('/<div[^>]*>\s*<span[^>]*>\s*Block\s*<\/span>[\s\S]*?<option[^>]*>\s*Heading\s*<\/option>[\s\S]*?<button[^>]*>\s*Left\s*<\/button>[\s\S]*?<button[^>]*>\s*Center\s*<\/button>[\s\S]*?<button[^>]*>\s*Right\s*<\/button>[\s\S]*?<button[^>]*>\s*Justify\s*<\/button>[\s\S]*?<\/div>/i', '', $value);
	$value = preg_replace('/\scontenteditable=["\'][^"\']*["\']/i', '', $value);

	return $value;
}

/** Convert all request method type parameter into strings */

if (sizeof($_REQUEST) != 0) {

	foreach ($_REQUEST as $key => $value) {

		if (!is_array($value)) {

			$secure[$key] = mysqli_real_escape_string($connection, stripEditorToolkitMarkup($value));
		} else {

			/** Convert string array to string  */

			foreach ($value as $arrayKey => $arrayValue) {
				$secure[$arrayKey] = mysqli_real_escape_string($connection, stripEditorToolkitMarkup($arrayValue));
			}
		}
	}

	extract($secure);
}

if ((isset($type))) {

	/** For all type of form process */

	if (DEVELOPMENT_MODE != true) {
		if (isset($recaptcha_token)) {
			$secretKey = GOOGLE_RECAPTCHA_SECRET_KEY;

			$verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
			$response = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $recaptcha_token);
			$responseKeys = json_decode($response, true);

			if (intval($responseKeys["success"]) !== 1) {
				http_response_code(400);
				echo json_encode([
					"status" => 'error',
					"msg" => "reCAPTCHA verification failed.",
					"recaptcha_token" => $recaptcha_token
				]);
				exit();
			} else {
				require_once 'formManager.php';
			}
		} else {
			require_once 'formManager.php';
		}
	} else {
		require_once 'formManager.php';
	}
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

// Internal app theme router

$themeRouterFile = USER_PATH . 'router.php';

switch ($urlStructure[0]) {

	case '':
		require_once 'homepage.php';
		break;

	// Service worker

	case 'sw.js':
		require_once 'service-worker.php';
		break;

	case 'offline.html':
		require_once 'offline.php';
		break;

	case 'firebase-messaging-sw.js':
		require_once 'service-worker.php';
		break;

	// Image render

	case 'viewmedia':
		require_once MODULES . 'media/image.php';
		break;

	// CSS Configuration

	case 'config.min.css':
		require_once 'css-theme.php';
		break;

	case 'website-setup':
		require_once 'website-setup.php';
		break;

	/** For Dashboard activity process */

	case 'dashboard':
		require_once 'admin.php';
		break;

	case 'oauth':
		require_once 'oauth.php';
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

	case 'webhook':
		require_once 'webhook.php';
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

	/** For all type of filter process */

	case 'filter':
		require_once 'filter/index.php';
		break;

	case 'tag':
		require_once 'tag.php';
		break;

	case 'author':
		require_once 'author.php';
		break;

	case 'category':
		require_once 'category.php';
		break;

	case 'location':
		require_once 'location.php';
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

	case 'checkout':
		require_once 'checkout.php';
		break;

	case 'cart':
		require_once 'cart.php';
		break;

	case 'shipping':
		require_once 'shipping.php';
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