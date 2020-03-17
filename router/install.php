<?php

    /* url router for admin dashboard */

    if(mysqli_num_rows($checkInstallationAnyWebsite)==0) {

        $installingStep=mysqli_real_escape_string($connection, $_GET['step']);
    
        $installingFile=INSTALLATION_PATH.'step-'.$installingStep.'.php';

        if(file_exists($installingFile)) {

            include_once $installingFile;

        } else {

            $installingFile=INSTALLATION_PATH.$installingStep.'.php';
            
            if(file_exists($installingFile)) {

                require_once $installingFile;

            } else {

                include_once ERROR_404;

            }

        }

    } else {

        header("Location: /", TRUE, 301);
        exit();

    }
	
	exit();