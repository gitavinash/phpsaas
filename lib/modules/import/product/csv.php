<?php

require_once LOGIN_CHECK;

$i = 0;
$success = 0;
$dublicate = 0;
$dublicateMsg = '';
$failed = 0;
$failedMsg = '';

if ($categoryID != '') {

    if ($_FILES['importFile']['error'] == 0) {

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

        $categoryID = explode('_', $categoryID);
        $categoryURL = str_replace('category/', '', $categoryID[1]);
        $categoryId = $categoryID[0];

        /** Check file extension is csv or not */

        if ($extension == 'csv' && (in_array($_FILES['importFile']['type'], $validCSV))) {

            $openFile = fopen($_FILES['importFile']['tmp_name'], 'r');

            while (($dataLines = fgetcsv($openFile)) !== false) {

                if ($i != 0) {

                    $productSKU = mysqli_real_escape_string($connection, $dataLines[1]);
                    $productName = mysqli_real_escape_string($connection, $dataLines[2]);
                    $productImages = mysqli_real_escape_string($connection, $dataLines[3]);
                    $productIsFeatured = mysqli_real_escape_string($connection, $dataLines[4]);
                    $productShortDescription = mysqli_real_escape_string($connection, $dataLines[5]);
                    $productDescription = mysqli_real_escape_string($connection, $dataLines[6]);
                    $productTaxStatus = mysqli_real_escape_string($connection, $dataLines[7]);
                    $productTaxRate = mysqli_real_escape_string($connection, $dataLines[8]);
                    $productInStock = mysqli_real_escape_string($connection, $dataLines[9]);
                    $productCurrentStock = mysqli_real_escape_string($connection, $dataLines[10]);
                    $productLowStockWarning = mysqli_real_escape_string($connection, $dataLines[11]);
                    $productBackorder = mysqli_real_escape_string($connection, $dataLines[12]);
                    $productWeight = mysqli_real_escape_string($connection, $dataLines[13]);
                    $productLenght = mysqli_real_escape_string($connection, $dataLines[14]);
                    $productWidth = mysqli_real_escape_string($connection, $dataLines[15]);
                    $productHeight = mysqli_real_escape_string($connection, $dataLines[16]);
                    $productPrice = str_replace(',', '', mysqli_real_escape_string($connection, $dataLines[17]));
                    $productMRP = str_replace(',', '', mysqli_real_escape_string($connection, $dataLines[18]));
                    $productTags = mysqli_real_escape_string($connection, $dataLines[19]);
                    $productMetaTitle = mysqli_real_escape_string($connection, $dataLines[20]);
                    $productMetaDescription = mysqli_real_escape_string($connection, $dataLines[21]);

                    $productMRP = (($productMRP != 0) ? $productMRP : $productPrice);
                    $productPrice = (($productPrice != 0) ? $productMRP : $productMRP);

                    $productGallery = explode(',', $productImages);

                    $productFeatureImage = '';
                    $productGalleryImages = array();

                    for ($i = 0; $i < sizeof($productGallery); $i++) {

                        $splitURL = explode('/', $productGallery[$i]);
                        $imageName = end($splitURL);

                        $imgType = explode('.', $imageName);
                        $imgType = end(($imgType));

                        $uploadFileLocation = $uploadPath . time() . '_' . $imageName;
                        $imageURL = str_replace(ROOT, '', $uploadFileLocation);

                        if (file_put_contents($uploadFileLocation, file_get_contents($productGallery[$i]))) {

                            $productGalleryImages[] = array(
                                'name' => $imageName,
                                'path' => $imageURL,
                                'type' => $imgType,
                            );

                            if ($i == 0) {
                                $productFeatureImage = $imageURL;
                            }
                        }
                    }

                    $productGallery = json_encode($productGalleryImages, TRUE);

                    if ($checkProduct = mysqli_query($connection, "SELECT product_title FROM " . TABLE_PREFIX . "product WHERE product_title='{$productName}'")) {

                        if (mysqli_num_rows($checkProduct) == 0) {

                            mysqli_query($connection, "START TRANSACTION");

                            $url = '/product/' . strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $productName)) . '/';

                            if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                                if (mysqli_num_rows($checkURL) != 0) {

                                    $url = ltrim($url, '/') . '-' . $productSKU . '/';
                                }
                            }

                            $addNewProduct = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product(product_site_id, product_title, product_image, product_thumbnail, product_gallery, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(" . APP_SITE_ID . ", '{$productName}', '{$productFeatureImage}', '{$productFeatureImage}', '{$productGallery}', '{$productDescription}', '{$productShortDescription}', '{$categoryId}', " . USER_ID . ",'1', 'product')");
                            $productID = mysqli_insert_id($connection);

                            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '{$productIsFeatured}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                            $linkID = mysqli_insert_id($connection);

                            $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_status, inventory_update) VALUES ('{$productID}', " . APP_SITE_ID . ", '{$productSKU}', 'INR', '{$productPrice}', '{$productMRP}', '{$productTaxStatus}', '{$productTaxRate}', '{$productInStock}', '{$productCurrentStock}', '{$productBackorder}', '{$productLowStockWarning}', '{$productWeight}', '{$productLenght}', '{$productWidth}', '{$productHeight}', '1', current_timestamp())");

                            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_type, meta_title, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . APP_SITE_ID . ",  'product', '{$productMetaTitle}', '{$productMetaDescription}', '{$productTags}', '', '', '', '{$productMetaTitle}', '{$productMetaDescription}', '', '{$productMetaTitle}', '{$productMetaDescription}')");

                            /* Insert tags into tag table */

                            if ($productTags != '') {

                                $tags = explode(',', $productTags);

                                for ($i = 0; $i < sizeof($tags); $i++) {

                                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                    $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '" . USER_ID . "', '" . SITE_ID . "', 'product')");
                                }
                            } else {

                                $addNewTags = TRUE;
                            }

                            if ($addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {

                                $success++;
                                $status = 'success';
                                $msg = 'Product import successfully';
                                mysqli_query($connection, "COMMIT");
                            } else {

                                $failed++;
                                $msg = 'Something is wrong while adding new product';
                                mysqli_query($connection, "ROLLBACK");
                            }
                        } else {

                            $status = 'warning';
                            $dublicate++;
                            $msg = 'Products import successfully but some dublicate products found';
                        }
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
} else {

    $status = 'warning';
    $msg = 'Please select product category';
}

$responseData = array(
    'success' => $success,
    'failed' => $failed,
    'dublicate' => $dublicate
);