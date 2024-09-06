<?php

	define('SYSTEM_THEME', ROOT.'/app/system/visitor/default/desktop/');

	/** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_site_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id INNER JOIN ".TABLE_PREFIX."site_installation ON site_installation_site_id=site_id OR site_installation_parent_site_id = site_id WHERE site_domain='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)==0) {

			if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site")) {

				if(mysqli_num_rows($checkDomain)==0) {
					
					$websiteIntallationFile=ROOT.'/app/system/visitor/install/website.php';

					if(file_exists($websiteIntallationFile)) {
						
						require_once $websiteIntallationFile;
						exit();

					} else {

						require_once SYSTEM_THEME.'installation-error.php';
						exit();

					}
			
				} else {

					require_once SYSTEM_THEME.'domain-not-found.php';
					exit();

				}
			
			}

		} else {

			/** Non default app website data extraction */

			extract(mysqli_fetch_assoc($checkInstallation));

			// Check website is expire or not

			if($site_installation_expiry_date<=date('Y-m-d')) {

				if(date('Y-m-d', strtotime('+1 month', strtotime($site_installation_expiry_date))) >= date('Y-m-d')) {

					// Website expire is less than 1 month

					require_once SYSTEM_THEME.'website-expire.php';

				} else {

					// Website expire is more than 1 month

					require_once SYSTEM_THEME.'website-suspend.php';

				}

				exit();

			} else {
			
				$site_app = $app_path;

				if($site_status!=1) {

					switch ($site_status) {

						case 2: 
							require_once SYSTEM_THEME.'website-expire.php';
							break;
	
						case 3:
							require_once SYSTEM_THEME.'website-suspend.php';
							break;
							
						default: 
							require_once SYSTEM_THEME.'website-default-status.php';
							break;
	
					}
					exit();

				}

			}

		}

	} else {

		/** Import Database */

		require_once  ROOT.'/lib/resource/database/default-database.php';
		exit();
				
	}