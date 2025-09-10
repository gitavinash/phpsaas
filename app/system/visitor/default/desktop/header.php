<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo ucwords(str_replace('/', ' ', str_replace('-', ' ', BASE_URL))) . ' | ' . SITE_NAME ?></title>
    <link rel="icon" type="image/png" href="<?php echo SITE_ICON ?>">
    <style>
    :root {
        --theme-color: <?php echo SITE_THEME_COLOUR ?>;
        --theme-color-light: color-mix(in srgb, var(--theme-color) 50%, white);
        --form-max-width: 420px;
        --radius-card: 14px;
        --shadow-card: 0 10px 30px rgba(0, 0, 0, 0.18);
    }
    </style>
    <link href="<?php echo CSS_LIBRARY ?>bootstrap_5/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/app/system/visitor/default/desktop/assets/css/onboarding.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
    <?php require_once PWA . 'header.php' ?>
</head>

<body>
    <div class="container">
        <div class="centered-box">
            <div class="row">