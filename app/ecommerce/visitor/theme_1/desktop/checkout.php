<?php

if ($checkCart = mysqli_query($connection, "SELECT cart_id, link_url, product_title, product_image, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id AND inventory_site_id=" . SITE_ID . " WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id=" . SITE_ID . " AND (cart_status=0 OR cart_status=2) AND product_status=1 AND link_status=200 AND link_type='product' AND link_site_id=" . SITE_ID)) {

	if (mysqli_num_rows($checkCart) != 0) {

		/** Show cart items */

		include_once 'cart-items.php';
	} else {

		/** Show continue shoping box */

		include_once 'continue-shopping.php';
	}
}