<?php

/** Use for url rounting in router index.php */

$urlStructure = explode('/', rtrim(ltrim(ACTIVE_URL_NON_QUERY, '/'), '/'));

$deviceOS = $_SERVER['HTTP_USER_AGENT'];

/** Defining Site Version  */


if (isset($_GET['site_version'])) {
    if ($_GET['site_version'] == 'app') {
        define('SITE_VERSION', strtolower(str_replace(' ', '_', $_GET['site_version'])));
    }
} else {
    switch ($urlStructure[0]) {

        case 'amp':
            define('SITE_VERSION', 'amp');
            break;

        default:
            if ($deviceType->isMobile()) {

                define('SITE_VERSION', 'mobile');
            } else if ($deviceType->isTablet()) {

                define('SITE_VERSION', 'tablet');
            } else {
                define('SITE_VERSION', 'desktop');
            }
            break;
    }
}

if (SITE_VERSION == 'mobile' || SITE_VERSION == 'tablet') {

    /** Check device is ios */

    if ($deviceType->isiOS()) {

        define('DEVICE_OS', 'ios');
    }

    /** Check device is android */

    if ($deviceType->isAndroidOS()) {

        define('DEVICE_OS', 'android');
    }
} else {

    switch ($deviceOS) {

        case (preg_match('/Mac/', $deviceOS) ? true : false):
            define('DEVICE_OS', 'mac');
            break;

        case stristr($deviceOS, 'Linux'):
            define('DEVICE_OS', 'linux');
            break;

        default:
            define('DEVICE_OS', 'windows');
            break;
    }
}

require_once 'browserDetect.php';
