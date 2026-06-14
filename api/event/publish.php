<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($eventDate != '') {

                require_once MODULES . 'imageUploader.php';

                if (isset($image['featureImage'])) {

                    // Extract the single upload image string to variables



                    $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
                    $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';
                    } else {

                        $gallery = '';
                    }

                    /** Category */

                    $category = explode('_', $category);
                    $categoryURL = $category[1];
                    $category = $category[0];

                    /** event page url */

                    $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

                    /* define facebook and twitter meta desciption */

                    $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
                    $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

                    if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                        if (mysqli_num_rows($checkURL) == 0) {

                            require_once EDITOR_CONTENT_FILTER;

                            $link_status = 200;

                            /** Start uploading the event */

                            mysqli_query($connection, "START TRANSACTION");

                            /* Insert data into link table */

                            $eventDate = date('Y-m-d', strtotime($eventDate));

                            $addNewPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "event(event_site_id, event_image, event_thumbnail, event_title, event_description, event_highlight, event_start_date, event_start_time, event_end_time, event_location, event_map, event_category, event_owner_id, event_status, event_type, event_gallery) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$highlight}', '{$eventDate}', '{$eventTime}', '{$eventEndTime}', '{$eventLocation}', '{$eventMap}', '{$category}', '" . USER_ID . "', '1', 'event', '{$gallery}')");
                            $eventId = mysqli_insert_id($connection);

                            if (isset($salePrice) && isset($mrp)) {

                                $salePrice = ((is_numeric($salePrice)) ? $salePrice : 0);
                                $mrp = ((is_numeric($mrp)) ? $mrp : 0);
                                $taxRate = ((is_numeric($taxRate)) ? $taxRate : 0);
                                $in_stock = ((is_numeric($in_stock)) ? $in_stock : 0);
                                $currentStock = ((is_numeric($currentStock)) ? $currentStock : 0);

                                $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_status, inventory_update, inventory_type) VALUES ('{$eventId}', " . SITE_ID . ", '{$sku}', 'INR', '{$salePrice}', '{$mrp}', '{$taxStatus}', '{$taxRate}', '{$in_stock}', '{$currentStock}', '1', current_timestamp(), 'event')");
                            } else {
                                $addNewInventory = TRUE;
                            }

                            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$eventId}', '{$link_status}', 'event', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                            $linkID = mysqli_insert_id($connection);

                            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . APP_SITE_ID . ", '{$meta_title}', 'post', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                            /* Insert tags into tag table */

                            if ($tags != '') {

                                $tags = explode(',', $tags);

                                for ($i = 0; $i < sizeof($tags); $i++) {

                                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                    $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$eventId}', '" . USER_ID . "', '" . SITE_ID . "', 'event')");
                                }
                            } else {

                                $addNewTags = TRUE;
                            }

                            if ($addNewPost && $addNewLink && $addNewMeta && $addNewTags && $addNewInventory) {

                                $status = 200;
                                $msg = 'Event add successfully';
                                $redirect = '/dashboard/event/?type=edit&editor=true&id=' . base64_encode($eventId);
                                mysqli_query($connection, "COMMIT");
                            } else {

                                $msg = 'Something is wrong while adding new post';

                                mysqli_query($connection, "ROLLBACK");
                            }
                        } else {

                            $msg = 'Please change url';
                        }
                    } else {

                        $msg = 'Technical errro';
                    }
                } else {

                    $msg = 'Please select event feature image.';
                }
            } else {

                $msg = 'Please select event date';
            }
        } else {

            $msg = 'Please select category';
        }
    } else {

        $msg = 'Please enter event name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
