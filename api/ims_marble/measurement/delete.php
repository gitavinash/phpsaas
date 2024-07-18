<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $configurationId = base64_decode($id);
} else {
    $msg = 'Configuration id is required';
}

if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_id='{$configurationId}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {

        extract(mysqli_fetch_assoc($checkData));

        if (isset($deleteData)) {

            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_id='{$configurationId}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                $msg = "Configuration area removed successfully";
                $httpStatus = 202;
                $status = 'success';
            } else {
                $status = 'error';
                $msg = "Something went wrong while removing Configuration";
            }
        }
    }
} else {
    $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Invalid configuration id');
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
