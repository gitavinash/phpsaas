<?php

	if(file_exists(USER_PATH.'index.php')) {

		// Fetch homepage meta tags for seo

		if($checkHomepageMeta=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."meta INNER JOIN ".TABLE_PREFIX."link ON link_id=meta_relation_id WHERE link_url='//' AND link_status=200 AND link_site_id=".SITE_ID)) {

			if(mysqli_num_rows($checkHomepageMeta)!=0) {

				extract(mysqli_fetch_assoc($checkHomepageMeta));

			}

		}

		require_once USER_PATH.'index.php';

	} else {

		if(DEVELOPMENT_MODE==false) {
		
			echo USER_PATH. '<h1>'.SITE_VERSION.' theme not found</h1>';
		
		} else {

			echo USER_PATH;

		}
		

	}