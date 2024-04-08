<?php

    // $file = 'checkout/'.strtolower(str_replace('/', '', $urlStructure[1]).'.php');

    // $themeFile = USER_PATH.$file;
    
    // if(!file_exists($themeFile)) {
    //     $themeFile = DEFAULT_USER_PATH.$file;
    // }

    // require_once $themeFile;
    // exit();
    $file = strtolower(str_replace('/', '', BASE_URL).'.php');

    $themeFile = USER_PATH.$file;
    
    if(!file_exists($themeFile)) {
        $themeFile = DEFAULT_USER_PATH.$file;
    }

    require_once $themeFile;
    exit();