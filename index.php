<?php

    if(file_exists('router/index.php')) {

        /** Including router index file  */

        require_once 'router/index.php';

    } else {

        echo '<h1>Something is wrong while including router library</h1>';

    }

?>