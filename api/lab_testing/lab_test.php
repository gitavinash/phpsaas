<?php

$packagesList[] = '';
$httpStatus = 404;
$nameQuery = '';

if (isset($test_name)) {

    $nameQuery = " AND LIKE '%{$test_name}%' ";
}

$labTestQuery = "SELECT product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price FROM isw_product INNER JOIN isw_product_meta ON product_id=product_meta_relation_id INNER JOIN isw_inventory ON product_id=inventory_relation_id WHERE product_site_id =  " . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND product_meta_site_id =  " . SITE_ID . " AND product_type = 'diagnostic' &nbsp {$nameQuery}";

if ($packageList = mysqli_query($connection, $labTestQuery)) {

    if (mysqli_num_rows($packageList) != 0) {
        $httpStatus = 200;
        while ($packageListInfo = mysqli_fetch_assoc($packageList)) {
            $packagesList[] = $packageListInfo;
        }
    }
} else {
    echo $labTestQuery;
    // echo mysqli_error($connection);
    $httpStatus = 500;
    $packageList = mysqli_error($connection);
}

http_response_code($httpStatus);
echo json_encode($packagesList, TRUE);
