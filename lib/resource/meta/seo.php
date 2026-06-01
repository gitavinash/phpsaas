<!-- SEO -->

<meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
<meta name='robots' content='<?php echo ((isset($meta_robots)) ? $meta_robots : $site_index) ?>' />
<meta name='title' content="<?php echo ucfirst($pageTitle) ?>" />
<meta name="description"
    content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description != '') ? $meta_description : SITE_DESC) : SITE_DESC))))) ?>">
<meta name="keywords"
    content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword != '') ? $meta_keyword : ((isset($tagName)) ? $tagName : '')) : SITE_TAGS) ?>">
<meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME); ?>">