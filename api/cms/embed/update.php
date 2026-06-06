<?php

/* Update video */

require_once LOGIN_CHECK;

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if ($checkPage = mysqli_query($connection, "SELECT link_id FROM " . TABLE_PREFIX . "video INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND video_id='{$id}'")) {

            if (mysqli_num_rows($checkPage) != 0) {

                extract(mysqli_fetch_assoc($checkPage));

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

                $url = '/video/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
                $linkID = $link_id;

                mysqli_query($connection, "START TRANSACTION");

                if ($oldURL != $url) {

                    if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='video' AND link_site_id='" . SITE_ID . "'")) {

                        if (mysqli_num_rows($checkURL) == 0) {

                            /** Add 301 redirect */

                            $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");

                            /** Add new active url of the video */

                            $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$id}', 200, '{$postType}', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())");
                            $linkID = mysqli_insert_id($connection);
                        } else {

                            $addRedirect = FALSE;
                            $status = 'warning';
                            $msg = "Please change video url. Because its already in used";
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

                /** Update video table */

                $updatePage = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "video SET video_image='{$featureImage}', video_thumbnail='{$thumbnailImage}', video_title='{$title}', video_description='{$description}', video_status='1' WHERE video_id='{$id}'");

                /** Update meta table */

                $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

                if ($addRedirect && $updateLink && $updatePage && $updateMeta) {

                    mysqli_query($connection, 'COMMIT');

                    $status = 200;
                    $msg = ucwords($postType) . ' is update successfully';
                    $redirect = '/dashboard/embed/?type=edit&editor=true&id=' . base64_encode($id);
                } else {

                    echo $addRedirect . ' && ' . $updateLink . ' && ' . $updatePage . ' && ' . $updateMeta . ' && ' . $tagData;
                    exit();
                    mysqli_query($connection, 'ROLLBACK');
                }
            } else {

                $msg = 'No embed information found';
            }
        } else {

            echo "SELECT link_id FROM " . TABLE_PREFIX . "video INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND video_id='{$id}'";
        }
    } else {

        $msg = "Please enter {$postType} title";
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating ' . $postType),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
