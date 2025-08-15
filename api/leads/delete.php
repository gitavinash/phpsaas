<?php

require_once LOGIN_CHECK;

$leadId = base64_decode($id);

mysqli_query($connection, "START TRANSACTION");

if ($checkLead = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$leadId}'  AND enquiry_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkLead) != 0) {

        extract(mysqli_fetch_assoc($checkLead));

        if ($updateLead = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$leadId}' AND enquiry_site_id=" . SITE_ID)) {
            $status = 'success';
            $responseMsg = "Lead delete successfully";
        } else {
            $status = 'error';
            $responseMsg = "Something went wrong while deleting lead";
        }
    } else {
        $status = 'warning';
        $responseMsg = 'Invalid lead information';
    }
} else {
    $status = 'error';
    $responseMsg = 'Something went wrong';
}

if ($status == 'success') {
    mysqli_query($connection, "COMMIT");
} else {
    mysqli_query($connection, "ROLLBACK");
}

echo json_encode(array(
    'status' => $status,
    'msg' => $responseMsg
), TRUE);


exit();