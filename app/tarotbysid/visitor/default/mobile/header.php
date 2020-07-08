<?php

  if(isset($_COOKIE['isLogin'])) {
    
    header("Location: /dashboard/");
    exit();

  }

  $redirectUrl=((isset($_GET['url'])) ? mysqli_escape_real_string($connection, $_GET['url']) : '/dashboard/' );

?>

<!DOCTYPE html>
<html lang="en">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="index" >
        <link rel="icon" href="<?php echo SITE_ICON ?>">
        <title><?php echo ucwords(str_replace('/', '', str_replace('-', ' ', str_replace('/', ' ', BASE_URL)))).' | '.SITE_NAME ?></title>
        <link rel="canonical" href="<?php echo SITE_IRL.ACTIVE_URL ?>" />
        <meta name="theme-color" content="#26ABDF">
        <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo APP_CSS_LIBRARY ?>login.min.css">
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>