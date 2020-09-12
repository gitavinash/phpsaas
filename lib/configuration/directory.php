<?php

	/* System default modules Directory and file */

	define('CONFIG', ROOT.'/lib/configuration/');
	define('MODULES', ROOT.'/lib/modules/');
	define('API_MODULES', ROOT.'/api/');
	define('ROUTER', ROOT.'/router/');
	define('RESOURCE', ROOT.'/lib/resource/');
	define('PAYMENT_GATEWAY', RESOURCE.'payment-gateway/');
	define('NOTIFICATION', RESOURCE.'notification/');
	
	/** Meta and Structure Data Resource */
	
	define('META_DIR', RESOURCE.'meta/');
	define('DEFAULT_META', META_DIR.'index.php');
	define('SCHEMA_DATA', RESOURCE.'schema/');

	/** Defualt UI Resource */

	define('UI_LIBRARY', SITE_URL.str_replace(ROOT, '', RESOURCE).'ui/');

	if(!is_dir('app/'.SITE_APP.'/'.SITE_VERSION.'/')) {

		/** Set default app library to desktop version */

		define('APP_LIBRARY', 'app/'.SITE_APP.'/desktop/');

	} else {

		define('APP_LIBRARY', 'app/'.SITE_APP.'/'.SITE_VERSION.'/');

	}

	define('JS_LIBRARY', UI_LIBRARY.'js/');
	define('APP_JS_LIBRARY', JS_LIBRARY.APP_LIBRARY);
	define('CSS_LIBRARY', UI_LIBRARY.'css/');
	define('APP_CSS_LIBRARY', CSS_LIBRARY.APP_LIBRARY);
	define('IMG_LIBRARY', UI_LIBRARY.'img/');
	define('APP_IMG_LIBRARY', IMG_LIBRARY.APP_LIBRARY);
	define('PLUGIN_LIBRARY', UI_LIBRARY.'plugin/');
	define('APP_PLUGIN_LIBRARY', PLUGIN_LIBRARY.APP_LIBRARY);

	/** Default jQuery Editor */

	define('EDITOR', PLUGIN_LIBRARY.'editor/editor.min.js');

	/** Default Service Worker */

	define('SERVICE_WORKER', RESOURCE.'service-worker/sw.min.js');

	/** System default and usefull modules */

	define('APP_MODULES', ROOT.'/lib/modules/'.SITE_APP.'/');
	define('IMAGE_UPLOADER', MODULES.'imageUploader.php');
	define('LOGIN_CHECK', MODULES.'isLogin.php');
	define('SITE_STATUS', MODULES.'siteStatus.php');

	/** API Directory */

	define('API_APP_MODULES', API_MODULES.SITE_APP.'/');
	
	/* Email Directory Configuration */

	define('SEND_EMAIL', CONFIG.'mail/send-email.php');
	define('EMAIL_TEMPLATE', RESOURCE.'email/');
	define('APP_EMAIL_TEMPLATE', EMAIL_TEMPLATE.SITE_APP.'/');
	
	/* SMS Directory Configuration */

	define('SEND_SMS', CONFIG.'sms/send-sms.php');
	define('SMS_TEMPLATE', RESOURCE.'sms/');
	define('APP_SMS_TEMPLATE', SMS_TEMPLATE.SITE_APP.'/');

	/* Installation Directory  */

	define('INSTALLATION_PATH', ROOT.'/lib/install/');
	define('INSTALLATION_STYLESHEET', SITE_DOMAIN.INSTALLATION_PATH.'assets/css/');
	define('INSTALLATION_JS', SITE_DOMAIN.INSTALLATION_PATH.'assets/js/');
	define('INSTALLATION_IMAGES', SITE_DOMAIN.INSTALLATION_PATH.'assets/images/');
	define('INSTALLATION_PLUGINS', SITE_DOMAIN.INSTALLATION_PATH.'assets/plugins/');

	/** Default Visitor Paths */

	define('DEFAULT_VISITOR_PATH', ROOT.'/app/system/visitor/default/'.SITE_VERSION.'/');

	/** Defauly System Directory Managment */

	define('SYSTEM_DIR', '/app/system/'.USER_TYPE.'/'.SITE_THEME.'/'.SITE_VERSION.'/');
	define('SYSTEM_VISITOR_DIR', '/app/system/visitor/'.SITE_THEME.'/'.SITE_VERSION.'/');
	define('SYSTEM_PATH', ROOT.SYSTEM_DIR);
	define('SYSTEM_VISITOR_PATH', ROOT.SYSTEM_VISITOR_DIR);

	/* Default logged in member user directory managment */

	define('DEFAULT_ADMIN_DIR', '/app/default/'.USER_TYPE.'/'.SITE_VERSION.'/');
	define('DEFAULT_ADMIN_PATH', ROOT.DEFAULT_ADMIN_DIR);
	define('DEFAULT_ADMIN_URL', SITE_URL.DEFAULT_ADMIN_DIR);
	define('DEFAULT_ADMIN_STYLESHEET', CSS_LIBRARY.'app/default/'.SITE_VERSION.'/');
	define('DEFAULT_ADMIN_JS', JS_LIBRARY.'app/default/'.SITE_VERSION.'/');
	define('DEFAULT_ADMIN_IMAGES', IMG_LIBRARY.'app/default/'.SITE_VERSION.'/');

	/* App logged in member user directory managment */

	$adminDirectory='/app/'.SITE_APP.'/'.USER_TYPE.'/'.SITE_VERSION.'/';
	
	if(is_dir($adminDirectory)) {

		/** specific device theme or version of request for visitor website */

		define('ADMIN_DIR', $adminDirectory);

	} else {

		/** define other version to only desktop theme for visitor website */

		define('ADMIN_DIR', '/app/'.SITE_APP.'/'.USER_TYPE.'/desktop/');

	}

	define('ADMIN_PATH', ROOT.ADMIN_DIR);
	define('ADMIN_URL', SITE_URL.ADMIN_DIR);
	define('ADMIN_STYLESHEET', CSS_LIBRARY.'app/'.SITE_APP.'/'.SITE_VERSION.'/');
	define('ADMIN_JS', JS_LIBRARY.'app/'.SITE_APP.'/'.SITE_VERSION.'/');
	define('ADMIN_IMAGES', IMG_LIBRARY.'app/'.SITE_APP.'/'.SITE_VERSION.'/');

	/* Visitor directory managment */

	$userDirectory='/app/'.SITE_APP.'/visitor/'.SITE_THEME.'/'.SITE_VERSION.'/';

	if(is_dir($userDirectory)) {

		/** specific device theme or version of request for visitor website */

		define('USER_DIR', $userDirectory);

	} else {

		/** define other version to only desktop theme for visitor website */

		define('USER_DIR', '/app/'.SITE_APP.'/visitor/'.SITE_THEME.'/desktop/');

	}

	define('USER_PATH', ROOT.USER_DIR);
	define('USER_URL', SITE_URL.USER_DIR);
	define('USER_STYLESHEET', USER_URL.'assets/css/');
	define('USER_JS', USER_URL.'assets/js/');
	define('USER_IMAGES', USER_URL.'assets/images/');
	define('USER_PLUGINS', USER_URL.'assets/plugins/');

	/** Specific AMP Feature */

	define('STATUS', RESOURCE.'/amp/stories.php');

	/** Cache Directory Managment */

	define('CACHE_PATH', ROOT.'/cache/'.SITE_DOMAIN.'/'.SITE_VERSION);
	define('CACHE_FILE', CACHE_PATH.'/'.str_replace('/', '-', rtrim(ltrim(ACTIVE_URL, '/'), '/')).'.html');
	define('CACHE_TIME', ((isset($cache_time)) ? $cache_time : 24 )*3600); // Default caching time is 24 hours

	/* Default HTTP Error pages path of visitor */

	define('ERROR_403', USER_PATH.'403.php');
	define('ERROR_404', USER_PATH.'404.php');
	define('ERROR_502', USER_PATH.'502.php');

	/* Default HTTP Error pages path of visitor */

	define('ADMIN_ERROR_403', ADMIN_PATH.'403.php');
	define('ADMIN_ERROR_404', ADMIN_PATH.'404.php');
	define('ADMIN_ERROR_502', ADMIN_PATH.'502.php');

	/** Site Logo and Icon */

	define('SITE_ICON', ((isset($site_icon)) ? (($site_icon=='') ? IMG_LIBRARY.'favicon.png' : $site_icon ) : IMG_LIBRARY.'favicon.png' ));
	define('SITE_LOGO', ((isset($site_logo)) ? (($site_logo=='') ? IMG_LIBRARY.'logo.png' : $site_logo )  : IMG_LIBRARY.'logo.png' ));