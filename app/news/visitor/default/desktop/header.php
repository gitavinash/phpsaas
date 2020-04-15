<!DOCTYPE html>
<html lang="hi_IN">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link rel="dns-prefetch" href="//www.googletagservices.com">
        <link rel="dns-prefetch" href="//www.googletagmanager.com">
        <link rel="dns-prefetch" href="//www.youtube.com">
        <link rel="dns-prefetch" href="//d1uck549nef0ok.cloudfront.net">
        <link rel="dns-prefetch" href="//cds.taboola.com">
        <link rel="dns-prefetch" href="//ups.analytics.yahoo.com">
        <link rel="dns-prefetch" href="//pixel.advertising.com">
        <link rel="dns-prefetch" href="//cdn.jsdelivr.net/">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="fb:pages" content="1779174612300730" />
        <link rel="manifest" href="/mainfest.json">
        <link rel="icon" href="<?php echo USER_IMAGES ?>favicon.png">
        <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
        <link rel="canonical" href="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" >
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick-theme.min.css" >
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Eczar:400,500,700|Fira+Sans+Condensed:300,400" >
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/progressive-image.js/dist/progressive-image.css">
        
        <!-- SEO -->

        <meta name="theme-color" content="#D01F28">
        <meta name='robots' content='index, follow' />
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

        <!-- Facebook, Google & Pinterest -->
		
        <meta property="og:locale" content="hi_IN" />
		<meta property="og:type" content="article" />
        <meta property="og:title" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($fb_title)) ? (($fb_title!='') ? $fb_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta property="og:description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" />
        <meta property="og:url" content="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
        <meta property="og:image" content="<?php echo ((isset($fb_image)) ? SITE_URL.(($fb_image!='') ? $fb_image : $news_image ) : SITE_URL.((isset($news_image)) ? $news_image : USER_IMAGES.'logo-main.png' ) ) ?>" />
        <meta property="og:site_name" content="Punjab Kesari" />
        <meta property="fb:admins" content="1779174612300730" />
        <meta property="fb:app_id" content="1078178022375029" />
        <?php
        
        if(isset($link_type)) {

            if($link_type=='news') { ?>

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
        <meta name="twitter:title" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_title)) ? (($tw_title!='') ? $tw_title : SITE_NAME ) : SITE_NAME ))))) ?>">
        <meta name="twitter:description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_desc)) ? (($tw_desc!='') ? $tw_desc : SITE_DESC ) : SITE_DESC ))))) ?>">
        <meta name="twitter:creator" content="@PunjabKesariCom">
        <meta name="twitter:image" content="<?php echo ((isset($news_image)) ? SITE_URL.(($news_image!='') ? $news_image : '' ) : USER_IMAGES.'logo-main.png' ) ?>">
        <meta name="twitter:url" content="<?php echo SITE_URL.BASE_URL ?>">

        <!-- Adx -->

        <script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
        <script>
        var googletag = googletag || {};
        googletag.cmd = googletag.cmd || [];
        </script>

        <script>
        googletag.cmd.push(function() {
        var mapping = googletag.sizeMapping().
        addSize([800,90], [728, 90]).
        addSize([700,90], [468, 60]).
        addSize([0, 0], [300, 250]).
        build();
            googletag.defineSlot('/1009127/RW_PunjabKesari_728x90', [728, 90], 'div-gpt-ad-1549609719157-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_300x250', [300, 250], 'div-gpt-ad-1549554871716-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_Resp_1', [728, 90], 'div-gpt-ad-1549975705782-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_Resp_2', [728, 90], 'div-gpt-ad-1549975808942-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_Resp_3', [728, 90], 'div-gpt-ad-1549975922100-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_Resp_4', [728, 90], 'div-gpt-ad-1549976009867-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.defineSlot('/1009127/RW_PunjabKesari_Resp_5', [728, 90], 'div-gpt-ad-1549976105111-0').defineSizeMapping(mapping).addService(googletag.pubads());
            googletag.pubads().enableSingleRequest();
            googletag.enableServices();
        });
        </script>

        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId=1687095354928063&autoLogAppEvents=1"></script>

        <?php
        
        if(isset($link_type)) {

            if($link_type!='category' && $link_type!='tag') {

                if(BASE_URL=='/') { ?>
                    <link rel="amphtml" href="https://m.punjabkesari.com/amp"/>
                    <link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.punjabkesari.com"/>
                <?php
                } else {
                    
                        ?>
                        <link rel="amphtml" href="https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>/amp"/>
                        <link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>"/>
                    <?php
    
                } ?>
    
                <!-- Readwhere redirection code -->
    
                <script type='text/javascript'>
                    if ((navigator.userAgent.match(/(iphone)|(ipod)|(ipad)|(android)|(blackberry)|(windows phone)|(symbian)/i))){
    
                        <?php
                        
                        if(BASE_URL=='/') { ?>
                            var request_uri = 'https://m.punjabkesari.com/  ';
                        <?php
                        } else { ?>
                            var request_uri = 'https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id ?><?php echo ((QUERY_URL=='') ? '' : '?'.QUERY_URL ) ?>';
                        <?php
                        }
    
                        ?>
    
                        top.location.href= request_uri;
                    }
                </script>
    
                <?php
    
            }

        } else {

if(BASE_URL=='/') { ?>
                    <link rel="amphtml" href="https://m.punjabkesari.com/amp"/>
                    <link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.punjabkesari.com"/>
                <?php
                } else {
                    
                        ?>
                        <link rel="amphtml" href="https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>/amp"/>
                        <link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id; ?>"/>
                    <?php
    
                } ?>

            <script type='text/javascript'>
                                if ((navigator.userAgent.match(/(iphone)|(ipod)|(ipad)|(android)|(blackberry)|(windows phone)|(symbian)/i))){
                
                                    <?php
                                    
                                    if(BASE_URL=='/') { ?>
                                        var request_uri = 'https://m.punjabkesari.com/  ';
                                    <?php
                                    } else { ?>
                                        var request_uri = 'https://m.punjabkesari.com/article/<?php $currentLink=explode('/', rtrim(ltrim(BASE_URL, '/'), '/')); echo end($currentLink); ?>/<?php echo $link_id ?><?php echo ((QUERY_URL=='') ? '' : '?'.QUERY_URL ) ?>';
                                    <?php
                                    }
                
                                    ?>
                
                                    top.location.href= request_uri;
                                }
                            </script>
            
                        <?php
                        }
        
        ?>

        <?php

            $schemaData=SCHEMA_DATA.((isset($link_type)) ? $link_type : 'homepage' ).'.php';

            if(file_exists($schemaData)) {

                require_once $schemaData;

            }
            
            if(isset($link_type)) {
                
                if($link_type=='news') { ?>
                    
                    <!-- News Schema -->

                    

                    <!-- Taboola Code -->

                    <script type="text/javascript">
                        window._taboola = window._taboola || [];
                        _taboola.push({article:'auto'});
                        !function (e, f, u, i) {
                            if (!document.getElementById(i)){
                            e.async = 1;
                            e.src = u;
                            e.id = i;
                            f.parentNode.insertBefore(e, f);
                            }
                        }(document.createElement('script'),
                        document.getElementsByTagName('script')[0],
                        '//cdn.taboola.com/libtrc/punjabkesaripublishersprivatelimited/loader.js',
                        'tb_loader_script');
                        if(window.performance && typeof window.performance.mark == 'function')
                            {window.performance.mark('tbl_ic');}
                    </script>
        
                    <?php
                }

            }

        ?>

        <!-- Global site tag (gtag.js) - Google Analytics ( Punjab Kesari Old ) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-10313152-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-10313152-1');
</script>


<!-- Global site tag (gtag.js) - Google Analytics ( Punjab Kesari Desktop ) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-154787764-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-154787764-1');
</script>

<!-- Global site tag (gtag.js) - Google Analytics ( Punjab Kesari Desktop ) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-154787764-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-154787764-2');
</script>

<script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script>
  window.googletag = window.googletag || {cmd: []};
  googletag.cmd.push(function() {
    googletag.defineSlot('/21704298696/REC_300_250_ad_unit', [300, 250], 'div-gpt-ad-1581925731733-0').addService(googletag.pubads());
    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
  });
</script>

<script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script>
  window.googletag = window.googletag || {cmd: []};
  googletag.cmd.push(function() {
    googletag.defineSlot('/21704298696/REC_728x90', [728, 90], 'div-gpt-ad-1582615224176-0').addService(googletag.pubads());
    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
  });
</script>


    </head>

    <body>
        
        <?php

            /** Show menu bar only for login users */

            if(isset($_COOKIE['user_data'])) {
                if($urlStructure[0]!='dashboard') { ?>
                    <div class="dashboard-bar english">
                        <ul class='left-menu'>
                            <li><a href="dashboard/"><i class='fas fa-tachometer-alt'></i><?php echo SITE_NAME ?></a></li>
                            <?php
                                if((isset($link_type)) ? $link_type=='page' || $link_type=='news' : '' ) { ?>
                                    <li><a href="dashboard/<?php echo $link_type ?>/?type=edit&editor=true&id=<?php echo $link_relation_id ?>"><i class='fas fa-pencil-alt'></i>Edit <?php echo ucwords($link_type); ?></a></li>
                                <?php
                                }
                            ?>
                            <li>
                                <form action="/search/" method="GET">
                                    <input type="text" class="header-search" name="q" placeholder="Search..." >
                                </form>
                            </li>
                        </ul>
                        <ul class='right-menu'>
                            <li><a href="logout/"><i class='fas fa-sign-out-alt'></i>Log Out</a></li>
                        </ul>
                    </div>
                <?php
                }
            }
        ?>

        <header>
            <div class="breaking-box">
                <div class="breaking-title">
                    <p class="english">BREAKING NEWS</p>
                </div>
                <div class="breaking-slider">

                <!-- Fetch Top News Data -->

                <?php

                if($topNews=mysqli_query($connection, "SELECT link_url, news_image, news_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."meta ON news_id=meta_relation_id WHERE link_status=200 AND link_is_featured=1 AND link_type='news' AND link_site_id=".SITE_ID." AND news_status=1 ORDER BY link_modified DESC LIMIT 0, 20")) {
                    if(mysqli_num_rows($topNews)!=0) {
                        while($topNewsData=mysqli_fetch_assoc($topNews)) {

                            $topNewsPost[]=$topNewsData;

                        }
                    }
                }

                echo "<marquee>";

                for($i=0; $i<sizeof($topNewsPost); $i++) {

                    echo $topNewsPost[$i]['news_title'].'&#9726;';

                }

                echo "</marquee>";

                ?>    
                </div>
            </div>
            <div class="header">
                <div class="logo">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>logo-main.png" alt=""></a>
                </div>
                <div class="menu">
                    <nav>
                        <ul>
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
                            <li><a href="http://epaper.punjabkesari.com" target="_blank" class="english">Old E-PAPER</a></li>
                            <li><a href="http://mpaper.punjabkesari.com" target="_blank" class="english">New E-PAPER</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <main>

            <section class="header-ad-box">

                <div class="covid-19-box">
                    <h1>भारत में कोरोना के आँकड़े <strong class="english" >#GharBaithoNaIndia</strong><span class="english">Source : <strong id="lastUpdate" ><a href="https://www.mohfw.gov.in/" target="_blank" title="Ministry of Health and Family Welfare" style="text-decoration: none; letter-spacing: 1px; color: #fff" >Ministry of Health and Family Welfare</a></strong></span></h1>
                    <div class="status-box">
                        <p class="bg-white" >कोरोना की पुष्टि<span id="confirmedCases" class="english" ></span><span id="dailyCases" class="english dailyNumber" ></span></p>
                    </div>
                    <div class="status-box">
                        <p class="bg-warning" >इलाज चल रहा है<span id="activeCases" class="english" ></span></p>
                    </div>
                    <div class="status-box">
                        <p class="bg-green" >ठीक हो चुके<span id="recoveredCases" class="english" ></span><span id="dailyRecovery" class="english dailyNumber" style="color: #000" ></span></p>
                    </div>
                    <div class="status-box">
                        <p class="bg-death" >मृत लोग<span id="deathCases" class="english" ></span><span id="dailyDeath" class="english dailyNumber" style="color: #fff" ></span></p>
                    </div>
                </div>
            
                <!-- <div id='div-gpt-ad-1549609719157-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549609719157-0'); });
                </script>
                </div> -->

                <!-- /21704298696/REC_728x90 -->
                <div id='div-gpt-ad-1582615224176-0' style='width: 728px; height: 90px;'>
                <script>
                    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1582615224176-0'); });
                </script>
                </div>

            </section>