<?php

	/** Including database connection file */

	phpinfo();
	exit();

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/connection.php';
	// require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/mongoDbConnection.php';

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

	if(isset($site_id)) {

		/** Fetch global website configuration settings */

		if($websiteConfigutration=mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_status=1 AND ( site_configuration_site_id={$site_id} OR site_configuration_site_id={$site_owner_id})")) {

			if(mysqli_num_rows($websiteConfigutration)!=0) {

				while($websiteConfigutrationData=mysqli_fetch_assoc($websiteConfigutration)) {

					if($websiteConfigutrationData['site_configuration_key']!='') {

						define(strtoupper($websiteConfigutrationData['site_configuration_key']), $websiteConfigutrationData['site_configuration_value']);
					
					}

				}

			}

		}

	}