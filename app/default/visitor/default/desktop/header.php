<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo ucwords(str_replace('/', '', str_replace('-', ' ', BASE_URL))) . ' | ' . SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/config.min.css">
    <link rel="stylesheet" href="<?php echo DEFAULT_USER_STYLESHEET ?>checkout.min.css">
    <?php
    $primaryColor = (defined('SITE_THEME_COLOUR') && !empty(SITE_THEME_COLOUR)) ? SITE_THEME_COLOUR : '#0a0a0a';
    $secondaryColor = (defined('SITE_BACKGROUND_COLOUR') && !empty(SITE_BACKGROUND_COLOUR)) ? SITE_BACKGROUND_COLOUR : '#fdfbf7';
    ?>
    <style>
        :root {
            --primary-color: <?php echo $primaryColor; ?>;
            --accent-color: <?php echo $primaryColor; ?>;
            --accent-hover: <?php echo $primaryColor; ?>;
            --theme-color: <?php echo $primaryColor; ?>;
            --theme-hover: <?php echo $primaryColor; ?>;
            --bg-color: <?php echo $secondaryColor; ?>;
            --bg-main: <?php echo $secondaryColor; ?>;
            --bg-secondary: <?php echo $secondaryColor; ?>;
            --secondary-color: <?php echo $secondaryColor; ?>;
        }
    </style>
    <?php require_once DEFAULT_META ?>
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR ?>
</head>

<body>

    <header class="checkout-header">
        <div class="container">
            <div class="header-inner">
                <a href="/" class="header-logo-link">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>" class="header-logo-img">
                </a>
            </div>
        </div>
    </header>
    <main>