<?php

/** Showing all view of settings related url like mainfest, ads.txt and robots.txt */

$fileName = explode('.', $urlStructure[0]);

$settingPath = RESOURCE . 'setting/' . $fileName[0] . '.php';

if (file_exists($settingPath)) {

    require_once $settingPath;
} else {

    if (file_exists($themeRouterFile)) {
        require_once $themeRouterFile;
        exit();
    } else {

        $defaultFilePath = DEFAULT_USER_PATH . $urlStructure[0] . '.php';

        if (file_exists($defaultFilePath)) {
            require_once $defaultFilePath;
            exit();
        }

        http_response_code(404);
        require_once ERROR_404;
    }
}