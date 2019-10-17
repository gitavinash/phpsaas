<?php

    foreach($_GET as $key => $value) {
        $secure[$key]=mysqli_real_escape_string($connection, $value);
    }

    $urlStructure=explode('/', rtrim(ltrim(BASE_URL, '/'), '/'));

    if(file_exists(MODULES.'verify.php')) {

        require_once MODULES.'verify.php';

        if(function_exists(end($urlStructure))) {
            
            call_user_func(end($urlStructure), $connection, $secure);

        } else {

            echo 'Function Not Found';

        }

    } else {
        echo 'file not found';
    }
    
	exit();