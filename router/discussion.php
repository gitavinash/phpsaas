<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if ($discussionData = mysqli_query($connection, "SELECT discussion_id, gallery_image_list, discussion_image, discussion_before_image, discussion_after_image, discussion_title, discussion_description, discussion_category, discussion_time, category_title, discussion_owner_id, profile_display_name, profile_image, user_username, discussion_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, GROUP_CONCAT(DISTINCT tag_title SEPARATOR ', ') as tagName, link_is_featured, link_published, link_modified FROM " . TABLE_PREFIX . "discussion INNER JOIN " . TABLE_PREFIX . "link ON link_id='{$link_id}' LEFT JOIN " . TABLE_PREFIX . "tag ON tag_relation_id=discussion_id AND tag_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id={$link_id} INNER JOIN " . TABLE_PREFIX . "category ON discussion_category=category_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=discussion_owner_id INNER JOIN " . TABLE_PREFIX . "user ON discussion_owner_id=user_id LEFT JOIN " . TABLE_PREFIX . "gallery ON gallery_id=discussion_gallery_id AND gallery_site_id=" . SITE_ID . " WHERE discussion_id='{$link_relation_id}' AND discussion_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($discussionData) != 0) {
                extract(mysqli_fetch_assoc($discussionData));
                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $categoryTemplate = USER_PATH . strtolower(str_replace(' ', '-', $category_title)) . '-discussion.php';

                    if (file_exists($categoryTemplate)) {

                        /** Opening category discussion template */

                        require_once $categoryTemplate;
                    } else {

                        $discussionTypeTemplate = USER_PATH . strtolower(str_replace('_', '-', str_replace(' ', '-', $discussion_type))) . '-discussion.php';

                        if (file_exists(($discussionTypeTemplate))) {

                            /** Opening category discussion type */

                            require_once $discussionTypeTemplate;
                        } else {

                            /** Display content in default template */

                            require_once USER_PATH . 'discussion.php';
                        }
                    }

                    $cached = fopen(CACHE_FILE, 'w');
                    fwrite($cached, ob_get_contents());
                    fclose($cached);
                    ob_end_flush(); // Send the output to the browser

                }
            } else {

                echo 'Error';
            }
        } else {

            echo mysqli_error($connection);
        }

        break;
}