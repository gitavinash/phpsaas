<?php

/* Update page */

require_once LOGIN_CHECK;

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if ($checkPage = mysqli_query($connection, "SELECT link_id, page_section FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND page_id='{$id}'")) {

            if (mysqli_num_rows($checkPage) != 0) {

                extract(mysqli_fetch_assoc($checkPage));

                $pageSection = json_decode(base64_decode($page_section), true);

                require_once MODULES . 'imageUploader.php';

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

                $url = '/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
                $linkID = $link_id;


                $updatedPageSection = json_encode(array(
                    'technology_option' => array(
                        0 => array(
                            'image' => (($technologyOptionFeatureImage_1 != '') ? $technologyOptionFeatureImage_1 : $pageSection['technology_option'][0]['image']),
                            'header' => $technologyOptionHeader_1,
                            'description' => $technologyOptionDescription_1,
                        ),
                        1 => array(
                            'image' => (($technologyOptionFeatureImage_2 != '') ? $technologyOptionFeatureImage_2 : $pageSection['technology_option'][1]['image']),
                            'header' => $technologyOptionHeader_2,
                            'description' => $technologyOptionDescription_2,
                        ),
                        2 => array(
                            'image' => (($technologyOptionFeatureImage_3 != '') ? $technologyOptionFeatureImage_3 : $pageSection['technology_option'][2]['image']),
                            'header' => $technologyOptionHeader_3,
                            'description' => $technologyOptionDescription_3,
                        ),
                        3 => array(
                            'image' => (($technologyOptionFeatureImage_4 != '') ? $technologyOptionFeatureImage_4 : $pageSection['technology_option'][3]['image']),
                            'header' => $technologyOptionHeader_4,
                            'description' => $technologyOptionDescription_4,
                        ),
                        4 => array(
                            'image' => (($technologyOptionFeatureImage_5 != '') ? $technologyOptionFeatureImage_5 : $pageSection['technology_option'][4]['image']),
                            'header' => $technologyOptionHeader_5,
                            'description' => $technologyOptionDescription_5,
                        ),
                        5 => array(
                            'image' => (($technologyOptionFeatureImage_6 != '') ? $technologyOptionFeatureImage_6 : $pageSection['technology_option'][5]['image']),
                            'header' => $technologyOptionHeader_6,
                            'description' => $technologyOptionDescription_6,
                        ),
                        6 => array(
                            'image' => (($technologyOptionFeatureImage_7 != '') ? $technologyOptionFeatureImage_7 : $pageSection['technology_option'][6]['image']),
                            'header' => $technologyOptionHeader_7,
                            'description' => $technologyOptionDescription_7,
                        ),
                        7 => array(
                            'image' => (($technologyOptionFeatureImage_8 != '') ? $technologyOptionFeatureImage_8 : $pageSection['technology_option'][7]['image']),
                            'header' => $technologyOptionHeader_8,
                            'description' => $technologyOptionDescription_8,
                        ),
                        8 => array(
                            'image' => (($technologyOptionFeatureImage_9 != '') ? $technologyOptionFeatureImage_9 : $pageSection['technology_option'][8]['image']),
                            'header' => $technologyOptionHeader_9,
                            'description' => $technologyOptionDescription_9,
                        ),
                        9 => array(
                            'image' => (($technologyOptionFeatureImage_10 != '') ? $technologyOptionFeatureImage_10 : $pageSection['technology_option'][9]['image']),
                            'header' => $technologyOptionHeader_10,
                            'description' => $technologyOptionDescription_10,
                        ),
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

                $updatedPageSection = base64_encode($updatedPageSection);

                mysqli_query($connection, "START TRANSACTION");

                if ($oldURL != $url) {

                    if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='page' AND link_site_id='" . SITE_ID . "'")) {

                        if (mysqli_num_rows($checkURL) == 0) {

                            /** Add 301 redirect */

                            $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");

                            /** Add new active url of the page */

                            $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$id}', 200, '{$postType}', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())");
                            $linkID = mysqli_insert_id($connection);
                        } else {

                            $addRedirect = FALSE;
                            $status = 'warning';
                            $msg = "Please change page url. Because its already in used";
                        }
                    }
                } else {

                    $addRedirect = TRUE;

                    if ($postStatus == 0) {

                        $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW() WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                    } else {

                        $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200 WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                    }
                }

                /** Update page table */

                $updatePage = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "page SET page_image='{$featureImage}', page_thumbnail='{$thumbnailImage}', page_title='{$title}', page_description='{$description}', page_highlight='{$highlight}', page_section='{$updatedPageSection}', page_status='1' WHERE page_id='{$id}'");

                /** Update meta table */

                $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

                if ($addRedirect && $updateLink && $updatePage && $updateMeta) {

                    mysqli_query($connection, 'COMMIT');

                    $status = 200;
                    $msg = ucwords($postType) . ' is update successfully';
                    $redirect = '/dashboard/' . $postType . '/?type=edit&editor=true&id=' . base64_encode($id);
                } else {

                    echo $addRedirect . ' && ' . $updateLink . ' && ' . $updatePage . ' && ' . $updateMeta . ' && ' . $tagData;

                    $msg = "Somthing is wrong while updating {$postType}";
                    mysqli_query($connection, 'ROLLBACK');
                }
            } else {

                $msg = 'Invalid activity found';
            }
        }
    } else {

        $msg = "Please enter {$postType} title";
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 500),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating ' . $postType),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}