<?php

	/** Use for url rounting in router index.php */

	$urlStructure=explode('/', rtrim(ltrim(ACTIVE_URL_NON_QUERY, '/'), '/'));

	/** Defining Site Version  */

	switch($urlStructure[0]) {

		case 'amp' : 	define('SITE_VERSION', 'amp');
						break;

		default : 	if($deviceType->isMobile()) {
						
						define('SITE_VERSION', 'mobile');

					} else if($deviceType->isTablet()) {

						define('SITE_VERSION', 'tablet');

					} else {

						define('SITE_VERSION', 'desktop');

					}
					break;

	}

    if(SITE_VERSION=='mobile' || SITE_VERSION=='tablet') {

        /** Check device is ios */
	
        if($deviceType->isiOS()) {

            define('DEVICE_OS', 'iOS');
            
        }

        /** Check device is android */

        if($deviceType->isAndroidOS()) {

            define('DEVICE_OS', 'Android');

        }

    } else {

        $deviceOS=$_SERVER['HTTP_USER_AGENT'];

        switch($deviceOS) {

            case (preg_match('/Mac/', $deviceOS) ? true : false) :
                define('DEVICE_OS', 'Mac');
                break;

            case stristr($deviceOS, 'Linux') :
                define('DEVICE_OS', 'Linux');
                break;

            default:
                define('DEVICE_OS', 'Windows');
                break;
        }

    }