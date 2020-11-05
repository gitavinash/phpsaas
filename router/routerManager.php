<?php

	/** Check physical file is available or not */

	$checkPhysicalPageTemplate=USER_PATH.str_replace('/', '', ACTIVE_URL).'.php';

	if(file_exists($checkPhysicalPageTemplate)) {

		require_once $checkPhysicalPageTemplate;

	} else {

		/* Search url in link table  */

		if($checkUrl=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

			if(mysqli_num_rows($checkUrl)!=0) {

				extract(mysqli_fetch_assoc($checkUrl));

				if(file_exists(ROUTER.$link_type.'.php')) {

					// require_once ROUTER.$link_type.'.php';
					echo  ROUTER.$link_type.'.php';

				} else {

					// echo 'Undefined request found';

					echo ROUTER.$link_type.'.php';

				}

			} else {
					
				require_once 'pagination.php';

			}

		} else {

			echo 'sfd';
			exit();

		}

	}