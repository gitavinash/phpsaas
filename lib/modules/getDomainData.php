<?php

    /** Check software is already install or not */

	if($checkInstallation=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site INNER JOIN ".TABLE_PREFIX."site_setting ON site_setting_id=site_id WHERE site_url='".SITE_DOMAIN."'")) {

		if(mysqli_num_rows($checkInstallation)!=0) {

            extract(mysqli_fetch_assoc($checkInstallation));

        }
    
    }