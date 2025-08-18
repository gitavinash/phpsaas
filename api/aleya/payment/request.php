<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($amount)) {
    $quoteId = base64_decode($quotationId);
    if ($checkQuotation = mysqli_query($connection, "SELECT quotation_id FROM " . TABLE_PREFIX . "quotation WHERE quotation_id={$quoteId} AND quotation_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkQuotation) != 0) {
            print_r(mysqli_fetch_assoc($checkQuotation));
        } else {
            $msg = 'No information found';
        }
    }
} else {
    $msg = 'Amount is required';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);