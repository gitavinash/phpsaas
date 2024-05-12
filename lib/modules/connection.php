<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')) {

	require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

	/* Established database connection */

	if (@!$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {

		// Database error template

		require_once ROOT . '/app/system/visitor/default/desktop/db-connection-error.php';
		exit();
	}

	/** Set default database collation */

	mysqli_set_charset($connection, 'utf8mb4');
} else {

	echo '<h1>Configuration file is not found.</h1>';
}