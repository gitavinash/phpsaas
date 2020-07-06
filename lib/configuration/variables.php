<?php

    define('DEFAULT_MAINFEST_JSON', json_encode('{
        "short_name": "%SITE_NAME%",
        "name": "%SITE_NAME%",
        "description": "%SITE_DESC%",
        "icons": [
            {
                "src": "%SITE_LOGO%",
                "type": "image/png",
                "sizes": "192x192"
            },
            {
                "src": "%SITE_LOGO%",
                "type": "image/png",
                "sizes": "512x512"
            }
        ],
        "start_url": "/",
        "background_color": "%SITE_THEME_COLOUR%",
        "display": "standalone",
        "scope": "/",
        "theme_color": "%SITE_THEME_COLOUR%",
        
        # Only for Desktop Version Application
        
        "shortcuts": [
            {
                "name": "Name of the Shortcut",
                "short_name": "Today",
                "description": "View weather information for today",
                "url": "/shortcut",
                "icons": [{ "src": "/images/today.png", "sizes": "192x192" }]
            },
            {
                "name": "Name of the Shortcut",
                "short_name": "Tomorrow",
                "description": "View weather information for tomorrow",
                "url": "/shortcut-2",
                "icons": [{ "src": "/images/tomorrow.png", "sizes": "192x192" }]
            }
        ]
    }', TRUE));