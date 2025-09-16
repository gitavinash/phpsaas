<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($id)) {
        if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "discussion SET discussion_status=1 WHERE discussion_id='{$id}' AND discussion_site_id=" . SITE_ID)) {
            $msg = 'Article is live on website';
            $httpStatus = 201;
            $status = 'success';
        } else {
            $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong');
        }
    } else {
        $httpStatus = 400;
        $msg = 'Id is missngs';
    }
} else {
    $httpStatus = 400;
    $msg = 'Inavlid request method';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);