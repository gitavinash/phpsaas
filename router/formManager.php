<?php

	// Call form manager only for visitor area

	if(strpos(BASE_URL, '/dashboard/') !== FALSE) {

		// Continue to next process

	} else {

		$requestFile=ROUTER.'/request/'.strtolower($_SERVER['REQUEST_METHOD']).'.php';
		
		if(file_exists($requestFile)) {

			require_once $requestFile;

		}
		exit();

	}