<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:
        /** Fetch data from database table  */

        if ($productData = mysqli_query($connection, "SELECT product_id, product_brand_id, product_image, product_gallery, product_title, product_description, product_highlight, product_category, category_title, product_owner_id, profile_display_name, profile_image, user_username, product_type, inventory_in_stock, inventory_sku, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_variation, inventory_addons, inventory_sell_online, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id={$link_id} INNER JOIN " . TABLE_PREFIX . "category ON category_id=product_category INNER JOIN " . TABLE_PREFIX . "user ON product_owner_id=user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE product_id={$link_relation_id} AND meta_type='product' AND inventory_type='product'")) {

            if (mysqli_num_rows($productData) != 0) {

                extract(mysqli_fetch_assoc($productData));

                if ($checkBrand = mysqli_query($connection, "SELECT category_title as brandName, link_url as brandURL FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id WHERE  link_status=200 AND link_site_id = " . APP_SITE_ID . " AND link_type='category' AND category_id='{$product_category}' AND category_site_id=" . APP_SITE_ID)) {
                    if (mysqli_num_rows(($checkBrand)) != 0) {
                        extract(mysqli_fetch_assoc($checkBrand));
                    }
                }

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    /** Check category template is avilable or not */

                    $categoryTemplate = USER_PATH . str_replace(' ', '-', strtolower($category_title)) . '-product.php';

                    if (file_exists($categoryTemplate)) {

                        /** Opening category product template */

                        require_once $categoryTemplate;
                    } else {

                        // Use product type template file

                        $productTypeTemplate = USER_PATH . $product_type . '-product.php';

                        if (file_exists($productTypeTemplate)) {

                            require_once $productTypeTemplate;
                        } else {

                            /** Display content in default template */

                            $defaultProductTemplatePath =  USER_PATH . 'product.php';

                            if (file_exists($defaultProductTemplatePath)) {
                                require_once $defaultProductTemplatePath;
                            } else {
                                http_response_code(404);
                                echo '<h1>Unable to find the template path</h1>';
                            }
                        }
                    }

                    $cached = fopen(CACHE_FILE, 'wb');
                    fwrite($cached, ob_get_contents());
                    fclose($cached);
                    ob_end_flush(); // Send the output to the browser

                }
            } else {

                http_response_code(404);
                include_once ERROR_404;
            }
        }

        break;
}
