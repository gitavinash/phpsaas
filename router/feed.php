<?php

	header('Content-Type: application/xml; charset=utf-8');

	$feedURL=str_replace('feed/', '', BASE_URL);

	$feedPath=RESOURCE.'feeds/';

	if($feedURL!='/') {

		if(file_exists($feedPath.$urlStructure[1].'.php')) {

			require_once $feedPath.$urlStructure[1].'.php';

		} else {

			require_once $feedPath.'single-default.php';

		}

	} else {

		/** Recent post from website */

		require_once RESOURCE.'feeds/default.php';

	}
	
	exit();