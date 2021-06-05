<?php

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

        case (preg_match('/Pinterestbot/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'pinterest bot');
            break;

        case (preg_match('/facebookexternalhit/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'facebook bot');
            break;

        case (preg_match('/Instagram/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'instagram');
            break;

        case (preg_match('/Chrome/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'chrome');
            break;

        case (preg_match('/e.ventures/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'e.ventures bot');
            break;

        case (preg_match('/AdsBot-Google/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'google ads bot');
            break;

        case (preg_match('/Mozilla/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'mozilla');
            break;

        case (preg_match('/Safari/', $deviceOS) ? true : false) :
            define('DEVICE_BROWSER', 'safari');
            break;

        default:
            define('DEVICE_BROWSER', $deviceOS);
            break;

    }