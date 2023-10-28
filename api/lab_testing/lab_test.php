<?php

$packagesList = [];
$httpStatus = 404;
$nameQuery = '';

if (isset($test_name)) {

    $nameQuery = " \n AND product_title LIKE '%$test_name%' \n GROUP BY product_title \n LIMIT 0, 5";
}

$labTestQuery = "SELECT category_title as product_brand, product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price \n FROM " . TABLE_PREFIX . "product \n INNER JOIN " . TABLE_PREFIX . "product_meta \n ON product_id=product_meta_relation_id \n INNER JOIN " . TABLE_PREFIX . "inventory \n ON product_id=inventory_relation_id \n INNER JOIN " . TABLE_PREFIX . "category \n ON product_brand_id = category_id  WHERE \n product_type = 'diagnostic' \n AND product_site_id = " . SITE_ID . " \n AND inventory_site_id = " . SITE_ID . "  \n AND product_meta_site_id =  " . SITE_ID . " \n {$nameQuery} ";

if ($packageList = mysqli_query($connection, $labTestQuery)) {

    if (mysqli_num_rows($packageList) != 0) {
        $httpStatus = 200;
        while ($packageListInfo = mysqli_fetch_assoc($packageList)) {
            $packagesList[] = $packageListInfo;
        }
    }
} else {
    $httpStatus = 500;
    $packageList = mysqli_error($connection);
}

http_response_code($httpStatus);
echo json_encode($packagesList, TRUE);
