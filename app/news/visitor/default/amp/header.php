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
        
    <!-- SEO -->

    <meta name="theme-color" content="#D01F28">
    <meta name="robots" content="index, follow" />
    <meta name="title" content="<?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : $post_title ) : SITE_NAME ) ?>" />
    <meta name="description" content="<?php echo ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ) ?>" >
    <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
    <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

    <!-- Facebook, Google & Pinterest -->
		
    <meta property="og:locale" content="hi_IN" />
	<meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo ((isset($fb_title)) ? (($fb_title!='') ? $fb_title : $meta_title ) : SITE_NAME ) ?>" />
    <meta property="og:description" content="<?php echo ((isset($fb_desc)) ? (($fb_desc!='') ? $fb_desc : $meta_description ) : SITE_DESC ) ?>" />
    <meta property="og:url" content="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
    <meta property="og:image" content="<?php echo ((isset($fb_image)) ? SITE_URL.(($fb_image!='') ? $fb_image : $post_image ) : SITE_URL.((isset($post_image)) ? $post_image : USER_IMAGES.'logo-main.png' ) ) ?>" />
    <meta property="og:site_name" content="<?php echo SITE_NAME ?>" />
    <meta property="fb:admins" content="1779174612300730" />
    <meta property="fb:app_id" content="1078178022375029" />
    <?php
        
        if(isset($link_type)) {

            if($link_type=='post') { ?>

                <meta property="article:published_time" content="<?php echo date('Y-m-d\TH:i:s+05:30', strtotime($link_published)) ?>" />
                <meta property="article:modified_time" content="<?php echo date('Y-m-d\TH:i:s+05:30', strtotime($link_modified)) ?>" />
                <meta property="article:section" content="<?php echo $category_title ?>" />
                <meta property="article:tag" content="<?php echo $tagName ?>" />

            <?php
            }

        }

    ?>

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@PunjabKesariCom">
    <meta name="twitter:title" content="<?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : $post_title ) : SITE_NAME ) ?>">
    <meta name="twitter:description" content="<?php echo ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ) ?>">
    <meta name="twitter:creator" content="@PunjabKesariCom">
    <meta name="twitter:image" content="<?php echo ((isset($post_image)) ? SITE_URL.(($post_image!='') ? $post_image : '' ) : USER_IMAGES.'logo-main.png' ) ?>">
    <meta name="twitter:url" content="<?php echo SITE_URL.BASE_URL ?>">

    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
    <script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
    <script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>
    <script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>
    <script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>

    
    <?php

        /** Custom Styling using CSS */

        require_once 'amp-style.php';     
        
        $schemaData=SCHEMA_DATA.((isset($link_type)) ? $link_type : 'homepage' ).'.php';

        if(file_exists($schemaData)) {

            require_once $schemaData;

        }

    ?>

  </head>

  <body>
    <header>
        <div class="logo-box">
            <div class="logo">
                <a href="<?php echo SITE_URL ?>/amp/">
                    <amp-img src="<?php echo USER_IMAGES ?>logo-main.png"
                        width="165"
                        height="50"
                        layout="responsive"
                        alt="">
                    </amp-img>
                </a>
            </div>
            <div class="other-option">
                <ul>
                    <li><a href=""><amp-img src="<?php echo USER_IMAGES ?>icons/newspaper.png" width="20" height="30" layout="fixed" alt=""></amp-img></a></li>
                    <li><a href=""><amp-img src="<?php echo USER_IMAGES ?>icons/search.png" width="20" height="30" layout="fixed" alt=""></amp-img></a></li>
                    <li><a href=""><amp-img src="<?php echo USER_IMAGES ?>icons/bars.png" width="20" height="30" layout="fixed" alt=""></amp-img></a></li>
                </ul>
            </div>
        </div>
        <div class="menu-box">
                <nav>
                    <ul>
                        <li><a href="/amp/" class="active" >होम</a></li>
                        <li>
                            <a href="/amp/category/india-news/">देश</a>
                            <ul>
                                <li><a href="/amp/category/delhi-ncr/">दिल्ली</a></li>
                                <li><a href="/amp/category/uttar-pradesh/">उत्तर प्रदेश</a></li>
                                <li><a href="/amp/category/haryana-news/">हरियाणा</a></li>
                                <li><a href="/amp/category/rajasthan-news/">राजस्‍थान</a></li>
                                <li><a href="/amp/category/jammu-and-kashmir/">जम्मू-कश्मीर</a></li>
                                <li><a href="/amp/category/punjab-news/">पंजाब</a></li>
                                <li><a href="/amp/category/bihar-news/">बिहार</a></li>
                                <li><a href="/amp/category/other-states/">अन्य राज्य</a></li>
                            </ul>
                        </li>
                        <li><a href="/amp/category/world-news/">विदेश</a></li>
                        <li><a href="/amp/category/bollywood-kesari/">बॉलीवुड</a></li>
                        <li><a href="/amp/category/sports-news/">खेल</a></li>
                        <li><a href="/amp/category/editorial/">संपादकीय</a></li>
                        <li><a href="/amp/category/business-news/">व्यापार</a></li>
                        <li><a href="/amp/category/horoscope/">भविष्यफल</a></li>
                        <li><a href="/amp/category/social-news/">दिलचस्प खबरें</a></li>
                    </ul>
                </nav>
            </div>
    </header>
    
    <main>