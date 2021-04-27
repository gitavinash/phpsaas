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
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cuba admin is super flexible, powerful, clean &amp; modern responsive bootstrap 5 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Cuba admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="<?php echo SITE_ICON ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo SITE_ICON ?>" type="image/x-icon">
    <title>Login | <?php echo SITE_NAME ?></title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>vendors/icofont.css">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>vendors/themify.css">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>vendors/flag-icon.css">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>vendors/feather-icon.css">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>vendors/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>style.css">
    <link id="color" rel="stylesheet" href="<?php echo ADMIN_STYLESHEET ?>color-1.css" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>responsive.css">
  </head>
  <body>
    <!-- login page start-->
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-5"><img class="bg-img-cover bg-center" src="<?php echo IMG_LIBRARY.'theme/'.SITE_THEME ?>/login-banner.jpg" alt="looginpage"></div>
        <div class="col-xl-7 p-0">
          <div class="login-card">
            <div>
              <div><a class="logo text-start" href="index.html"><img class="img-fluid for-light" src="https://itssoftworld.com/images/logo.png" alt="looginpage"><img class="img-fluid for-dark" src="https://itssoftworld.com/images/logo.png" alt="looginpage"></a></div>
              <div class="login-main"> 
                <form id="login" class="theme-form">
                  <h4>Sign in to account</h4>
                  <p>Enter your email & password to login</p>
                  <div class="form-group">
                    <label class="col-form-label">Email Address</label>
                    <input class="form-control" type="email" name="email" id="email" required="" placeholder="Test@gmail.com">
                  </div>
                  <div class="form-group">
                    <label class="col-form-label">Password</label>
                    <div class="form-input position-relative">
                      <input class="form-control" type="password" id="pass" name="pass" required="" placeholder="*********">
                      <div class="show-hide"><span class="show">                         </span></div>
                    </div>
                  </div>
                  <div class="form-group mb-0">
                    <div class="checkbox p-0">
                      <input id="checkbox1" type="checkbox" checked >
                      <label class="text-muted" for="checkbox1">Remember password</label>
                    </div><a class="link" href="forget-password.html">Forgot password?</a>
                    <div class="text-end mt-3">
                      <input type="hidden" name="type" value="auth" >
                      <input type="hidden" name="action" value="login" >
                      <input type="hidden" name="url" value="<?php echo $redirectUrl ?>" >
                      <button class="btn btn-primary btn-block w-100" id="loginBtn" type="submit">Sign in</button>
                    </div>
                  </div>
                  <p class="mt-4 mb-0 text-center">Don't have account?<a class="ms-2" href="sign-up.html">Create Account</a></p>
                  <script>
                    (function() {
                    'use strict';
                    window.addEventListener('load', function() {
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.getElementsByClassName('needs-validation');
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                    }, false);
                    });
                    }, false);
                    })();
                    
                  </script>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php require_once NOTIFICATION.'index.php' ?>
      <script src="<?php echo ADMIN_JS ?>bootstrap.bundle.min.js"></script>
      <script src="<?php echo ADMIN_PLUGIN ?>feather-icon/feather.min.js"></script>
      <script src="<?php echo ADMIN_PLUGIN ?>feather-icon/feather-icon.js"></script>
      <script src="<?php echo ADMIN_JS ?>config.js"></script>
      <script src="<?php echo ADMIN_JS ?>script.js"></script>
      <script src="<?php echo ADMIN_JS ?>app.js"></script>
    </div>
  </body>
</html>