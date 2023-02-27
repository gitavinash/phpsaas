<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	header("Content-type: application/json; charset=utf-8");

	foreach ($_POST as $key => $value) {

		if (!is_array($value)) {

			$secure[$key] = mysqli_real_escape_string($connection, $value);
		} else {

			/** Convert string array to string  */

			foreach ($value as $arrayKey => $arrayValue) {

				$secure[$arrayKey] = mysqli_real_escape_string($connection, $arrayValue);
			}
		}
	}

	if (isset($secure)) {

		extract($secure);

		// Call API using the exact url

		$apiCall = API_MODULES . str_replace('api/', '', ltrim(rtrim(BASE_URL, '/'), '/') . '.php');

		if (!file_exists($apiCall)) {

			/** Default API Request */

			$apiCall = API_APP_MODULES . $type . '/' . $action . '.php';

			if (!file_exists($apiCall)) {

				/** Website APP Api Request */

				$apiCall = API_MODULES . $type . '/' . $action . '.php';

				if (file_exists($apiCall)) {

					/** Call the API */

					require_once $apiCall;
				} else {

					/** Call dedicated api */

					$apiCall = API_MODULES . $type . '.php';

					if (!file_exists($apiCall)) {

						$displaErrorInJSON = true;
					}
				}
			}
		}
	} else {

		// Call API using the exact url

		$apiCall = API_MODULES . str_replace('/api/', '', rtrim(BASE_URL, '/')) . '.php';

		if (!file_exists($apiCall)) {

			$displaErrorInJSON = true;
		}
	}

	// Include required API file and call the function if required

	require_once $apiCall;

	if (isset($action)) {

		// Swap parameter values if request type is from any form

		if ($type == 'enquiry') {

			$type = $action;
			$action = 'enquiry';
		}

		if (function_exists($action)) {

			call_user_func($action, $connection, $secure);
		} else {

			if ($type == 'enquiry') {

				$displaErrorInJSON = TRUE;
				$msg = $action . ' method not found';
			}
		}
	} else {

		$displaErrorInJSON = TRUE;
		$msg = 'Missing function parameter';
	}
} else {

	$displaErrorInJSON = true;
	$msg = 'Invalid request found';
}

if (isset($displaErrorInJSON)) {

	$result = array(
		'status' => ((isset($status)) ? $status : 'error'),
		'msg' => ((isset($msg)) ? $msg : ((DEVELOPMENT_MODE == true) ? "Not found : {$apiCall}" : 'Invalid api request')),
	);

	echo json_encode($result, TRUE);
}

exit();