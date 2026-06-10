<?php

/* Upload Image for Page Builder */

require_once LOGIN_CHECK;
require_once IMAGE_UPLOADER;

if (isset($file) && !empty($file)) {
    echo json_encode([
        'status' => 200,
        'msg' => 'Image uploaded successfully',
        'url' => $file
    ]);
} else {
    echo json_encode([
        'status' => 400,
        'msg' => 'No image file uploaded or upload failed'
    ]);
}
exit();
