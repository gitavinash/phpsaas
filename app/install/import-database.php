<?php

    $filename = ROOT.'/lib/resource/database/database.php';

    if(file_exists($filename)) {

        require_once $filename;
        header("Refresh:0");
        exit();

    } else {

        echo '';
        exit();

    }

?>