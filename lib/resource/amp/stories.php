<!doctype html>
<html amp lang="<?php echo SITE_LANG ?>">
    <head>
        <meta charset="utf-8">
        <script async src="https://cdn.ampproject.org/v0.js"></script>
        <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
        <link rel="canonical" href="<?php echo str_replace('/amp', '', ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL )) ?>">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>">
        <link rel="preload" as="script" href="https://cdn.ampproject.org/v0.js">
    
        <?php require_once DEFAULT_META; ?>

        <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
    </head>
    <body>

        <!-- AMP Story code start here -->

        <amp-stoty standalone
            title="PK>"
            publisher="<?php echo SITE_NAME ?>"
            publisher-logo-src="<?php echo SITE_LOGO ?>?width=1&height=1"
            poster-portrait-src="">

            <amp-story-page id="cover" >
                <amp-story-grid-layer template="fill">
                    <amp-img src="/upload/sachin.webp"
                      width="720"
                      height="1280"
                      layout="responsive"
                      alt="Sachin Tendulkar Birthday">
                    </amp-img>
                </amp-story-grid-layer>
                <amp-story-grid-layer template="vertical">
                    <h1>The Joy of Pets</h1>
                    <p>By AMP Tutorials</p>
                </amp-story-grid-layer>
            </amp-story-page>

        </amp-stoty>

    </body>
</html>