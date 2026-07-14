<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if ($technologyData = mysqli_query($connection, "SELECT technology_id, technology_image, technology_gallery, technology_title, technology_description, technology_highlight, technology_category, technology_owner_id, technology_section, profile_display_name, profile_image, user_username, technology_type, inventory_in_stock, inventory_sku, inventory_price, inventory_mrp, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, category_title, l.link_url as category_url FROM " . TABLE_PREFIX . "technology INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=technology_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id={$link_id} INNER JOIN " . TABLE_PREFIX . "user ON technology_owner_id=user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN isw_category ON category_id = technology_category INNER JOIN " . TABLE_PREFIX . "link AS l ON l.link_relation_id = category_id AND l.link_type = 'category' WHERE technology_id={$link_relation_id} AND meta_type='technology' AND inventory_type='technology'")) {

            if (mysqli_num_rows($technologyData) != 0) {

                extract(mysqli_fetch_assoc($technologyData));

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $technologyTypeTemplate = USER_PATH . strtolower($technology_type) . '.php';

                    if (file_exists($technologyTypeTemplate)) {

                        /** Opening technology type template */

                        require_once $technologyTypeTemplate;
                    } else {

                        $category_title = ((isset($category_title)) ? $category_title . '-' : '');

                        $categoryTemplate = USER_PATH . strtolower($category_title) . 'technology.php';

                        if (file_exists($categoryTemplate)) {

                            /** Opening category technology template */

                            require_once $categoryTemplate;
                        } else {

                            /** Display content in default template */

                            require_once USER_PATH . 'technology.php';
                        }
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
