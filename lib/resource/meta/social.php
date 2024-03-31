<!-- Facebook, Google & Pinterest -->

<meta property="og:locale" content="<?php echo SITE_LANG ?>" />
<meta property="og:type"
    content="<?php echo ((isset($link_type)) ? (($link_type=='post') ? 'article' : $link_type ) : 'website' ) ?>" />
<meta property="og:title"
    content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($fb_title)) ? (($fb_title!='') ? $fb_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
<meta property="og:description"
    content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" />
<meta property="og:url"
    content="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : 'https://'.SITE_DOMAIN.BASE_URL ) : 'https://'.SITE_DOMAIN.BASE_URL ) ?>" />
<meta property="og:image"
    content="<?php if(isset($fb_image)) {echo $fb_image;} else {echo ((isset(${$link_type.'_image'})) ? ${$link_type.'_image'} : SITE_LOGO );} ?>" />
<meta property="og:site_name" content="<?php echo SITE_NAME ?>" />
<meta property="fb:admins" content="" />
<meta property="fb:app_id" content="" />
<meta property="fb:pages" content="" />
<?php

    if(isset($link_type)) {

        if(file_exists(META_DIR.'social-media/'.$link_type.'.php')) {

            require_once META_DIR.'social-media/'.$link_type.'.php';

        }

    }

?>

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@">
<meta name="twitter:title"
    content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_title)) ? (($tw_title!='') ? $tw_title : SITE_NAME ) : SITE_NAME ))))) ?>">
<meta name="twitter:description"
    content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_desc)) ? (($tw_desc!='') ? $tw_desc : SITE_DESC ) : SITE_DESC ))))) ?>">
<meta name="twitter:creator" content="@">
<meta property="og:image"
    content="<?php if(isset($tw_image)) {echo $tw_image;} else {echo ((isset(${$link_type.'_image'})) ? ${$link_type.'_image'} : SITE_LOGO );} ?>" />
<meta name="twitter:url" content="<?php echo SITE_URL.BASE_URL ?>">