<?php

	session_start();
	date_default_timezone_set($time_zone);

	/** Current Time */

	define('CURRENT_DISPLAY_TIME', date('Y-m-d H:i:s'));		// This time is used for users
	define('CURRENT_DISPLAY_DATE', date('Y-m-d'));		// This Date is used for users
	define('CURRENT_MACHINE_TIME', date('Y-m-d H:i:s'));		// This time is user for all backend tasks
	define('EXPIRE_TIME', date('Y-m-d H:i:s', strtotime("+ ".TIME_LIMIT." minutes", strtotime(CURRENT_DISPLAY_TIME))));
	

	/** Software Credits */
	
	define('CREDIT', 'ITS Soft World');
	define('CREDIT_MAIL', 'avinash@itssoftword.com');
	define('CREDIT_URL', 'http://www.itssoftworld.com');

	/** Use for url rounting in router index.php */

	$urlStructure=explode('/', rtrim(ltrim(ACTIVE_URL, '/'), '/'));

	/** Including Device Detect Library and Check Device Type and Network */

	require_once 'lib/modules/device.php';
	$deviceType=new Device();

	if($deviceType->isMobile()) {

		define('SITE_VERSION', 'mobile');

	} else {

		define('SITE_VERSION', 'desktop');

	}

	/** Define website default name and description according to domain */

	define('SITE_ID', ((isset($site_id)) ? $site_id : 1 ));
	define('SITE_LANG', ((isset($site_lang)) ? $site_lang : 'en' ));
	define('SITE_NAME', ((isset($site_name)) ? $site_name : CREDIT ));
	define('SITE_DESC', ((isset($site_description)) ? $site_description : '' ));
	define('SITE_TAGS', ((isset($site_keyword)) ? $site_keyword : '' ));
	define('SITE_APP', ((isset($site_app)) ? $site_app : 'system' )); // deafult all for visitors
	define('SITE_THEME', ((isset($site_theme)) ? $site_theme : 'default' ));	// default theme of website for visitor

	/** Use when user is logged in */

	define('USER_TYPE', ((isset($_COOKIE['user_type'])) ? base64_decode($_COOKIE['user_type']) : 'administrator' ));	// directory of logged in user
	define('USER_APP', ((isset($site_app)) ? $site_app : 'system' )); // user type of logged in user
	define('PROFILE_DISPLAY_NAME',((isset($_COOKIE['user_name'])) ? base64_decode($_COOKIE['user_name']) : SITE_NAME ));	// Logged n user display name
	define('PROFILE_IMAGE',((isset($_COOKIE['user_image'])) ? SITE_URL.base64_decode($_COOKIE['user_image']) : '' ));	// Logged in profile picture

	/* Dashboard URL Management */

	define('ADMIN_ACTIVE_URL', str_replace('/dashboard', '', (str_replace('', '', ACTIVE_URL)))); // echo complete url show on the url bar
	define('ADMIN_QUERY_URL', str_replace('/dashboard', '', QUERY_URL)); // echo only query string of the url
	define('ADMIN_BASE_URL', str_replace('/dashboard', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string
	define('API_BASE_URL', str_replace('/api', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string

	/* Email Server setup configuration */

	define('SMTP_HOST', 'smtp-relay.sendinblue.com'); // host address of email server like :- mail.itssoftworld.com
	define('SMTP_SSL', 'false'); // is email server is using ssl if yes then change to true
	define('SMTP_PORT', '587'); // PORT id of their email server like :- 587, 465
	define('SMTP_USERNAME', 'avinash@itssoftworld.com'); // email user like :- contact@itssoftworld.com
	define('SMTP_PASSWORD', 'Ng7n1T0KZJkEF9z3'); // password of the email
	define('SMTP_EMAIL', 'donotreply@'.MAIN_DOMAIN); // Which email is display to receiver end like :- donotreply@itssoftworld.com
	define('SMTP_NAME', SITE_NAME); // Show Name on email :- ITS Soft World, Avinash Kumar
	define('REPLY_EMAIL', SITE_NAME); // Reply email where user can send a reply mail like :- contact@itssoftworld.com

	/** Sitemap and Feed Configuration */

	define('SITEMAP_LIMIT', 1000); // total links show on sitemap
	define('PER_PAGE_LIMIT', 35); // 
	define('FEED_LIMIT', 10); // total links show on feeds