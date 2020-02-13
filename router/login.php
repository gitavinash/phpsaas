<?php

    /** Check customized login page */

    if(file_exists(USER_PATH.'login.php')) {

        /** Theme login page */

        require_once USER_PATH.'login.php';

    } else {

        /** System default login page */

        require_once SYSTEM_LOGIN;

    }