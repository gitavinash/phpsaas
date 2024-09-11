<?php

require_once LOGIN_CHECK;
$status = 'warning';

if (isset($mobile)) {

    if (substr($mobile, 0, 1) === "0") {
        // If it is, remove the first character
        $mobile = substr($mobile, 1);
    }
    if (substr($mobile, 0, 3) === "+91") {
        // If it is, remove the first character
        $mobile = substr($mobile, 3);
    }

    mysqli_query($connection, "START TRANSACTION");

    // Check user is exiting or not

    $findUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

    $userId = 0;

    if (mysqli_num_rows($findUser) != 0) {
        $userInfo = mysqli_fetch_assoc($findUser);
        $userId = $userInfo['user_id'];
    }

    $enquiryHistory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '" . USER_ID . "', 'update', '{$logginUserName}')");

    $messageDB = $_REQUEST;
    unset($messageDB['first_name']);
    unset($messageDB['middle_name']);
    unset($messageDB['last_name']);
    unset($messageDB['email']);
    unset($messageDB['package']);
    unset($messageDB['leadId']);
    unset($messageDB['mobile']);
    $messageDB = json_encode($messageDB, true);

    $updateEnquiry = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_first_name='{$first_name}',enquiry_middle_name='{$middle_name}', enquiry_last_name='{$last_name}', enquiry_email='{$email}', enquiry_message='{$messageDB}' WHERE enquiry_id='{$leadId}'");

    if ($updateEnquiry) {
        $status = 'success';
        $msg = 'Enquiry update successfully';
        mysqli_query($connection, "COMMIT");
    } else {
        $status = 'error';
        $msg = 'Something went wrong while updating enquiry';
        mysqli_query($connection, "ROLLBACK");
    }

} else {
    $msg = 'Please enter mobile number';
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'leadId' => $leadId,
);

echo json_encode($result, true);
exit();
