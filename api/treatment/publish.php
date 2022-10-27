<?php

function publish($connection, $data)
{
    extract($data);

    if ($title != '') {

        require_once MODULES . 'imageUploader.php';

        if (isset($image['featureImage'])) {

            // Extract the single upload image string to variables

            $featurePost = ((isset($featurePost)) ? 1 : 0);
            $postType = ((isset($postType)) ? $postType : $type);
            $currentStock = ((isset($currentStock)) ? $currentStock : 0);

            $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
            $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

            if (isset($_FILES['gallery'])) {

                require_once MODULES . 'galleryUploader.php';
            } else {

                $gallery = '';
            }

            $service_section = json_encode(array(
                'feature_video' => array(
                    'url' => $featureVideo
                ),
                'quote' => array(
                    'quote' => $quote
                ),
                'treatment_option' => array(
                    0 => array(
                        'image' => $treatmentOptionFeatureImage_1,
                        'header' => $treatmentOptionHeader_1,
                        'indications' => $treatmentOptionIndications_1,
                        'process' => $treatmentOptionProcess_1,
                        'duration' => $treatmentOptionDuration_1,
                        'sitting' => $treatmentOptionSitting_1,
                        'bookNow' => $treatmentOptionBookNow_1,
                        'watchVideo' => $treatmentOptionWatchVideo_1,
                    ),
                    1 => array(
                        'image' => $treatmentOptionFeatureImage_2,
                        'header' => $treatmentOptionHeader_2,
                        'indications' => $treatmentOptionIndications_2,
                        'process' => $treatmentOptionProcess_2,
                        'duration' => $treatmentOptionDuration_2,
                        'sitting' => $treatmentOptionSitting_2,
                        'bookNow' => $treatmentOptionBookNow_2,
                        'watchVideo' => $treatmentOptionWatchVideo_2,
                    ),
                    2 => array(
                        'image' => $treatmentOptionFeatureImage_3,
                        'header' => $treatmentOptionHeader_3,
                        'indications' => $treatmentOptionIndications_3,
                        'process' => $treatmentOptionProcess_3,
                        'duration' => $treatmentOptionDuration_3,
                        'sitting' => $treatmentOptionSitting_3,
                        'bookNow' => $treatmentOptionBookNow_3,
                        'watchVideo' => $treatmentOptionWatchVideo_3,
                    ),
                    3 => array(
                        'image' => $treatmentOptionFeatureImage_4,
                        'header' => $treatmentOptionHeader_4,
                        'indications' => $treatmentOptionIndications_4,
                        'process' => $treatmentOptionProcess_4,
                        'duration' => $treatmentOptionDuration_4,
                        'sitting' => $treatmentOptionSitting_4,
                        'bookNow' => $treatmentOptionBookNow_4,
                        'watchVideo' => $treatmentOptionWatchVideo_4,
                    ),
                    4 => array(
                        'image' => $treatmentOptionFeatureImage_5,
                        'header' => $treatmentOptionHeader_5,
                        'indications' => $treatmentOptionIndications_5,
                        'process' => $treatmentOptionProcess_5,
                        'duration' => $treatmentOptionDuration_5,
                        'sitting' => $treatmentOptionSitting_5,
                        'bookNow' => $treatmentOptionBookNow_5,
                        'watchVideo' => $treatmentOptionWatchVideo_5,
                    ),
                    5 => array(
                        'image' => $treatmentOptionFeatureImage_6,
                        'header' => $treatmentOptionHeader_6,
                        'indications' => $treatmentOptionIndications_6,
                        'process' => $treatmentOptionProcess_6,
                        'duration' => $treatmentOptionDuration_6,
                        'sitting' => $treatmentOptionSitting_6,
                        'bookNow' => $treatmentOptionBookNow_6,
                        'watchVideo' => $treatmentOptionWatchVideo_6,
                    ),
                    6 => array(
                        'image' => $treatmentOptionFeatureImage_7,
                        'header' => $treatmentOptionHeader_7,
                        'indications' => $treatmentOptionIndications_7,
                        'process' => $treatmentOptionProcess_7,
                        'duration' => $treatmentOptionDuration_7,
                        'sitting' => $treatmentOptionSitting_7,
                        'bookNow' => $treatmentOptionBookNow_7,
                        'watchVideo' => $treatmentOptionWatchVideo_7,
                    ),
                    7 => array(
                        'image' => $treatmentOptionFeatureImage_8,
                        'header' => $treatmentOptionHeader_8,
                        'indications' => $treatmentOptionIndications_8,
                        'process' => $treatmentOptionProcess_8,
                        'duration' => $treatmentOptionDuration_8,
                        'sitting' => $treatmentOptionSitting_8,
                        'bookNow' => $treatmentOptionBookNow_8,
                        'watchVideo' => $treatmentOptionWatchVideo_8,
                    ),
                    8 => array(
                        'image' => $treatmentOptionFeatureImage_9,
                        'header' => $treatmentOptionHeader_9,
                        'indications' => $treatmentOptionIndications_9,
                        'process' => $treatmentOptionProcess_9,
                        'duration' => $treatmentOptionDuration_9,
                        'sitting' => $treatmentOptionSitting_9,
                        'bookNow' => $treatmentOptionBookNow_9,
                        'watchVideo' => $treatmentOptionWatchVideo_9,
                    ),
                    9 => array(
                        'image' => $treatmentOptionFeatureImage_10,
                        'header' => $treatmentOptionHeader_10,
                        'indications' => $treatmentOptionIndications_10,
                        'process' => $treatmentOptionProcess_10,
                        'duration' => $treatmentOptionDuration_10,
                        'sitting' => $treatmentOptionSitting_10,
                        'bookNow' => $treatmentOptionBookNow_10,
                        'watchVideo' => $treatmentOptionWatchVideo_10,
                    ),
                ),
                'before_after' => array(
                    'header' => $berforeAfterHeader,
                    'subHeader' => $berforeAfterSubHeader,
                    'description' => $berforeAfterDescription,
                    'videoLink' => $berforeAfterVideLink,
                    'images' => array(
                        'before' => $before,
                        'after' => $after
                    )
                ),
                'why_choose_us' => array(
                    'image' => $whyChooseUs,
                    'header' => $whyChooseTitle,
                    'description' => $whyChooseDescription,
                    'steps' => array(
                        array(
                            'icon' => $stepIcon_1,
                            'description' => $stepHeader_1
                        ),
                        array(
                            'icon' => $stepIcon_2,
                            'description' => $stepHeader_2
                        ),
                        array(
                            'icon' => $stepIcon_3,
                            'description' => $stepHeader_3
                        ),
                    )
                ),
            ), true);

            $service_section = base64_encode($service_section);

            if (isset($_FILES['gallery'])) {

                require_once MODULES . 'galleryUploader.php';
            }

            /** Category */

            if (isset($category)) {

                $category = explode('_', $category);
                $categoryURL = $category[1];
                $category = $category[0];
            } else {

                $category = 0;
                $categoryURL = '/';
            }

            /** treatment page url */

            $url = strtolower('/treatment' . $categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

            /* define facebook and twitter meta desciption */

            $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
            $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

            if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                if (mysqli_num_rows($checkURL) == 0) {

                    require_once EDITOR_CONTENT_FILTER;

                    /** Start uploading the treatment */

                    mysqli_query($connection, "START TRANSACTION");

                    /* Insert data into link table */

                    $addNewTreatment = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "service(service_site_id, service_image, service_thumbnail, service_gallery, service_title, service_description, service_highlight, service_category, service_owner_id, service_status, service_type, service_section) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$title}', '{$description}', '', '{$category}', " . USER_ID . ",'1', 'treatment', '{$service_section}')");
                    $treatmentID = mysqli_insert_id($connection);

                    $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$treatmentID}', '200', 'service', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                    $linkID = mysqli_insert_id($connection);

                    $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_type, inventory_status, inventory_update) VALUES ('{$treatmentID}', '', 'INR', '', '', 'service', '1', current_timestamp())");

                    $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'service', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                    /* Insert tags into tag table */

                    if ($tags != '') {

                        $tags = explode(',', $tags);

                        for ($i = 0; $i < sizeof($tags); $i++) {

                            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                            $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                        }
                    } else {

                        $addNewTags = TRUE;
                    }

                    if ($addNewTreatment && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {

                        $status = 200;
                        $msg = 'Treatment add successfully';
                        $redirect = '/dashboard/treatment/?type=edit&editor=true&id=' . base64_encode($treatmentID);
                        mysqli_query($connection, "COMMIT");
                    } else {

                        $msg = 'Something is wrong while adding new Treatment';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {

                    $msg = 'Invalid activity';
                }
            } else {

                $msg = 'Technical errro';
            }
        } else {

            $msg = 'Please select treatment feature image.';
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