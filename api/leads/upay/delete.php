<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $upayId = base64_decode($id);
} else {
    $msg = 'Upay id is required';
}

if (isset($deleteData)) {
    if ($checkUpay = mysqli_query($connection, "SELECT enquiry_notes_id FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$upayId}' AND enquiry_notes_meta_type='upay'")) {
        if (mysqli_num_rows($checkUpay) != 0) {
            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$upayId}' AND enquiry_notes_meta_type='upay'")) {
                $httpStatus = 201;
                $status = 'success';
                $msg = "Upay delete succesfully";
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to delete upay');
            }
        } else {
            $httpStatus = 404;
            $msg = 'No upay record found';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to fetch upay');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
