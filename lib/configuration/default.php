<?php

session_start();
date_default_timezone_set($time_zone);

define('ALEYA_APP_VERSION', '1.5.0');

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
define('SITE_APP_NAME', ((isset($app_title)) ? $app_title : 'system')); // deafult all for visitors
define('SITE_APP_IMAGE', ((isset($app_image)) ? $app_image : '/app/cms/visitor/blumox/desktop/assets/images/logo.svg')); // deafult all for visitors
define('SITE_THEME', ((isset($theme_path)) ? $theme_path : 'default'));	// default theme of website for visitor

/** Use when user is logged in */

define('USER_TYPE', ((isset($_COOKIE['user_type'])) ? base64_decode($_COOKIE['user_type']) : 'visitor'));	// directory of logged in user
define('PROFILE_DISPLAY_NAME', ((isset($_COOKIE['user_name'])) ? base64_decode($_COOKIE['user_name']) : SITE_NAME));	// Logged n user display name
define('PROFILE_EMAIL', ((isset($_COOKIE['user_email'])) ? base64_decode($_COOKIE['user_email']) : ''));	// Logged n user display name
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

// Pagination starting fromy

// Get current page number from query, default to 1 if not set or invalid
$pageNo = isset($_GET['pg']) && is_numeric($_GET['pg']) && (int)$_GET['pg'] > 0 ? (int)$_GET['pg'] : 1;
define('PAGE_NO', $pageNo);

// SQL LIMIT clause
$offset = ($pageNo - 1) * PER_PAGE_LIMIT;
define('PAGINATION', "LIMIT $offset, " . PER_PAGE_LIMIT);

// Previous and next page numbers
define('PREVIOUS_PAGE', ($pageNo > 1) ? $pageNo - 1 : 1);
define('NEXT_PAGE', $pageNo + 1);

/** Image Compression or Other Image Related Configuration */

define('PDF_FILE', 'application/pdf');
define('PNG_IMAGE', 'image/png');
define('JPEG_IMAGE', 'image/jpeg');
define('JPG_IMAGE', 'image/jpg');
define('GIF_IMAGE', 'image/gif');
define('IMAGE_COMPRESSION_QUALITY', ((isset($site_image_quality)) ? $site_image_quality : 60));
define('IMAGE_THUMBNAIL_WIDTH', ((isset($site_thumbnail_width)) ? $site_thumbnail_width : 223));
define('IMAGE_THUMBNAIL_HEIGHT', ((isset($site_thumbnail_height)) ? $site_thumbnail_height : 96));

/** Notification Configuration */

define(
	'NOTIFICATION_KEY',
	'AAAA022yuIE:APA91bGmpLAcWlFEJB731Z-pTMjYvQu0xPBLQTtElWwsK83HM5mS-Ua0dnYaykR2QIhlxqRfH1vhgX2KHnsNpVZ_lQUbyHk-f7K9CGXcgFPkfIufQ5zbqp7mQfoF1qHoapHxyllk1U76'
);
define('NOTIFICATION_SENDER', '908078528641');
define(
	'NOTIFICATION_PUBLIC_KEY',
	'BFkEn6a9UgVzM0eOG0T75xQMoatlFkPe7B24d-RlTF548AyvVIhJ8fLkQBdJOZl_m4DCiGKkCTs_A9r209DJT0c'
);

/** Site Settings Configuration */

define('SITE_THEME_COLOUR', ((isset($site_theme_color)) ? $site_theme_color : '#00b7ff'));
define('SITE_BACKGROUND_COLOUR', ((isset($site_background_color)) ? $site_background_color : SITE_THEME_COLOUR));

// Encrption and Descryption

define('ENCRYPT_METHOD', 'AES-128-CTR');
// define('ENCRYPT_KEY', 'blumox.com');

// Maps key

define('HERE_MAPS_KEY', '6zO_obkSkxtx4j1O9u6QpVTMuKuI75J_ZsWcXaW11To');

if (!defined('GOOGLE_RECAPTCHA_SITE_KEY')) {
	define('GOOGLE_RECAPTCHA_SITE_KEY', '6LdKyVwrAAAAAOws6wV4B3BAR7-H_iv_VNZTSDMx');
}

if (!defined('GOOGLE_RECAPTCHA_SECRET_KEY')) {
	define('GOOGLE_RECAPTCHA_SECRET_KEY', '6LdKyVwrAAAAAN1aeIoKZadN7TuOAiIZXEwrNIV_');
}