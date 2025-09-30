<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $warehouseId = base64_decode($id);
} else {
    $msg = 'Warehouse id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_id={$warehouseId} and warehouse_status=1 AND warehouse_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {
            $deleteDataFromDatabase = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "warehouse SET warehouse_status=2 WHERE warehouse_id={$warehouseId}");
            if ($deleteDataFromDatabase) {
                $msg = ((SITE_APP == 'spa') ? 'Branch' : 'Warehouse') . " deleted successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = "Something went wrong while deleting warehouse";
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid warehouse id';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);