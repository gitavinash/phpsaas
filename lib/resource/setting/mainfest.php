<?php

    /** Mainfest.json content goes here */

    header("Content-type: application/json; charset=utf-8");

    echo json_encode($site_mainfest_json, TRUE);
    exit();