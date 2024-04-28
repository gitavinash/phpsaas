<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

if (isset($responseMsg)) {
  echo "<script>alert('{$responseMsg}')</script>";
}

if (isset($_COOKIE['user_data'])) {

  header("Location: {$redirect_url}");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update password | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
</head>

<body>
    <div class="login_form">
        <div class="login__header">
            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
        </div>
        <div class="login_content sign_up__content">
            <!-- <button><img src="<?php echo USER_IMAGES ?>google.png" class="img-fluid" alt=""> continue with
                google</button>
            <div class="or">
                <p>or</p>
            </div> -->
            <!-- <button>sign up using email</button> -->
            <div class="form_box">
                <form id="update">
                    <!-- <h4>Update your password.<span>Change password of
                            <strong><?php echo base64_decode($_GET['email']); ?></strong></span></h4> -->
                    <div class="login_input">
                        <p>Update your password.</p>
                    </div>
                    <div class="beforeClick">
                        <div class="login_input">
                            <input type="password" id="password" placeholder="Enter new password" name="pass" required>
                        </div>
                        <div class="login_input">
                            <input type="password" id="confirmPassword" placeholder="Confirm passowrd" name="cnf_pass"
                                required>
                        </div>
                        <div class="sign--in_btn">
                            <input type="hidden" name="type" value="auth">
                            <input type="hidden" name="action" value="updatePassword">
                            <input type="hidden" name="transaction" value="<?php echo $_GET['transaction'] ?>">
                            <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>">
                            <input type="submit" id="updatePassword" class="button" value="Change Password">
                        </div>
                    </div>
                    <div class="afterClick" style='display: none;'>
                        <p><span id="statusMsg"></span> <a href='/login/?email=<?php echo $_GET['email'] ?>'>click here
                                to login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>