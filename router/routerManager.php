<?php

	/** Check physical file is available or not */

	$checkPhysicalFile=USER_PATH.str_replace('/', '', ACTIVE_URL);

	if(file_exists($checkPhysicalFile)) {

		require_once $checkPhysicalFile;

	} else {

		$checkPhysicalPageTemplate=USER_PATH.str_replace('/', '', ACTIVE_URL).'.php';

		if(file_exists($checkPhysicalPageTemplate)) {

			require_once $checkPhysicalPageTemplate;

		} else {

			/* Search url in link table  */

			if($checkUrl=mysqli_query($connection, "SELECT link_id, link_relation_id, link_status, link_type, link_is_featured, link_index, link_creation, link_published, link_modified FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

				if(mysqli_num_rows($checkUrl)!=0) {

					extract(mysqli_fetch_assoc($checkUrl));

					if($link_status==200) {

						if(file_exists(ROUTER.$link_type.'.php')) {

							require_once ROUTER.$link_type.'.php';

						} else {

							echo "{$link_type} router not found";

						}

					} else {

						require_once $link_status.'.php';

					}

				} else {
						
					require_once 'pagination.php';

				}

			} else {

				exit();

			}

		}

	}