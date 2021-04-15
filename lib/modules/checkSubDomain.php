<?php

    /** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_parent_site_id=site_id WHERE site_url='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)!=0) {

            extract(mysqli_fetch_assoc($checkInstallation));

            if($site_custom_domain==1) {

                header("Location: http://{$site_domain}", TRUE, 301);
                exit();

            }

        } else {

            /** Check is Sub Domain is not a main domain of website */

            if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_site_id=site_id WHERE site_domain='".SITE_DOMAIN."'")) {

                if(mysqli_num_rows($checkInstallation)!=0) {

                    extract(mysqli_fetch_assoc($checkInstallation));

                    /** Fetch global website configuration settings */

                    if($websiteConfigutration=mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_status=1 AND site_configuration_global=0 AND site_configuration_site_id={$site_owner_id}")) {

                        if(mysqli_num_rows($websiteConfigutration)!=0) {

                            while($websiteConfigutrationData=mysqli_fetch_assoc($websiteConfigutration)) {

                                $websiteConfigutrationList[$websiteConfigutrationData['site_configuration_key']]=$websiteConfigutrationData['site_configuration_value'];

                            }

                            // Convert the website configuration key as a value
                            extract($websiteConfigutrationList);

                        }

                    }
        
                } else {

                    require_once ROOT.'/app/system/visitor/default/desktop/domain-not-found.php';
                    exit();

                }

            }

        }
    
    } else {

        require_once ROOT.'/app/system/visitor/default/desktop/verify-domain.php';
        exit();

    }