<?php

	/** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id WHERE site_domain='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)==0) {

			if($checkInstallationAnyWebsite=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id AND site_domain='".SITE_DOMAIN."'")) {

				if(mysqli_num_rows($checkInstallationAnyWebsite)==0) {

					if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site")) {

						if(mysqli_num_rows($checkDomain)==0) {
							
							$websiteIntallationFile=ROOT.'/app/install/website.php';

							if(file_exists($websiteIntallationFile)) {
								
								require_once $websiteIntallationFile;
								exit();

							} else {

								echo 'Unable to find <strong>Website Installation File</strong> on the server';

							}
					
						} else {

							echo "Installation is not complete please contact to system administrator";
							exit();

						}
					
					}
					
				}

			}

		} else {

			/** Non default app website data extraction */

			extract(mysqli_fetch_assoc($checkInstallation));
			
			$site_app = $app_path;

			if($site_status==0) {
	
				die('Something went wrong while installing application on your server. please contact to your system administration');

			}

		}

	} else {

		/** Import Database */

		require_once ROOT.'/app/install/import-database.php';
		exit();
				
	}