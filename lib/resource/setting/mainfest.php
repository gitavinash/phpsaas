<?php

    /** Mainfest.json content goes here */

    header("Content-type: application/json; charset=utf-8");

    $mainfest=str_replace('%SITE_DESC%', SITE_DESC, (str_replace('%SITE_NAME%', SITE_NAME, $site_mainfest_json)));

    echo json_decode($mainfest, TRUE);
    exit();