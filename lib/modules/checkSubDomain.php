<?php

    /** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id WHERE site_url='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)!=0) {

            extract(mysqli_fetch_assoc($checkInstallation));
            // exit();

        } else {

            require_once $_SERVER['DOCUMENT_ROOT'].'/app/system/visitor/default/desktop/domain-not-found.php';
            exit();

        }
    
    } else {

        require_once $_SERVER['DOCUMENT_ROOT'].'/app/system/visitor/default/desktop/verify-domain.php';
        exit();

    }