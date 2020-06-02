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

		case 'notification' :
			require_once API.'notification/'.end($urlStructure).'.php';
			break;
			
		default:

			/** Check API request file in system app directory */

			$systemAPI=API.end($urlStructure).'.php';
			
			if(file_exists($systemAPI)) {

				require_once $systemAPI;

			} else {

				// Check Application API Request

				$appAPI=API.SITE_APP.'/'.end($urlStructure).'.php';

				if(file_exists($appAPI)) {

					require_once $appAPI;

				} else {

					header('Content-Type: text/html; charset=utf-8');
					require_once ERROR_404;

				}

			}
			break;
			
	}