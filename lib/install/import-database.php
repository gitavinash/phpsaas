<?php

    $filename = ROOT.'/lib/resource/database/database.php';

    if(file_exists($filename)) {

        require_once $filename;

        header("Location: /install/?step=1", TRUE, 301);

    } else {

        echo '';
        exit();

    }

?>