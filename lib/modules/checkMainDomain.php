<?php

	/** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id INNER JOIN ".TABLE_PREFIX."app ON site_app_id=app_id INNER JOIN ".TABLE_PREFIX."theme ON theme_id=site_theme_id WHERE site_url='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)==0) {

			if($checkInstallationAnyWebsite=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id AND site_domain='".SITE_DOMAIN."'")) {

				if(mysqli_num_rows($checkInstallationAnyWebsite)==0) {

					if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site")) {

						if(mysqli_num_rows($checkDomain)==0) {
							
							$websiteIntallationFile=ROOT.'/app/install/website-install.php';

							if(file_exists($websiteIntallationFile)) {
								
								require_once $websiteIntallationFile;
								exit();

							} else {

								echo 'Unable to find <strong>Website Installation File</strong> on the server';

							}
					
						} else {

							echo "Installation is not complete please contact to ".CREDIT_MAIL;

						}
					
					}
					
				}

			}

		} else {

			extract(mysqli_fetch_assoc($checkInstallation));

			$site_app = $app_path;

			if($site_status==0) {

				/** Redirect to Installation step 2 url */
						
				if($_SERVER['REQUEST_URI']!='/install/?step=2') {

					header("Location: /install/?step=2", TRUE, 301);
					exit();
			
				}

				/** Step 2 of installation process */
						
				$installFile2=$_SERVER['DOCUMENT_ROOT'].'/lib/install/step-2.php';

				if(file_exists($installFile2)) {
			
					require_once $installFile2;
					exit();
			
				} else {
			
					die('Something went wrong while installing application on your server. kindly empty your database and restart installation process.');
			
				}

			}

		}

	} else {

		/** Import Database */

		require_once ROOT.'/app/install/import-database.php';
		exit();
				
	}