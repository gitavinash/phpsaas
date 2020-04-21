<!DOCTYPE html>
<html lang="<?php echo SITE_LANG ?>">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link rel="dns-prefetch" href="//www.googletagservices.com">
        <link rel="dns-prefetch" href="//www.googletagmanager.com">
        <link rel="dns-prefetch" href="//www.youtube.com">
        <link rel="dns-prefetch" href="//www.gstatic.com">
        <link rel="dns-prefetch" href="//d1uck549nef0ok.cloudfront.net">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="fb:pages" content="1779174612300730" />
        <!-- PWA Start -->
        <link rel="manifest" href="/mainfest.json">
        <link rel="apple-touch-icon" href="/upload/isw-logo.png">
        <meta name="apple-mobile-web-app-status-bar" content="#D01F29">
        <meta name="theme-color" content="#D01F29">
        <!-- PWA end -->
        <link rel="icon" href="<?php echo USER_IMAGES ?>favicon.png">
        <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
        <link rel="canonical" href="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick-theme.min.css" >
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Eczar:400,500,700|Fira+Sans+Condensed:300,400&display=swap" >
        
        <!-- SEO -->

        <meta name="theme-color" content="#D01F28">
        <meta name='robots' content='index, follow' />
        <meta name='title' content='<?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : $post_title ) : SITE_NAME ) ?>' />
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

        <?php
        
        if(isset($link_type)) {

            if($link_type!='category' && $link_type!='tag') {

                if(BASE_URL=='/') { ?>
                    <link rel="amphtml" href="<?php echo SITE_URL ?>/amp/"/>
                    <link rel="alternate" media="only screen and (max-width: 640px)" href="<?php echo SITE_URL ?>"/>
                <?php
                } else {
                    
                        ?>
                        <link rel="amphtml" href="<?php echo SITE_URL ?>/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>/amp"/>
                        <link rel="alternate" media="only screen and (max-width: 640px)" href="<?php echo SITE_URL ?>/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>"/>
                    <?php
    
                } ?>
    
                <?php
    
            }

        }
        
        ?>

        <?php
            
            $schemaData=SCHEMA_DATA.((isset($link_type)) ? $link_type : 'homepage' ).'.php';

            if(file_exists($schemaData)) {

                require_once $schemaData;

            }

        ?>

    </head>

    <body>

        <header>
            <div class="header">
                <div class="logo">
                    <a href="/" title="<?php echo SITE_NAME ?>"><img src="<?php echo USER_IMAGES ?>logo-main.png" alt="<?php SITE_NAME ?>"  title="<?php echo SITE_NAME.' Official Logo' ?>" ></a>
                </div>
                <div class="other-option">
                    <ul>
                        <li><a href="http://mpaper.punjabkesari.com" title="<?php echo SITE_NAME.' Newspaper' ?>"><img src="<?php echo USER_IMAGES ?>icons/newspaper.png" alt="<?php echo SITE_NAME ?> Newspaper" ></i></a></li>
                        <li id="search" ><a href="#" title="<?php echo SITE_NAME ?>"><img src="<?php echo USER_IMAGES ?>icons/search.png" alt="<?php echo SITE_NAME ?>"></i></a></li>
                        <li id="menu"><a href="#"><img src="<?php echo USER_IMAGES ?>icons/bars.png" alt="<?php echo SITE_NAME ?>"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="menu-box">
                <nav>
                    <ul>
                        <li><a href="/" class="active" >होम</a></li>
                        <li>
                            <a href="/category/india-news/">देश</a>
                            <ul>
                                <li><a href="/category/delhi-ncr/">दिल्ली</a></li>
                                <li><a href="/category/uttar-pradesh/">उत्तर प्रदेश</a></li>
                                <li><a href="/category/haryana-news/">हरियाणा</a></li>
                                <li><a href="/category/rajasthan-news/">राजस्‍थान</a></li>
                                <li><a href="/category/jammu-and-kashmir/">जम्मू-कश्मीर</a></li>
                                <li><a href="/category/punjab-news/">पंजाब</a></li>
                                <li><a href="/category/bihar-news/">बिहार</a></li>
                                <li><a href="/category/other-states/">अन्य राज्य</a></li>
                            </ul>
                        </li>
                        <li><a href="/category/world-news/">विदेश</a></li>
                        <li><a href="/category/bollywood-kesari/">बॉलीवुड</a></li>
                        <li><a href="/category/sports-news/">खेल</a></li>
                        <li><a href="/category/editorial/">संपादकीय</a></li>
                        <li><a href="/category/business-news/">व्यापार</a></li>
                        <li><a href="/category/horoscope/">भविष्यफल</a></li>
                        <li><a href="/category/social-news/">दिलचस्प खबरें</a></li>
                    </ul>
                </nav>
            </div>
            <div class="full-screen-menu" style="display: none" >
                <div class="header">
                    <div class="logo">
                        <a href="/"><img src="<?php echo USER_IMAGES ?>logo-main.png" alt=""></a>
                    </div>
                    <div class="other-option">
                        <ul>
                            <li><a href="https://play.google.com/store/apps/details?id=com.punjab.kesari" rel="noreferrer" target="_blank" ><img src="<?php echo USER_IMAGES ?>icons/play-store.png" alt=""></i></a></li>
                            <li><a href="https://itunes.apple.com/in/app/hindi-news-by-punjab-kesari/id1420361527" rel="noreferrer" target="_blank" ><img src="<?php echo USER_IMAGES ?>icons/apple-store.png" alt="" style="margin-top: 6px" ></i></a></li>
                            <li id="closeMenu"><a href="#"><img src="<?php echo USER_IMAGES ?>icons/close.png" alt=""></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="menu-links">
                    <ul class="menu-list" >
                        <li><a href="/" class="active" >होम</a></li>
                        <li>
                            <a href="/category/india-news/">देश</a>
                            <ul>
                                <li><a href="/category/delhi-ncr/">दिल्ली</a></li>
                                <li><a href="/category/uttar-pradesh/">उत्तर प्रदेश</a></li>
                                <li><a href="/category/haryana-news/">हरियाणा</a></li>
                                <li><a href="/category/rajasthan-news/">राजस्‍थान</a></li>
                                <li><a href="/category/jammu-and-kashmir/">जम्मू-कश्मीर</a></li>
                                <li><a href="/category/punjab-news/">पंजाब</a></li>
                                <li><a href="/category/bihar-news/">बिहार</a></li>
                                <li><a href="/category/other-states/">अन्य राज्य</a></li>
                            </ul>
                        </li>
                        <li><a href="/category/world-news/">विदेश</a></li>
                        <li><a href="/category/bollywood-kesari/">बॉलीवुड</a></li>
                        <li><a href="/category/sports-news/">खेल</a></li>
                        <li><a href="/category/editorial/">संपादकीय</a></li>
                        <li><a href="/category/business-news/">व्यापार</a></li>
                        <li><a href="/category/horoscope/">भविष्यफल</a></li>
                        <li><a href="/category/social-news/">दिलचस्प खबरें</a></li>
                    </ul>
                </div>
                <div class="trending-tags">
                </div>
            </div>
            <div class="search-box" style="display: none" >
                <div class="header">
                    <div class="logo">
                        <a href="/" title="<?php echo SITE_NAME.' Official Logo' ?>"><img src="<?php echo USER_IMAGES ?>logo-main.png" alt=""></a>
                    </div>
                    <div class="other-option">
                        <ul>
                            <li><a href="https://play.google.com/store/apps/details?id=com.punjab.kesari" title="<?php echo SITE_NAME.' Android App' ?>" rel="noreferrer" target="_blank" ><img src="<?php echo USER_IMAGES ?>icons/play-store.png" alt=""></i></a></li>
                            <li><a href="https://itunes.apple.com/in/app/hindi-news-by-punjab-kesari/id1420361527" title="<?php echo SITE_NAME.' iOS App' ?>" rel="noreferrer" target="_blank" ><img src="<?php echo USER_IMAGES ?>icons/apple-store.png" alt="" style="margin-top: 6px" ></i></a></li>
                            <li id="closeSearch"><a href="#" title="Close menu" ><img src="<?php echo USER_IMAGES ?>icons/close.png" alt="close Menu"></i></a></li>
                        </ul>
                    </div>
                </div>
                <form action="/search/" >
                    <input type="text" id="searchBar" class="form-control inside-shadow" name="q" placeholder="Search News here..." >
                    <input type="submit" class="btn btn-block btn-green" style="margin-top: 5px" value="Search News" >
                </form>
            </div>
        </header>

        <main>