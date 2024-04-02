<?php

	if(ADMIN_BASE_URL=='/sw.js') {
		require_once SERVICE_WORKER;
		exit();
	}
	
	/* Check user is logged in or not */

	require_once LOGIN_CHECK;

	/* Checking dashboard user type directory is exits or not */

	if($site_dashboard_version==1) {

		// Verion 1 theme type it's use in beggining of the project

		$dashboardDirectory=ADMIN_PATH;

		if(!is_dir($dashboardDirectory)) {

			if(!is_dir($dashboardDirectory=str_replace('/default/', '/default/', ADMIN_PATH))) {

				$dashboardDirectory=str_replace('/mobile/', '/desktop/', $dashboardDirectory);

			}

		}

	} else {

		// Latest dashboard directory 

		$dashboardDirectory=str_replace('/default/', '/'.USER_TYPE.'/', ADMIN_PATH);

		if(!is_dir(ROOT.$dashboardDirectory)) {

			$dashboardDirectory=str_replace('/default/', '/default/', ADMIN_PATH);

		}


	}


	// v2 Request url file path 

	$dashboardFilePath = ((isset($type)) ? 'templates'.ADMIN_BASE_URL.$type : ((ADMIN_BASE_URL!='/') ? ltrim(rtrim(ADMIN_BASE_URL, '/'), '/') : 'dashboard' ) ).'.php';

	$dashboardFile=$dashboardDirectory.$dashboardFilePath;

	if(!file_exists($dashboardFile)) {

		// When dashboard theme need to call file from the default theme directory ( v1 )

		$dashboardFile=ROOT.'/app/default/'.USER_TYPE.'/'.SITE_VERSION.'/'.$dashboardFilePath;
		
		if(!file_exists($dashboardFile)) {

			http_response_code(404);
			$dashboardFile=$dashboardDirectory.'404.php';

		}

	}

	// User for detect while type of page is load

	$postType=explode('/', mysqli_real_escape_string($connection, rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') ));
	$postType=end($postType);
	
	// html title tag value

	$dashboardPageTitle=ucwords(str_replace('/', ' &#8702; ', mysqli_real_escape_string($connection, ((rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')!='') ? rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') : 'Dashboard' ) )).((isset($type)) ? ' &#8702; '.$type : '' ));
	
	// Call dashboard theme file header, footer and main required file as per uri

	require_once $dashboardDirectory.'/header.php';
	
	require_once $dashboardFile;

	require_once $dashboardDirectory.'/footer.php';

	exit();