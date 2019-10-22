<?php

	/** Including database connection file */

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/modules/connection.php';

    /** Check this domain is register or not */

    if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site WHERE site_domain='".SITE_DOMAIN."'")) {
        
        if(mysqli_num_rows($checkDomain)==0) {
            
            require_once 'checkInstallation.php';

        } else {

            /** Domain is register with us */

            extract(mysqli_fetch_assoc($checkDomain));

            require_once 'siteStatus.php';

        }

    } else {

        /** Import Database */

        require_once $_SERVER['DOCUMENT_ROOT'].'/lib/install/import-database.php';

    }