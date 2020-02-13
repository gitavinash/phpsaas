<?php

	if(file_exists(USER_PATH.'index.php')) {

		require_once USER_PATH.'index.php';

	} else {

		echo '<h1>'.SITE_VERSION.' theme not found</h1>'.USER_PATH.'index.php';

	}

	exit();