<?php

$checkCartQuery = "SELECT 
    c.cart_id, 
    c.cart_relation_id, 
    c.cart_relation_type, 
    c.cart_qty,
    c.cart_price,
    l.link_url, 
    COALESCE(p.product_title, e.event_title) AS product_title, 
    COALESCE(p.product_image, e.event_image, e.event_thumbnail) AS product_image, 
    COALESCE(inv.inventory_price, c.cart_price) AS inventory_price, 
    COALESCE(inv.inventory_mrp, c.cart_price) AS inventory_mrp
FROM " . TABLE_PREFIX . "cart c
LEFT JOIN " . TABLE_PREFIX . "product p 
    ON (c.cart_relation_id = p.product_id AND c.cart_relation_type = 'product')
LEFT JOIN " . TABLE_PREFIX . "event e 
    ON (c.cart_relation_id = e.event_id AND c.cart_relation_type = 'event')
LEFT JOIN " . TABLE_PREFIX . "link l 
    ON (l.link_relation_id = c.cart_relation_id 
        AND ((c.cart_relation_type = 'product' AND l.link_type = 'product') OR (c.cart_relation_type = 'event' AND l.link_type = 'event')) 
        AND l.link_site_id = " . SITE_ID . " 
        AND l.link_status = 200)
LEFT JOIN " . TABLE_PREFIX . "inventory inv 
    ON (inv.inventory_relation_id = c.cart_relation_id AND inv.inventory_site_id = " . SITE_ID . ")
WHERE c.cart_session_id = '" . SESSION_ID . "' 
  AND c.cart_site_id = " . SITE_ID . " 
  AND (c.cart_status = 0 OR c.cart_status = 2)";

if ($checkCart = mysqli_query($connection, $checkCartQuery)) {

	if (mysqli_num_rows($checkCart) != 0) {

		/** Show cart items */

		include_once 'cart-items.php';
	} else {

		/** Show continue shoping box */

		include_once 'continue-shopping.php';
	}
}