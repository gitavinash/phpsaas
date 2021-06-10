<?php

    if($_SERVER['REQUEST_METHOD']=='GET') {

        // Call API using the exact url

		$apiCall=API_APP_MODULES.$type.'/'.$action.'.php';

		if(!file_exists($apiCall)) {

			/** Website APP Api Request */

			$apiCall=API_MODULES.$type.'/'.$action.'.php';

			if(!file_exists($apiCall)) {

				/** Default API Request */

				$apiCall=API_MODULES.str_replace('/api/', '', ltrim(rtrim(BASE_URL, '/'), '/').'.php');

				if(file_exists($apiCall)) {

					/** Call the API */

					require_once $apiCall;

				} else {

					/** Call dedicated api */

					$apiCall=API_MODULES.$type.'.php';

					if(!file_exists($apiCall)) {

						$displaErrorInJSON=TRUE;

					} else {

                        require_once $apiCall;

                    }

				}

			}

		}

    } else {

        $displaErrorInJSON=TRUE;
        $msg='Invalid request method';

    }

    if(isset($displaErrorInJSON)) {

		$result = array(
			'status' => ((isset($status)) ? $status : 'error' ),
			'msg' => ((isset($msg)) ? $msg : ((DEVELOPMENT_MODE==true) ? "Not found : {$apiCall}" : 'Invalid api request')),
		);
		
		echo json_encode($result, TRUE);

	}

	exit();