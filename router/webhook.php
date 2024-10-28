<?php

/* Actual url of AMP Version */

$url = str_replace('/webhook', '', BASE_URL);

$urlStructure = explode('/', rtrim(ltrim(str_replace('', '', $url), '/'), '/'));

switch ($urlStructure[0]) {

	case 'capture-facebook-lead':
		require_once 'capture-facebook-lead.php';
		break;

	default:
		require_once ROUTER . 'cache.php';
		break;
}
