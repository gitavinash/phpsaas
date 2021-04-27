<?php

    // User selected theme login template

    $themeLoginFile=USER_PATH.$urlStructure[0].'.php';
        
    if(file_exists($themeLoginFile)) {

        require_once $themeLoginFile;

    } else if(file_exists(ADMIN_PATH.'login.php')) {

        // Current admin login template

        require_once ADMIN_PATH.'login.php';

    } else {

        // Default dashboard theme login template

        require_once DEFAULT_VISITOR_PATH.'login.php';

    }
    
    exit();