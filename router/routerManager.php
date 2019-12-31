<?php

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
				
				/** Check Pagination */

				echo BASE_URL;

			}

	}

	exit();