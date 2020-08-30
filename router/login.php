<?php

    $themeLoginFile=USER_PATH.$urlStructure[0].'.php';

    if(file_exists($themeLoginFile)) {

        require_once $themeLoginFile;

    } else if(file_exists(SYSTEM_VISITOR_PATH.'login.php')) {

        require_once SYSTEM_VISITOR_PATH.'login.php';

    } else {

        require_once DEFAULT_VISITOR_PATH.'login.php';

    }
    
    exit();