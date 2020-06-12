<?php

	/** Including database connection file */

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/connection.php';

	/** This script run only when Request method is not POST */

	if($_SERVER['REQUEST_METHOD']!='POST') {


		if(SITE_DOMAIN==MAIN_DOMAIN) {

			require_once 'checkMainDomain.php';
		
		} else {

			require_once 'checkSubDomain.php';
			
		}

	} else {

		require_once 'getDomainData.php';

	}