<?php

require_once LOGIN_CHECK;

$leadId = base64_decode($leadId);
$userId = base64_decode($userId);

$userData = explode("_", $userId);
$userId = $userData[0];
$userName = $userData[1];

mysqli_query($connection, "START TRANSACTION");

if ($checkLead = mysqli_query($connection, "SELECT enquiry_id, enquiry_subject FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$leadId}' AND enquiry_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkLead) != 0) {

        extract(mysqli_fetch_assoc($checkLead));

        if ($checkUser = mysqli_query($connection, "SELECT profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE user_id={$userId} AND site_access_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkUser) != 0) {
                extract(mysqli_fetch_assoc($checkUser));
                if ($updateLead = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_agent='{$userId}', enquiry_status=2 WHERE enquiry_id='{$leadId}' AND enquiry_site_id=" . SITE_ID)) {

                    if ($createNotes = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '" . USER_ID . "', 'assign', 'Lead assign to {$profile_display_name}')")) {


                        // Notification payload

                        $title = "New {$enquiry_subject} lead received";
                        $body = "Hi {$profile_display_name}, New {$enquiry_subject} lead received.";
                        $redirectURL = '/dashboard/leads/view/?id=' . base64_encode($leadId);

                        require_once API_MODULES . 'notification/send.php';

                        $status = 'success';
                        $responseMsg = "Lead assing to {$userName} successfully";
                    } else {
                        $status = 'error';
                        $responseMsg = "Something went wrong while assigning lead to user";
                    }
                } else {
                    $status = 'error';
                    $responseMsg = 'Something went wrong';
                }
            } else {
                $status = 'warning';
                $responseMsg = 'Invalid user information';
            }
        } else {
            $status = 'error';
            $responseMsg = 'Something went wrong while finding user details';
        }
    } else {
        $status = 'warning';
        $responseMsg = 'Invalid lead information';
    }
} else {
    $status = 'error';
    $responseMsg = 'Something went wrong';
}

if ($status == 'success') {
    mysqli_query($connection, "COMMIT");
} else {
    mysqli_query($connection, "ROLLBACK");
}

$status = urlencode($status);
$responseMsg = urlencode($responseMsg);

header("Location: /dashboard/leads/list/?status={$status}&responseMsg={$responseMsg}", TRUE, 301);
exit();
