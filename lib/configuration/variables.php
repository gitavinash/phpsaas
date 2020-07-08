<?php

    /** Default mainfest.json file */

    define('DEFAULT_MAINFEST_JSON', json_encode('{
        "short_name": "%SITE_NAME%",
        "name": "%SITE_NAME%",
        "description": "%SITE_DESC%",
        "icons": [
            {
                "src": "%SITE_ICON%",
                "type": "image/png",
                "sizes": "192x192"
            },
            {
                "src": "%SITE_ICON%",
                "type": "image/png",
                "sizes": "512x512"
            }
        ],
        "start_url": "/",
        "background_color": "%SITE_THEME_COLOUR%",
        "display": "standalone",
        "scope": "/",
        "theme_color": "%SITE_THEME_COLOUR%",
    }', TRUE));

    /** Default robots.txt file */

    define("DEFAULT_ROBOTS_TXT", "User-Agent: *\nDisallow:");