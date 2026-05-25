<?php

require_once LOGIN_CHECK;

if (mysqli_query(
    $connection,
    "DELETE FROM " . TABLE_PREFIX . "user_sessions
     WHERE us_user_id='" . USER_ID . "'
     AND us_site_id='" . SITE_ID . "'"
)) {
    header('Location: /logout/');
} else {
    echo "Something went wrong";
}

exit;