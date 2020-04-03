<?php

	if($urlStructure[0]!='') {

		$themeFile=USER_PATH.$urlStructure[0];

		if(file_exists($themeFile)) {

			require_once $themeFile;

		}

	} else {

		if(file_exists(USER_PATH.'index.php')) {

			require_once USER_PATH.'index.php';

		} else {

			echo '<h1>'.SITE_VERSION.' theme not found</h1>';

		}

	}

	exit();