<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

// Only handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($checkGallery = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "gallery WHERE gallery_id = '{$galleryId}' AND gallery_site_id = " . SITE_ID)) {
        if (mysqli_num_rows($checkGallery) > 0) {

            // Set the featured image
            $updateQuery = "UPDATE " . TABLE_PREFIX . "gallery SET gallery_image = '{$featureImage}' WHERE gallery_id = '{$galleryId}' AND gallery_category='{$categoryId}' AND gallery_site_id = " . SITE_ID;

            $updateTourPackageImage = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "tour_package SET tour_package_image='{$featureImage}' WHERE tour_package_id='{$categoryId}' AND tour_package_site_id=" . SITE_ID);

            if (mysqli_query($connection, $updateQuery)) {
                $httpStatus = 200;
                $status = 'success';
                $msg = 'Featured image set successfully';
            } else {
                $httpStatus = 200;
                $status = 'error';
                $msg = 'Failed to set featured image';
            }
        } else {
            $msg = 'Gallery not found';
        }
    } else {
        $msg = 'Invalid gallery ID';
    }
}

// Response
$result = array(
    'status' => $status,
    'msg' => $msg,
);

http_response_code($httpStatus);
echo json_encode($result, true);