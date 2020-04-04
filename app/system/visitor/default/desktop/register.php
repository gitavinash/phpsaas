<?php

  if(isset($_COOKIE['isLogin'])) {
    header("Location: /dashboard/");
    exit();
  }

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
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>login.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>all.min.css" >
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>
           <div class="form-box">
               <form id="register" method='POST' >
                     <h1>Register<span>Get your free account now</span></h1>
                     <div class="form-group">
                       <input type="text" name="first_name" placeholder="First name" class="form-control" autofocus required />
                     </div>
                     <div class="form-group">
                       <input type="text" name="last_name" placeholder="Last name" class="form-control" required />
                     </div>
                     <div class="form-group">
                       <input type="text" name="mobile" placeholder="Mobile no" class="form-control" required />
                     </div>
                     <div class="form-group">
                       <input type="email" class="form-control" placeholder="Email address" name="email" required />
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" placeholder="Password" name="password" required />
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required />
                     </div>
                     <div class="form-group" >
                      <input type="hidden" name="type" value="authentication" >
                      <input type="hidden" name="action" value="register" >
                      <input type="submit" id="registerBtn" class="btn btn-block btn-success" value="Register Now">
                     </div>
                     <!-- <div class="form-group">
                       <p class="center-link">Already have an Account? <a href="login/" class="center-link">Log In</a></p>
                     </div> -->
               </form>
               <p class="footer">By joining ITS Soft World, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
           </div>
       </section>

       <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
       <script src="<?php echo SYSTEM_PLUGINS ?>sweetalert/sweetalert2.all.min.js"></script>
       <script src="<?php echo SYSTEM_JS ?>app.js"></script>

    </body>

</html>