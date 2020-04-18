<?php

	/* Actual url of AMP Version */

	$url=str_replace('/amp', '', BASE_URL);

	$urlStructure=explode('/', rtrim(ltrim(str_replace('', '', $url), '/'), '/'));

	switch($urlStructure[0]) {

		case '' :
			require_once 'homepage.php';
			break;

		case 'tag' :
			require_once 'tag.php';
			break;

		case 'category' :
			require_once 'category.php';
			break;
		
		default :
			require_once ROUTER.'cache.php';
			break;

	}