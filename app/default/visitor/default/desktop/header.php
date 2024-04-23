<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo ucwords(str_replace('/', '', str_replace('-', ' ', BASE_URL))).' | '.SITE_NAME ?></title>
    <link rel="stylesheet" href="/config.min.css">
    <link rel="stylesheet" href="<?php echo DEFAULT_USER_STYLESHEET ?>checkout.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <?php require_once DEFAULT_META ?>
</head>

<body>

    <header class="">
        <div class="container">
            <div class="row">
                <div class="col-5 m-col">
                    <div class="logo">
                        <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
                    </div>
                </div>
                <div class="col-5 m-col-3"></div>
            </div>
        </div>
    </header>
    <main>