<?php

    if(isset($_COOKIE['user_data'])) {

        /** Show draft only for login user */

        $checkCategoryTemplate=USER_PATH.strtolower($category_title).'-'.$link_type.'.php';

        if(file_exists($checkCategoryTemplate)) {

            /** Opening category post template */

            require_once $checkCategoryTemplate;

        } else {

            /** Display content in default template */

            require_once USER_PATH.$link_type.'.php';

        }

    } else {

        require_once ERROR_403;

    }

    exit();