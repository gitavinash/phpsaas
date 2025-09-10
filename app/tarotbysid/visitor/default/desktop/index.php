<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Welcome to <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <style>
        body {
            float: left;
            width: 100%;
            padding: 0;
            margin: 0 auto;
        }

        img {
            float: left;
            width: 100%;
        }

        .display-btn-box {
            float: left;
            width: 100%;
            padding: 20px 0px;
            text-align: center;
        }
    </style>
</head>

<body>
    <img src="<?php echo USER_IMAGES ?>homepage.jpeg" alt="Welcome to <?php echo SITE_NAME ?>">
    <div class="display-btn-box">
        <a href="welcome/" class="btn btn-success">Continue to Website</a>
    </div>
</body>

</html>