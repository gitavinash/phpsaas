<?php
	
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/config.php')) {

		require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

		if(DEVELOPMENT_MODE==true) {

			/* hide errors message */

			error_reporting(0);
			ini_set('display_errors', 0);

		} else {

			error_reporting(E_ALL);
			ini_set('display_errors', 1);

		}

		/* Established database connection */

		if(@!$connection=mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {

			// Database error template

			require_once ROOT.'/app/system/visitor/default/desktop/db-connection-error.php';
			exit();
			
		}

		/** Set default database collation */

		mysqli_set_charset($connection, 'utf8mb4');

	} else {

		echo '<h1>Configuration file is not found.</h1>';

	}

?>