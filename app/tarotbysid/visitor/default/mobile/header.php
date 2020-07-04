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
        
        <?php require_once DEFAULT_META; ?>

        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick-theme.min.css" >
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Eczar:400,500,700|Fira+Sans+Condensed:300,400&display=swap" >


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