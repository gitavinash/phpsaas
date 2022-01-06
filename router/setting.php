<?php

    /** Showing all view of settings related url like mainfest, ads.txt and robots.txt */

    $fileName=explode('.', $urlStructure[0]);

    $settingPath=RESOURCE.'setting/'.$fileName[0].'.php';

    if(file_exists($settingPath)) {

        require_once $settingPath;

    } else {
        
        http_response_code(404);
        require_once ERROR_404;

    }