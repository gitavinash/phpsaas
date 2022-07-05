<?php
	
	/* Project root directory of the server */

	define('ROOT', dirname(__FILE__));

	define('DEVELOPMENT_MODE', true); // Error reporting true to show error or false to hide error

	/* database connection setting */

	if(DEVELOPMENT_MODE==true) {

		define('DB_HOST', 'localhost');		// database hostname ex :- hostname
		define('DB_USER', 'root');		// database username ex :- root
		define('DB_PASS', '');		// datapase user password :- 12345
		define('DB_NAME', 'isw');		// database name ex :- cms_db

	} else {

		define('DB_HOST', 'localhost');		// database hostname ex :- hostname
		define('DB_USER', 'root');		// database username ex :- root
		define('DB_PASS', 'A=2ADu5P7WqCPm!Unc6*a?N^8Ka2?Htp');		// datapase user password :- 12345
		define('DB_NAME', 'isw_saas');		// database name ex :- cms_db

	}

	/* database table prefix */

	define('TABLE_PREFIX', 'isw_'); 

	/* dafault timezone is asia calcutta for indian user if you want to change timezone please read http://php.net/manual/en/timezones.php */

	$time_zone = 'Asia/Calcutta';
	$time_zone_value = '+5:30';

	/** User IP Address */

	if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}

	define('IP', $_SERVER['REMOTE_ADDR']);

	/** User Login Path */
	
	define('LOGIN_PATH', '/login/');
	define('LOGOUT_PATH', '/logout/');
	define('FORGOT_PASSWORD_PATH', '/forgot-password/');
	define('REGISTRATION_PATH', '/register/');

	#############################################################################################
	############################  DO NOT CHANGE CODE BELOW THIS LINE  ###########################
	#############################################################################################

	/* Active domain and URL information */
	
	define('HTTP_PROTOCOL', $_SERVER['REQUEST_SCHEME'].'://'); // Check HTTP protocol is secure or not
	define('SITE_DOMAIN', $_SERVER['HTTP_HOST']); // return only site domain with protocol
	define('SITE_URL', HTTP_PROTOCOL.$_SERVER['HTTP_HOST']); // return site domain without protocol
	define('MAIN_DOMAIN', implode('.', array_slice(explode('.', SITE_DOMAIN), -2, 3, true))); // show only the actual domain like :- example.com
	define('SUB_DOMAIN', implode('.', array_slice(explode('.', SITE_DOMAIN), -3, 1, true))); // show only the actual domain like :- example.com

	define('QUERY_URL', htmlspecialchars_decode($_SERVER['QUERY_STRING'])); // echo only query string of the url

	if(isset($_SERVER['REDIRECT_URL'])) {

		define('ACTIVE_URL', ($_SERVER['REDIRECT_URL']).((QUERY_URL)!='' ? '?'.QUERY_URL : '' )); // echo complete url show on the url bar

	} else {

		define('ACTIVE_URL', ($_SERVER['REQUEST_URI'])); // echo complete url show on the url bar

	}

	define('ACTIVE_SITE_URL', SITE_URL.ACTIVE_URL);
	define('ACTIVE_URL_NON_QUERY', str_replace('?'.QUERY_URL, '', ACTIVE_URL));
	define('BASE_URL', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?'))); // return only the actual url and remove query string

	/** Redirect www url to non www url */

	if(SUB_DOMAIN=='www') {
		if((substr(BASE_URL, -1))!='/') {
			$redirectURL=BASE_URL.'/'.((QUERY_URL!='') ? '?'.QUERY_URL : '' );
			header("Location: {$redirectURL}", TRUE, 301);
		} else {
			header("Location: ".HTTP_PROTOCOL.MAIN_DOMAIN.ACTIVE_URL, TRUE, 301);
		}
		exit();
	}
	
?>