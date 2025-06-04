<?php

require_once 'header.php';

if ($checkCart = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id=" . SITE_ID . " AND (cart_status=0 OR cart_status=2) AND product_status=1 AND link_status=200 AND inventory_site_id=" . SITE_ID . " AND link_type='product' AND link_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkCart) != 0) {

        extract(mysqli_fetch_assoc($checkCart));

        /** Show cart items */

        $checkAppFile = DEFAULT_USER_PATH . ltrim(BASE_URL, '/') . SITE_APP . '.php';

        if (file_exists($checkAppFile)) {
            require_once $checkAppFile;
        } else {
            require_once DEFAULT_USER_PATH . ltrim(BASE_URL, '/') . 'default.php';
        }
    } else {
        require_once DEFAULT_USER_PATH . ltrim(BASE_URL, '/') . 'empty-cart.php';
    }
}


require_once 'footer.php';