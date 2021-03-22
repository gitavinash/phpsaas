<?php

    /** Check link status */

    switch ($link_status) {

        case 301 :  require_once ROUTER.'301.php';
                    break;

        default :   
            
            /** Fetch data from database table  */

            if($videoData=mysqli_query($connection, "SELECT video_id, video_image, video_title, video_description, video_owner_id, profile_display_name, profile_image, user_username, video_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, GROUP_CONCAT(DISTINCT tag_title SEPARATOR ', ') as tagName FROM ".TABLE_PREFIX."video INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=video_id INNER JOIN ".TABLE_PREFIX."tag ON tag_relation_id=video_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=video_owner_id INNER JOIN ".TABLE_PREFIX."user ON video_owner_id=user_id WHERE video_id='{$link_relation_id}'")) {

                if(mysqli_num_rows($videoData)!=0) {

                    extract(mysqli_fetch_assoc($videoData));

                    if(file_exists(ROUTER.$link_status.'.php')) {

                        /** Include status router file */

                        require_once ROUTER.$link_status.'.php';

                    } else {

                        // ob_start();

                        /** Check category template is avilable or not */

                        $checkCategoryTemplate=USER_PATH.strtolower($video_type).'-video.php';

                        if(file_exists($checkCategoryTemplate)) {

                            /** Opening category video template */

                            require_once $checkCategoryTemplate;

                        } else {

                            /** Display content in default template */

                            require_once USER_PATH.'video.php';

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