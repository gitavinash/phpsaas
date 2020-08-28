<?php

	if(file_exists(USER_PATH.'index.php')) {

		require_once USER_PATH.'index.php';

	} else {

		if(DEVELOPMENT_MODE==false) {
		
			echo '<h1>'.SITE_VERSION.' theme not found</h1>';
		
		} else {

			echo USER_PATH;

		}
		

	}