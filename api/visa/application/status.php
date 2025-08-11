<?php

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS . 'getInitials.php';

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status     = 'warning';
$msg        = 'Invalid request';
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    extract($_GET);

    // Split into two parts only: before -00 and after -00
    $parts = explode('-00', $referenceNumber, 2);

    // Validate prefix and presence of value after -00
    if (!isset($parts[0], $parts[1]) || $parts[0] !== getInitials(SITE_NAME)) {
        $httpStatus = 400;
        $status     = 'warning';
        $msg        = 'Invalid request number found';
    } else {
        $order_id = $parts[1];

        $checkApplicationQuery = "
            SELECT 
                visa_application_order_id, 
                DATE_FORMAT(visa_application_time, '%e %M %Y') AS visa_application_time,
                DATE_FORMAT(visa_application_update, '%e %M %Y %l:%i:%s %p') AS visa_application_update,
                CONCAT(UCASE(LEFT(visa_country_title, 1)), LOWER(SUBSTRING(visa_country_title, 2))) AS visa_country_title,
                CONCAT(UCASE(LEFT(visa_package_visa_type, 1)), LOWER(SUBSTRING(visa_package_visa_type, 2))) AS visa_package_visa_type,
                CONCAT(UCASE(LEFT(visa_package_entry_type, 1)), LOWER(SUBSTRING(visa_package_entry_type, 2))) AS visa_package_entry_type,
                visa_application_status, 
                transaction_status,
                CASE visa_application_status
                        WHEN 0 THEN 'Initiated'
                        WHEN 1 THEN 'Success'
                        WHEN 2 THEN 'Pending'
                        WHEN 3 THEN 'Followup'
                        WHEN 4 THEN 'Failed'
                        WHEN 5 THEN 'Duplicate'
                        WHEN 6 THEN 'Payment'
                        WHEN 7 THEN 'Requested'
                        WHEN 8 THEN 'Completed'
                        ELSE 'Unknown'
                    END AS status_label
            FROM " . TABLE_PREFIX . "visa_application
            INNER JOIN " . TABLE_PREFIX . "transaction 
                ON transaction_order_id = visa_application_order_id 
                AND transaction_site_id = " . SITE_ID . "
            INNER JOIN " . TABLE_PREFIX . "order
                ON order_id = visa_application_order_id 
                AND order_site_id = " . SITE_ID . "
            INNER JOIN " . TABLE_PREFIX . "visa_package 
                ON order_product_id = visa_package_id 
                AND visa_package_site_id = " . SITE_ID . "
            INNER JOIN " . TABLE_PREFIX . "visa_country 
                ON visa_package_country_id = visa_country_id 
                AND visa_country_site_id = " . SITE_ID . "
            INNER JOIN " . TABLE_PREFIX . "user 
                ON visa_application_user_id = user_id 
            WHERE visa_application_order_id = {$order_id} 
              AND user_mobile = '{$mobileNumber}' 
              AND visa_application_site_id = " . SITE_ID;

        $checkApplication = mysqli_query($connection, $checkApplicationQuery);

        if ($checkApplication && mysqli_num_rows($checkApplication) > 0) {
            $httpStatus    = 200;
            $status        = 'success';
            $msg           = mysqli_fetch_assoc($checkApplication);
        } else {
            $httpStatus    = 404;
            $status        = 'warning';
            $msg           = 'Invalid application number';
        }
    }
}

$result = [
    'status' => $status,
    'msg'    => $msg,
    'data'   => $responseData
];

http_response_code($httpStatus);
echo json_encode($result, JSON_PRETTY_PRINT);