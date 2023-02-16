<!DOCTYPE html>
<html>

<head>

    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8">
    <link rel="shortcut icon" href=" ">
    <meta name="" content="">
    <meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
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
    <link
        href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Righteous&display=swap"
        rel="stylesheet">

</head>

<body>

    <!-- header start -->
    <header>
        <!-- <div class="logo">
            <a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
        </div> -->
    </header>
    <!-- header end -->