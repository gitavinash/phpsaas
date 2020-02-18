<?php

	/* System default modules Directory and file */

	define('CONFIG', ROOT.'/lib/configuration/');
	define('MODULES', ROOT.'/lib/modules/');
	define('ROUTER', ROOT.'/router/');
	define('RESOURCE', ROOT.'/lib/resource/');

	define('APP_MODULES', ROOT.'/lib/modules/'.SITE_APP.'/');
	define('LOGIN_CHECK', MODULES.'isLogin.php');
	define('SITE_STATUS', MODULES.'siteStatus.php');
	define('APP_DIR', ROOT.'/app/');
	define('API', ROOT.'/api/');

	/* Email Directory Configuration */

	define('SEND_EMAIL', CONFIG.'mail/send_email.php');
	define('EMAIL_TEMPLATE', RESOURCE.'email/');
	define('APP_EMAIL_TEMPLATE', RESOURCE.'email/'.SITE_APP.'/');

	/* Installation Directory  */

	define('INSTALLATION_PATH', ROOT.'/lib/install/');
	define('INSTALLATION_STYLESHEET', SITE_DOMAIN.INSTALLATION_PATH.'assets/css/');
	define('INSTALLATION_JS', SITE_DOMAIN.INSTALLATION_PATH.'assets/js/');
	define('INSTALLATION_IMAGES', SITE_DOMAIN.INSTALLATION_PATH.'assets/images/');
	define('INSTALLATION_PLUGINS', SITE_DOMAIN.INSTALLATION_PATH.'assets/plugins/');

	/** Defauly System Directory Managment */

	define('SYSTEM_DIR', '/app/system/'.USER_TYPE.'/'.SITE_VERSION.'/');
	define('SYSTEM_LOGIN', ROOT.SYSTEM_DIR.'login.php');
	define('SYSTEM_PATH', ROOT.SYSTEM_DIR);
	define('SYSTEM_URL', SITE_URL.SYSTEM_DIR);
	define('SYSTEM_STYLESHEET', SYSTEM_URL.'assets/css/');
	define('SYSTEM_JS', SYSTEM_URL.'assets/js/');
	define('SYSTEM_IMAGES', SYSTEM_URL.'assets/images/');
	define('SYSTEM_PLUGINS', SYSTEM_URL.'assets/plugins/');	

	/* Administrator and login member user directory managment */

	define('ADMIN_DIR', '/app/'.SITE_APP.'/'.USER_TYPE.'/'.SITE_VERSION.'/');
	define('ADMIN_PATH', ROOT.ADMIN_DIR);
	define('ADMIN_URL', SITE_URL.ADMIN_DIR);
	define('ADMIN_STYLESHEET', ADMIN_URL.'assets/css/');
	define('ADMIN_JS', ADMIN_URL.'assets/js/');
	define('ADMIN_IMAGES', ADMIN_URL.'assets/images/');
	define('ADMIN_PLUGINS', ADMIN_URL.'assets/plugins/');	

	/* visitor directory managment */

	define('USER_DIR', '/app/'.SITE_APP.'/visitor/'.SITE_THEME.'/'.SITE_VERSION.'/');
	define('USER_PATH', ROOT.USER_DIR);
	define('USER_URL', SITE_URL.USER_DIR);
	define('USER_STYLESHEET', USER_URL.'assets/css/');
	define('USER_JS', USER_URL.'assets/js/');
	define('USER_IMAGES', USER_URL.'assets/images/');
	define('USER_PLUGINS', USER_URL.'assets/plugins/');

	/** Cache Directory Managment */

	define('CACHE_PATH', ROOT.'/cache/'.SITE_DOMAIN);
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

	/* Default site identity files */

	define('SITE_ICON', ((isset($_COOKIE['SITE_ICON'])) ? base64_decode($_COOKIE['SITE_ICON']) : ADMIN_IMAGES.'favicon.png' ));	
	define('SITE_LOGO', ((isset($_COOKIE['SITE_LOGO'])) ? base64_decode($_COOKIE['SITE_LOGO']) : ADMIN_IMAGES.'logo.png' ));

?>