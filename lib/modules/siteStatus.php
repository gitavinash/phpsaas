<?php

	/** Check site status */

	if($site_status!=1) {

		/** Currently Website is not active */

		echo 'Website is not active';
		exit();

	}

	if($site_expire_date<date('Y-m-d')) {

		/** Website is Expired */

		echo 'Website is expire';
		exit();

	}

	/** Installation step complete webpage */

	if(ACTIVE_URL=='/install/?step=2') {
		
		require_once $_SERVER['DOCUMENT_ROOT'].'lib/install/step-2.php';
		exit();

	}