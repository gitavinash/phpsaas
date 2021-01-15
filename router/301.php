<?php

    if($linkStatus=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_relation_id='{$link_relation_id}' AND link_status=200 AND link_type='{$link_type}'")) {

        if(mysqli_num_rows($linkStatus)!=0) {

            extract(mysqli_fetch_assoc($linkStatus));

            header("Location: {$link_url}", TRUE, 301);
            exit();

        } else {

            require_once ERROR_404;

        }

    }