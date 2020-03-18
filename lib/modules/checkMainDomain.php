<?php

	/** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id WHERE site_domain='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)==0) {

			if($checkInstallationAnyWebsite=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id")) {

				if(mysqli_num_rows($checkInstallationAnyWebsite)==0) {

					/** Redirect to Installation step 1 url */
			
					if($_SERVER['REQUEST_URI']!='/install/?step=1') {
						header("Location: /install/?step=1", TRUE, 301);
						exit();
					}

					/** Installtion step 1 */

					if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site")) {

						if(mysqli_num_rows($checkDomain)==0) {

							/* Include installation file */
							$installFile1=$_SERVER['DOCUMENT_ROOT'].'/lib/install/step-1.php';

							if(file_exists($installFile1)) {
						
								require_once $installFile1;
								exit();

							} else {
										
								echo 'Unable to find installation files on server';

							}

						}

					}

				} else {
					
					echo "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id WHERE site_url='".SITE_DOMAIN."'";
					exit();

					require_once $_SERVER['DOCUMENT_ROOT'].'app/system/visitor/default/desktop/domain-not-found.php';
					exit();

				}

			}

		} else {

			extract(mysqli_fetch_assoc($checkInstallation));

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

		require_once $_SERVER['DOCUMENT_ROOT'].'/lib/install/import-database.php';
		exit();
				
	}