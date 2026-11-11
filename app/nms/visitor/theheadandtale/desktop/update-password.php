<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 5) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$redirect_url = function_exists('iswNormalizeRedirectPath')
    ? iswNormalizeRedirectPath($redirect_url ?? '/manage-subscription/', '/manage-subscription/')
    : '/manage-subscription/';

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
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body>
    <div class="login_form">
        <div class="login__header">
            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
        </div>
        <div class="login_content">
            <div class="card-body">
                <div class="login_input">
                    <p>Update Your Password</p>
                </div>
                <form id="updatePasswordForm" autocomplete="off">
                    <div class="login_input">
                        <input type="password" class="form-control input-with-label" name="pass" id="password"
                            placeholder="New Password">
                    </div>
                    <div class="login_input">
                        <input type="password" class="form-control input-with-label" name="cnf_pass"
                            id="confirmPassword" placeholder="Confirm New Password">
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="updatePassword">
                    <input type="hidden" name="transaction" value="<?php echo htmlspecialchars((string) ($_GET['transaction'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars((string) ($_GET['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" id="updatePassword" class="button theme-button">Update Password</button>
                    <div class="login_input" style="margin-top: 20px;">
                        <p>Remembered everything? <a href="/login/">Back to Log in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="<?php echo USER_JS ?>script.min.js"></script>
    <?php

    if (isset($alertMsg)) {
        echo '<script>alert(' . json_encode((string) $alertMsg) . ');</script>';
    }

    ?>
</body>

</html>