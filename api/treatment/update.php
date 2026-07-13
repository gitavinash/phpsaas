<?php

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($checkTreatment = mysqli_query($connection, "SELECT link_id, meta_relation_id as oldLinkID, treatment_section, treatment_gallery FROM " . TABLE_PREFIX . "treatment INNER JOIN " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_url='{$oldURL}' AND meta_type='treatment' AND treatment_id='{$id}'")) {

                if (mysqli_num_rows($checkTreatment) != 0) {

                    extract(mysqli_fetch_assoc($checkTreatment));

                    require_once MODULES . 'imageUploader.php';

                    if (isset($image)) {

                        // Extract the single upload image string to variables

                        $featureImage = (((isset($featureImage)) ? $featureImage : $oldFeatureImage));
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $featureImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    } else {

                        $featureImage = $oldFeatureImage;
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $oldFeatureImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    }


                    $in_stock = ((isset($in_stock)) ? 1 : 0);
                    $backorder = ((isset($backorder)) ? 1 : 0);
                    $postType = ((isset($postType)) ? $postType : $type);
                    $currentStock = ((isset($currentStock)) ? $currentStock : 0);
                    $highlight = ((isset($highlight)) ? $highlight : '');
                    $sku = ((isset($sku)) ? $sku : '');
                    $salePrice = ((isset($salePrice)) ? $salePrice : '');
                    $mrp = ((isset($mrp)) ? $mrp : '');
                    $taxStatus = ((isset($taxStatus)) ? $taxStatus : '');
                    $taxRate = ((isset($taxRate)) ? $taxRate : '');
                    $stock_warning = ((isset($stock_warning)) ? $stock_warning : '');
                    $weight = ((isset($weight)) ? $weight : '');
                    $lenght = ((isset($lenght)) ? $lenght : '');
                    $width = ((isset($width)) ? $width : '');
                    $height = ((isset($height)) ? $height : '');

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';

                        if ($gallery != '') {

                            $isGalleryUpdate = TRUE;
                        }
                    }

                    $oldGallery = json_decode($treatment_gallery, TRUE);

                    // Match old gallery db from from value
                    $newProductGallery = array();

                    if (isset($_POST['oldGallery'])) {

                        for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {

                            $j = $_POST['oldGallery'][$i] - 1;

                            $newProductGallery[] = $oldGallery[$j];
                        }
                    }

                    /** Update old gallery */

                    if (isset($isGalleryUpdate)) {

                        $newProductGallery = array_merge($newProductGallery, $images);
                    }

                    $newProductGallery = json_encode($newProductGallery, TRUE);

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';
                    } else {

                        $gallery = $oldGallery;
                    }

                    /** Category */

                    $category = explode('_', $category);
                    $categoryURL = $category[1];
                    $category = $category[0];

                    /** treatment page url */

                    $url = strtolower('/treatment' . $categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

                    /* define facebook and twitter meta desciption */

                    $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
                    $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

                    /** Start updating in treatments */

                    mysqli_query($connection, "START TRANSACTION");

                    if ($oldURL != $url) {

                        if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='treatment' AND link_site_id='" . SITE_ID . "'")) {

                            if (mysqli_num_rows($checkURL) == 0) {

                                /** Add 301 redirect */

                                $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_type='treatment' AND link_site_id='" . SITE_ID . "'");

                                /** Add new active url of the post */

                                $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, 'treatment', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), '{$featurePost}')");
                                $link_id = mysqli_insert_id($connection);
                            } else {

                                $addRedirect = FALSE;
                                $status = 'warning';
                                $msg = "Please change post url. Because its already in used";
                            }
                        }
                    } else {

                        $addRedirect = TRUE;

                        $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='treatment' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                    }

                    $updateservice = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "treatment SET treatment_image='{$featureImage}', treatment_thumbnail='{$thumbnailImage}', treatment_gallery='{$newProductGallery}', treatment_title='{$title}', treatment_description='{$description}', treatment_highlight='{$highlight}', treatment_category='{$category}', treatment_owner_id='" . USER_ID . "', treatment_status=1, treatment_type='treatment' WHERE treatment_id='{$id}'");

                    $updateInventtory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "inventory SET inventory_sku='{$sku}', inventory_currency='INR', inventory_price='{$salePrice}', inventory_mrp='{$mrp}', inventory_tax_status='{$taxStatus}', inventory_tax_rate='{$taxRate}', inventory_in_stock='{$in_stock}', inventory_current_stock='{$currentStock}', inventory_backorder='{$backorder}', inventory_stock_notice='{$stock_warning}', inventory_weight='{$weight}', inventory_lenght='{$lenght}', inventory_width='{$width}', inventory_height='{$height}', inventory_status=1 WHERE inventory_relation_id='{$id}'");

                    $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$link_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$oldLinkID}'");

                    $deleteTags = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$id}' AND tag_site_id=" . SITE_ID . " AND tag_type='treatment'");

                    /* Insert tags into tag table */

                    if ($tags != '') {

                        $tags = explode(',', $tags);

                        for ($i = 0; $i < sizeof($tags); $i++) {

                            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                            $updateTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$id}', '" . USER_ID . "', '" . SITE_ID . "', 'treatment')");
                        }
                    } else {

                        $updateTags = TRUE;
                    }

                    if ($updateservice && $addRedirect && $updateInventtory && $updateMeta && $updateTags) {

                        $status = 200;
                        $msg = 'Treatment upload successfully';
                        $redirect = "/dashboard/treatment/?type=edit&editor=true&id=" . base64_encode($id);
                        mysqli_query($connection, "COMMIT");
                    } else {

                        echo $updateservice . ' && ' . $addRedirect . ' && ' . $updateInventtory . ' && ' . $updateMeta . ' && ' . $updateTags;
                        $msg = 'Something is wrong while updateing treatment';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {

                    $msg = 'Invalid value received';
                }
            }
        } else {

            $msg = 'Please select treatment category';
        }
    } else {

        $msg = 'Please enter treatment name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
