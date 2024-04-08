<?php

    $file = strtolower(str_replace('/', '', BASE_URL).'.php');

    $themeFile = USER_PATH.$file;
    
    if(!file_exists($themeFile)) {
        $themeFile = DEFAULT_USER_PATH.$file;
    }

    echo $themeFile;
    // require_once $themeFile;
    exit();