<?php

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($title != '') {

        $pageId = base64_decode($id);

        require_once MODULES . 'imageUploader.php';

        $featureImage = ((isset($featureImage)) ? $featureImage : '');

        if (!isset($featureImage)) {

            /** if feature image is not upload then set featureImage as blank */

            $featureImage = '';
            $thumbnailImage = '';
        }

        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));


        /* define facebook and twitter meta desciption */

        if ($checkPage = mysqli_query($connection, "SELECT page_id FROM " . TABLE_PREFIX . "page WHERE page_id='{$pageId}' AND page_type='policy' AND page_site_id='" . SITE_ID . "'")) {
            if (mysqli_num_rows($checkPage) != 0) {
                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "page SET page_title='{$title}', page_description='{$description}' WHERE page_id='{$pageId}' AND page_type='policy' AND page_site_id=" . SITE_ID)) {

                    $status = 'success';
                    $msg = 'Policy update successfully';
                } else {
                    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong while updating policy');
                }
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
