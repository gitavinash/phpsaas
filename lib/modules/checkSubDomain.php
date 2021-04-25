<?php

    define('SYSTEM_THEME', '/app/system/visitor/default/desktop/');

    function checkSiteIsExpire($site_expire_date, $currentTheme=ROOT.'/app/system/visitor/default/desktop/') {

        if($site_expire_date<=date('Y-m-d')) {


            if(date('Y-m-d', strtotime('+1 month', strtotime($site_expire_date))) >= date('Y-m-d')) {

                // Website expire is less than 1 month

                require_once SYSTEM_THEME.'website-suspend.php';

            } else {

                // Website expire is more than 1 month

                require_once SYSTEM_THEME.'website-expire.php';

            }

            exit();

        }

    } 

    /** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_parent_site_id=site_id WHERE site_url='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)!=0) {

            extract(mysqli_fetch_assoc($checkInstallation));

            checkSiteIsExpire($site_expire_date);

            if($site_custom_domain==1) {

                header("Location: http://{$site_domain}", TRUE, 301);
                exit();

            }

        } else {
            
            /** Check is Sub Domain is not a main domain of website */

            if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_site_id=site_id WHERE site_domain='".SITE_DOMAIN."'")) {

                if(mysqli_num_rows($checkInstallation)!=0) {

                    extract(mysqli_fetch_assoc($checkInstallation));

                    checkSiteIsExpire($site_expire_date);
                    
                } else {

                    require_once SYSTEM_THEME.'domain-not-found.php';
                    exit();

                }

            }

        }
    
    } else {

        require_once SYSTEM_THEME.'verify-domain.php';
        exit();

    }