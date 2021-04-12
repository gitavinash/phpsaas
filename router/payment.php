<?php

    $pgFile = PAYMENT_GATEWAY.$pg_name.'/'.((isset($urlStructure[1])) ? $urlStructure[1] : 'generate' ).'.php';

    if(file_exists($pgFile)) {

        require_once $pgFile;

    } else {

        // echo 'Something is wrong while calling payment gateway function';
        echo $pgFile;

    }