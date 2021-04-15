<?php

	$currentTheme=ROOT.'/app/system/visitor/default/desktop/';

	/** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_parent_site_id=site_id WHERE site_domain='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)==0) {

			if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site")) {

				if(mysqli_num_rows($checkDomain)==0) {
					
					$websiteIntallationFile=ROOT.'/app/install/website.php';

					if(file_exists($websiteIntallationFile)) {
						
						require_once $websiteIntallationFile;
						exit();

					} else {

						require_once $currentTheme.'installation-error.php';
						exit();

					}
			
				} else {

					require_once $currentTheme.'domain-not-found.php';
					exit();

				}
			
			}

		} else {

			/** Non default app website data extraction */

			extract(mysqli_fetch_assoc($checkInstallation));

			// Check website is expire or not

			if($site_expire_date<=date('Y-m-d')) {


				if(date('Y-m-d', strtotime('+1 month', strtotime($site_expire_date))) >= date('Y-m-d')) {

					// Website expire is less than 1 month

					require_once $currentTheme.'website-suspend.php';

				} else {

					// Website expire is more than 1 month

					require_once $currentTheme.'website-expire.php';

				}

				exit();

			} else {
			
				$site_app = $app_path;

				if($site_status==0) {
		
					die('Something went wrong while installing application on your server. please contact to your system administration');

				} else {

					/** Fetch global website configuration settings */

					if($websiteConfigutration=mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_status=1 AND site_configuration_global=0 AND site_configuration_site_id={$site_id}")) {

						if(mysqli_num_rows($websiteConfigutration)!=0) {

							while($websiteConfigutrationData=mysqli_fetch_assoc($websiteConfigutration)) {

								$websiteConfigutrationList[$websiteConfigutrationData['site_configuration_key']]=$websiteConfigutrationData['site_configuration_value'];

							}

							// Convert the website configuration key as a value
							extract($websiteConfigutrationList);

						}

					}

				}

			}

		}

	} else {

		/** Import Database */

		require_once  ROOT.'/lib/resource/database/setup-database.php';
		exit();
				
	}