<?php

    $fileName=explode('.', $urlStructure[0]);

    $settingPath=RESOURCE.'setting/'.$fileName[0].'.php';
    
    if(file_exists($settingPath)) {

        require_once $settingPath;

    } else {

        require_once ERROR_404;

    }