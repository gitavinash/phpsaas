<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $requestId = base64_decode($id);
} else {
    $msg = 'Request id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT request_data, request_relation_id FROM " . TABLE_PREFIX . "request WHERE request_id={$requestId} AND request_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {

            extract(mysqli_fetch_assoc($checkData));

            $requestData = json_decode($request_data, TRUE);
            $oldBlockStatus = $requestData['data']['old_status'];

            // Delete request

            $deleteDataFromDatabase = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "request WHERE request_id={$requestId} AND request_site_id=" . SITE_ID);

            // Update block status to old status

            $updateBlockStatus = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status={$oldBlockStatus} WHERE block_id='{$request_relation_id}' AND block_site_id=" . SITE_ID);

            if (isset($requestData['data']['hide_slabs_id_array'])) {
                $updateBlockStatus = false;
                $msg = 'Feature is under development';
            }

            if ($deleteDataFromDatabase && $updateBlockStatus) {
                $msg = "Request deleted successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : "Something went wrong while deleting request");
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid request id';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
