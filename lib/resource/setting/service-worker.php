<?php

    header('Content-Type: application/javascript');

    /** including service worker file */

    $customServeiceWorker=USER_PATH.'sw.js';        // User theme custom service worker file

    if(file_exists($customServeiceWorker)) {

        require_once $customServeiceWorker;         // including custome service worker file

    } else {

        require_once SERVICE_WORKER;                // Default system service worker file

    }

    exit();