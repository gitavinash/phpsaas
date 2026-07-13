<?php

/* Update team member */

require_once LOGIN_CHECK;

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if ($checkTeamMember = mysqli_query($connection, "SELECT link_id FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND post_id='{$id}'")) {

            if (mysqli_num_rows($checkTeamMember) != 0) {

                extract(mysqli_fetch_assoc($checkTeamMember));

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

                $url = '/team/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
                $linkID = $link_id;

                mysqli_query($connection, "START TRANSACTION");

                if ($oldURL != $url) {

                    if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='post' AND link_site_id='" . SITE_ID . "'")) {

                        if (mysqli_num_rows($checkURL) == 0) {

                            /** Add 301 redirect */

                            $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");

                            /** Add new active url of the team member */

                            $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$id}', 200, 'post', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())");
                            $linkID = mysqli_insert_id($connection);
                        } else {

                            $addRedirect = FALSE;
                            $status = 'warning';
                            $msg = "Please change team member url. Because its already in used";
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

                /** Update post table */

                $updateTeamMember = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "post SET post_image='{$featureImage}', post_thumbnail='{$thumbnailImage}', post_title='{$title}', post_description='{$description}', post_status='1', post_category='{$category}' WHERE post_id='{$id}'");

                /** Update meta table */

                $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

                if ($addRedirect && $updateLink && $updateTeamMember && $updateMeta) {

                    mysqli_query($connection, 'COMMIT');

                    $status = 200;
                    $msg = 'Team member is update successfully';
                    $redirect = '/dashboard/team/?type=edit&editor=true&id=' . base64_encode($id);
                } else {
                    $msg = "Somthing is wrong while updating team member";
                    mysqli_query($connection, 'ROLLBACK');
                }
            } else {

                $msg = 'No team member information found';
            }
        }
    } else {

        $msg = "Please enter team member title";
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 500),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating team member'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
