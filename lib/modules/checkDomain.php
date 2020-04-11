<?php

    /** Check this domain is register or not */

    if($checkDomain=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site WHERE site_domain='".SITE_DOMAIN."'")) {
        if(mysqli_num_rows($checkDomain)==0) {

            echo SITE_DOMAIN.' is not register with us';
            exit();

        } else {

            /** Domain is register with us */

            extract(mysqli_fetch_assoc($checkDomain));

            if($site_status!=1) {
                echo 'Your Site has been expire';
                exit();
            }

        }

    }