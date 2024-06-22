<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $companyId = base64_decode($id);
} else {
    $msg = 'Company id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company WHERE company_id={$companyId} and company_status=1 AND company_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {
            $deleteDataFromDatabase = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "company SET company_status=2 WHERE company_id={$companyId}");
            if ($deleteDataFromDatabase) {
                $msg = "Company deleted successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = "Something went wrong while deleting company";
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid company id';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);