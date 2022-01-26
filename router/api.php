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
			if(SITE_ID==1) {
				require_once RESOURCE.'database/repair-database.php';
			} else {
				
				$genarateError=true;
				$status=403;
				$msg="Unauthorized access";

			}
			break;

		case 'notification' :
			require_once API_MODULES.'notification/'.end($urlStructure).'.php';
			break;
			
		default:

			// Check api as per url request

			$urlAPI=API_APP_MODULES.str_replace('/api/', '', rtrim(BASE_URL, '/')).'.php';

			if(file_exists($urlAPI)) {

				require_once $urlAPI;

			} else {

				/** Check API request file in system app directory */

				$appAPI=API_APP_MODULES.end($urlStructure).'.php';
				
				if(file_exists($appAPI)) {

					require_once $appAPI;

				} else {

					// Check Application API Request

					$systemAPI=API_MODULES.end($urlStructure).'.php';

					if(file_exists($systemAPI)) {

						require_once $systemAPI;

					} else {

						$genarateError=true;

					}

				}

			}
			
			break;
			
	}

	if(isset($genarateError)) {

		$response = array(
			'status' => ((isset($status)) ? $status : 404 ),
			'msg' => ((isset($msg)) ? $msg : 'Not found' ),
		);

		http_response_code($response['status']);

		echo json_encode($response, TRUE);

	}