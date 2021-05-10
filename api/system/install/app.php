<?php

    function app($connection, $data) {

        extract($data);

        if($mobile!='') {

            if(preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {

                $mobile=ltrim((str_replace('+91', '', $mobile)), 0);
                
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    $websiteExpiryDate=date('Y-m-d', strtotime('+10 years'));

                    // Start query transaction

                    mysqli_query($connection, "START TRANSACTION");

                    // Create site

                    $createNewSite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_domain, site_url, site_app_id, site_theme_id, site_dashboard_version, site_status) VALUES(1, '".SITE_DOMAIN."', '".SITE_DOMAIN."', 1, 1, 2, 1)");
                    $installedSiteID=mysqli_insert_id($connection);

                    // Call generate user library

                    require_once MODULES.'user/generate.php';

                    // Create site configuration

                    // Set site configuration for default email use

                    $createNewSiteConfiguration=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_configuration(site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES('{$site_id}', 'email_smtp_default', 1, 1, 1)");

                    // Set site configuration for default sms use

                    $createNewSiteConfiguration=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_configuration(site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES('{$site_id}', 'sms_default', 1, 1, 1)");

                    // Create site installation

                    $createNewSiteInstallation=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_installation(site_installation_site_id, site_installation_parent_site_id, site_installation_user_id, site_installation_package_id, site_installation_expiry_date, site_app_status) VALUES('{$site_id}', '{$site_id}', '{$user_id}', 1, '{$websiteExpiryDate}', 1)");

                    // Create site setting

                    $createNewSiteSetting=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_setting(site_setting_site_id, site_custom_domain) VALUES('{$site_id}', 1)");

                    if($createNewUser && $createNewUserProfile && $createNewSite && $createNewSiteAccess && $createNewSiteConfiguration && $createNewSiteInstallation && $createNewSiteSetting) {

                        // Execute the transaction
                        
                        mysqli_query($connection, "COMMIT");

                        $status=200;
                        $msg="App is installed successfully";
                        $redirect='/login/';




                    } else {

                        // Something went wrong rollback the transaction

                        echo $createNewUser.' && '.$createNewUserProfile.' && '.$createNewSite.' && '.$createNewSiteAccess.' && '.$createNewSiteConfiguration.' && '.$createNewSiteInstallation.' && '.$createNewSiteSetting;
                        mysqli_query($connection, "ROLLBACK");
                        $msg='Something went wrong while installating the app';

                    }

                } else {

                    $msg='Please enter a valid email';

                }

            } else {

                $msg='Please enter a valid mobile number';

            }

        } else {

            $msg='Please enter mobile number';

        }

        // Response of the api
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);
    
    }