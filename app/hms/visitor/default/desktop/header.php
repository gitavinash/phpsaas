<!DOCTYPE html>
<html lang="hi_IN">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">

        <!-- Prefetch DNS -->

        <link rel="dns-prefetch" href="https://fonts.googleapis.com">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="<?php echo SITE_ICON ?>">
        <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
        <link rel="canonical" href="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
        
        <!-- Styleshhet -->

        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" >
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick-theme.min.css" >
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700&display=swap" rel="stylesheet">
        
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

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@PunjabKesariCom">
        <meta name="twitter:title" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_title)) ? (($tw_title!='') ? $tw_title : SITE_NAME ) : SITE_NAME ))))) ?>">
        <meta name="twitter:description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($tw_desc)) ? (($tw_desc!='') ? $tw_desc : SITE_DESC ) : SITE_DESC ))))) ?>">
        <meta name="twitter:creator" content="@PunjabKesariCom">
        <meta name="twitter:image" content="<?php echo ((isset($news_image)) ? SITE_URL.(($news_image!='') ? $news_image : '' ) : USER_IMAGES.'logo-main.png' ) ?>">
        <meta name="twitter:url" content="<?php echo SITE_URL.BASE_URL ?>">

        <?php

            $schemaData=SCHEMA_DATA.((isset($link_type)) ? $link_type : 'homepage' ).'.php';

            if(file_exists($schemaData)) {

                require_once $schemaData;

            }
        
        ?>

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
            <section class="padding-tb-10 box-shadow-d8d8d8" >
                <div class="inner-box">
                    <div class="logo-box">
                        <div class="logo">
                            <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?> logo">
                        </div>
                    </div>
                    <div class="side-menu">
                        <ul>
                            <li><a href="/login/"><img src="<?php echo USER_IMAGES ?>icon/user.png" alt="<?php echo SITE_NAME ?>"> Login / Signup</a></li>
                        </ul>
                    </div>
                </div>
            </section>
            <section class="bg-404397" >
                <div class="inner-box">
                    <div class="menu">
                        <ul>
                            <li><a href="#">Delhi</a></li>
                            <li><a href="#">Shimla</a></li>
                            <li><a href="#">Manali</a></li>
                        </ul>
                    </div>
                </div>
            </section>