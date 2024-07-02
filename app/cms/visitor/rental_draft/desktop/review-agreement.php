<?php

if (isset($request_id)) {
    if ($request_id != '') {
        if ($checkRequest = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkRequest) != 0) {
                $validId = true;
            }
        }
    }
}

if (!isset($validId)) {
    header("Location: /", TRUE, 301);
    exit();
}