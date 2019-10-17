<?php

	/* System default modules Directory and file */

	define('CONFIG', ROOT.'/lib/configuration/');
	define('MODULES', ROOT.'/lib/modules/');
	define('ROUTER', ROOT.'/router/');
	define('RESOURCE', ROOT.'/lib/resource/');

	define('APP_MODULES', ROOT.'/lib/modules/'.USER_APP.'/');
	define('LOGIN_CHECK', MODULES.'isLogin.php');
	define('SITE_STATUS', MODULES.'siteStatus.php');
	define('APP_DIR', ROOT.'/app/');
	define('API', ROOT.'/api/');

	/* Email Directory Configuration */

	define('SEND_EMAIL', CONFIG.'mail/send_email.php');
	define('EMAIL_TEMPLATE', RESOURCE.'email/');
	define('APP_EMAIL_TEMPLATE', RESOURCE.'email/'.USER_APP.'/');

	/* Installation Directory  */

	define('INSTALLATION_VIEW_DIR', ROOT.'/lib/install/');
	define('INSTALLATION_VIEW_PATH', ROOT.INSTALLATION_VIEW_DIR);
	define('INSTALLATION_STYLESHEET', SITE_DOMAIN.INSTALLATION_VIEW_DIR.'assets/css/');
	define('INSTALLATION_JS', SITE_DOMAIN.INSTALLATION_VIEW_DIR.'assets/js/');
	define('INSTALLATION_IMAGES', SITE_DOMAIN.INSTALLATION_VIEW_DIR.'assets/images/');
	define('INSTALLATION_PLUGINS', SITE_DOMAIN.INSTALLATION_VIEW_DIR.'assets/plugins/');

	/* Administrator and login member user directory managment */

	define('ADMIN_VIEW_DIR', '/app/'.USER_APP.'/'.USER_TYPE.'/'.SITE_VERSION.'/');
	define('ADMIN_VIEW_PATH', ROOT.ADMIN_VIEW_DIR);
	define('ADMIN_VIEW_URL', SITE_URL.ADMIN_VIEW_DIR);
	define('ADMIN_STYLESHEET', ADMIN_VIEW_URL.'assets/css/');
	define('ADMIN_JS', ADMIN_VIEW_URL.'assets/js/');
	define('ADMIN_IMAGES', ADMIN_VIEW_URL.'assets/images/');
	define('ADMIN_PLUGINS', ADMIN_VIEW_URL.'assets/plugins/');	

	/* visitor directory managment */

	define('USER_VIEW_DIR', '/app/'.SITE_APP.'/visitor/'.SITE_THEME.'/'.SITE_VERSION.'/');
	define('USER_VIEW_PATH', ROOT.USER_VIEW_DIR);
	define('USER_VIEW_URL', SITE_URL.USER_VIEW_DIR);
	define('USER_STYLESHEET', USER_VIEW_URL.'assets/css/');
	define('USER_JS', USER_VIEW_URL.'assets/js/');
	define('USER_IMAGES', USER_VIEW_URL.'assets/images/');
	define('USER_PLUGINS', USER_VIEW_URL.'assets/plugins/');

	/** Cache Directory Managment */

	define('CACHE_PATH', ROOT.'/cache/');


	/* Default HTTP Error pages path of visitor */

	define('ERROR_403', USER_VIEW_PATH.'403.php');
	define('ERROR_404', USER_VIEW_PATH.'404.php');
	define('ERROR_502', USER_VIEW_PATH.'502.php');

	/* Default HTTP Error pages path of visitor */

	define('ADMIN_ERROR_403', ADMIN_VIEW_PATH.'403.php');
	define('ADMIN_ERROR_404', ADMIN_VIEW_PATH.'404.php');
	define('ADMIN_ERROR_502', ADMIN_VIEW_PATH.'502.php');

	/* Default site identity files */

	define('SITE_ICON', ((isset($_COOKIE['SITE_ICON'])) ? base64_decode($_COOKIE['SITE_ICON']) : ADMIN_IMAGES.'favicon.png' ));	
	define('SITE_LOGO', ((isset($_COOKIE['SITE_LOGO'])) ? base64_decode($_COOKIE['SITE_LOGO']) : ADMIN_IMAGES.'logo.png' ));

?>