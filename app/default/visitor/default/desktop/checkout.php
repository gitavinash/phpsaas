<?php

    require_once 'header.php';

    $checkAppFile = DEFAULT_USER_PATH.ltrim(BASE_URL, '/').SITE_APP.'.php';

    if(file_exists($checkAppFile)) {
        require_once $checkAppFile;
    } else {
        require_once DEFAULT_USER_PATH.ltrim(BASE_URL, '/').'default.php';
    }

    require_once 'footer.php';