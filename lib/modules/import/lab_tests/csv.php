<?php

require_once LOGIN_CHECK;

$i = 0;
$success = 0;
$dublicate = 0;
$dublicateMsg = '';
$failed = 0;
$failedMsg = '';

if ($_FILES['importFile']['error'] == 0) {

    $testType = ((isset($isPackage)) ? 'packages' : 'diagnostic');

    $extension = explode('.', $_FILES['importFile']['name']);
    $extension = end($extension);

    $validCSV = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt'
    );


    /** Check file extension is csv or not */

    if ($extension == 'csv' && (in_array($_FILES['importFile']['type'], $validCSV))) {

        $openFile = fopen($_FILES['importFile']['tmp_name'], 'r');

        while (($dataLines = fgetcsv($openFile)) !== false) {

            if ($i != 0) {

                $test_title = mysqli_real_escape_string($connection, $dataLines[0]);
                $test_category = mysqli_real_escape_string($connection, $dataLines[1]);
                $test_brand = mysqli_real_escape_string($connection, $dataLines[2]);
                $test_description = mysqli_real_escape_string($connection, $dataLines[3]);
                $test_short_description = mysqli_real_escape_string($connection, $dataLines[4]);
                $test_selling_price = mysqli_real_escape_string($connection, $dataLines[5]);
                $test_mrp = mysqli_real_escape_string($connection, $dataLines[6]);
                $test_tags = mysqli_real_escape_string($connection, $dataLines[7]);
                $test_deivery_time = mysqli_real_escape_string($connection, $dataLines[8]);

                $test_selling_price = (($test_selling_price == '') ? $test_mrp : $test_selling_price);

                // Check test category

                if ($checkCategory = mysqli_query($connection, "SELECT link_url as categoryURL, category_id as categoryId FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id=category_id WHERE link_type='category' AND link_site_id=" . SITE_ID . " AND category_id='{$test_category}' AND category_site_id=" . SITE_ID)) {

                    if (mysqli_num_rows($checkCategory) != 0) {

                        extract(mysqli_fetch_assoc($checkCategory));

                        // check test brand

                        if ($checkBrand = mysqli_query($connection, "SELECT link_url as brandURL, category_title as brandName FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id=category_id WHERE link_type='category' AND link_site_id=" . SITE_ID . " AND category_id='{$test_brand}' AND category_type='brand' AND category_site_id=" . SITE_ID)) {

                            if (mysqli_num_rows($checkBrand) != 0) {

                                extract(mysqli_fetch_assoc($checkBrand));

                                if ($checkProduct = mysqli_query($connection, "SELECT product_title FROM " . TABLE_PREFIX . "product WHERE product_title='{$test_title}' AND product_brand_id='{$test_brand}'")) {

                                    if (mysqli_num_rows($checkProduct) == 0) {

                                        mysqli_query($connection, "START TRANSACTION");

                                        $categoryURL = str_replace('/category/', '', $categoryURL);

                                        $url = '/product' . strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $test_title)) . '/';

                                        if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                                            if (mysqli_num_rows($checkURL) != 0) {

                                                $url = ltrim($url, '/') . '-' . strtolower(str_replace(' ', '_', $checkBrand)) . '/';
                                            }
                                        }

                                        $addNewProduct = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product(product_site_id, product_brand_id, product_title, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(" . APP_SITE_ID . ", '{$test_brand}', '{$test_title}', '{$test_description}', '{$test_short_description}', '{$categoryId}', " . USER_ID . ",'1', '{$testType}')");
                                        $productID = mysqli_insert_id($connection);

                                        $addNewProductMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product_meta(product_meta_relation_id, product_meta_site_id, product_meta_key, product_meta_value, product_meta_status) VALUES('{$productID}', " . SITE_ID . ", 'delivery_time', '{$test_deivery_time}', 1)");

                                        $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                                        $linkID = mysqli_insert_id($connection);

                                        $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_currency, inventory_price, inventory_mrp, inventory_status, inventory_update) VALUES ('{$productID}', " . APP_SITE_ID . ", 'INR', '{$test_selling_price}', '{$test_mrp}', '1', current_timestamp())");

                                        $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_type) VALUES('{$linkID}', " . APP_SITE_ID . ",  'product')");

                                        if ($addNewProduct && $addNewProductMeta && $addNewLink && $addNewInventory && $addNewMeta) {

                                            $success++;
                                            $status = 'success';
                                            $msg = 'Lab Tests import successfully';
                                            mysqli_query($connection, "COMMIT");
                                        } else {

                                            $failed++;
                                            $msg = 'Something is wrong while adding new product';
                                            mysqli_query($connection, "ROLLBACK");
                                        }
                                    } else {

                                        $status = 'warning';
                                        $dublicate++;
                                        $msg = 'Lab Tests import successfully but some dublicate products found';
                                    }
                                }
                            } else {
                                $failed++;
                                $msg = 'Brand not found';
                            }
                        } else {
                            echo mysqli_error($connection);
                            exit();
                        }
                    } else {
                        $failed++;
                        echo mysqli_error($connection);
                        exit();
                    }
                } else {
                    echo mysqli_error($connection);
                    exit();
                }
            }

            $i++;
        }
    } else {

        $msg = 'Please upload valid file';
    }
} else {

    $msg = 'Please upload file';
}

$responseData = array(
    'success' => $success,
    'failed' => $failed,
    'dublicate' => $dublicate
);
