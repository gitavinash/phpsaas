<?php

    if(isset($_COOKIE['user_data'])) {

        /** Show draft only for login user */

        $checkCategoryTemplate=USER_VIEW_PATH.strtolower($category_title).'-post.php';

        if(file_exists($checkCategoryTemplate)) {

            /** Opening category post template */

            require_once $checkCategoryTemplate;

        } else {

            /** Display content in default template */

            require_once USER_VIEW_PATH.'post.php';

        }

    } else {

        require_once ERROR_403;

    }

    exit();