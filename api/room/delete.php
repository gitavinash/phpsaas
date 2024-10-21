<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $propertyId = base64_decode($id);
} else {
    $msg = 'Room id is required';
}

mysqli_query($connection, "START TRANSACTION");

if ($checkData = mysqli_query($connection, "SELECT room_id FROM " . TABLE_PREFIX . "room WHERE room_id={$propertyId} AND room_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {
        extract(mysqli_fetch_assoc($checkData));
        $deleteRoom = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "room WHERE room_id='{$room_id}'");
        if ($deleteRoom) {
            $status = 'success';
            $httpStatus = 200;
            $msg = 'Room delete successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $status = 'error';
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to delete property');
            mysqli_query($connection, "ROLLBACK");
        }
    } else {
        $msg = 'No room found';
    }
} else {
    $msg = 'Invalid room id';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
