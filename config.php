<?php

/* Project root directory of the server */

define('ROOT', dirname(__FILE__));

define('DEVELOPMENT_MODE', true); // Error reporting true to show error or false to hide error

/* database connection setting */

if (DEVELOPMENT_MODE == true) {

	define('DB_HOST', 'localhost');		// database hostname ex :- hostname
	define('DB_USER', 'root');		// database username ex :- root
	define('DB_PASS', '9289786810');		// datapase user password :- 12345
	define('DB_NAME', 'isw_app');		// database name ex :- cms_db
	define('MONGO_DB_CONNECTION_URL', 'mongodb://localhost:27017/');		// MongoDB Connection URL

	error_reporting(0);
	ini_set('display_errors', 0);

	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);
} else {

	define('DB_HOST', 'localhost');		// database hostname ex :- hostname
	define('DB_USER', 'root');		// database username ex :- root
	define('DB_PASS', 'A=2ADu5P7WqCPm!Unc6*a?N^8Ka2?Htp');		// datapase user password :- 12345
	define('DB_NAME', 'isw_saas');		// database name ex :- cms_db
	define('MONGO_DB_CONNECTION_URL', 'mongodb+srv://iswone:!KL0KdVY65K8@cluster0.vldbb2n.mongodb.net/test');		// MongoDB Connection URL
	error_reporting(0);
	ini_set('display_errors', 0);
}

/* database table prefix */

define('TABLE_PREFIX', 'isw_');

/* dafault timezone is asia calcutta for indian user if you want to change timezone please read http://php.net/manual/en/timezones.php */

$time_zone = 'Asia/Kolkata';
$time_zone_value = '+5:30';

/** User IP Address */

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
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

if (DEVELOPMENT_MODE == true) {
	define('HTTP_PROTOCOL', 'http://'); // Check HTTP protocol is secure or not
} else {
	define('HTTP_PROTOCOL', 'https://'); // Check HTTP protocol is secure or not
}

define('SITE_DOMAIN', $_SERVER['HTTP_HOST']); // return only site domain with protocol
define('SITE_URL', HTTP_PROTOCOL . $_SERVER['HTTP_HOST']); // return site domain without protocol
define('MAIN_DOMAIN', implode('.', array_slice(explode('.', SITE_DOMAIN), -2, 3, true))); // show only the actual domain like :- example.com
define('SUB_DOMAIN', implode('.', array_slice(explode('.', SITE_DOMAIN), -3, 1, true))); // show only the actual domain like :- example.com

define('QUERY_URL', htmlspecialchars_decode($_SERVER['QUERY_STRING'])); // echo only query string of the url

if (isset($_SERVER['REDIRECT_URL'])) {

	define('ACTIVE_URL', ($_SERVER['REDIRECT_URL']) . ((QUERY_URL) != '' ? '?' . QUERY_URL : '')); // echo complete url show on the url bar

} else {

	define('ACTIVE_URL', ($_SERVER['REQUEST_URI'])); // echo complete url show on the url bar

}

define('ACTIVE_SITE_URL', SITE_URL . ACTIVE_URL);
define('ACTIVE_URL_NON_QUERY', str_replace('?' . QUERY_URL, '', ACTIVE_URL));
define('BASE_URL', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?'))); // return only the actual url and remove query string

/** Redirect www url to non www url */

if (SUB_DOMAIN == 'www') {
	if ((substr(BASE_URL, -1)) != '/') {
		$redirectURL = BASE_URL . '/' . ((QUERY_URL != '') ? '?' . QUERY_URL : '');
		header("Location: {$redirectURL}", TRUE, 301);
	} else {
		header("Location: " . HTTP_PROTOCOL . MAIN_DOMAIN . ACTIVE_URL, TRUE, 301);
	}
	exit();
}