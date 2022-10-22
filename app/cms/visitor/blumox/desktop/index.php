<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Development</title>
    <link rel="stylesheet" href="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/css/style.min.css">
    <link href="https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    .whatsapp {
        position: fixed;
        bottom: 10%;
        right: 5%;
        height: 80px;
        width: 80px;
        z-index: 9999;
    }

    .whatsapp img {
        height: 80px;
    }
    </style>
</head>

<body>
    <img src="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/images/blumox-logo.png"
        style="height: 100px; width: auto;" alt="">
    <img src="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/images/under-development.png" alt="">
    <h1>Under Development <span>Sorry for the dust! We know it’s taking a while but sit tight and we’ll be with you
            soon.</span></h1>
    <div class="whatsapp">
        <a
            href="https://api.whatsapp.com/send?phone=919990026008&text=Hi%2C%20I%20want%20to%20discuss%20my%20new%20project."><img
                src="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/images/whatsapp-business-icon.png"
                alt=""></a>
    </div>
</body>

</html>
<?php

exit();
?>