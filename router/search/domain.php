<?php

    // Dedicated template of search type

    $domainSearchTemplate=USER_PATH.'search-'.$type.'.php';

    if(!file_exists($domainSearchTemplate)) {

        // Default search template

        $domainSearchTemplate=USER_PATH.'search.php';

    }

    if(!file_exists($domainSearchTemplate)) {

        // Error 404 template
        http_response_code(404);
        $domainSearchTemplate=ERROR_404;

    }

    require_once $domainSearchTemplate;