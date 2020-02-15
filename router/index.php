<?php

	header( 'Content-Type: text/html; charset=utf-8' );

	/** Check website domain status */

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/siteStatus.php';
	
	/** Check configuration file is exist or not */

	if(file_exists('lib/configuration/index.php')) {

        /** Including configuration file file  */

        require_once 'lib/configuration/index.php';

    } else {

		echo '<h1>Something is wrong while including configuration files</h1>';
		exit();

	}

	/** Manage All POST method request */

	if($_SERVER['REQUEST_METHOD']=="POST") {

		require_once 'formManager.php';
		exit();

	}
	
	/** Redirect all double slash url request to single slash */

	if(strpos(BASE_URL, '//')!==FALSE) {

		$redirectURL=str_replace('//', '/', BASE_URL);
		header("Location: $redirectURL", TRUE, 301);
		exit();

	} else {
		
		/** Redirect non end slash url to slash url */

		if(substr(BASE_URL , -1)!='/') {
			
			$redirectURL=BASE_URL.'/';
			header("Location: $redirectURL", TRUE, 301);
			exit();
			
		}

	}

	if($urlStructure[0]=='') {

		require_once 'homepage.php';

	} else {

		switch ($urlStructure[0]) {

			case 'install' :
				require_once 'install.php';
				break;

			case 'dashboard' :
				require_once 'admin.php';
				break;

			case 'login' :
				require_once 'login.php';
				break;

			case 'logout' :
				require_once 'logout.php';
				break;

			/** For all type of verification process */
		
			case 'verify' :
				require_once 'verify.php';
				break;

			case 'email' :
				require_once 'email.php';
				break;

			case 'search' :
				require_once 'search.php';
				break;

			case 'tag' :
				require_once 'tag.php';
				break;

			case 'category' :
				require_once 'category.php';
				break;

			case 'feed' :
				require_once 'feed.php';
				break;

			case 'redirect' :
				require_once ROOT.'/redirect.php';
				break;
			
			case (preg_match('/sitemap.xml/', $urlStructure[0]) ? true : false) :
				require_once 'sitemap.php';
				break;
			
			default : 
				require_once ROUTER.'cache.php';
				break;

		}

	}

	mysqli_close($connection);

	exit();