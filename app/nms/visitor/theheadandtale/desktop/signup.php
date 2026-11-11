<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 5) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$redirect_url = function_exists('iswNormalizeRedirectPath')
    ? iswNormalizeRedirectPath($redirect_url ?? '/dashboard/', '/dashboard/')
    : '/dashboard/';

if (isset($responseMsg)) {
    echo '<script>alert(' . json_encode((string) $responseMsg) . ');</script>';
}

if (
    isset($connection)
    && $connection instanceof mysqli
    && defined('SITE_ID')
    && function_exists('iswValidateCurrentSiteSession')
) {
    $sessionCheck = iswValidateCurrentSiteSession($connection, (int) SITE_ID);
    if (!empty($sessionCheck['ok'])) {
        header("Location: {$redirect_url}");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Sign up | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="robots" content="noindex,nofollow">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
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
                        <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                        <input type="hidden" name="mobile" value="<?php echo time() ?>">
                        <input type="hidden" name="userRole" value="subscriber">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="action" value="subscriber">
                        <!-- <input type="hidden" name="hardRedirectURL" value="<?php echo ((isset($redirect_url)) ? $redirect_url : '/thankyou_signup/') ?>"> -->
                        <input type="hidden" name="hardRedirectURL"
                            value="/thankyou_signup/<?php echo ((isset($redirect_url)) ? '?refUrl=' . $redirect_url : '') ?>">
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
                    <a href='/oauth/google/<?php echo ((isset($redirect_url)) ? '?redirect_url=' . $redirect_url : '/manage-subscription/') . ((isset($method)) ? '&method=' . $method : '') . ((isset($id)) ? '&id=' . $id : '') . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl : '') ?>'
                        class="authBtn"><img src="<?php echo USER_IMAGES ?>google.png" class="img-fluid" alt="">Continue
                        with Google</a>
                    <br />
                    <div class="login_input">
                        <p><br />Already have an Account? <a
                                href="/login/<?php echo ((isset($redirect_url)) ? '?redirect_url=' . $redirect_url : '') . ((isset($method)) ? '&method=' . $method : '') . ((isset($id)) ? '&id=' . $id : '') . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl : '') ?>">Log
                                in</a></p>
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