<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $followupId = base64_decode($id);
} else {
    $msg = 'Follow up id is required';
}

if (isset($deleteData)) {
    if ($checkFollowup = mysqli_query($connection, "SELECT enquiry_notes_id FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$followupId}' AND enquiry_notes_meta_type='assign'")) {
        if (mysqli_num_rows($checkFollowup) != 0) {
            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$followupId}' AND enquiry_notes_meta_type='assign'")) {
                $httpStatus = 201;
                $status = 'success';
                $msg = "Follow up delete succesfully";
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to delete follow up');
            }
        } else {
            $httpStatus = 404;
            $msg = 'No follow up record found';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to fetch follow up');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
