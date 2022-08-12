<?php

    /** Check link status */

    switch ($link_status) {

        case 301 :  require_once ROUTER.'301.php';
                    break;

        default :
            
            /** Fetch data from database table  */

            if($serviceData=mysqli_query($connection, "SELECT service_id, service_image, service_gallery, service_title, service_description, service_highlight, service_category, service_owner_id, service_section, profile_display_name, profile_image, user_username, service_type, inventory_in_stock, inventory_sku, inventory_price, inventory_mrp, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, category_title FROM ".TABLE_PREFIX."service INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=service_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id={$link_id} INNER JOIN ".TABLE_PREFIX."user ON service_owner_id=user_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=user_id INNER JOIN isw_category ON category_id = service_category WHERE service_id={$link_relation_id} AND meta_type='service' AND inventory_type='service'")) {

                if(mysqli_num_rows($serviceData)!=0) {

                    extract(mysqli_fetch_assoc($serviceData));

                    if(file_exists(ROUTER.$link_status.'.php')) {

                        /** Include status router file */

                        require_once ROUTER.$link_status.'.php';

                    } else {

                        ob_start();

                        /** Check category template is avilable or not */

                        $category_title=((isset($category_title)) ? $category_title.'-' : '' );

                        $categoryTemplate=USER_PATH.strtolower($category_title).'service.php';

                        if(file_exists($categoryTemplate)) {

                            /** Opening category service template */

                            require_once $categoryTemplate;

                        } else {

                            /** Display content in default template */

                            require_once USER_PATH.'service.php';

                        }

                        $cached = fopen(CACHE_FILE, 'w');
                        fwrite($cached, ob_get_contents());
                        fclose($cached);
                        ob_end_flush(); // Send the output to the browser

                    }

                }

            }

        break;

    }