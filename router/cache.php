<?php

    $cacheFileName=str_replace('/', '-', rtrim(ACTIVE_URL, '/')).'.php';
    $cacheFilePath=CACHE_PATH.'cache'.$cacheFileName;

    if(file_exists($cacheFilePath)) {

        echo 'Cache file found';

    } else {

        require_once 'routerManager.php';

    }

    exit();