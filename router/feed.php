<?php


$feedURL = str_replace('feed/', '', BASE_URL);

$feedPath = RESOURCE . 'feeds/';

if ($feedURL != '/') {

	$feedResourceFile = $feedPath . $urlStructure[1] . '.php';

	if (!file_exists($feedPath . $urlStructure[1] . '.php')) {
		require_once $feedPath . 'single-default.php';
	}
} else {

	$feedResourceFile = RESOURCE . 'feeds/default.php';
}

if (file_exists($feedResourceFile)) {
	// header('Content-Type: application/xml; charset=utf-8');
	require_once $feedResourceFile;
} else {
	require_once ERROR_404;
}
exit();