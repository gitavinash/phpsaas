<?php

	/** Including database connection file */

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/connection.php';

	/** This script run only when Request method is not POST */

	if($_SERVER['REQUEST_METHOD']!='POST') {

		/** Check software is already install or not */

		if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site WHERE site_domain='".SITE_DOMAIN."'")) {

			if(mysqli_num_rows($checkInstallation)==0) {

				/** Redirect to Installation step 1 url */
				
				if(ACTIVE_URL!='/install/?step=1') {
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

					} else {

						/** Check Domain */

						require_once 'checkDomain.php';

					}

				}

			} else {

				extract(mysqli_fetch_assoc($checkInstallation));

				if($site_status==0) {

					/** Redirect to Installation step 2 url */
					
					if(ACTIVE_URL!='/install/?step=2') {
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

	}

?>