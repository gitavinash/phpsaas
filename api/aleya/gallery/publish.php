<?php

require_once LOGIN_CHECK;
$responseCode = 500;

$packageId = base64_decode($packageId);

if ($checkPackage = mysqli_query($connection, "SELECT gallery_image_list FROM " . TABLE_PREFIX . "tour_package LEFT JOIN " . TABLE_PREFIX . "gallery ON gallery_parent_id=tour_package_id AND gallery_site_id=" . SITE_ID . " AND tour_package_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkPackage) != 0) {
        extract(mysqli_fetch_assoc($checkPackage));
        print_r($gallery_image_list);
        if (isset($_FILES['gallery'])) {
            require_once MODULES . 'galleryUploader.php';
            $gallery = json_encode($images, TRUE);
            echo $gallery;
        }
    } else {
        $responseCode = 400;
        $msg = 'Invalid request found';
    }
} else {
    $msg = "Something went wrong";
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, true);