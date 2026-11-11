<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$sessionValidation = (isset($connection) && $connection instanceof mysqli && function_exists('iswValidateCurrentSiteSession'))
    ? iswValidateCurrentSiteSession($connection, (int) SITE_ID)
    : ['ok' => false];

if (!empty($sessionValidation['ok'])) {

    /** Show draft only for login user */

    $draftRouter = ROUTER . 'httpStatus/206/' . $link_type . '.php';

    if (file_exists($draftRouter)) {

        require_once $draftRouter;
    } else {

        echo "{$link_type} router not found";
    }
} else {

    require_once ERROR_403;
}