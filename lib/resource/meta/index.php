<?php

    $link_type=((isset($link_type)) ? $link_type : 'homepage' );

    /** Default site information meta data */

    require_once 'site.php';

    /** SEO meta data */

    require_once 'seo.php';

    /** Social media sharing card meta data */

    require_once 'social.php';

    /** Structure data json format */


    $defaultSchemaData=SCHEMA_DATA.((isset($link_type)) ? $link_type : 'organization' ).'.php';

    if(file_exists($defaultSchemaData)) {

        require_once $defaultSchemaData;

    }