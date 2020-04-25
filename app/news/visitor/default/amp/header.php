<!doctype html>
<html amp lang="<?php echo SITE_LANG ?>">
  <head>
    <meta charset="utf-8">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>">
    <link rel="preload" as="script" href="https://cdn.ampproject.org/v0.js">
  
    <?php require_once DEFAULT_META; ?>

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