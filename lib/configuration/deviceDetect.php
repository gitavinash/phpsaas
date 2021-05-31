<?php

	/** Use for url rounting in router index.php */

	$urlStructure=explode('/', rtrim(ltrim(ACTIVE_URL_NON_QUERY, '/'), '/'));

    $deviceOS=$_SERVER['HTTP_USER_AGENT'];

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

            define('DEVICE_OS', 'ios');
            
        }

        /** Check device is android */

        if($deviceType->isAndroidOS()) {

            define('DEVICE_OS', 'android');

        }

    } else {

        switch($deviceOS) {

            case (preg_match('/Mac/', $deviceOS) ? true : false) :
                define('DEVICE_OS', 'mac');
                break;

            case stristr($deviceOS, 'Linux') :
                define('DEVICE_OS', 'linux');
                break;

            default:
                define('DEVICE_OS', 'windows');
                break;
        }

    }

    // Detect Browser
    
    switch($deviceOS) {

        case (preg_match('/EdgA/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'edge mobile');
            break;
            
        case (preg_match('/Edg/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'edge');
            break;

        case (preg_match('/OPR/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'opera');
            break;

        case (preg_match('/UCTurbo/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'uc turbo');
            break;

        case (preg_match('/UCBrowser/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'uc browser');
            break;

        case (preg_match('/Firefox/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'firefox');
            break;

        case (preg_match('/Rocket/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'firefox lite');
            break;

        case (preg_match('/Chrome/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'chrome');
            break;

        case (preg_match('/Safari/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'safari');
            break;

        default:
            define('DEVICE_BROWSER', 'Unknown | '.$deviceOS);
            break;

    }