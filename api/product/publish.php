<?php

require_once LOGIN_CHECK;
require_once FUNCTIONS . 'duplicate.php';

function publish($connection, $data)
{

    extract($data);

    if (isset($title) && $title != '') {

        $image = array();
        $thumbnailImage = '';
        $images = array();
        $gallery = '';
        require_once MODULES . 'imageUploader.php';

        $resolvedImages = iswResolveDuplicateImages(
            $connection,
            $data,
            (isset($image) && is_array($image)) ? $image : array(),
            isset($thumbnailImage) ? (string)$thumbnailImage : ''
        );
        $featureImage = $resolvedImages['featureImage'];
        $thumbnailImage = $resolvedImages['thumbnailImage'];
        $facebookImage = $resolvedImages['facebookImage'];
        $twitterImage = $resolvedImages['twitterImage'];

        if ($featureImage != '') {

            // Extract the single upload image string to variables

            $description = isset($description) ? $description : '';
            $highlight = isset($highlight) ? (string)$highlight : '';
            $postType = ((isset($postType) && $postType != '') ? $postType : ($type ?? 'product'));
            $currentStock = iswNormalizeInt($currentStock ?? 0);
            $featurePost = iswNormalizeFlag($featurePost ?? 0);
            $in_stock = iswNormalizeFlag($in_stock ?? 0);
            $backorder = iswNormalizeFlag($backorder ?? 0);
            $taxStatus = iswNormalizeInt($taxStatus ?? 0);
            $taxRate = iswNormalizeInt($taxRate ?? 0);
            $sku = isset($sku) ? (string)$sku : '';
            $stock_warning = isset($stock_warning) ? (string)$stock_warning : '';
            $weight = isset($weight) ? (string)$weight : '';
            $lenght = isset($lenght) ? (string)$lenght : '';
            $width = isset($width) ? (string)$width : '';
            $height = isset($height) ? (string)$height : '';

            if (isset($_FILES['gallery'])) {

                require_once MODULES . 'galleryUploader.php';
            }

            $gallery = iswMergeDuplicateGallery(
                $connection,
                'product',
                $_POST,
                (isset($images) && is_array($images)) ? $images : array(),
                SITE_ID,
                false
            );
            $gallery = mysqli_real_escape_string($connection, $gallery);

            // Product Variation

            $productVariation = '';
            $rawSalePrice = iswNormalizeDecimal($salePrice ?? 0);
            $rawMrp = iswNormalizeDecimal($mrp ?? 0);
            $mrp = ((float)$rawMrp == 0.0) ? $rawSalePrice : $rawMrp;
            $salePrice = ((float)$rawSalePrice == 0.0) ? $mrp : $rawSalePrice;

            if (isset($_POST['similarProducts']) && is_array($_POST['similarProducts']) && count($_POST['similarProducts']) > 0) {

                $similarSplit = array();
                for ($i = 0; $i < sizeof($_POST['similarProducts']); $i++) {

                    $similarSplit[] = explode('&&', (string)$_POST['similarProducts'][$i]);
                }

                $productVariation = json_encode($similarSplit, TRUE);
            }

            $productVariation = mysqli_real_escape_string($connection, base64_encode((string)$productVariation));

            // Product add-ons

            $productAddons = '';
            if (isset($_POST['addOnProducts']) && is_array($_POST['addOnProducts']) && count($_POST['addOnProducts']) > 0) {

                $addonSplit = array();
                for ($i = 0; $i < sizeof($_POST['addOnProducts']); $i++) {

                    $addonSplit[] = explode('&&', (string)$_POST['addOnProducts'][$i]);
                }

                $productAddons = json_encode($addonSplit, TRUE);
            }

            $productAddons = mysqli_real_escape_string($connection, base64_encode((string)$productAddons));

            /** Category */

            if (isset($category)) {

                $category = explode('_', $category);
                $categoryURL = $category[1];
                $category = $category[0];

                /** product page url */

                $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)($url ?? ''), '/')), '-');
                if ($slugClean === '') {
                    $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)$title)), '-');
                }
                $url = strtolower('/product' . $categoryURL . $slugClean) . '/';

                /* define facebook and twitter meta desciption */

                $meta_title = isset($meta_title) ? $meta_title : '';
                $meta_desc = isset($meta_desc) ? $meta_desc : '';
                $meta_key = isset($meta_key) ? $meta_key : '';
                $canonical = isset($canonical) ? $canonical : '';
                $robots = isset($robots) ? $robots : 'index, follow';
                $fb_title = isset($fb_title) ? $fb_title : '';
                $fb_desc = ((!isset($fb_desc) || $fb_desc == '') ? $meta_desc : $fb_desc);
                $tw_title = isset($tw_title) ? $tw_title : '';
                $tw_desc = ((!isset($tw_desc) || $tw_desc == '') ? $meta_desc : $tw_desc);
                $sell_online = (isset($sell_online) && (string)$sell_online === '0') ? 0 : 1;

                if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                    if (mysqli_num_rows($checkURL) == 0) {

                        require_once EDITOR_CONTENT_FILTER;

                        $brand = iswNormalizeInt($brand ?? 0);

                        /** Start uploading the product */

                        mysqli_query($connection, "START TRANSACTION");

                        /* Insert data into link table */

                        $addNewProduct = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product(product_site_id, product_brand_id, product_image, product_thumbnail, product_gallery, product_title, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(" . SITE_ID . ", '{$brand}', '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$title}', '{$description}', '{$highlight}', '{$category}', " . USER_ID . ",'1', 'product')");
                        $productID = mysqli_insert_id($connection);

                        $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                        $linkID = mysqli_insert_id($connection);

                        $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_status, inventory_update, inventory_sell_online, inventory_variation, inventory_addons) VALUES ('{$productID}', " . SITE_ID . ", '{$sku}', 'INR', '{$salePrice}', '{$mrp}', '{$taxStatus}', '{$taxRate}', '{$in_stock}', '{$currentStock}', '{$backorder}', '{$stock_warning}', '{$weight}', '{$lenght}', '{$width}', '{$height}', '1', current_timestamp(), '{$sell_online}', '{$productVariation}', '{$productAddons}')");

                        $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'product', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                        /* Insert tags into tag table */

                        $addNewTags = TRUE;
                        $tagsStr = isset($tags) ? trim((string)$tags) : '';
                        if ($tagsStr != '') {

                            $tagsArr = explode(',', $tagsStr);

                            for ($i = 0; $i < sizeof($tagsArr); $i++) {

                                $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($tagsArr[$i])));
                                if ($tagsName !== '') {
                                    $tagInserted = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                                    if (!$tagInserted) {
                                        $addNewTags = FALSE;
                                    }
                                }
                            }
                        }

                        $addProductMeta = TRUE;
                        if (isset($_POST['productMeta']) && is_array($_POST['productMeta'])) {

                            foreach ($_POST['productMeta'] as $product_meta_key => $product_meta_value) {

                                $safeMetaKey = mysqli_real_escape_string($connection, (string)$product_meta_key);
                                $safeMetaVal = mysqli_real_escape_string($connection, (string)$product_meta_value);
                                $metaInserted = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product_meta(product_meta_relation_id, product_meta_site_id, product_meta_key, product_meta_value, product_meta_status) VALUES('{$productID}', " . SITE_ID . ", '{$safeMetaKey}', '{$safeMetaVal}', 1)");
                                if (!$metaInserted) {
                                    $addProductMeta = FALSE;
                                }
                            }
                        }

                        if ($addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags && $addProductMeta) {

                            $status = 200;
                            $msg = 'Product add successfully';
                            $redirect = '/dashboard/product/?type=edit&editor=true&id=' . base64_encode($productID);
                            mysqli_query($connection, "COMMIT");
                        } else {

                            $msg = 'Something is wrong while adding new product';
                            mysqli_query($connection, "ROLLBACK");
                        }
                    } else {

                        $msg = 'Permalink value is not unique';
                    }
                } else {

                    $msg = 'Technical errro';
                }
            } else {

                $msg = 'Please select product category';
            }
        } else {

            $msg = 'Please select product feature image.';
        }
    } else {

        $msg = 'Please enter product name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
