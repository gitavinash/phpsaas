<?php

    $type = ((isset($type)) ? $type : 'default' );
    $tag_type='';
    $tag_description='';



    /** Search query */

    $searchQuery=str_replace('-', ' ', mysqli_real_escape_string($connection, $q));

    switch($type) {

        case 'domain' : 

            // Include search from end theme template

            require_once 'domain.php';
            break;

        default : 

            // Search query from database tag table

            require_once 'database.php';
            break;

    }

    exit();