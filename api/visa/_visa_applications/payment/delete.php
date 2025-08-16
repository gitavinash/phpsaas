<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $paymentId = base64_decode($id);
} else {
    $msg = 'Payment id is required';
}

if (isset($deleteData)) {
    if ($checkPayment = mysqli_query($connection, "SELECT enquiry_notes_id FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$paymentId}' AND enquiry_notes_meta_type='payment'")) {
        if (mysqli_num_rows($checkPayment) != 0) {
            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$paymentId}' AND enquiry_notes_meta_type='payment'")) {
                $httpStatus = 201;
                $status = 'success';
                $msg = "Payment delete succesfully";
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to delete Payment');
            }
        } else {
            $httpStatus = 404;
            $msg = 'No Payment record found';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while trying to fetch Payment');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
