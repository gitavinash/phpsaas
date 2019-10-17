<?php
	
	/* check if local file is available in theme or not */

	if(file_exists(USER_VIEW_PATH.$urlStructure[0].'.php')) {

		require_once USER_VIEW_PATH.$urlStructure[0].'.php';

	} else {

		/* Search url in link table  */

		if($checkUrl=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."link WHERE link_url='".BASE_URL."'")) {

			if(mysqli_num_rows($checkUrl)!=0) {

				extract(mysqli_fetch_assoc($checkUrl));

				if(file_exists(ROUTER.$link_type.'.php')) {

					require_once ROUTER.$link_type.'.php';

				} else {

					echo ROUTER.$link_type.'.php';

				}

			} else {
				
				include_once ERROR_404;

			}

		} else {

			/** Add physical file */
			
			$physicalFile=$_SERVER['DOCUMENT_ROOT'].$urlStructure[0].'.php';
			
			if(file_exists($physicalFile)) {

				require_once $physicalFile;

			}

		}

	}

	exit();