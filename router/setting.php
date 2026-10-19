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
        if (defined('ERROR_404') && file_exists(ERROR_404)) {
            require_once ERROR_404;
        } elseif (defined('ADMIN_ERROR_404') && file_exists(ADMIN_ERROR_404)) {
            require_once ADMIN_ERROR_404;
        } else {
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><div style="text-align:center;padding:50px;font-family:sans-serif;"><h1>404</h1><p>Page Not Found</p><a href="/">Return Home</a></div></body></html>';
        }
    }
}