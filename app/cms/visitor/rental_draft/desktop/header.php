<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php require_once DEFAULT_META ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="logo dir-left">
                        <a href="/"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></a>
                    </div>
                </div>
                <div class="col">
                    <div class="dir-right">
                        <div class="btn btn-success callUsBtn">
                            <a href="tel:+91-9013622555">
                                <?php echo ((SITE_VERSION == 'mobile') ? 'Call Us' : "<i class='fi fi-rr-phone-call'></i>+91-9013622555") ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>