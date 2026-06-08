<?php

/* Update Post */

require_once LOGIN_CHECK;

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($checkPost = mysqli_query($connection, "SELECT link_id, event_gallery FROM " . TABLE_PREFIX . "event INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND event_id='{$id}'")) {

                if (mysqli_num_rows($checkPost) != 0) {

                    extract(mysqli_fetch_assoc($checkPost));

                    require_once MODULES . 'imageUploader.php';

                    $featurePost = ((isset($featurePost)) ? 1 : 0);

                    if (isset($image)) {

                        /** when image is upload by user */

                        $featureImage = (((isset($featureImage)) ? $featureImage : $oldFeatureImage));
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $oldThumbnailImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    } else {

                        /** When image is not upload by user */

                        $featureImage = $oldFeatureImage;
                        $thumbnailImage = $featureImage;
                        $facebookImage = (($oldFacebookImage == '') ? $oldFeatureImage : $oldFacebookImage);
                        $twitterImage = (($oldTwitterImage == '') ? $oldFeatureImage : $oldTwitterImage);
                    }

                    /** Fetch category url */

                    $category = explode('_', $category);
                    $categoryURL = str_replace('/category/', '', $category[1]);
                    $category = $category[0];

                    $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
                    $linkID = $link_id;

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';

                        if ($gallery != '') {

                            $isGalleryUpdate = TRUE;
                        }
                    }

                    $oldGallery = json_decode($event_gallery, TRUE);

                    // Match old gallery db from from value
                    $newEventGallery = array();

                    if (isset($_POST['oldGallery'])) {

                        for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {

                            $j = $_POST['oldGallery'][$i] - 1;

                            $newEventGallery[] = $oldGallery[$j];
                        }
                    }

                    /** Update old gallery */

                    if (isset($isGalleryUpdate)) {

                        $newEventGallery = array_merge($newEventGallery, $images);
                    }

                    $newEventGallery = json_encode($newEventGallery, TRUE);

                    mysqli_query($connection, "START TRANSACTION");

                    if ($oldURL != $url) {

                        if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='post' AND link_site_id='" . SITE_ID . "'")) {

                            if (mysqli_num_rows($checkURL) == 0) {

                                /** Add 301 redirect */

                                $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");

                                /** Add new active url of the post */

                                $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$postType}', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), '{$featurePost}')");
                                $linkID = mysqli_insert_id($connection);
                            } else {

                                $addRedirect = FALSE;
                                $status = 'warning';
                                $msg = "Please change post url. Because its already in used";
                            }
                        }
                    } else {

                        $addRedirect = TRUE;

                        if ($postStatus == 0) {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        } else {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        }
                    }

                    /** Update post table */

                    $updatePost = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "event SET event_image='{$featureImage}', event_thumbnail='{$thumbnailImage}', event_title='{$title}', event_description='{$description}', event_highlight='{$highlight}', event_category='{$category}', event_start_date='{$eventDate}', event_end_time='{$eventEndTime}', event_location='{$eventLocation}', event_map='{$eventMap}', event_status='1', event_gallery='{$newEventGallery}' WHERE event_id='{$id}'");

                    // Update inventory table
                    if (isset($salePrice) && isset($mrp)) {

                        $updateInventory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "inventory SET inventory_sku='{$sku}', inventory_price='{$salePrice}', inventory_mrp='{$mrp}', inventory_tax_status='{$taxStatus}', inventory_tax_rate='{$taxRate}', inventory_in_stock='{$in_stock}', inventory_current_stock='{$currentStock}' WHERE inventory_relation_id='{$id}' AND inventory_type='event' AND inventory_site_id='" . SITE_ID . "'");
                    } else {
                        $updateInventory = TRUE;
                    }

                    /** Update meta table */

                    $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

                    /** Manage Tags  */

                    $deleteTags = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$id}' AND tag_site_id=" . SITE_ID . " AND tag_type='{$postType}'");
                    $tags = explode(',', $tags);

                    /* Insert tags into tag table */

                    for ($i = 0; $i < sizeof($tags); $i++) {

                        if ($tags != '') {

                            $tagName = strtolower($tags[$i]);
                            $tagData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$id}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                        } else {

                            $tagData = '';
                        }
                    }

                    if ($addRedirect && $updateLink && $updatePost && $updateMeta && $tagData && $updateInventory) {

                        mysqli_query($connection, 'COMMIT');

                        $status = 200;
                        $msg = 'Event is update successfully';
                        $redirect = '/dashboard/' . $postType . '/?type=edit&editor=true&id=' . base64_encode($id);
                    } else {

                        echo $addRedirect . ' && ' . $updateLink . ' && ' . $updatePost . ' && ' . $updateMeta . ' && ' . $tagData;
                        mysqli_query($connection, 'ROLLBACK');
                    }
                } else {

                    $msg = 'No event data found';
                }
            }
        } else {

            $msg = 'Please select category';
        }
    } else {

        $msg = 'Please enter post title';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating ' . $postType),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
