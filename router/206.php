<?php

if (isset($_COOKIE['user_data'])) {

    /** Show draft only for login user */

    $draftRouter = ROUTER . 'httpStatus/206/' . $link_type . '.php';

    if (file_exists($draftRouter)) {

        require_once $draftRouter;
    } else {

        echo "{$link_type} router not found";
    }
} else {

    require_once ERROR_403;
}