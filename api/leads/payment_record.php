<?php

header('Content-Type: application/json');
$httpStatus = 405;

require_once LOGIN_CHECK;

$leadId = base64_decode($leadId);

if ($_FILES['files']) {
    require_once MODULES . 'singleFileUploader.php';
    $paymentImages = json_encode($files, true);
} else {
    $paymentImages = '';
}

mysqli_query($connection, 'START TRANSACTION');

if ($checkEnquiryAccess = mysqli_query($connection, "SELECT enquiry_id, enquiry_status FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$leadId}' AND enquiry_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkEnquiryAccess) != 0) {
        extract((mysqli_fetch_assoc($checkEnquiryAccess)));

        if ($enquiry_status != 4 || $enquiry_status != 5) {

            $msg = json_encode(
                array(
                    'paymentAmount' => $amount,
                    'paymentImages' => $paymentImages,
                    'paymentMethod' => $payment_method,
                    'paymentDate' => $payment_date,
                    'paymentReference' => $reference_number,
                    'paymentMsg' => $payment_notes,
                    'paymentByCustomer' => false,
                )
            );

            $msg = addslashes($msg);

            if ($updateNotes = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '" . USER_ID . "', 'payment', '{$msg}')")) {

                $enquiryStatus = 8;

                if ($updateEnquiry = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_status={$enquiryStatus} WHERE enquiry_id='{$leadId}' AND enquiry_site_id=" . SITE_ID)) {
                    $status = 'success';
                    $httpStatus = 201;
                    $msg = 'Payment added successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    echo mysqli_error($connection);
                    $status = 'error';
                    $msg = 'Something went wrong while updating the Payment';
                    mysqli_query($connection, "ROLLBACK");
                }

            } else {
                echo mysqli_error($connection);
                $status = 'error';
                $msg = 'Something went wrong while updating the Payment';
                mysqli_query($connection, "ROLLBACK");
            }

        } else {
            $status = 'warning';
            $msg = 'Final interaction note is already updated';
        }

    } else {
        $status = 'warning';
        $msg = "You're not authorized to update this lead.";
    }
} else {
    $status = 'error';
    $msg = 'Invalid request found';
}

$result = array(
    'status' => $status,
    'msg' => $msg,
);

http_response_code($httpStatus);
echo json_encode($result, true);
exit();
