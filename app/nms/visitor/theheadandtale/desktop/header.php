<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <header>
        <div class="container">
            <div class="row">
                <div class="logo">
                    <a href="/"><img src="<?php echo SITE_LOGO ?>" alt=""></a>
                </div>
                <div class="timestamp">
                    <p>
                        <?php echo date('l, d F Y') ?>
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main>