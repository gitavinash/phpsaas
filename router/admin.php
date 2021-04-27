<?php

	/* Check user is logged in or not */

	require_once LOGIN_CHECK;

	/* Checking dashboard user type directory is exits or not */

	if($site_dashboard_version==1) {

		// Verion 1 theme type it's use in beggining of the project

		$dashboardDirectory=ADMIN_PATH;

		if(!is_dir($dashboardDirectory)) {

			$dashboardDirectory=str_replace('/default/', '/default/', ADMIN_PATH);

		}
		
	} else {

		// Latest dashboard directory 

		$dashboardDirectory=str_replace('/default/', '/'.USER_TYPE.'/', ADMIN_PATH);

		if(!is_dir(ROOT.$dashboardDirectory)) {

			$dashboardDirectory=str_replace('/default/', '/default/', ADMIN_PATH);

		}

	}

	// Request url file path 

	$dashboardFilePath=((isset($_GET['type'])) ? 'templates'.ADMIN_BASE_URL.mysqli_real_escape_string($connection, $_GET['type']) : ltrim(rtrim(ADMIN_BASE_URL, '/'), '/').'dashboard' ).'.php';

	$dashboardFile=$dashboardDirectory.$dashboardFilePath;

	if(!file_exists($dashboardFile)) {

		// When dashboard theme need to call file from the default theme directory ( v1 )

		$dashboardFile=ROOT.'/app/default/'.USER_TYPE.'/'.SITE_VERSION.'/'.$dashboardFilePath;
		
		if(!file_exists($dashboardFile)) {

			$dashboardFile=$dashboardDirectory.'404.php';

		}

	}

	// User for detect while type of page is load

	$postType=mysqli_real_escape_string($connection, rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') );

	// Call dashboard theme file header, footer and main required file as per uri

	require_once $dashboardDirectory.'/header.php';
	
	require_once $dashboardFile;

	require_once $dashboardDirectory.'/footer.php';

	exit();