<?php

/**
 * V3 Executive Login Page - Passwordless Email OTP & Google OAuth
 * Path: /app/dashboard/v3/default/desktop/login.php
 */

$rawRedirect = isset($redirect_url) && !empty($redirect_url) ? $redirect_url : ($_GET['redirect_url'] ?? ($_GET['redirect'] ?? '/dashboard/'));
while (is_string($rawRedirect) && (stripos($rawRedirect, '%2f') !== false || stripos($rawRedirect, '%3a') !== false)) {
    $rawRedirect = urldecode($rawRedirect);
}
$rawRedirect = trim((string)$rawRedirect);

if (empty($rawRedirect) || $rawRedirect === '#' || $rawRedirect === 'dashboard' || $rawRedirect === 'dashboard/' || $rawRedirect === '/login/dashboard/' || $rawRedirect === '/login/dashboard') {
    $redirect_url = '/dashboard/';
} elseif (strpos($rawRedirect, 'http://') === 0 || strpos($rawRedirect, 'https://') === 0) {
    $redirect_url = $rawRedirect;
} else {
    $redirect_url = '/' . ltrim($rawRedirect, '/');
}

if (isset($_COOKIE['isLogin']) && isset($_COOKIE['user_data'])) {
    header("Location: {$redirect_url}");
    exit();
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int)SITE_ID : 1;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'ISW Platform';
$siteLogo = defined('SITE_LOGO') && !empty(SITE_LOGO) ? SITE_LOGO : '';
$siteIcon = defined('SITE_ICON') && !empty(SITE_ICON) ? SITE_ICON : '';
$appVersion = defined('ALEYA_APP_VERSION') ? ALEYA_APP_VERSION : (defined('SITE_VERSION') ? SITE_VERSION : '3.0');
$jqueryJs = defined('JS_LIBRARY') ? JS_LIBRARY . 'jquery.min.js' : 'https://code.jquery.com/jquery-3.7.1.min.js';
$sweetAlertJs = defined('PLUGIN_LIBRARY') ? PLUGIN_LIBRARY . 'sweetalert/sweetalert.min.js' : 'https://cdn.jsdelivr.net/npm/sweetalert2@11';

// Google OAuth URL
$googleOAuthUrl = '/oauth/google/?redirect_url=' . urlencode($redirect_url);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign In &bull; <?php echo htmlspecialchars($siteName); ?></title>
    <?php if (!empty($siteIcon)): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($siteIcon); ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="/app/dashboard/v3/default/desktop/assets/css/auth.css">

    <?php
    if (defined('GOOGLE_RECAPTCHA_GENERATOR') && file_exists(GOOGLE_RECAPTCHA_GENERATOR)) {
        require_once GOOGLE_RECAPTCHA_GENERATOR;
    }
    if (defined('PWA') && file_exists(PWA . 'header.php')) {
        require_once PWA . 'header.php';
    }
    ?>
</head>

<body class="v3-auth-body">
    <div class="v3-auth-container">
        <div class="v3-auth-box">
            <!-- Auth Card -->
            <div class="v3-auth-card">
                <!-- Brand Logo Section -->
                <div class="logo">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>">
                    <?php else: ?>
                        <div class="logo-monogram">
                            <i class="fi fi-rr-shield-check"></i>
                            <span><?php echo htmlspecialchars($siteName); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <!-- Heading -->
                    <div class="form-heading">
                        <strong>Welcome Back!</strong>
                        <span>Sign in to access your <?php echo htmlspecialchars($siteName); ?> workspace</span>
                    </div>

                    <!-- Dynamic Live Alert Banner -->
                    <div id="authAlertBox" class="v3-auth-alert">
                        <i id="authAlertIcon" class="fi fi-rr-exclamation"></i>
                        <span id="authAlertText"></span>
                    </div>

                    <!-- Google Sign-In Option -->
                    <div class="auth-social-row">
                        <a href="<?php echo htmlspecialchars($googleOAuthUrl); ?>" id="btnGoogleSignIn" class="btn-google-login">
                            <svg class="google-svg-icon" viewBox="0 0 24 24" width="18" height="18">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" />
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" />
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.04 0 12s.45 3.82 1.25 5.42l4.03-3.15z" />
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>or sign in with email OTP</span>
                    </div>

                    <!-- ======================================================= -->
                    <!-- EMAIL OTP LOGIN WORKFLOW -->
                    <!-- ======================================================= -->
                    <div id="authOtpSection" class="auth-section">
                        <!-- Step A: Enter Email Address -->
                        <div id="paneOtpStepEmail" class="otp-pane">
                            <form id="formSendOtp" autocomplete="off">
                                <div class="form-group">
                                    <label class="auth-label" for="otpEmail">Work Email Address</label>
                                    <div class="input-icon-wrap">
                                        <i class="fi fi-rr-envelope input-icon"></i>
                                        <input
                                            type="email"
                                            class="form-control input-with-label"
                                            name="email"
                                            id="otpEmail"
                                            placeholder="name@domain.com"
                                            required
                                            autofocus>
                                    </div>
                                </div>

                                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                                <button type="submit" id="btnSendOtp" class="button theme-button">
                                    <span class="btn-spinner"></span>
                                    <span class="btn-text">Send Verification Code</span>
                                    <i class="fi fi-rr-paper-plane btn-arrow"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Step B: Verify 6-Digit OTP Code -->
                        <div id="paneOtpStepVerify" class="otp-pane" style="display: none;">
                            <div class="otp-sent-info">
                                <span>Code sent to <strong id="displayOtpTarget"></strong></span>
                                <a href="#" id="btnChangeOtpEmail" class="otp-edit-link">Change</a>
                            </div>

                            <form id="formVerifyOtp" autocomplete="off">
                                <div class="form-group">
                                    <label class="auth-label" for="otpCodeInput">Enter 6-Digit Code</label>
                                    <div class="input-icon-wrap">
                                        <i class="fi fi-rr-shield-check input-icon"></i>
                                        <input
                                            type="text"
                                            class="form-control input-with-label otp-input-field"
                                            name="otp_code"
                                            id="otpCodeInput"
                                            placeholder="&bull; &bull; &bull; &bull; &bull; &bull;"
                                            maxlength="6"
                                            pattern="\d{6}"
                                            inputmode="numeric"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group form-link-row">
                                    <span class="otp-countdown-text">
                                        Didn't receive code?
                                        <a href="#" id="btnResendOtp" class="resend-link is-disabled">Resend code in <span id="resendTimer">30</span>s</a>
                                    </span>
                                </div>

                                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                                <button type="submit" id="btnVerifyOtp" class="button theme-button">
                                    <span class="btn-spinner"></span>
                                    <span class="btn-text">Verify & Sign In</span>
                                    <i class="fi fi-rr-arrow-right btn-arrow"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Register Row -->
                    <!-- <div class="form-group register-row">
                        <p>Need a workspace account? <a href="/register/">Register Now</a></p>
                    </div> -->
                </div>

                <!-- Legal Notice -->
                <div class="legal">
                    <p>By continuing you agree to our <a href="https://blumox.com/terms-and-conditions/" target="_blank">Terms of Service</a> and <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy Policy</a></p>
                </div>
            </div>

            <!-- Version Tag -->
            <p class="version">
                v 3.0
            </p>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars($jqueryJs); ?>"></script>
    <script src="/app/dashboard/v3/default/desktop/assets/js/auth.js"></script>

    <?php
    if (defined('PWA') && file_exists(PWA . 'footer.php')) {
        require_once PWA . 'footer.php';
    }
    ?>
</body>

</html>