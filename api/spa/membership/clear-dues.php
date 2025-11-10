<?php

require_once COMMUNICATION_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($membershipId)) {

        $membershipId = base64_decode($membershipId);

        if ($checkPost = mysqli_query($connection, "SELECT membership_id, membership_amount_due, profile_user_id, profile_mobile, profile_display_name as name FROM " . TABLE_PREFIX . "membership INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=membership_user_id WHERE membership_site_id=" . SITE_ID . " AND membership_id='{$membershipId}'")) {

            if (mysqli_num_rows($checkPost) != 0) {

                extract(mysqli_fetch_assoc($checkPost));

                if ($paymentAmount <= $membership_amount_due) {

                    mysqli_query($connection, "START TRANSACTION");

                    $saveTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction (transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_amount, transaction_type, transaction_comment, transaction_status) VALUES (" . SITE_ID . ", '{$profile_user_id}', '{$membership_id}', '{$paymentMode}', '{$paymentAmount}', 'membership', '{$paymentNotes}', 1)");
                    $transactionId = mysqli_insert_id($connection);

                    $updateMembership = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "membership SET membership_amount_due=membership_amount_due-{$paymentAmount} WHERE membership_site_id=" . SITE_ID . " AND membership_id='{$membership_id}'");

                    if ($saveTransaction && $updateMembership) {
                        $status = 'success';
                        $msg = 'Dues updated successfully';
                        $httpStatus = 201;

                        if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                            if (ENABLE_WHATSAPP_BUSINESS_API == 'yes') {
                                require_once WHATSAPP_TEMPLATE_ADMIN . 'payment_alert.php';
                            }
                        }
                        mysqli_query($connection, "COMMIT");
                    } else {
                        $msg = 'Technical error';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {
                    $status = 'warning';
                    $msg = 'Amount exceeds due amount';
                }
            } else {

                $msg = 'Invalid request found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {
        $msg = 'Something is wrong while sending request to server';
    }
} else {
    $msg = 'Invalid request method';
}


$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);