<?php

function socialproof($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkSocialPost = mysqli_query($connection, "SELECT sp_id, link_id FROM " . TABLE_PREFIX . "social_proof INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=sp_id WHERE link_type='social_proof' AND link_site_id=" . SITE_ID . " AND sp_id='{$id}'")) {

            if (mysqli_num_rows($checkSocialPost) != 0) {

                extract(mysqli_fetch_assoc($checkSocialPost));

                mysqli_query($connection, "START TRANSACTION");

                $deleteSocialPost = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "social_proof WHERE sp_id='{$sp_id}' AND sp_site_id=" . SITE_ID);
                $deleteLink = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "link WHERE link_relation_id='{$sp_id}' AND link_type='social_proof' AND link_site_id=" . SITE_ID);
                $deleteMeta = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "meta WHERE meta_relation_id='{$link_id}' AND meta_type='social_proof' AND meta_site_id=" . SITE_ID);
                $deleteTag = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_id='{$sp_id}' AND tag_type='social_proof' AND tag_site_id=" . SITE_ID);

                if ($deleteSocialPost && $deleteLink && $deleteMeta && $deleteTag) {

                    $status = 200;
                    $msg = 'Social Post delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = 'Something is wrong while deleting social post';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Invalid request found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {

        $msg = 'Something is wrong while sending request to server';
    }

    $result = array(
        'status' => (isset($status)) ? $status : 'error',
        'msg' => ((isset($msg)) ? $msg : 'Invalid response')
    );

    echo json_encode($result, TRUE);
}
