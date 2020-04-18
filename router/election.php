<?php

	if(isset($urlStructure[1])) {

        $includeFile=ROOT.'/election-result-2019/'.$urlStructure[1].'.php';

        if(file_exists($includeFile)) {

            require_once $includeFile;

        } else {

            require_once ERROR_404;

        }

	} else {

		/* Redirect to homepage of site version */

		header("Location: ".SITE_DOMAIN, TRUE, 301);

	}