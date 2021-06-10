<?php

	$requestFile=ROUTER.'/request/'.strtolower($_SERVER['REQUEST_METHOD']).'.php';
	
	if(file_exists($requestFile)) {

		require_once $requestFile;

	}