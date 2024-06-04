<?php

require_once LOGIN_CHECK;

extract($data);

if ($name != '') {

    require_once MODULES . 'imageUploader.php';

    if (!isset($icon)) {
        /** if feature image is not upload then set featureImage as blank */
        $icon = '';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
