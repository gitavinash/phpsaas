<?php

$url = ((isset($_GET['url'])) ? mysqli_real_escape_string($connection, $_GET['url']) : '');

if ($url == '') {
    die('No URL provided.');
}

$linkQuery = "SELECT link_type as urlType FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_status!=301 AND link_site_id=" . SITE_ID;

$linkInfo = mysqli_query($connection, $linkQuery);

if (mysqli_num_rows($linkInfo) == 0) {
    die('No link found for the provided URL.');
}

extract(mysqli_fetch_assoc($linkInfo));

$linkDataQuery = "SELECT * FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$urlType} ON link_relation_id={$urlType}_id AND {$urlType}_status=1 WHERE link_url='{$url}' AND link_status!=301 AND link_site_id=" . SITE_ID;


if ($linkData = mysqli_query($connection, $linkDataQuery)) {
    if (mysqli_num_rows($linkData) > 0) {
        $linkData = mysqli_fetch_assoc($linkData);
        extract($linkData);

        $urlTypeVariable = strtolower($urlType) . '_type';

        $requiredThemeFile = '';
        $dedicatedThemeFile = USER_PATH . ${$urlTypeVariable} . '.php';
        $urlTypeThemeFile = USER_PATH . strtolower($link_type) . '.php';

        if (file_exists($dedicatedThemeFile)) {
            $requiredThemeFile = $dedicatedThemeFile;
        } elseif (file_exists($urlTypeThemeFile)) {
            $requiredThemeFile = $urlTypeThemeFile;
        }

        if ($requiredThemeFile == '') {
            die('No theme file found for the provided URL.');
        }
    } else {
        die('No link data found for the provided URL.');
    }
} else {
    die('Error fetching link data.');
}
