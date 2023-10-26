<?php

session_start();
date_default_timezone_set($time_zone);

/** Browser current active session id */

if (!isset($_SESSION['active_session_id'])) {

	$_SESSION['active_session_id'] = hash('sha256', uniqid(mt_rand(), true) . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 100) . time());
	define('SESSION_ID', $_SESSION['active_session_id']);
} else {

	define('SESSION_ID', $_SESSION['active_session_id']);
}

/** Developers Credit */

define('CREDIT', 'Blumox Technologies');
define('CREDIT_MAIL', 'avinash@isw.one');
define('CREDIT_URL', 'http://www.blumox.com');

/** Define website default name and description according to domain */

define('SITE_ID', ((isset($site_id)) ? $site_id : 0));
define('APP_SITE_ID', ((isset($site_installation_parent_site_id)) ? $site_installation_parent_site_id : ((isset($site_installation_site_id)) ? $site_installation_site_id : 0)));
define('SITE_APP_ID', ((isset($site_app_id)) ? $site_app_id : ((isset($site_app_id)) ? $site_app_id : 1)));
define('SITE_LANG', ((isset($site_lang)) ? $site_lang : 'en'));
define('SITE_NAME', ((isset($site_name)) ? $site_name : CREDIT));
define('SITE_DESC', ((isset($site_description)) ? $site_description : ''));
define('SITE_TAGS', ((isset($site_keyword)) ? $site_keyword : ''));
define('SITE_APP', ((isset($app_path)) ? $app_path : 'system')); // deafult all for visitors
define('SITE_THEME', ((isset($theme_path)) ? $theme_path : 'default'));	// default theme of website for visitor

/** Use when user is logged in */

define('USER_TYPE', ((isset($_COOKIE['user_type'])) ? base64_decode($_COOKIE['user_type']) : 'visitor'));	// directory of logged in user
define('PROFILE_DISPLAY_NAME', ((isset($_COOKIE['user_name'])) ? base64_decode($_COOKIE['user_name']) : SITE_NAME));	// Logged n user display name
define('PROFILE_IMAGE', ((isset($_COOKIE['user_image'])) ? base64_decode($_COOKIE['user_image']) : ''));	// Logged in profile picture

/* Dashboard URL Management */

define('ADMIN_ACTIVE_URL', str_replace('/dashboard', '', (str_replace('', '', ACTIVE_URL)))); // echo complete url show on the url bar
define('ADMIN_QUERY_URL', str_replace('/dashboard', '', QUERY_URL)); // echo only query string of the url
define('ADMIN_BASE_URL', str_replace('/dashboard', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string
define('API_BASE_URL', str_replace('/api', '', str_replace(QUERY_URL, '', strtok(ACTIVE_URL, '?')))); // return only the actual url and remove query string

/** Pagination Sitemap and Feed Configuration */

define('PER_PAGE_LIMIT', ((isset($site_pagination_limit)) ? $site_pagination_limit : 10)); // Per Page Pagination limit
define('SITEMAP_LIMIT', ((isset($site_sitemap_limit)) ? $site_sitemap_limit : 1000)); // total links show on sitemap
define('FEED_LIMIT', ((isset($site_feed_limit)) ? $site_feed_limit : 10)); // total links show on feeds

// Pagination starting from

define('PAGE_NO', ((isset($_GET['pg'])) ? mysqli_real_escape_string($connection, $_GET['pg']) - 1 : 0));
define('PAGINATION', "LIMIT " . PAGE_NO * PER_PAGE_LIMIT . ", " . PER_PAGE_LIMIT);
define('NEXT_PAGE', ((PAGE_NO == 0) ? 2 : PAGE_NO + 2));
define('PREVIOUS_PAGE', PAGE_NO);

/** Image Compression or Other Image Related Configuration */

define('PNG_IMAGE', 'image/png');
define('JPEG_IMAGE', 'image/jpeg');
define('JPG_IMAGE', 'image/jpg');
define('GIF_IMAGE', 'image/gif');
define('IMAGE_COMPRESSION_QUALITY', ((isset($site_image_quality)) ? $site_image_quality : 60));
define('IMAGE_THUMBNAIL_WIDTH', ((isset($site_thumbnail_width)) ? $site_thumbnail_width : 223));
define('IMAGE_THUMBNAIL_HEIGHT', ((isset($site_thumbnail_height)) ? $site_thumbnail_height : 96));

/** Notification Configuration */

define('NOTIFICATION_KEY', 'AAAAjm__7wg:APA91bE09Yzo6K0GidEpDzpkbTRdWpkAcjXSwi3mL7gasl4ZMi0tkpBUhFzSFdBORh-SWDsgWo-OU7xO3epzU7b85zBSku8LIQWfnjJkUTK-ngp-WRjJIE9sUdd4yjf1cjwlkRFwJkns');
define('NOTIFICATION_SENDER', '611764399880');

/** Site Settings Configuration */

define('SITE_THEME_COLOUR', ((isset($site_theme_color)) ? $site_theme_color : '#00b7ff'));

// Encrption and Descryption

define('ENCRYPT_METHOD', 'AES-128-CTR');
// define('ENCRYPT_KEY', 'blumox.com');