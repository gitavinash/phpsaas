<?php

// $redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

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
    <title>Sign up | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
                <form method="POST" action="">
                    <div class="login_input">
                        <input type="text" placeholder="Enter Name" name="name" required>
                    </div>
                    <div class="login_input">
                        <input type="text" placeholder="Email" name="email" required>
                    </div>
                    <div class="login_input">
                        <input type="password" placeholder="Password" name="password" required>
                    </div>
                    <div class="sign--in_btn">
                        <input type="hidden" name="mobile" value="<?php echo time() ?>">
                        <input type="hidden" name="userRole" value="subscriber">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="action" value="subscriber">
                        <!-- <input type="hidden" name="hardRedirectURL" value="<?php echo ((isset($refUrl)) ? $refUrl : '/thankyou_signup/') ?>"> -->
                        <input type="hidden" name="hardRedirectURL" value="/thankyou_signup/<?php echo ((isset($refUrl)) ? '?refUrl=' . $refUrl : '') ?>">
                        <?php

                        if (isset($auth)) { ?>
                            <input type="hidden" name="autologin" value="true">
                        <?php
                        }

                        ?>
                        <input type="submit" class="button" value="Sign up">
                    </div>
                    <div class="or">
                        <p>or</p>
                    </div>
                    <a href='/oauth/google/?redirect_url=<?php echo ((isset($refUrl)) ? $refUrl : '/manage-subscription/') ?>' class="authBtn"><img src="<?php echo USER_IMAGES ?>google.png"
                            class="img-fluid" alt=""> login
                        with
                        google</a>
                    <br />
                    <div class="login_input">
                        <p><br />Already have an Account? <a href="/login/">Log in</a></p>
                    </div>
                </form>

                <div class="form_bottom">
                    <p>By signing up for The Head And Tale, you agree to the <a href="/terms-and-conditions/"
                            target="_blank"> Terms &
                            Conditions</a>
                        and <a href="/privacy-policy/" target="_blank"> Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>