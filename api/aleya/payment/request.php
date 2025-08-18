<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($amount)) {
    $quoteId = base64_decode($quotationId);
    if ($checkQuotation = mysqli_query($connection, "SELECT quotation_id, CONCAT(enquiry_first_name, enquiry_middle_name, enquiry_last_name) as profile_display_name, enquiry_mobile as mobile, tour_package_title FROM " . TABLE_PREFIX . "quotation INNER JOIN " . TABLE_PREFIX . "enquiry ON enquiry_id=quotation_ref_id AND quotation_ref_type='enquiry' AND quotation_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "tour_package ON tour_package_id=quotation_type_id AND quotation_type='tour_package' AND tour_package_site_id=" . SITE_ID . " WHERE quotation_id={$quoteId} AND quotation_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkQuotation) != 0) {
            extract(mysqli_fetch_assoc($checkQuotation));
            if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                if (ENABLE_WHATSAPP_BUSINESS_API == 'yes') {
                    require_once WHATSAPP_TEMPLATE_VISITOR . 'payment_request.php';
                }
            }
            $status = 'success';
            $httpStatus = 200;
            $msg = 'Payment link sent to customer';
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