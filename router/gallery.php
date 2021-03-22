<?php

    /** Check link status */

    switch ($link_status) {

        case 301 :  require_once ROUTER.'301.php';
                    break;

        default :   
            
                    /** Fetch data from database table  */

                    if($galleryData=mysqli_query($connection, "SELECT gallery_id, gallery_image, gallery_title, gallery_description, gallery_image_list, gallery_category, category_title, gallery_owner_id, profile_display_name, profile_image, user_username, gallery_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, GROUP_CONCAT(DISTINCT tag_title SEPARATOR ', ') as tagName FROM ".TABLE_PREFIX."gallery INNER JOIN ".TABLE_PREFIX."tag ON tag_relation_id=gallery_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id={$link_id} INNER JOIN ".TABLE_PREFIX."category ON gallery_category=category_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=gallery_owner_id INNER JOIN ".TABLE_PREFIX."user ON gallery_owner_id=user_id WHERE gallery_id='{$link_relation_id}'")) {

                        if(mysqli_num_rows($galleryData)!=0) {

                            extract(mysqli_fetch_assoc($galleryData));

                            if(file_exists(ROUTER.$link_status.'.php')) {

                                /** Include status router file */

                                require_once ROUTER.$link_status.'.php';

                            } else {

                                ob_start();

                                /** Check category template is avilable or not */

                                $categoryTemplate=USER_PATH.strtolower($category_title).'-gallery.php';

                                if(file_exists($categoryTemplate)) {

                                    /** Opening category gallery template */

                                    require_once $categoryTemplate;

                                } else {

                                    /** Display content in default template */

                                    require_once USER_PATH.'gallery.php';

                                }

                                $cached = fopen(CACHE_FILE, 'w');
                                fwrite($cached, ob_get_contents());
                                fclose($cached);
                                ob_end_flush(); // Send the output to the browser

                            }

                        } else {

                            echo 'Error';

                        }

                    }
                    break;

    }