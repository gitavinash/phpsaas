<?php

$appDashboardPageDirectory = $appDashboardDirectory . strtolower(SITE_APP) . '/page/';
$appUserDashboardFile = $appDashboardPageDirectory . strtolower(trim($site_access_type)) . '.php';

if (file_exists($appUserDashboardFile)) {
    require_once $appUserDashboardFile;
} else {
    require_once $appDashboardPageDirectory . 'default.php';
}