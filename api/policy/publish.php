<?php

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($title != '') {

        require_once MODULES . 'imageUploader.php';

        if (!isset($featureImage)) {

            /** if feature image is not upload then set featureImage as blank */

            $featureImage = '';
            $thumbnailImage = '';
        }

        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

        /** policy url */

        $url = '/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $title)) . '/';

        /* define facebook and twitter meta desciption */

        if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

            if (mysqli_num_rows($checkURL) == 0) {

                require_once EDITOR_CONTENT_FILTER;

                $link_status = 200;

                /** Start uploading the policy */

                mysqli_query($connection, "START TRANSACTION");

                /* Insert data into link table */

                $addNewPolicy = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "page(page_site_id, page_image, page_thumbnail, page_title, page_description, page_owner_id, page_status, page_type) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '" . USER_ID . "', '1', 'policy')");
                $pageID = mysqli_insert_id($connection);

                $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$pageID}', '{$link_status}', 'page', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                $linkID = mysqli_insert_id($connection);

                $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id) VALUES('{$linkID}', " . APP_SITE_ID . ")");

                if ($addNewPolicy && $addNewLink && $addNewMeta) {
                    $status = 'success';
                    $msg = 'Policy add successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $msg = 'Something is wrong while adding new policy';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Please change url';
            }
        } else {

            $msg = 'Technical errro';
        }
    } else {

        $msg = 'Please enter policy name';
    }
} else {
    $msg = 'Invalid request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
