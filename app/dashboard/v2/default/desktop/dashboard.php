<?php

$appDashboardFile = $appDashboardDirectory . 'page/' . strtolower(SITE_APP) . '.php';

if (file_exists($appDashboardFile)) {
    require_once $appDashboardFile;
} else {
    require_once $appDashboardDirectory . 'page/default.php';
}
