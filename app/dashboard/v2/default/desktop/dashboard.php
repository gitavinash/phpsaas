<?php

$appDashboardFile = $appDashboardDirectory . 'page/' . strtolower(SITE_APP) . '.php';
$appUserDashboardFile = $appDashboardDirectory . 'page/' . strtolower(SITE_APP) . '_' . $site_access_type . '.php';

if (file_exists($appUserDashboardFile)) {
    require_once $appUserDashboardFile;
} else if (file_exists($appDashboardFile)) {
    require_once $appDashboardFile;
} else {
    require_once $appDashboardDirectory . 'page/default.php';
}
