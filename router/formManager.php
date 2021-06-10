<?php

	$requestFile=ROUTER.'/request/'.strtolower($_SERVER['REQUEST_METHOD']).'.php';
	
	if(file_exists($requestFile)) {

		require_once $requestFile;

	} else {

		/** For all type of payment process */
		
		if($urlStructure[0]=='payment') {
			
			require_once 'payment.php';

		}

	}