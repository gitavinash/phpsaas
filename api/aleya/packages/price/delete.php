<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $priceId = base64_decode($id);
} else {
    $msg = 'Price id is required';
}

if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_id='{$priceId}' AND tour_package_price_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {

        extract(mysqli_fetch_assoc($checkData));
        if (isset($deleteData)) {
            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_id='{$priceId}' AND tour_package_price_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Price is deleted successfully';
            } else {
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : "Something went wrong while deliting price");
            }
        }
    } else {
        $httpStatus = 204;
        $msg = 'Price id is not found';
    }
} else {
    $msg = 'Invalid price id';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
