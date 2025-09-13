<?php

// User selected theme login template

$themeLoginFile = USER_PATH . $urlStructure[0] . '.php';

if (!file_exists($themeLoginFile)) {

    if (SITE_APP == 'spa') {
        $authby = 'mobile';
    }
    $fileName = ((isset($authby)) ? strtolower($authby) : 'email') . '-login';

    $themeLoginFile = str_replace('/default/', '/' . USER_TYPE . '/', ADMIN_PATH) . $fileName . '.php';

    if (!file_exists($themeLoginFile)) {

        $themeLoginFile = ADMIN_PATH . $fileName . '.php';
    }

    if (!file_exists($themeLoginFile)) {

        // Default dashboard theme login template

        $themeLoginFile = DEFAULT_VISITOR_PATH . $fileName . '.php';
    }
}

require_once $themeLoginFile;

exit();