<?php
require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

$statsForPatients = '1,500';
$statsForYears = 15;
$statsForTreatments = 20;
$statsForHappyClients = 500;

?>
<!doctype html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php require_once DEFAULT_META ?>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" />
</head>

<body>
    <!-- 1. PREMIUM STICKY HEADER -->
    <header class="main-header" id="mainHeader">
        <div class="container header-container">
            <!-- Logo -->
            <a href="#hero" class="logo-link" id="logoLink">
                <img
                    src="<?php echo SITE_LOGO ?>"
                    alt="<?php echo SITE_NAME ?>" />
            </a>

            <!-- Desktop/Mobile Navigation Menu -->
            <nav class="header-nav" aria-label="Main Navigation">
                <ul class="nav-menu" id="navMenu">
                    <?php

                    generateMenu('SITE_NAV_MAIN_HEADER', 'dropdown', true, 'nav-link');

                    ?>
                </ul>
            </nav>

            <!-- Action Buttons (Desktop Minimalist Concierge Icons + CTA) -->
            <div class="header-actions">
                <?php
                generateMenu('SITE_NAV_HEADER_MENU_CTA', 'icons', true, 'ntn btn-primary btn-book');
                ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
                class="mobile-nav-toggle"
                id="mobileNavToggle"
                aria-label="Toggle navigation menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>