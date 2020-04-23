<!-- News Article Schema -->

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "mainEntityOfPage":{
            "@type":"WebPage",
            "@id":"<?php echo SITE_URL.BASE_URL ?>"
        },
        "headline": "<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>",
        "image": {
            "@type": "ImageObject",
            "url": "<?php echo ((isset($news_image)) ? SITE_URL.(($news_image!='') ? $news_image : '' ) : USER_IMAGES.'logo-main.png' ) ?>",
            "height": 800,
            "width": 800
        },
        "datePublished": "<?php echo date('Y-m-d\TH:i:s+05:30', strtotime($link_published)) ?>",
        "dateModified": "<?php echo date('Y-m-d\TH:i:s+05:30', strtotime($link_modified)) ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo $profile_display_name ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo SITE_NAME ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo USER_IMAGES ?>logo-main.png",
                "width": 240,
                "height": 35
            }
        },
        "keywords": ["<?php echo preg_replace('~[^\p{M}\w\s,]+~u', '', $tagName) ?>"],
        "articleSection": "<?php echo $category_title ?>",
        "description": "<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>"
    }
</script>