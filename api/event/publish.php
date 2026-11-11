<?php

require_once LOGIN_CHECK;
require_once FUNCTIONS . 'duplicate.php';

function publish($connection, $data)
{

    extract($data);

    if (isset($title) && $title != '') {

        if (isset($category)) {

            if (isset($eventDate) && $eventDate != '') {

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
                $description = isset($description) ? $description : '';

                if ($featureImage != '') {

                    // Extract the single upload image string to variables

                    $featurePost = iswNormalizeFlag($featurePost ?? 0);

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';
                    }

                    $gallery = iswMergeDuplicateGallery(
                        $connection,
                        'event',
                        $_POST,
                        (isset($images) && is_array($images)) ? $images : array(),
                        SITE_ID,
                        false
                    );
                    $gallery = mysqli_real_escape_string($connection, $gallery);

                    /** Category */

                    $category = explode('_', $category);
                    $categoryURL = $category[1];
                    $category = $category[0];

                    /** event page url */

                    $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)($url ?? ''), '/')), '-');
                    if ($slugClean === '') {
                        $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)$title)), '-');
                    }
                    $url = strtolower($categoryURL . $slugClean) . '/';

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

                    $highlight = isset($highlight) ? $highlight : '';
                    $eventLocation = isset($eventLocation) ? $eventLocation : '';
                    $eventMap = isset($eventMap) ? $eventMap : '';
                    $eventTime = iswFormatTimeDb($eventTime ?? '');
                    $eventEndTime = iswFormatTimeDb($eventEndTime ?? '');

                    if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                        if (mysqli_num_rows($checkURL) == 0) {

                            require_once EDITOR_CONTENT_FILTER;

                            $link_status = 200;

                            /** Start uploading the event */

                            mysqli_query($connection, "START TRANSACTION");

                            /* Insert data into link table */

                            $parsedDate = strtotime(str_replace('/', '-', (string)$eventDate));
                            $eventDateSql = ($parsedDate !== false) ? date('Y-m-d', $parsedDate) : date('Y-m-d');

                            $addNewPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "event(event_site_id, event_image, event_thumbnail, event_title, event_description, event_highlight, event_start_date, event_start_time, event_end_time, event_location, event_map, event_category, event_owner_id, event_status, event_type, event_gallery) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$highlight}', '{$eventDateSql}', '{$eventTime}', '{$eventEndTime}', '{$eventLocation}', '{$eventMap}', '{$category}', '" . USER_ID . "', '1', 'event', '{$gallery}')");
                            $eventId = mysqli_insert_id($connection);

                            $sku = isset($sku) ? (string)$sku : '';
                            $salePrice = iswNormalizeDecimal($salePrice ?? 0);
                            $mrp = iswNormalizeDecimal($mrp ?? 0);
                            $taxStatus = iswNormalizeInt($taxStatus ?? 0);
                            $taxRate = iswNormalizeInt($taxRate ?? 0);
                            $in_stock = iswNormalizeFlag($in_stock ?? 0);
                            $currentStock = iswNormalizeInt($currentStock ?? 0);

                            $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_status, inventory_update, inventory_type) VALUES ('{$eventId}', " . SITE_ID . ", '{$sku}', 'INR', '{$salePrice}', '{$mrp}', '{$taxStatus}', '{$taxRate}', '{$in_stock}', '{$currentStock}', '1', current_timestamp(), 'event')");

                            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$eventId}', '{$link_status}', 'event', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                            $linkID = mysqli_insert_id($connection);

                            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'post', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                            /* Insert tags into tag table */

                            $addNewTags = TRUE;
                            $tagsStr = isset($tags) ? trim((string)$tags) : '';
                            if ($tagsStr != '') {

                                $tagsArr = explode(',', $tagsStr);

                                for ($i = 0; $i < sizeof($tagsArr); $i++) {

                                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($tagsArr[$i])));
                                    if ($tagsName !== '') {
                                        $tagInserted = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$eventId}', '" . USER_ID . "', '" . SITE_ID . "', 'event')");
                                        if (!$tagInserted) {
                                            $addNewTags = FALSE;
                                        }
                                    }
                                }
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
