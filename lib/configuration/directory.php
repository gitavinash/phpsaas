<?php

/* System default modules Directory and file */

define('CONFIG', ROOT . '/lib/configuration/');
define('MODULES', ROOT . '/lib/modules/');
define('FUNCTIONS', ROOT . '/lib/functions/');
define('API_MODULES', ROOT . '/api/');
define('ROUTER', ROOT . '/router/');
define('RESOURCE', ROOT . '/lib/resource/');
define('SOCIAL_LOGIN', RESOURCE . 'social-login/');
define('PAYMENT_GATEWAY', RESOURCE . 'payment-gateway/');
define('NOTIFICATION', RESOURCE . 'notification/');
define('QR_CODE', RESOURCE . 'qrcode/');
define('CONTLO', RESOURCE . 'contlo/');
define('RECAPTCHA', RESOURCE . 'recaptcha/');
define('CONTLO_API_URL', 'https://api.contlo.com/v1/track');

/** Meta and Structure Data Resource */

define('META_DIR', RESOURCE . 'meta/');
define('DEFAULT_META', META_DIR . 'index.php');
define('SCHEMA_DATA', RESOURCE . 'schema/');

/** Log directory */

define('ERROR_LOG', ROOT . '/logs/error/');

/** Defualt UI Resource */

define('UI_LIBRARY', str_replace(ROOT, '', RESOURCE) . 'ui/');

if (!is_dir('app/' . SITE_APP . '/' . SITE_VERSION . '/')) {

	/** Set default app library to desktop version */

	define('APP_LIBRARY', 'app/' . SITE_APP . '/desktop/');
} else {

	define('APP_LIBRARY', 'app/' . SITE_APP . '/' . SITE_VERSION . '/');
}

define('JS_LIBRARY', UI_LIBRARY . 'js/');
define('APP_JS_LIBRARY', JS_LIBRARY . APP_LIBRARY);
define('CSS_LIBRARY', UI_LIBRARY . 'css/');
define('APP_CSS_LIBRARY', CSS_LIBRARY . APP_LIBRARY);
define('IMG_LIBRARY', UI_LIBRARY . 'img/');
define('VIDEO_LIBRARY', UI_LIBRARY . 'video/');
define('APP_IMG_LIBRARY', IMG_LIBRARY . APP_LIBRARY);
define('PLUGIN_LIBRARY', UI_LIBRARY . 'plugin/');
define('APP_PLUGIN_LIBRARY', PLUGIN_LIBRARY . APP_LIBRARY);

/** Default jQuery Editor */

define('EDITOR', PLUGIN_LIBRARY . 'editor/editor.min.js');

/** Default Service Worker */

define('DASHBOARD_SERVICE_WORKER', RESOURCE . 'service-worker/admin/sw.js');
define('VISITOR_SERVICE_WORKER', RESOURCE . 'service-worker/visitor/sw.min.js');
define('MAINFEST_JSON', SITE_URL . '/mainfest.json');

/** System default and usefull modules */

define('APP_MODULES', MODULES . SITE_APP . '/');
define('IMAGE_UPLOADER', MODULES . 'imageUploader.php');
define('LOGIN_CHECK', MODULES . 'isLogin.php');
define('SITE_STATUS', MODULES . 'siteStatus.php');

/** System default and usefull modules */

define('UTIL_FUNCTIONS', FUNCTIONS . '/utils/');

/** Font Directory */

define('FONT_LIBRARY', RESOURCE . 'fonts/');

/** API Directory */

define('API_APP_MODULES', API_MODULES . SITE_APP . '/');	// Use for API router
define('API_LIB_MODULES', MODULES . 'api/');	// User for API function and other resource

/* Email Directory Configuration */

define('SEND_EMAIL', CONFIG . 'mail/send-email.php');
define('EMAIL_TEMPLATE', RESOURCE . 'email/');
define('APP_EMAIL_TEMPLATE', EMAIL_TEMPLATE . SITE_APP . '/' . USER_TYPE . '/');
define('ADMIN_EMAIL_TEMPLATE', EMAIL_TEMPLATE . SITE_APP . '/admin/');

/* SMS Directory Configuration */

define('SEND_SMS', CONFIG . 'sms/send-sms.php');
define('SMS_TEMPLATE', RESOURCE . 'sms/');
define('APP_SMS_TEMPLATE', SMS_TEMPLATE . SITE_APP . '/default/');
define('ADMIN_SMS_TEMPLATE', SMS_TEMPLATE . SITE_APP . '/admin/');

/* WhatsApp Directory Configuration */

define('WHATSAPP_TEMPLATE', RESOURCE . 'whatsapp/');
define('WHATSAPP_TEMPLATE_ADMIN', WHATSAPP_TEMPLATE . SITE_APP . '/administrator/');
define('WHATSAPP_TEMPLATE_VISITOR', WHATSAPP_TEMPLATE . SITE_APP . '/visitor/');

/* Installation Directory  */

define('INSTALLATION_PATH', ROOT . '/lib/install/');
define('INSTALLATION_STYLESHEET', SITE_DOMAIN . INSTALLATION_PATH . 'assets/css/');
define('INSTALLATION_JS', SITE_DOMAIN . INSTALLATION_PATH . 'assets/js/');
define('INSTALLATION_IMAGES', SITE_DOMAIN . INSTALLATION_PATH . 'assets/images/');
define('INSTALLATION_PLUGINS', SITE_DOMAIN . INSTALLATION_PATH . 'assets/plugins/');

/** Default Visitor Paths */

define('DEFAULT_VISITOR_PATH', ROOT . '/app/system/visitor/default/' . SITE_VERSION . '/');

/** Defauly System Directory Managment */

define('SYSTEM_DIR', '/app/system/' . USER_TYPE . '/' . SITE_THEME . '/' . SITE_VERSION . '/');
define('SYSTEM_VISITOR_DIR', '/app/system/visitor/' . SITE_THEME . '/' . SITE_VERSION . '/');
define('SYSTEM_PATH', ROOT . SYSTEM_DIR);
define('SYSTEM_VISITOR_PATH', ROOT . SYSTEM_VISITOR_DIR);

/* Default logged in member user directory managment */

$tempDashboardDir = '/app/dashboard/v' . ((isset($site_dashboard_version)) ? $site_dashboard_version : 1) . '/default/';

if (is_dir(ROOT . $tempDashboardDir)) {

	// Check site version theme is available or not

	if (is_dir(ROOT . $tempDashboardDir . SITE_VERSION . '/')) {

		// include theme as per site version

		define('ADMIN_DIR', $tempDashboardDir . SITE_VERSION . '/');
	} else {

		// Include the desktop theme

		define('ADMIN_DIR', $tempDashboardDir . 'desktop/');
	}

	define('ADMIN_PATH', ROOT . ADMIN_DIR);
	define('ADMIN_URL', SITE_URL . ADMIN_DIR);
	define('ADMIN_STYLESHEET', ADMIN_URL . 'assets/css/');
	define('ADMIN_CSS_LIBRARY', CSS_LIBRARY . 'app/default/' . SITE_VERSION . '/');
	define('ADMIN_JS', ADMIN_URL . 'assets/js/');
	define('ADMIN_JS_LIBRARY', JS_LIBRARY . 'app/default/' . SITE_VERSION . '/');
	define('ADMIN_IMAGES', ADMIN_URL . 'assets/images/');
	define('ADMIN_PLUGIN', ADMIN_URL . 'assets/plugins/');
} else {

	// Default or previous admin dashbaord url

	define('ADMIN_DIR', '/app/' . SITE_APP . '/' . USER_TYPE . '/' . SITE_VERSION . '/');
	define('ADMIN_PATH', ROOT . ADMIN_DIR);
	define('ADMIN_URL', SITE_URL . ADMIN_DIR);
	define('ADMIN_STYLESHEET', CSS_LIBRARY . 'app/' . SITE_APP . '/' . SITE_VERSION . '/');
	define('ADMIN_CSS_LIBRARY', CSS_LIBRARY . 'app/default/' . SITE_VERSION . '/');
	define('ADMIN_JS', JS_LIBRARY . 'app/' . SITE_APP . '/' . SITE_VERSION . '/');
	define('ADMIN_JS_LIBRARY', JS_LIBRARY . 'app/default/' . SITE_VERSION . '/');
	define('ADMIN_IMAGES', IMG_LIBRARY . 'app/' . SITE_APP . '/' . SITE_VERSION . '/');
}

/* Visitor directory managment */

$userDirectory = '/app/' . SITE_APP . '/visitor/' . SITE_THEME . '/' . SITE_VERSION . '/';

if (is_dir($userDirectory)) {

	/** specific device theme or version of request for visitor website */

	define('USER_DIR', $userDirectory);
} else {

	/** define other version to only desktop theme for visitor website */

	define('USER_DIR', '/app/' . SITE_APP . '/visitor/' . SITE_THEME . '/desktop/');
}

define('USER_PATH', ROOT . USER_DIR);
define('USER_URL', SITE_URL . USER_DIR);
define('USER_STYLESHEET', USER_URL . 'assets/css/');
define('USER_JS', USER_URL . 'assets/js/');
define('USER_IMAGES', USER_URL . 'assets/images/');
define('USER_PLUGINS', USER_URL . 'assets/plugins/');

// Default theme for visitors

define('DEFAULT_USER_DIR', '/app/default/visitor/default/desktop/');
define('DEFAULT_USER_PATH', ROOT . DEFAULT_USER_DIR);
define('DEFAULT_USER_URL', SITE_URL . DEFAULT_USER_DIR);
define('DEFAULT_USER_STYLESHEET', DEFAULT_USER_DIR . 'assets/css/');
define('DEFAULT_USER_JS', DEFAULT_USER_DIR . 'assets/js/');
define('DEFAULT_USER_IMAGES', DEFAULT_USER_DIR . 'assets/images/');
define('DEFAULT_USER_PLUGINS', DEFAULT_USER_DIR . 'assets/plugins/');

/** Specific AMP Feature */

define('STATUS', RESOURCE . '/amp/stories.php');

/** Cache Directory Managment */

define('CACHE_PATH', ROOT . '/cache/' . SITE_DOMAIN . '/' . SITE_VERSION);

/** Create cache directory path */

if (!is_dir(CACHE_PATH)) {

	mkdir(CACHE_PATH, 0755, true);
}

define('CACHE_FILE', CACHE_PATH . '/' . str_replace('/', '-', rtrim(ltrim(ACTIVE_URL_NON_QUERY, '/'), '/')) . '.html');
define('CACHE_TIME', ((isset($cache_time)) ? $cache_time : 24) * 3600); // Default caching time is 24 hours

/* Default HTTP Error pages path of visitor */

if (file_exists(USER_PATH . '403.php')) {

	define('ERROR_403', USER_PATH . '403.php');
} else {

	define('ERROR_403', DEFAULT_VISITOR_PATH . '403.php');
}

if (file_exists(USER_PATH . '404.php')) {

	define('ERROR_404', USER_PATH . '404.php');
} else {

	define('ERROR_404', DEFAULT_VISITOR_PATH . '404.php');
}

if (file_exists(USER_PATH . '502.php')) {

	define('ERROR_502', USER_PATH . '502.php');
} else {

	define('ERROR_502', DEFAULT_VISITOR_PATH . '502.php');
}

if (file_exists(USER_PATH . '500.php')) {

	define('ERROR_500', USER_PATH . '500.php');
} else {

	define('ERROR_500', DEFAULT_VISITOR_PATH . '500.php');
}

/* Default HTTP Error pages path of visitor */

define('ADMIN_ERROR_403', ADMIN_PATH . '403.php');
define('ADMIN_ERROR_404', ADMIN_PATH . '404.php');
define('ADMIN_ERROR_502', ADMIN_PATH . '502.php');

/** Site Logo and Icon */

define('SITE_ICON', SITE_URL . '/' . ((isset($site_icon)) ? (($site_icon == '') ? IMG_LIBRARY . 'favicon.png' : $site_icon) : IMG_LIBRARY . 'favicon.png'));
define('SITE_LOGO', SITE_URL . '/' . ((isset($site_logo)) ? (($site_logo == '') ? IMG_LIBRARY . 'logo.png' : $site_logo)  : IMG_LIBRARY . 'logo.png'));

/** Editor */

define('EDITOR_CONTENT_FILTER', MODULES . 'editor/content.php');

// Google reCaptcha

define('GOOGLE_RECAPTCHA_GENERATOR', RECAPTCHA . 'google/generate-recaptcha.php');