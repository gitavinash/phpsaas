<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if (
            $postData = mysqli_query(
                $connection,
                "
        SELECT
            n.news_id,
            n.news_image,
            n.news_title,
            n.news_description,
            n.news_category,
            n.news_price,
            n.news_gallery,
            c.category_title,

            n.news_owner_id,
            n.news_paid,
            n.news_is_public,
            n.news_published,
            n.news_type,

            p.profile_display_name,
            p.profile_email,
            p.profile_image,

            u.user_username,

            m.meta_title,
            m.meta_description,
            m.meta_canonical,
            m.meta_keyword,
            m.meta_robots,
            m.meta_geo_position,
            m.meta_geo_place_name,
            m.meta_geo_region,

            m.fb_img,
            m.fb_title,
            m.fb_desc,
            m.tw_img,
            m.tw_title,
            m.tw_desc,

            GROUP_CONCAT(DISTINCT t.tag_title ORDER BY t.tag_title SEPARATOR ', ') AS tagName

        FROM " . TABLE_PREFIX . "news n

        /* OPTIONAL: TAGS */
        LEFT JOIN " . TABLE_PREFIX . "tag t
            ON t.tag_relation_id = n.news_id
           AND t.tag_site_id = " . SITE_ID . "

        /* OPTIONAL: META (FIXED) */
        LEFT JOIN " . TABLE_PREFIX . "meta m
            ON m.meta_relation_id = n.news_id
           AND m.meta_type = 'news'

        /* REQUIRED */
        INNER JOIN " . TABLE_PREFIX . "category c
            ON c.category_id = n.news_category

        INNER JOIN " . TABLE_PREFIX . "user u
            ON u.user_id = n.news_owner_id

        /* OPTIONAL */
        LEFT JOIN " . TABLE_PREFIX . "profile p
            ON p.profile_user_id = n.news_owner_id

        WHERE n.news_id = '" . mysqli_real_escape_string($connection, $link_relation_id) . "'

        GROUP BY n.news_id
        "
            )
        ) {

            if (mysqli_num_rows($postData) != 0) {

                extract(mysqli_fetch_assoc($postData));

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $categoryTemplate = USER_PATH . strtolower(str_replace(' ', '-', $category_title)) . '-news.php';

                    if (file_exists($categoryTemplate)) {

                        /** Opening category news template */

                        require_once $categoryTemplate;
                    } else {

                        /** Display content in default template */

                        require_once USER_PATH . 'news.php';
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