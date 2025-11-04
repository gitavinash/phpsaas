<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($id)) {
        if ($checkPost = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "discussion WHERE discussion_id='{$id}' AND discussion_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkPost) > 0) {

                mysqli_query($connection, "START TRANSACTION");

                $saveDiscussion = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "discussion SET discussion_status=1 WHERE discussion_id='{$id}' AND discussion_site_id=" . SITE_ID);

                $saveActivity = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "activity (activity_site_id, activity_user_id, activity_ref_id, activity_ref_type, activity_ref, activity_is_fresh, activity_type, activity_capital_id, activity_time) VALUES (" . SITE_ID . ", " . $currentUser['user_id'] . ", 'discussion', 'live', '{$id}', NOW())");

                if ($saveDiscussion) {
                    $msg = 'Article is live on website';
                    $httpStatus = 201;
                    $status = 'success';
                } else {
                    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong');
                }
            } else {
                $msg = ((DEVELOPMENT_MODE == true) ? 'Discussion not found' : 'Invalid discussion id');
            }
        } else {
            $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong discussion');
        }
    } else {
        $httpStatus = 400;
        $msg = 'Id is missing';
    }
} else {
    $httpStatus = 400;
    $msg = 'Invalid request method';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);