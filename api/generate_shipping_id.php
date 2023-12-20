<?php

// print_r($_POST);

$productId = base64_decode($productId);
$productType = base64_decode($productType);


$findProductQuery = "SELECT product_id, product_brand_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id WHERE product_site_id = " . SITE_ID . " AND product_id IN ($productId) AND inventory_site_id = " . SITE_ID;

if ($checkProduct = mysqli_query($connection, $findProductQuery)) {

    if (mysqli_num_rows($checkProduct) != 0) {
        while ($productInfo = mysqli_fetch_assoc($checkProduct)) {
            print_r($productInfo);
        }
    }
}

exit();