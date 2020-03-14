<?php

	/* url router for admin dashboard */
	
	header("Content-type: application/json; charset=utf-8");

	switch ($urlStructure[0]) {

		case 'secure' :
			require_once LOGIN_CHECK;

			/* for secure api request */

			break;
		
		default:

			/** Check API request file in system app directory */

			if(file_exists(API.SITE_APP.'/'.end($urlStructure).'.php')) {

				require_once API.SITE_APP.'/'.end($urlStructure).'.php';

			} else {

				require_once API.end($urlStructure).'.php';

			}
			break;
			
	}
	
	exit();