<?php

$filterTypePath = ROUTER . '/filter/' . ((isset($urlStructure[1])) ? $urlStructure[1] : 'index') . '.php';

if (file_exists($filterTypePath)) {
    require_once $filterTypePath;
} else {
    require_once ERROR_404;
}

exit();
