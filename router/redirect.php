<?php

    if($linkStatus=mysqli_query($connection, "SELECT redirection_to, redirection_http_status FROM ".TABLE_PREFIX."redirection WHERE redirection_id='{$link_relation_id}' AND redirection_site_id=".SITE_ID)) {

        if(mysqli_num_rows($linkStatus)!=0) {

            extract(mysqli_fetch_assoc($linkStatus));

            header("Location: {$redirection_to}", TRUE, $redirection_http_status);
            exit();

        } else {
            
			http_response_code(404);
            require_once ERROR_404;

        }

    }