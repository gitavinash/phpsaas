<?php

if ($packageList = mysqli_query($connection, "SELECT product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price FROM isw_product INNER JOIN isw_product_meta ON product_id=product_meta_relation_id INNER JOIN isw_inventory ON product_id=inventory_relation_id WHERE product_site_id =  " . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND product_meta_site_id =  " . SITE_ID . " AND product_type = 'diagnostic' LIMIT 0, 9")) {

    if (mysqli_num_rows($packageList) != 0) {
        while ($packageListInfo = mysqli_fetch_assoc($packageList)) {

            $packagesList[] = $packageListInfo;
        }
    }
}

echo json_encode($packagesList, TRUE);
