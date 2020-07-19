<!DOCTYPE html>
<html lang="<?php echo SITE_LANG ?>" class="no-js">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
      <link rel="shortcut icon" href="<?php echo SITE_ICON ?>" />
      <!-- <title>Tarot | Online Tarot Courses</title> -->
      
      <link rel='dns-prefetch' href='//stackpath.bootstrapcdn.com' />
      <link rel='dns-prefetch' href='//fonts.googleapis.com' />

      <?php require_once DEFAULT_META; ?>
  
     
      <style type="text/css">img.wp-smiley,
         img.emoji {
         display: inline !important;
         border: none !important;
         box-shadow: none !important;
         height: 1em !important;
         width: 1em !important;
         margin: 0 .07em !important;
         vertical-align: -0.1em !important;
         background: none !important;
         padding: 0 !important;
         }
      </style>
      <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css?ver=5.4.2' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>BiddyTarotIcons.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>early.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>style.min.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>wpcdt-public-style.css?ver=1.3' type='text/css' media='all' />
      <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat:300,600,700,800' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>bt_16-styles.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>grid-system.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>style.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>header-secondary-nav.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>element-wpb-column-border.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>jquery.fancybox.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>responsive.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>flickity.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>select2.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>bt_18_style.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>skin-material.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>js_composer.min.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>portfolio.css' type='text/css' media='all' />
      <link rel='stylesheet' href='<?php echo USER_STYLESHEET ?>styleguide.css' type='text/css' media='all' />
      <link rel='stylesheet'  href='<?php echo USER_STYLESHEET ?>salient-dynamic-styles.css' type='text/css' media='all' />
      
      <script type='text/javascript' src='<?php echo USER_JS ?>jquery.js'></script>
      <script type='text/javascript' src='<?php echo USER_JS ?>jquery-migrate.min.js'></script>
      <script type='text/javascript' src='<?php echo USER_JS ?>itro-scripts.js'></script> 

      <style type="text/css">
         .isw-row {
            float:left;
            width: 100%;
            margin: 0 auto;
            padding: 0;
         }
         .isw-col {
            float: left;
            width: 50%;
            margin: 0 auto;
            padding: 0;
         }
         .isw-col-full-size {
            float: left;
            width: 100%;
            margin: 0 auto;
            padding: 0;
         }
      </style>
     
      <style type="text/css" data-type="vc_custom-css">.nectar-flickity.nectar-carousel.nectar-carousel:not(.masonry) .flickity-viewport {
         margin-top: 25px !important;
         }
         .nectar-flickity:not(.masonry) .flickity-slider .cell img {
         width: auto;
         margin: 0 auto;
         }
         .home-header.meditation.full-width-content.vc_row-fluid .wpb_column .column-image-bg-wrap .column-image-bg,
         .home-header.intuitivetarot.full-width-content.vc_row-fluid .wpb_column .column-image-bg-wrap .column-image-bg {
         background-position: top center;
         }
         .centercol {
         display: table !important;
         }
         .home-header.topcenter .column-image-bg {
         background-position: top center !important;
         }
         .home-header.meditation img.brigitmeditationimage,
         .home-header.intuitivetarot img.brigitmeditationimage {
         max-height: 700px;
         width: 100%;
         object-fit: cover;
         object-position: top center;
         }
         .mobilespacing {display: none;}
         .btcpebtimer .wpcdt-clock {width: 100%;}
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-col {
         background-color: #b79b62;
         border-radius: 50%;
         padding: 0;
         padding-right: 0 !important;
         padding-top: 27px;
         margin: 5px 5px;
         font-size: 24px;
         width: 85px;
         height: 85px;
         }
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-days-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-hours-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-minutes-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-seconds-label {
         font-size: 22px;
         font-weight: 400 !important;
         margin-left: -3px;
         }
         @media screen and (min-width: 1000px) {
         .bthoverbox {
         width: 31%;
         margin: 0 1% !important;
         }
         }
         @media screen and (max-width: 700px) {
         .btcpebtimer .wpcdt-countdown-wrp {
         padding: 15px 0;
         }
         .btcpebtimer .wpcdt-clock {width: 100%;}
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-col {
         width: 70px;
         height: 70px;
         float: none;
         padding: 20px 12px;
         padding-right: 12px !important;
         margin: 5px 4px;
         font-size: 20px;
         }
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-days-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-hours-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-minutes-label,
         .btcpebtimer .wpcdt-countdown-timer-design-6 .ce-seconds-label {
         font-size: 16px;
         }
         .home-et-header {
         padding-top: 100px !important;
         padding-bottom: 100px !important;
         }
         .home-et-header .row-bg {
         background-image: url('/wp-content/uploads/2018/09/ET_BG_NowAvailable_Header_Mobile.jpg') !important;
         background-position: top center !important;
         }
         .home-header.meditation .row-bg {
         background-image: url('/wp-content/uploads/2018/04/header-bg-brigit-feb-18-4.png') !important;
         background-position: top center !important;
         }
         /*.home-header .col.span_12 {
         padding-top: 100px;
         }*/
         .home-header.gytboquiz .row-bg {
         background-image: url('/wp-content/uploads/2019/07/GYTBO-Home-Mobile.jpg') !important;
         }
         }
         @media screen and (max-width: 999px) {
         .mobilespacing {display: table; height: 250px;}
         .home-header.meditation .row-bg {
         background-image: none !important;
         }
         .home-header.meditation .col.span_12,
         .home-header.intuitivetarot .col-span_12 {
         padding-top: 0;
         }
         body .home-header.meditation.wpb_row .span_12>.col.meditationtext[data-padding-pos=left-right],
         body .home-header.intuitivetarot.wpb_row .span_12>.col.meditationtext[data-padding-pos=left-right] {
         padding: 50px 2% !important;
         }
         .home-header.meditation img.brigitmeditationimage,
         .home-header.intuitivetarot img.brigitmeditationimage {
         max-height: 400px;   
         }
         .btcpchallengeheader .column-bg-overlay {
         background-image: url(/wp-content/uploads/2020/02/BTCP-Challenge-HomeBG-Mobile.jpg) !important;
         background-position: top right;
         background-size: cover;
         }
         }
      </style>
      <noscript>
         <style>.wpb_animate_when_almost_visible { opacity: 1; }</style>
      </noscript>
   </head>
   <body class="home page-template page-template-template-homepage page-template-template-homepage-php page page-id-2273 material wpb-js-composer js-comp-ver-6.2.0 vc_responsive" data-footer-reveal="false" data-footer-reveal-shadow="none" data-header-format="default" data-body-border="off" data-boxed-style="" data-header-breakpoint="1320" data-dropdown-style="minimal" data-cae="linear" data-cad="650" data-megamenu-width="contained" data-aie="none" data-ls="fancybox" data-apte="standard" data-hhun="0" data-fancy-form-rcs="1" data-form-style="default" data-form-submit="regular" data-is="minimal" data-button-style="default" data-user-account-button="false" data-flex-cols="true" data-col-gap="default" data-header-inherit-rc="false" data-header-search="true" data-animated-anchors="true" data-ajax-transitions="false" data-full-width-header="true" data-slide-out-widget-area="true" data-slide-out-widget-area-style="slide-out-from-right" data-user-set-ocm="off" data-loading-animation="spin" data-bg-header="false" data-responsive="1" data-ext-responsive="true" data-header-resize="0" data-header-color="custom" data-transparent-header="false" data-cart="false" data-remove-m-parallax="" data-remove-m-video-bgs="" data-m-animate="0" data-force-header-trans-color="light" data-smooth-scrolling="0" data-permanent-transparent="false">
      <script type="text/javascript">if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) { document.body.className += " using-mobile-browser "; }</script>
      <div class="ocm-effect-wrap">
         <div class="ocm-effect-wrap-inner">
            <div id="header-space" data-secondary-header-display="full" data-header-mobile-fixed='1'></div>
            <div id="header-outer" data-has-menu="true" data-has-buttons="yes" data-header-button_style="default" data-using-pr-menu="false" data-mobile-fixed="1" data-ptnm="false" data-lhe="default" data-user-set-bg="#ffffff" data-format="default" data-permanent-transparent="false" data-megamenu-rt="0" data-remove-fixed="0" data-header-resize="0" data-cart="false" data-transparency-option="0" data-box-shadow="small" data-shrink-num="40" data-using-secondary="1" data-using-logo="1" data-logo-height="60" data-m-logo-height="60" data-padding="20" data-full-width="true" data-condense="false">
               <div id="header-secondary-outer" class="default" data-mobile="display_full" data-remove-fixed="0" data-lhe="default" data-secondary-text="true" data-full-width="true" data-mobile-fixed="1" data-permanent-transparent="false" style="background-color: #000000;">
                  <div id="bt-optin-bar">
                     <div class="container">
                        <div class="row">
                           <div class="optin-bar-col optintext" style="width: 66%;">
                              <p class=" bold padbot0 white">We stand for Social Justice, Diversity and Equality</p>
                           </div>
                           <div class="optin-bar-col"> <a class="center-col button green-bg" href="" target="_blank">Learn More</a></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="search-outer" class="nectar">
                  <!-- <div id="search">
                     <div class="container">
                        <div id="search-box">
                           <div class="inner-wrap">
                              <div class="col span_12">
                                 <form role="search" action="/search/" method="GET"> <input type="text" name="s" id="s" value="" placeholder="Search" /> <span>Hit enter to search or ESC to close</span></form>
                              </div>
                           </div>
                        </div>
                        <div id="close"><a href="#"> <span class="close-wrap"> <span class="close-line close-line1"></span> <span class="close-line close-line2"></span> </span> </a></div>
                     </div>
                  </div> -->
               </div>
               <header id="top">
                  <div class="container">
                     <div class="row">
                        <div class="col span_3"> <a id="logo" href="/welcome/" data-supplied-ml-starting-dark="false" data-supplied-ml-starting="false" data-supplied-ml="false"> <img class="stnd default-logo dark-version" alt="Biddy Tarot" src="<?php echo SITE_LOGO ?>" style="height: 60px !important; width: auto" /> </a></div>
                        <div class="col span_9 col_last">
                           <a class="mobile-search" href="#searchbox"><span class="nectar-icon icon-salient-search" aria-hidden="true"></span></a>
                           <div class="slide-out-widget-area-toggle mobile-icon slide-out-from-right" data-custom-color="false" data-icon-animation="simple-transform">
                              <div> <a href="#sidewidgetarea" aria-label="Navigation Menu" aria-expanded="false" class="closed"> <span aria-hidden="true"> <i class="lines-button x2"> <i class="lines"></i> </i> </span> </a></div>
                           </div>
                           <nav>
                              <ul class="sf-menu">
                                 <li class="menu-item"><a href="/welcome/" title="<?php echo SITE_NAME ?>" >Home</a></li>
                                 <li class="menu-item"><a href="/about-us/" title="About Us" >About Us</a></li>
                                 <li class="menu-item"><a href="/our-services/" title="Our Services" >Our Services</a></li>
                                 <li class="menu-item"><a href="/blog/" title="<?php echo SITE_NAME.' Blog' ?>" >Blog</a></li>
                                 <li class="menu-item"><a href="/contact-us/" title="Contact Us" >Contact Us</a></li>
                                 <li class="menu-item"><a href="/login/" title="Login" >Login</a></li>
                              </ul>
                           </nav>
                        </div>
                     </div>
                  </div>
               </header>
            </div>