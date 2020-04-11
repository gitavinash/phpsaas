<?php

	/* url router for admin dashboard */
	
	header("Content-type: application/json; charset=utf-8");

	/** Secure all request method from sql injection */

	foreach($_GET as $key => $value) {

		$secure[$key]=mysqli_real_escape_string($connection, $value);

	}

	/** Convert all secure request key from secure to original */

	if(isset($secure)) {

		extract($secure);
		
	}

	switch ($urlStructure[1]) {

		case 'secure' :

			/* for secure api request */
			require_once LOGIN_CHECK;
			break;

		case 'update-database' :

			require_once RESOURCE.'database/repair-database.php';
			break;
			
		default:

			/** Check API request file in system app directory */

			if(file_exists(API.$urlStructure[1].'/'.$urlStructure[2].'.php')) {

				require_once API.$urlStructure[1].'/'.$urlStructure[2].'.php';

			} else {

				if(file_exists(API.SITE_APP.'/'.end($urlStructure).'.php')) {

					require_once API.SITE_APP.'/'.end($urlStructure).'.php';

				} else {

					require_once API.end($urlStructure).'.php';

				}

			}
			break;
			
	}
	
	exit();