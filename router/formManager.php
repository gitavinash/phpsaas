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

		/** Default API Request */

		$apiCall=API_APP_MODULES.$type.'/'.$action.'.php';

		if(!file_exists($apiCall)) {

			/** Website APP Api Request */

			$apiCall=API_MODULES.$type.'/'.$action.'.php';

		}
		
		if(file_exists($apiCall)) {

			/** Call the API */

			require_once $apiCall;

			if(function_exists($action)) {

				call_user_func($action, $connection, $secure);

			} else {
				
				$msg='<h1>Undefine function '.$type.' -> '.$action.' </h1>';

			}

		} else {

			/** Call dedicated api */

			$apiCall=API_MODULES.$type.'.php';

			if(file_exists($apiCall)) {

				require_once $apiCall;

				if(function_exists($action)) {

					call_user_func($action, $connection, $secure);

				} else {
					
					$msg='<h1>Undefine function '.$type.' -> '.$action.' </h1>';

				}

			} else {

				echo "Unable to find current request method -> {$apiCall}";

			}

		}

	}

	exit();