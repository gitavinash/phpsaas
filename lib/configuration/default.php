<?php

	session_start();
	date_default_timezone_set($time_zone);

	/** Developers Credit */

	define('CREDIT', 'ITS Soft World');
	define('CREDIT_MAIL', 'avinash@isw.one');
	define('CREDIT_URL', 'http://www.itssoftworld.com');

	/** Use for url rounting in router index.php */

	$urlStructure=explode('/', rtrim(ltrim(ACTIVE_URL_NON_QUERY, '/'), '/'));

	/** Defining Site Version  */

	switch($urlStructure[0]) {

		case 'amp' : 	define('SITE_VERSION', 'amp');
						break;

		default : 	if($deviceType->isMobile()) {
						define('SITE_VERSION', 'mobile');
					} else {
						define('SITE_VERSION', 'desktop');
					}
					break;

	}

	/** Define website default name and description according to domain */

	define('SITE_ID', ((isset($site_id)) ? $site_id : 0 ));
	define('SITE_LANG', ((isset($site_lang)) ? $site_lang : 'en' ));
	define('SITE_NAME', ((isset($site_name)) ? $site_name : CREDIT ));
	define('SITE_DESC', ((isset($site_description)) ? $site_description : '' ));
	define('SITE_TAGS', ((isset($site_keyword)) ? $site_keyword : '' ));
	define('SITE_APP', ((isset($app_path)) ? $app_path : 'system' )); // deafult all for visitors
	define('SITE_THEME', ((isset($theme_path)) ? $theme_path : 'default' ));	// default theme of website for visitor

	/** Use when user is logged in */

	define('USER_TYPE', ((isset($_COOKIE['user_type'])) ? base64_decode($_COOKIE['user_type']) : 'visitor' ));	// directory of logged in user
	define('PROFILE_DISPLAY_NAME',((isset($_COOKIE['user_name'])) ? base64_decode($_COOKIE['user_name']) : SITE_NAME ));	// Logged n user display name
	define('PROFILE_IMAGE',((isset($_COOKIE['user_image'])) ? SITE_URL.base64_decode($_COOKIE['user_image']) : '' ));	// Logged in profile picture

	/* Dashboard URL Management */

	define('ADMIN_ACTIVE_URL', str_replace('/dashboard', '', (str_replace('', '', ACTIVE_URL)))); // echo complete url show on the url bar
	define('ADMIN_QUERY_URL', str_replace('/dashboard', '', QUERY_URL)); // echo only query string of the url
	define('ADMIN_BASE_URL', str_replace('/dashboard', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string
	define('API_BASE_URL', str_replace('/api', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string

	/* Email Server setup configuration */

	define('SMTP_HOST', 'smtp-relay.sendinblue.com'); // host address of email server like :- mail.punjabkesari.com
	define('SMTP_SSL', 'false'); // is email server is using ssl if yes then change to true
	define('SMTP_PORT', '587'); // PORT id of their email server like :- 587, 465
	define('SMTP_USERNAME', 'avinash@itssoftworld.com'); // email user like :- contact@punjabkesari.com
	define('SMTP_PASSWORD', 'Ng7n1T0KZJkEF9z3'); // password of the email
	define('SMTP_EMAIL', 'donotreply@isw.one'); // Which email is display to receiver end like :- donotreply@punjabkesari.com
	define('SMTP_NAME', 'ITS Soft World'); // Show Name on email :- ITS Soft World, Avinash Kumar
	define('REPLY_EMAIL', 'ITS Soft World'); // Reply email where user can send a reply mail like :- contact@punjabkesari.com

	/** Sitemap and Feed Configuration */

	define('SITEMAP_LIMIT', ((isset($site_sitemap_limit)) ? $site_sitemap_limit : 1000 )); // total links show on sitemap
	define('PER_PAGE_LIMIT', ((isset($site_pagination_limit)) ? $site_pagination_limit : 10 )); // Per Page Pagination limit
	define('FEED_LIMIT', ((isset($site_feed_limit)) ? $site_feed_limit : 10 )); // total links show on feeds

	/** Image Compression or Other Image Related Configuration */

	define('PNG_IMAGE', 'image/png');
	define('JPEG_IMAGE', 'image/jpeg');
	define('JPG_IMAGE', 'image/jpg');
	define('GIF_IMAGE', 'image/gif');
	define('IMAGE_COMPRESSION_QUALITY', ((isset($site_image_quality)) ? $site_image_quality : 60 ));
	define('IMAGE_THUMBNAIL_WIDTH', ((isset($site_thumbnail_width)) ? $site_thumbnail_width : 223 ));
	define('IMAGE_THUMBNAIL_HEIGHT', ((isset($site_thumbnail_height)) ? $site_thumbnail_height : 96 ));

	/** Notification Configuration */

	define('NOTIFICATION_KEY', 'AAAAjm__7wg:APA91bE09Yzo6K0GidEpDzpkbTRdWpkAcjXSwi3mL7gasl4ZMi0tkpBUhFzSFdBORh-SWDsgWo-OU7xO3epzU7b85zBSku8LIQWfnjJkUTK-ngp-WRjJIE9sUdd4yjf1cjwlkRFwJkns');
	define('NOTIFICATION_SENDER', '611764399880');

	/** Site Settings Configuration */

	define('SITE_THEME_COLOR', ((isset($site_theme_color)) ? $site_theme_color : '' ));
	define('GA_ID', ((isset($site_ga_id)) ? $site_ga_id : '' ));
	define('FB_PAGE_ID', ((isset($site_fb_id)) ? $site_fb_id : '' ));
	define('FB_APP_ID', ((isset($site_fb_app_id)) ? $site_fb_app_id : '' ));
	define('TWITTER_ID', ((isset($site_tw_id)) ? $site_tw_id : '' ));