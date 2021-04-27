<?php

	// include_once 'header.php';

    $includeDashboardFile=ADMIN_PATH.((isset($_GET['type'])) ? 'templates'.ADMIN_BASE_URL.mysqli_real_escape_string($connection, $_GET['type']) : ltrim(rtrim(ADMIN_BASE_URL, '/'), '/').'dashboard' ).'.php';
	
	if(file_exists($includeDashboardFile)) {

		include_once $includeDashboardFile;

	} else {

        include_once ADMIN_ERROR_404;
		
	}

	// include_once 'footer.php';

?>