<?php

require_once LOGIN_CHECK;

if (isset($action)) {

    if ($action != 'publish') {

        echo "Invalid action received";
        exit();
    }

    mysqli_query($connection, "START TRANSACTION");

    $fromURL = str_replace("http://" . SITE_DOMAIN, '', $fromURL);
    $fromURL = str_replace("http://www." . SITE_DOMAIN, '', $fromURL);
    $fromURL = str_replace("https://" . SITE_DOMAIN, '', $fromURL);
    $fromURL = str_replace("https://www." . SITE_DOMAIN, '', $fromURL);

    $updatePreviousLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$fromURL}' AND link_site_id=" . SITE_ID);

    $addRedirection = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "redirection(redirection_from, redirection_to, redirection_http_status, redirection_owner_id, redirection_site_id, redirection_status, redirection_generated, redirection_update) VALUES('{$fromURL}', '{$toURL}', '{$httpCode}', '" . USER_ID . "', '" . SITE_ID . "', 1, NOW(), NOW())");
    $redirectionId = mysqli_insert_id($connection);

    $addLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$fromURL}', '{$redirectionId}', 200, 'redirect', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), NOW())");

    if ($addLink && $addRedirection) {

        mysqli_query($connection, "COMMIT");
        $location = "/dashboard/setting/?type=redirection&status=all&statusType=success&message=" . urlencode("Redirection applied successfully");
    } else {

        mysqli_query($connection, "ROLLBACK");
        if (DEVELOPMENT_MODE == true) {
            echo mysqli_error($connection);
            exit();
        }
        $location = "/dashboard/setting/?type=redirection&status=all&statusType=error&message=" . urlencode("Something went wrong while applying redirection");

        exit();
    }

    header("Location: {$location}", TRUE, 301);
    exit();
}