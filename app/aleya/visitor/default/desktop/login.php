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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="<?php echo USER_IMAGES ?>aleya_logo.png">
</head>

<body>
    <section class="login_wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 px-0">
                    <div class="login_wrapper__left">
                        <div class="logo">
                            <a href="/"><img src="<?php echo USER_IMAGES ?>aleya_logo.png" class="img-fluid" alt=""></a>
                        </div>
                        <div class="login_title">
                            <h3>welcome <br> back!</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 px-0">
                    <div class="login_wrapper__right">
                        <h6>Login</h6>
                        <!-- <div class="login_img">
                            <img src="<?php echo USER_IMAGES ?>login_img.png" class="img-fluid" alt="">
                        </div> -->
                        <form id="login">
                            <div class="login_input">
                                <label for="">Enter your email</label>
                                <input type="text" id="email" name="email" placeholder="Enter your email" autofocus
                                    required>
                            </div>
                            <div class="login_input">
                                <label for="">Enter your password</label>
                                <input type="password" id="pass" name="pass" placeholder="Enter your password" required>
                                <p>Forgot your password? <a href="/forgot-password/">click here</a></p>
                            </div>
                            <div class="continue_btn">
                                <input type="hidden" name="type" value="auth">
                                <input type="hidden" name="action" value="login">
                                <input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>">
                                <input type="submit" id="loginBtn" class="global_btn" value="Login">
                            </div>
                        </form>
                        <div class="register">
                            <p>Don't have an account yet? <a href="/register/">Register</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo ADMIN_JS_LIBRARY ?>app.min.js"></script>
</body>

</html>