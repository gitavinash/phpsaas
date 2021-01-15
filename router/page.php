<?php

    /** Check link status */

    switch ($link_status) {

        case 301 :  require_once ROUTER.'301.php';
                    break;

        default :   
            
            /** Fetch data from database table  */

            if($pageData=mysqli_query($connection, "SELECT page_id, page_image, page_title, page_description, page_owner_id, profile_display_name, profile_image, user_username, page_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc FROM ".TABLE_PREFIX."page INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=page_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id INNER JOIN ".TABLE_PREFIX."profile ON profile_id=page_owner_id INNER JOIN ".TABLE_PREFIX."user ON page_owner_id=user_id WHERE page_id='{$link_relation_id}' AND link_site_id=".APP_SITE_ID)) {

                if(mysqli_num_rows($pageData)!=0) {

                    extract(mysqli_fetch_assoc($pageData));
                    
                    if(file_exists(ROUTER.$link_status.'.php')) {

                        /** Include status router file */

                        require_once ROUTER.$link_status.'.php';

                    } else {

                        // ob_start();

                        /** Check category template is avilable or not */

                        $checkCategoryTemplate=USER_PATH.strtolower($page_type).'-page.php';
                        $checkPageNameTemplate=USER_PATH.strtolower(str_replace(' ', '-', $page_title)).'-page.php';

                        if(file_exists($checkCategoryTemplate)) {

                            /** Opening category page template */

                            require_once $checkCategoryTemplate;

                        } else if(file_exists($checkPageNameTemplate)) {

                            /** Open template as same name of page name */

                            require_once $checkPageNameTemplate;

                        } else {

                            /** Display content in default template */

                            require_once USER_PATH.'page.php';

                        }

                        // $cached = fopen($cacheFileName, 'w');
                        // fwrite($cached, ob_get_contents());
                        // fclose($cached);
                        // ob_end_flush(); // Send the output to the browser

                    }

                } else {

                    echo 'Error';

                }

            } else {

                echo mysqli_error($connection);

            }

    }