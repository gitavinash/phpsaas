<?php

// Link shortener theme router
if (!empty($urlStructure[0])) {
    require_once USER_PATH . 'link-shorter.php';
    exit();
}

// Fallback to homepage
require_once USER_PATH . 'index.php';
exit();
