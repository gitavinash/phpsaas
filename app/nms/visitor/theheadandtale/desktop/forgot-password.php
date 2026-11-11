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
    <title>Login | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body>
    <div class="login_form">
        <div class="login__header">
            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
        </div>
        <div class="login_content">
            <div class="form_box">
                <form id="forgot" method="POST">
                    <div class="login_input">
                        <input type="text" placeholder="Email" id="email" name="email" required>
                    </div>
                    <div class="sign--in_btn">
                        <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                        <input type="hidden" name="type" value="auth">
                        <input type="hidden" name="action" value="forgotPassword">
                        <input type="submit" id="forgotPassword" class="button" value="Recover password">
                    </div>
                </form>
                <div class="form_bottom">
                    <p>By signing for The Head And Tale, you agree to the <a href="/terms-and-conditions/"
                            target="_blank"> Terms &
                            Conditions</a>
                        and <a href="/privacy-policy/" target="_blank"> Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>