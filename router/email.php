<?php

	/* Check user is logged in or not */

	/* url router for admin dashboard */

	$fileName=EMAIL_TEMPLATE.str_replace('email/', '',rtrim(ltrim(BASE_URL, '/'), '/')).'.php';

	if(file_exists($fileName)) {

		include_once $fileName;

	}