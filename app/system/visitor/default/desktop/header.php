<?php

  if(isset($_COOKIE['isLogin'])) {
    
    header("Location: /dashboard/");
    exit();

  } else {

    if(isset($_COOKIE['site_app'])) {

      /** Redirect to website setup url to those users who has not complete his setup */

      require_once MODULES.'isLogin.php';

    }

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
        <title>Register | <?php echo SITE_NAME ?></title>
        <link rel="canonical" href="http://www.shaktipedia.com/durga-chalisa/" />
        <meta name="theme-color" content="#FE6F06">
        <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo APP_CSS_LIBRARY ?>login.min.css">
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>