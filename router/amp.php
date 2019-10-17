<?php

	if(isset($urlStructure[1])) {

		/* Actual url of AMP Version */

		$url=str_replace('/amp', '', BASE_URL);

		echo 'i am AMP version';

	} else {

		/* Redirect to homepage of site version */

		header("Location: ".SITE_DOMAIN, TRUE, 301);

	}

	exit();