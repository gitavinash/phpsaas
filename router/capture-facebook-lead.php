<?php

$checkPath = USER_PATH . 'capture-facebook-lead.php';

if (file_exists($checkPath)) {
    require_once $checkPath;
} else {
    echo json_encode(array(
        'status' => 404,
        'message' => 'Request url is not found'
    ), TRUE);
}
exit();
