<!DOCTYPE html>
<html>

<head>
    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8">
    <link rel="shortcut icon" href=" ">
    <meta name="" content="">
    <meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo USER_IMAGES ?>favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo USER_IMAGES ?>favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo USER_IMAGES ?>favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo USER_IMAGES ?>favicon/site.webmanifest">
    <link rel="mask-icon" href="<?php echo USER_IMAGES ?>favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#3ab4f1">
    <meta name="theme-color" content="#3ab4f1">

    <title>
        <?php echo ((isset($meta_title)) ? (($meta_title != '') ? $meta_title : ${$link_type . '_title'} . ' | ' . SITE_NAME) : SITE_DESC . ' | ' . SITE_NAME) ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- css -->
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css">
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Righteous&display=swap" rel="stylesheet">

</head>

<body>

    <!-- header start -->
    <header>
        <!-- <div class="logo">
            <a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
        </div> -->
    </header>
    <!-- header end -->