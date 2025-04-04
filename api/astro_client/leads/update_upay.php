<?php

header('Content-Type: application/json');
$httpStatus = 405;

require_once LOGIN_CHECK;

$leadId = base64_decode($upayId);

// if ($_FILES['files']) {
//     require_once MODULES . 'fileUploader.php';
//     $upayImages = json_encode($files, true);
// }

mysqli_query($connection, 'START TRANSACTION');

if ($checkEnquiryAccess = mysqli_query($connection, "SELECT enquiry_notes_meta_value FROM " . TABLE_PREFIX . "enquiry_notes WHERE enquiry_notes_id='{$leadId}' AND enquiry_notes_meta_type = 'upay'")) {
    if (mysqli_num_rows($checkEnquiryAccess) != 0) {
        extract((mysqli_fetch_assoc($checkEnquiryAccess)));

            $msg = json_decode($enquiry_notes_meta_value, true);

            $msg = json_encode(
                array(
                    'upayMsg' => $upayText,
                    'upayImages' => $msg['upayImages'],
                )
            );

            $msg = addslashes($msg);

            if ($updateNotes = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry_notes SET enquiry_notes_meta_value='{$msg}' WHERE enquiry_notes_id='{$leadId}' AND enquiry_notes_meta_type = 'upay'")) {
                    $status = 'success';
                    $httpStatus = 201;
                    $msg = 'Upay updated successfully';
                    mysqli_query($connection, "COMMIT");
            } else {
                    echo mysqli_error($connection);
                    $status = 'error';
                    $msg = 'Something went wrong while updating the Upay';
                    mysqli_query($connection, "ROLLBACK");
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