<html>
    <head>
        <base href="<?php echo SITE_URL ?>">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo SITE_NAME ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
        <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.css">
        <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick-theme.css">
    </head>
    <body>
        <header>
            <ul class="menu desktop">
                <li><a href="publication/" <?php echo ((ACTIVE_URL=='/publication/') ? 'class="active"' : '' ) ?> >Publication</a></li>
                <li><a href="gallery/" <?php echo ((ACTIVE_URL=='/gallery/') ? 'class="active"' : '' ) ?> >Gallery</a></li>
                <li><a href="events/" <?php echo ((ACTIVE_URL=='/events/') ? 'class="active"' : '' ) ?>>Events</a></li>
                <li class="logo"><h1><a href="/">Sri Sanwali Murat</a></h1></li>
                <li><a href="discourses/" <?php echo ((ACTIVE_URL=='/discourses/') ? 'class="active"' : '' ) ?>>Discourses</a></li>
                <li><a href="bhajan/" <?php echo ((ACTIVE_URL=='/bhajan/') ? 'class="active"' : '' ) ?>>Bhajan/Aarti</a></li>
                <li><a href="//pangat.<?php echo SITE_DOMAIN ?>">Request For Pangat</a></li>
                <!-- <li><a href="">Account</a></li> -->
            </ul>
            <div class="mobile-menu">
                <h1><a href="">Sri Sanwali Murat</a></h1>
                <div class="menu-box " id="menu-box" ><img src="<?php echo IMG_LIBRARY ?>menu-icon.png" alt="" class="show"></div>
                <div id="target">
                    <ul>
                        <span class="hide"><img src="<?php echo USER_IMAGES ?>cancel.png"></span>
                        <li><a href="publication/" style="color: #fff" >Publication</a></li>
                        <li><a href="gallery/">Gallery</a></li>
                        <li><a href="events/">Events</a></li>
                        <li><a href="discourses/">Discourses</a></li>
                        <li><a href="bhajan/">Bhajan/Aarti</a></li>
                        <li><a href="//pangat.<?php echo SITE_DOMAIN ?>">Request For Pangat</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <main>