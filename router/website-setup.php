<?php

$pageName = in_array($urlStructure[0], ['onboarding', 'website-setup']) ? 'website-setup' : $urlStructure[0];

$themeLoginFile = USER_PATH . $urlStructure[0] . '.php';

if (!file_exists($themeLoginFile)) {

    $themeLoginFile = str_replace('/default/', '/' . USER_TYPE . '/', ADMIN_PATH) . $pageName . '.php';

    if (!file_exists($themeLoginFile)) {

        $themeLoginFile = ADMIN_PATH . $pageName . '.php';
    }

    if (!file_exists($themeLoginFile)) {

        $themeLoginFile = DEFAULT_VISITOR_PATH . $pageName . '.php';
    }
}

require_once $themeLoginFile;

exit();
