<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $userId = base64_decode($id);
} else {
    $msg = 'User id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON user_id=site_access_user_id WHERE user_id={$userId} and user_status=1 AND site_access_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {
            $deleteDataFromDatabase = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_access SET site_access_status=2 WHERE site_access_user_id={$userId} AND site_access_site_id=" . SITE_ID);
            if ($deleteDataFromDatabase) {
                $msg = "User removed successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = "Something went wrong while deleting user";
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid user id';
        }
    } else {
        $msg = "SELECT * FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON user_id=site_access_user_id WHERE user_id={$userId} and user_status=1 AND user_site_id=" . SITE_ID;
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);