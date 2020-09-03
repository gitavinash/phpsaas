<?php

	include_once 'header.php';

	$includeDefaultFile=DEFAULT_ADMIN_PATH.((isset($_GET['type'])) ? 'templates'.ADMIN_BASE_URL.mysqli_real_escape_string($connection, $_GET['type']) : ltrim(rtrim(ADMIN_BASE_URL, '/'), '/').'dashboard' ).'.php';
	$includeAppFile=ADMIN_PATH.((isset($_GET['type'])) ? 'templates'.ADMIN_BASE_URL.mysqli_real_escape_string($connection, $_GET['type']) : ltrim(rtrim(ADMIN_BASE_URL, '/'), '/').'dashboard' ).'.php';
	
	if(file_exists($includeAppFile)) {

		include_once $includeAppFile;

	} else {

		if(file_exists($includeDefaultFile)) {

			include_once $includeDefaultFile;

		} else {

			include_once ADMIN_ERROR_404;

		}
		
	}

	include_once 'footer.php';

?>