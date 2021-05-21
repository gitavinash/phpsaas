<?php $canonicalURL=str_replace('amp/', '', ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL )); ?>
<!-- Site Default Configuration -->
<link rel="icon" href="<?php echo SITE_ICON ?>">
<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
<!-- Canonical URL -->
<link rel="canonical" href="<?php echo $canonicalURL; ?>" />
<!-- PWA Start -->
<link rel="manifest" href="/mainfest.json">
<link rel="apple-touch-icon" href="<?php echo SITE_ICON ?>">
<meta name="apple-mobile-web-app-status-bar" content="<?php echo SITE_THEME_COLOUR ?>">


<?php

    if(defined('ENABLE_AMP')) {

        // AMP
    
        if(SITE_VERSION=='amp') {

            // AMP site tag
            
            echo "<link rel='alternate' href='{$canonicalURL}' />";
        
        } else {
            
            // Non AMP site tag
    
            echo "<link rel='amphtml' href='".str_replace(SITE_URL, SITE_URL.'/amp', $canonicalURL)."' >";
    
        }

    }

?>