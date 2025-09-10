<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Forbidden Error</title>
    <link rel="stylesheet" href="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/css/style.min.css">
    <link href="https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <img src="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/images/forbidden-error.png" alt="">
    <h1>Error 403 <span>Forbidden Error</span></h1>
</body>

</html>
<?php

exit();
?>