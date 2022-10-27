<?php

function publish($connection, $data)
{
    extract($data);

    if ($title != '') {

        $sku = ((isset($sku)) ? $sku : '');
        $salePrice = ((isset($salePrice)) ? $salePrice : '');
        $mrp = ((isset($mrp)) ? $mrp : '');
        $taxStatus = ((isset($taxStatus)) ? $taxStatus : '');
        $taxRate = ((isset($taxRate)) ? $taxRate : '');
        $stock_warning = ((isset($stock_warning)) ? $stock_warning : '');
        $weight = ((isset($weight)) ? $weight : '');
        $height = ((isset($height)) ? $height : '');
        $width = ((isset($width)) ? $width : '');
        $lenght = ((isset($lenght)) ? $lenght : '');
        $highlight = ((isset($highlight)) ? $highlight : '');

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
                'treatment_steps' => array(
                    'header' => $treatment_header,
                    'description' => $treatment_description,
                    'steps' => array(
                        array(
                            'image' => $treatmentImage_1,
                            'header' => $treatmentImageHeader_1,
                            'description' => $treatmentImageDescription_1
                        ),
                        array(
                            'image' => $treatmentImage_2,
                            'header' => $treatmentImageHeader_2,
                            'description' => $treatmentImageDescription_2
                        ),
                        array(
                            'image' => $treatmentImage_3,
                            'header' => $treatmentImageHeader_3,
                            'description' => $treatmentImageDescription_3
                        )
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
                'watch_video' => array(
                    'image' => $watchVideoImage,
                    'header' => $watchVideoHeader,
                    'description' => $watchVideoDescription,
                    'videoLink' => $watchVideoLink
                ),
                'our_commitment' => array(
                    'image' => $commitmentImage,
                    'header' => $commitmentHeader,
                    'subHeader' => $commitmentSubHeader,
                    'descripton' => $commitmentDescription
                ),
                'faq' => array(
                    0 => array(
                        'question' => $faqQuestion1,
                        'answer' => $faqAnswer1
                    ),
                    1 => array(
                        'question' => $faqQuestion2,
                        'answer' => $faqAnswer2
                    ),
                    2 => array(
                        'question' => $faqQuestion3,
                        'answer' => $faqAnswer3
                    ),
                    3 => array(
                        'question' => $faqQuestion4,
                        'answer' => $faqAnswer4
                    ),
                    4 => array(
                        'question' => $faqQuestion5,
                        'answer' => $faqAnswer5
                    )
                )
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

            /** service page url */

            $url = strtolower('/service' . $categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

            /* define facebook and twitter meta desciption */

            $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
            $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

            if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                if (mysqli_num_rows($checkURL) == 0) {

                    require_once EDITOR_CONTENT_FILTER;

                    /** Start uploading the service */

                    mysqli_query($connection, "START TRANSACTION");

                    /* Insert data into link table */

                    $addNewservice = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "service(service_site_id, service_image, service_thumbnail, service_gallery, service_title, service_description, service_highlight, service_category, service_owner_id, service_status, service_type, service_section) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$title}', '{$description}', '', '{$category}', " . USER_ID . ",'1', 'service', '{$service_section}')");
                    $serviceID = mysqli_insert_id($connection);

                    $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$serviceID}', '200', 'service', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                    $linkID = mysqli_insert_id($connection);

                    $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_type, inventory_status, inventory_update) VALUES ('{$serviceID}', '', 'INR', '', '', 'service', '1', current_timestamp())");

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

                    if ($addNewservice && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {

                        $status = 200;
                        $msg = 'Service add successfully';
                        $redirect = '/dashboard/service/?type=edit&editor=true&id=' . base64_encode($serviceID);
                        mysqli_query($connection, "COMMIT");
                    } else {

                        $msg = 'Something is wrong while adding new service';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {

                    $msg = 'Invalid activity';
                }
            } else {

                $msg = 'Technical errro';
            }
        } else {

            $msg = 'Please select service feature image.';
        }
    } else {

        $msg = 'Please enter service name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}