<?php

header('Content-Type: application/json; charset=utf-8');

$manifestFile = MANIFEST . strtolower(SITE_APP) . '.php';

if (!file_exists($manifestFile)) {
    $manifestFile = MANIFEST . 'default.php';
}

if (file_exists($manifestFile)) {
    require_once $manifestFile;
} else {
    echo '{}';
}

exit();