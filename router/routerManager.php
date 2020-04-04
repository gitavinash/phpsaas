<?php

	$themeAuthenticationFile=USER_PATH.$urlStructure[0].'.php';

	if(file_exists($themeAuthenticationFile)) {

		/** Theme page */

		require_once $themeAuthenticationFile;

	} else {

		/** System default theme file */

		$systemAuthenticationFile=SYSTEM_PATH.$urlStructure[0].'.php';

		echo $systemAuthenticationFile;
		exit();

		if(file_exists($systemAuthenticationFile)) {

			require_once $systemAuthenticationFile;

		} else {

			/* Search url in link table  */

			if($checkUrl=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

				if(mysqli_num_rows($checkUrl)!=0) {

					extract(mysqli_fetch_assoc($checkUrl));

					if(file_exists(ROUTER.$link_type.'.php')) {

						require_once ROUTER.$link_type.'.php';

					} else {

						echo 'Undefined request found';

					}

				} else {
						
					/** Check Pagination */

					require_once ERROR_404;

				}

			}

		}

	}

	exit();