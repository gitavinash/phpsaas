<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $propertyId = base64_decode($id);
} else {
    $msg = 'Property id is required';
}

mysqli_query($connection, "START TRANSACTION");

if ($checkData = mysqli_query($connection, "SELECT property_id, link_id, meta_id FROM " . TABLE_PREFIX . "property INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=property_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id WHERE property_id={$propertyId} AND link_type='property' AND link_site_id=" . SITE_ID . " AND property_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {
        extract(mysqli_fetch_assoc($checkData));
        $deleteProperty = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "property WHERE property_id='{$property_id}'");
        $deleteLink = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "link WHERE link_id={$link_id}");
        $deleteMeta = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "meta WHERE meta_id={$meta_id}");
        $deleteRoom = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "room WHERE room_property_id='{$property_id}'");
        if ($deleteProperty && $deleteLink && $deleteMeta && $deleteRoom) {
            $status = 'success';
            $httpStatus = 200;
            $msg = 'Property delete successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $status = 'error';
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to delete property');
            mysqli_query($connection, "ROLLBACK");
        }
    }
} else {
    $msg = 'Invalid property id';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
