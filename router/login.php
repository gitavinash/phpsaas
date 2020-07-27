<?php

    $themeLoginFile=USER_PATH.$urlStructure[0].'.php';

    if(file_exists($themeLoginFile)) {

        require_once $themeLoginFile;

    } else {

        require_once SYSTEM_VISITOR_PATH.'login.php';

    }
    
    exit();