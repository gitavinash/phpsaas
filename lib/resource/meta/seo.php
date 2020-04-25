<!-- SEO -->

<meta name="theme-color" content="<?php echo SITE_THEME_COLOR ?>">
<meta name='robots' content='index, follow' />
<meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
<meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
<meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
<meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">