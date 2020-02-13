<?php

	/* Check user is logged in or not */

	require_once LOGIN_CHECK;

	/* url router for admin dashboard */

	$urlStructure=explode('/', rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'));

	switch ($urlStructure[0]) {

		default:
		
			include_once ADMIN_PATH.'index.php';
			break;
			
	}

	exit();