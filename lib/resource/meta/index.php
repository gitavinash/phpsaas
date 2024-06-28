<?php

$link_type = ((isset($link_type)) ? $link_type : 'homepage');

$pageTitle = ((isset($meta_title)) ? (($meta_title != '') ? $meta_title : ${$link_type . '_title'} . ' | ' . SITE_NAME) : SITE_DESC . ' | ' . SITE_NAME);
/** Default site information meta data */

require_once 'site.php';

/** SEO meta data */

require_once 'seo.php';

/** Social media sharing card meta data */

require_once 'social.php';

/** Structure data json format */

// $defaultSchemaData = SCHEMA_DATA . "{$link_type}.php";

// if (file_exists($defaultSchemaData)) {
//     require_once $defaultSchemaData;
// } else {
//     require_once $defaultSchemaData = SCHEMA_DATA . "organization.php";
// }