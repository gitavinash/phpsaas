<?php

$invalidLink = true;

if (isset($link)) {

    $link = '/' . trim(str_replace(SITE_URL, '', $link), '/') . '/';

    // Check URL

    if ($checkUrl = mysqli_query($connection, "SELECT link_id, link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_creation, link_published, link_modified FROM " . TABLE_PREFIX . "link WHERE link_url='{$link}' AND link_site_id='" . SITE_ID . "' ORDER BY link_modified DESC")) {

        if (mysqli_num_rows($checkUrl) != 0) {

            extract(mysqli_fetch_assoc($checkUrl));

            if ($link_status == 200) {
                $invalidLink = false;
            }
        }
    }
}

if ($invalidLink == true) {
    http_response_code(404);
    header("HTTP/1.1 404 Not Found");
    header("Content-Type: text/html; charset=utf-8");
    require_once ERROR_404;
} else {
}