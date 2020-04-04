<?php

  if(isset($_COOKIE['user_data'])) {

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
        <title>Login | <?php echo SITE_NAME ?></title>
        <link rel="canonical" href="http://www.shaktipedia.com/durga-chalisa/" />
        <meta name="theme-color" content="#FE6F06">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>login.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo SYSTEM_PLUGINS ?>sweetalert/sweetalert2.min.css" >
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>
           <div class="form-box">
               <form id="login" method='POST' >
                     <h1>Login<span>Sign in your account</span></h1>
                     <div class="form-group">
                       <input type="email" class="form-control" placeholder="Email address" name="email" autofocus required >
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" placeholder="Password" name="pass" required >
                     </div>
                     <div class="form-group">
                       <a href="forgot-password/" class="link">Forgot Password?</a>
                     </div>
                     <div class="form-group">
                      <input type="hidden" name="type" value="authentication" >
                      <input type="hidden" name="action" value="login" >
                      <input type="hidden" name="url" value="<?php echo $redirectUrl ?>" >
                      <input type="submit" id="loginBtn" class="btn btn-block btn-success" value="Log In">
                     </div>
                     <!-- <div class="form-group">
                       <p class="center-link">Don't have an Account? <a href="register/" class="center-link">Join Now</a></p>
                     </div> -->
               </form>
               <p class="footer">By joining <?php echo SITE_NAME ?>, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
           </div>
       </section>

       <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
       <script src="<?php echo SYSTEM_PLUGINS ?>sweetalert/sweetalert2.all.min.js"></script>
       <script src="<?php echo SYSTEM_JS ?>app.js"></script>

    </body>

</html>