<?php

require_once 'header.php';

if (SITE_ID == 34) {
    header("Location: /coming-soon/", true, 301);
    exit;
}

require_once 'footer.php';