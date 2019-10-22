<?php

	/** This script run only when Request method is not POST */

	if($_SERVER['REQUEST_METHOD']!='POST') {

        if(ACTIVE_URL!='/install/?step=1') {
					
            header("Location: /install/?step=1", TRUE, 301);
            exit();

        }

        /* Include installation file */
                        
        $installFile1=$_SERVER['DOCUMENT_ROOT'].'/lib/install/step-1.php';

		if(file_exists($installFile1)) {
			
			require_once $installFile1;
			exit();

		} else {
							
			echo 'Unable to find installation files on server';

		}

    }
