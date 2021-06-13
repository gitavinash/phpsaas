<?php

  $redirect_url=((isset($redirect_url)) ? $redirect_url : '/dashboard/' );

  if(isset($_COOKIE['isLogin'])) {
    
    header("Location: {$redirect_url}");
    exit();

  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" value="index">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title>
        <?php echo ucwords(str_replace('/', '', str_replace('-', ' ', str_replace('/', ' ', BASE_URL)))).' | '.SITE_NAME ?>
    </title>
    <link rel="canonical" href="<?php echo SITE_URL.ACTIVE_URL ?>" />
    <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_LIBRARY ?>login.min.css">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
</head>

<body>

    <section>