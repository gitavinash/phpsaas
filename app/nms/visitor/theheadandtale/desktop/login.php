<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

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
    <title>Login | <?php echo SITE_NAME ?></title>
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
        <div class="login_content">
            <!-- <button>sign in using email</button> -->
            <div class="form_box login">
                <form method="POST" id="login">
                    <div class="login_input">
                        <input type="text" placeholder="Email" name="email" required>
                    </div>
                    <div class="login_input">
                        <input type="password" placeholder="Password" name="pass" required>
                    </div>
                    <div class="login_input">
                        <p>Forgot your password? <a href="/forgot-password/">Click Here</a></p>
                    </div>
                    <div class="sign--in_btn">
                        <input type="hidden" name="type" value="auth">
                        <input type="hidden" name="action" value="login">
                        <input type="hidden" name="redirect_url" value="<?php echo ((isset($redirect_url)) ? $redirect_url : '/') ?>">
                        <input type="hidden" name="hardRedirect" value="true">
                        <input type="hidden" name="hardRedirectURL" value="/dashboard/">
                        <?php

                        if (isset($baseUrl)) {
                            echo "<input type='hidden' name='baseUrl' value='{$baseUrl}'>";
                            echo "<input type='hidden' name='method' value='{$method}'>";
                            echo "<input type='hidden' name='id' value='{$id}'>";
                        }

                        ?>
                        <input type="submit" class="button" value="Sign in">
                    </div>
                </form>
                <div class="or">
                    <p>or</p>
                </div>
                <a href='/oauth/google/?redirect_url=<?php echo ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/') . ((isset($method)) ? '&method=' . $method : '') . ((isset($id)) ? '&id=' . $id : '') . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl  : '') ?>' class="authBtn"><img
                        src="<?php echo USER_IMAGES ?>google.png" class="img-fluid" alt=""> login
                    with
                    google</a>
                <br />
                <p>Don't have an account? <a href="/signup/<?php echo ((isset($redirect_url)) ? '?redirect_url=' . $redirect_url . '&auth=true' : '/manage-subscription/') . ((isset($method)) ? '&method=' . $method : '') . ((isset($id)) ? '&id=' . $id : '') . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl . '&auth=true' : '') ?>">Sign up</a></p>
                <div class="form_bottom">
                    <p>By signing for The Head And Tale, you agree to the <a href="/terms-and-conditions/"
                            target="_blank"> Terms &
                            Conditions</a>
                        and <a href="/privacy-policy/" target="_blank"> Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="<?php echo USER_JS ?>script.min.js"></script>
    <?php

    if (isset($alertMsg)) {
        echo "<script>alert('{$alertMsg}')</script>";
    }

    ?>
</body>

</html>