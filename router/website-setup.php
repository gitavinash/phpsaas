<?php

// User selected theme login template

$themeLoginFile = USER_PATH . $urlStructure[0] . '.php';

if (!file_exists($themeLoginFile)) {

    $themeLoginFile = str_replace('/default/', '/' . USER_TYPE . '/', ADMIN_PATH) . $urlStructure[0] . '.php';

    if (!file_exists($themeLoginFile)) {

        $themeLoginFile = ADMIN_PATH . $urlStructure[0] . '.php';
    }

    if (!file_exists($themeLoginFile)) {

        // Default dashboard theme login template

        $themeLoginFile = DEFAULT_VISITOR_PATH . $urlStructure[0] . '.php';
    }
}

require_once $themeLoginFile;

exit();
