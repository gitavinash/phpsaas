<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if ($postData = mysqli_query($connection, "SELECT event_id, event_image, event_title, event_description, event_category, event_start_date, event_start_time, event_end_time, event_location, event_map, category_title, event_owner_id, profile_display_name, profile_image, user_username, event_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, GROUP_CONCAT(DISTINCT tag_title SEPARATOR ', ') as tagName FROM " . TABLE_PREFIX . "event INNER JOIN " . TABLE_PREFIX . "tag ON tag_relation_id=event_id AND tag_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id={$link_id} INNER JOIN " . TABLE_PREFIX . "category ON event_category=category_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=event_owner_id INNER JOIN " . TABLE_PREFIX . "user ON event_owner_id=user_id WHERE event_id='{$link_relation_id}'")) {

            if (mysqli_num_rows($postData) != 0) {
                extract(mysqli_fetch_assoc($postData));

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $categoryTemplate = USER_PATH . strtolower(str_replace(' ', '-', $category_title)) . '-event.php';

                    if (file_exists($categoryTemplate)) {

                        /** Opening category event template */

                        require_once $categoryTemplate;
                    } else {

                        /** Display content in default template */

                        require_once USER_PATH . 'event.php';
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
