<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if ($treatmentData = mysqli_query($connection, "SELECT treatment_id, treatment_image, treatment_gallery, treatment_title, treatment_description, treatment_highlight, treatment_category, treatment_owner_id, treatment_section, profile_display_name, profile_image, user_username, treatment_type, inventory_in_stock, inventory_sku, inventory_price, inventory_mrp, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, category_title FROM " . TABLE_PREFIX . "treatment INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=treatment_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id={$link_id} INNER JOIN " . TABLE_PREFIX . "user ON treatment_owner_id=user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN isw_category ON category_id = treatment_category WHERE treatment_id={$link_relation_id} AND meta_type='treatment' AND inventory_type='treatment'")) {

            if (mysqli_num_rows($treatmentData) != 0) {

                extract(mysqli_fetch_assoc($treatmentData));

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $treatmentTypeTemplate = USER_PATH . strtolower($treatment_type) . '.php';

                    if (file_exists($treatmentTypeTemplate)) {

                        /** Opening treatment type template */

                        require_once $treatmentTypeTemplate;
                    } else {

                        $category_title = ((isset($category_title)) ? $category_title . '-' : '');

                        $categoryTemplate = USER_PATH . strtolower($category_title) . 'treatment.php';

                        if (file_exists($categoryTemplate)) {

                            /** Opening category treatment template */

                            require_once $categoryTemplate;
                        } else {

                            /** Display content in default template */

                            require_once USER_PATH . 'treatment.php';
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
