<?php

	/** Check month range setting is already defined or not */

	if(!defined('CALENDER_MONTH_VISIBLE')) {

		define('CALENDER_MONTH_VISIBLE', 2);

 	}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&family=Playfair+Display&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <title>Booking Slot</title>
</head>

<body class="bg-body">
    <header>
        <div class="header-top">
            <ul class="menu desktop">
                <li><a href="https://srisanwalimurat.com/publication/">Publication</a></li>
                <li><a href="https://srisanwalimurat.com/gallery/">Gallery</a></li>
                <li><a href="https://srisanwalimurat.com/events/">Events</a></li>
                <li class="logo">
                    <h1><a href="https://srisanwalimurat.com/"><?php echo SITE_NAME ?></a></h1>
                </li>
                <li><a href="https://srisanwalimurat.com/discourses/">Discourses</a></li>
                <li><a href="https://srisanwalimurat.com/bhajan/">Bhajan/Aarti</a></li>
                <li class="active"><a href="https://pangat.srisanwalimurat.com/" style="color: #fff">Request For
                        Pangat</a></li>
                <li><a href="#" class="openSearchForm"><i class="fas fa-search"></i> Search</a></li>
            </ul>
            <div class="mobile-menu">
                <h1><a href="http://srisanwalimurat.com/"><?php echo SITE_NAME ?></a></h1>
                <div class="menu-box " id="menu-box"><img src="<?php echo IMG_LIBRARY ?>menu-icon.png" alt=""
                        class="show"></div>
                <div id="target">
                    <ul>
                        <span class="hide"><img src="<?php echo USER_IMAGES ?>cancel.png"></span>
                        <li><a href="https://srisanwalimurat.com/publication/">Publication</a></li>
                        <li><a href="https://srisanwalimurat.com/gallery/">Gallery</a></li>
                        <li><a href="https://srisanwalimurat.com/events/">Events</a></li>
                        <li><a href="https://srisanwalimurat.com/discourses/">Discourses</a></li>
                        <li><a href="https://srisanwalimurat.com/bhajan/">Bhajan/Aarti</a></li>
                        <li><a href="https://pangat.srisanwalimurat.com/" style="color: #fff">Request For Pangat</a>
                        <li><a href="#" class="openSearchForm">Search</a></li>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="openSearchFormBox" style="display:none">
            <span class="closeSearchFormBtn"><img src="<?php echo USER_IMAGES ?>cancel.png"></span>
            <form action="https://srisanwalimurat.com/search/">
                <input type="text" class="search-bar" name="q" placeholder="Press enter after typing...">
            </form>
        </div>

    </header>