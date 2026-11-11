<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 5) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$redirect_url = function_exists('iswNormalizeRedirectPath')
    ? iswNormalizeRedirectPath($redirect_url ?? '/dashboard/', '/dashboard/')
    : '/dashboard/';

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

// Check SITE_AUTH_METHOD constant value (defaults to 'OTP' if not defined or empty)
if (!defined('SITE_AUTH_METHOD')) {
    define('SITE_AUTH_METHOD', 'OTP');
}
$siteAuthMethod = defined('SITE_AUTH_METHOD') && !empty(SITE_AUTH_METHOD) ? strtoupper(trim((string)SITE_AUTH_METHOD)) : 'OTP';

// In development or testing, query param can override if provided
if (isset($_GET['auth_method']) && in_array(strtoupper(trim($_GET['auth_method'])), ['PASSWORD', 'OTP'])) {
    $siteAuthMethod = strtoupper(trim($_GET['auth_method']));
}

if ($siteAuthMethod !== 'PASSWORD') {
    $siteAuthMethod = 'OTP';
}

require_once 'header.php';

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <?php if ($siteAuthMethod === 'PASSWORD'): ?>
                <div class="form-heading">
                    <strong>Welcome Back!</strong>Log in to access your account.
                </div>
                <form id="login" autocomplete="off">
                    <div class="form-group">
                        <input type="text" class="form-control" name="email" id="email"
                            placeholder="Email address" required autofocus>
                    </div>
                    <div class="form-group">
                        <div class="input-password-wrapper">
                            <input type="password" class="form-control" name="pass" id="pass"
                                placeholder="Password" required>
                            <button type="button" class="password-toggle-btn" aria-label="Show password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="auth-links-row">
                            <span></span>
                            <a href="/forgot-password/">Forgot password?</a>
                        </div>
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" id="loginBtn" class="button theme-button">Log in</button>
                    <div class="form-group">
                        <p>Don't have an account yet? <a href="/register/">Register Now</a></p>
                    </div>
                </form>
            <?php else: ?>
                <div id="enterEmail">
                    <div class="form-heading">
                        <strong>Welcome</strong>Enter your email to get started.
                    </div>
                    <form id="login" autocomplete="off">
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" id="email" required
                                placeholder="Enter your email address"
                                value="<?php echo ((isset($email)) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : '') ?>" autofocus>
                        </div>
                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                        <button type="submit" id="emailLoginContinueBtn" class="button theme-button">Continue</button>
                    </form>
                </div>
                <div id="verifyEmailOTP" style="display: none;">
                    <div class="form-heading">
                        <strong>Verify OTP</strong>Please enter the 6-digit code sent to your email.
                    </div>
                    <form id="login" autocomplete="off">
                        <div class="form-group">
                            <input type="number" class="form-control" name="otp" id="otpEmailValue"
                                required placeholder="Enter 6-digit OTP" value="<?php echo ((isset($email)) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : '') ?>">
                        </div>
                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                        <div class="otp-meta-row">
                            <span></span>
                            <a href="#" id="resendEmailOTPBtn" class="resend-link-action disabled">Resend OTP in 30s</a>
                        </div>
                        <button type="submit" id="verifyEmailOTPBtn" class="button theme-button">Verify OTP</button>
                    </form>
                </div>
                <div id="registrationForm" style="display: none;">
                    <div class="form-heading">
                        <strong>Complete Profile</strong>Tell us a bit about yourself to finish setup.
                    </div>
                    <form id="registration" autocomplete="off">
                        <div class="form-group">
                            <input type="text" class="form-control" name="full_name" id="fullName" required
                                placeholder="Enter your full name*">
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="mobile" id="mobile" required
                                placeholder="Enter your mobile number">
                        </div>
                        <button type="submit" id="registrationeBtn" class="button theme-button">Complete Registration</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <?php

        require_once 'footer.php';

        ?>