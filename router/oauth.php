<?php

if (!isset($urlStructure[1])) {
    echo "<h2>Something went wrong oAuth method is missing</h2>";
    exit();
}

$oAuthRequestType = ((isset($urlStructure[2])) ? $urlStructure[2] : 'request');

$oAuthRequestTypeFile =
    SOCIAL_LOGIN . $urlStructure[1] . '/' . $oAuthRequestType . '.php';

if (file_exists($oAuthRequestTypeFile)) {
    require_once $oAuthRequestTypeFile;
} else {
    echo "<h2>Invalid oAuth request found for {$urlStructure[1]}</h2>";
}