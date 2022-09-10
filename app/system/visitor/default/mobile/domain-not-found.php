<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request website is not found on server</title>
    <link rel="stylesheet" href="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/css/style.min.css">
    <link href="https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <img src="<?php echo str_replace(ROOT, '', SYSTEM_THEME) ?>assets/images/domain-not-found.jpg" alt="">
    <h1>Invalid Domain <span>Request website is not found on server</span></h1>
</body>

</html>
<?php

exit();
?>