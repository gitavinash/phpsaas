<?php
	
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/config.php')) {

		require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

		/* Established mongo database connection */
		
		$mongoDB = new MongoClient();
		
		// phpinfo();
		// exit();

	} else {

		echo '<h1>Configuration file is not found.</h1>';

	}

?>