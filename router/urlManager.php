<?php

    switch ($urlStructure[0]) {

        case ''	:
            require_once 'homepage.php';
            break;
        
        case 'amp' :
            require_once 'amp.php';
            break;

        case 'api'	:
            require_once 'api.php';
            break;

        case 'search' :
            require_once 'search.php';
            break;
        
        case 'feed' :
            require_once 'feed.php';
            break;
        
        case 'tag' :
            require_once 'tag.php';
            break;
        
        case 'category' :
            require_once 'category.php';
            break;
        
        default :
            require_once 'routerManager.php';
            break;

    }