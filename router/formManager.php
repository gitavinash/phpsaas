<?php

	if($_SERVER['REQUEST_METHOD']=='POST') {

		header("Content-type: application/json; charset=utf-8");

		foreach($_POST as $key => $value) {
			
			if(!is_array($value)) {

				$secure[$key]=mysqli_real_escape_string($connection, $value);

			} else {

				/** Convert string array to string  */

				foreach($value as $arrayKey => $arrayValue) {

					$secure[$arrayKey]=mysqli_real_escape_string($connection, $arrayValue);

				}

			}

		}

		extract($secure);

		/** Default App API Request and directory is /api/{app}/{$type} */

		$appApiCall=API_APP_MODULES.$type.'/'.$action.'.php';
		
		if(!file_exists($appApiCall)) {

			/** Website APP Api Request */

			$apiCall=API_MODULES.$type.'/'.$action.'.php';

		}
		
		if(file_exists($apiCall)) {

			/** Call the API and directory is /api/{$type} */

			require_once $apiCall;

			if(function_exists($action)) {

				call_user_func($action, $connection, $secure);

			} else {

				echo $apiCall;

			}

		} else {

			// Api directory /api/

			$dedicatedApiCall=API_MODULES.$type.'.php';

			if(file_exists($dedicatedApiCall)) {

				require_once $dedicatedApiCall;

				if(function_exists($type)) {

					call_user_func($type, $connection, $secure);

				} else {

					echo $apiCall;

				}

			} else {

				echo 'Api not found';	

			}

		}

	}

	exit();