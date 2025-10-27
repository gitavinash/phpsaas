<?php

/** Pagination */

$currentPage = end($urlStructure);

if (is_numeric($currentPage)) {

    /** Remove page no from url */
    $categoryURL = str_replace($currentPage . '/', '', $url);
} else {

    $currentPage = 1;
    $categoryURL = $url;
}

if ($checkProducts = mysqli_query($connection, "SELECT product_title,product_image, product_thumbnail, link_url, inventory_price, inventory_mrp, inventory_in_stock, inventory_currency_symbol FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 AND inventory_site_id=" . SITE_ID . " AND inventory_in_stock=1 AND inventory_price BETWEEN {$min} AND {$max} ORDER BY RAND()")) {
    if (mysqli_num_rows($checkProducts) != 0) {
        while ($productData = mysqli_fetch_assoc($checkProducts)) {
            $productList[] = $productData;
        }

        // Set SEO meta tags

        $meta_title = "Products under ₹ {$max} | " . SITE_NAME;

        $filterThemePath = USER_PATH . 'filter.php';

        if (file_exists($filterThemePath)) {

            /** Use custome template of this category */

            require_once $filterThemePath;
        } else {
            http_response_code(404);
            echo '<h1>Unable to find the template path</h1>';
        }
    } else {
        require_once ERROR_404;
    }
} else {
    if (DEVELOPMENT_MODE == TRUE) {
        echo mysqli_error($connection);
    } else {
        require_once ERROR_500;
    }
}