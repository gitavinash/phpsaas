<?php

	/* url router for admin dashboard */
	
	header("Content-type: application/json; charset=utf-8");

	$urlStructure=explode('/', rtrim(ltrim(API_BASE_URL, '/'), '/'));

	switch ($urlStructure[0]) {

		case 'secure' :
			require_once LOGIN_CHECK;

			/* for secure api request */

			break;
		
		default:

			include_once API.$urlStructure[0].'.php';
			break;
			
	}
	
	exit();