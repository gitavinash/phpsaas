<?php
    header('Content-Type: application/javascript');

    echo "const siteName = ".strtolower(str_replace(' ', '_', SITE_NAME)).";";

    if($urlStructure[0]=='dashboard') {
        require_once DASHBOARD_SERVICE_WORKER;
    } else {
        require_once VISITOR_SERVICE_WORKER;
    }