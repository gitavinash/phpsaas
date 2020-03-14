<?php

    header( 'Content-Type: text/html; charset=utf-8' ); 

    if(file_exists('router/index.php')) {

        /** Including Device Detect Library and Check Device Type and Network */

        require_once 'lib/modules/device.php';
        $deviceType=new Device();

        /** Including router index file  */

        require_once 'router/index.php';

    } else {

        echo '<h1>Something is wrong while including router library</h1>';

    }

?>