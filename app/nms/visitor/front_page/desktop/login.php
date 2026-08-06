<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

if (isset($responseMsg)) {
    echo "<script>alert('{$responseMsg}')</script>";
}

if (isset($_COOKIE['isLogin'])) {
    header("Location: {$redirect_url}");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Sign In | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET; ?>style-visitor.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET; ?>style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="robots" content="noindex,nofollow">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body class="auth-portal-page">

    <main class="auth-portal-box">
        <div class="auth-logo-header">
            <a href="/"><img src="<?php echo SITE_LOGO; ?>" alt="<?php echo SITE_NAME; ?>" /></a>
        </div>

        <h1 class="auth-form-title">Sign In</h1>
        <p class="auth-form-subtitle">Enter your email to receive a secure one-time verification code.</p>

        <form id="login" autocomplete="off">
            <div id="enterEmail">
                <div class="auth-input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@company.com" required />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="submit" id="emailLoginContinueBtn" class="auth-btn-primary" formnovalidate>
                    Continue &rarr;
                </button>
            </div>

            <div id="verifyEmailOTP" style="display: none;">
                <div class="auth-input-group">
                    <label for="otpEmailValue">Enter 6-Digit OTP</label>
                    <input type="text" inputmode="numeric" pattern="^\d{6}$" maxlength="6" name="otp" id="otpEmailValue" placeholder="123456" disabled />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <div style="margin-bottom: 16px;">
                    <a href="#" id="resendEmailOTPBtn" class="disabled" style="pointer-events: none; text-decoration: none; font-size: 0.85rem; color: #888;">Resend OTP in 30s</a>
                </div>
                <button type="button" id="verifyEmailOTPBtn" class="auth-btn-primary">Verify OTP &rarr;</button>
            </div>

            <div id="userRegistration" style="display: none;">
                <div class="auth-input-group">
                    <label for="userName">Full Name</label>
                    <input type="text" name="name" id="userName" placeholder="Your Name" />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="button" id="userRegistrationBtn" class="auth-btn-primary">Complete Sign In &rarr;</button>
            </div>
        </form>

        <div class="auth-divider">
            <span>or</span>
        </div>

        <a href='/oauth/google/?redirect_url=<?php
                                                echo ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/')
                                                    . ((isset($method)) ? '&method=' . $method : '')
                                                    . ((isset($id)) ? '&id=' . $id : '')
                                                    . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl  : '');
                                                ?>' class="auth-google-btn">
            <img src="<?php echo USER_IMAGES; ?>google.png" alt="Google" /> Continue with Google
        </a>

        <!-- <div style="text-align: center; margin-top: 24px; font-size: 0.88rem; color: var(--text-secondary);">
            Don't have an account?
            <a href="/signup/<?php
                                echo ((isset($redirect_url)) ? '?redirect_url=' . $redirect_url . '&auth=true' : '/manage-subscription/')
                                    . ((isset($method)) ? '&method=' . $method : '')
                                    . ((isset($id)) ? '&id=' . $id : '')
                                    . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl . '&auth=true' : '');
                                ?>" style="color: var(--primary-color); font-weight: 600; text-decoration: underline;">Sign up</a>
        </div> -->

        <div style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-color); font-size: 0.78rem; color: #888;">
            By continuing, you agree to our
            <a href="/terms-and-conditions/" target="_blank" style="color: #666; text-decoration: underline;">Terms &amp; Conditions</a>
            and <a href="/privacy-policy/" target="_blank" style="color: #666; text-decoration: underline;">Privacy Policy</a>.
        </div>
    </main>

    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo USER_JS; ?>script.js"></script>

    <?php if (isset($alertMsg)) {
        echo "<script>alert('{$alertMsg}')</script>";
    } ?>
</body>

</html>