<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

if (isset($_COOKIE['isLogin'])) {

    header("Location: {$redirect_url}");
    exit();
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
                    <strong>Welcome Back!</strong> Log In to Your Account
                </div>
                <form id="login" autocomplete="off">
                    <div class="form-group">
                        <input type="text" class="form-control input-with-label" name="email" id="email"
                            placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control input-with-label" name="pass" id="pass"
                            placeholder="Password">
                    </div>
                    <div class="form-group">
                        <p>Forgot your password? <a href="/forgot-password/">Click Here</a></p>
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>">
                    <button type="submit" id="loginBtn" class="button theme-button">Log in</button>
                    <div class="form-group">
                        <p>Don't have an account yet? <a href="/register/">Register Now</a></p>
                    </div>
                </form>
            <?php else: ?>
                <div id="enterEmail">
                    <div class="form-heading">
                        <strong>Welcome</strong> Enter your email to get started.
                    </div>
                    <form id="login" autocomplete="off">
                        <div class="form-group">
                            <input type="email" class="form-control input-with-label" name="email" id="email" required
                                placeholder="Enter your email address"
                                value="<?php echo ((isset($email)) ? $email : '') ?>">
                        </div>
                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                        <button type="submit" id="emailLoginContinueBtn" class="button theme-button">Continue</button>
                    </form>
                </div>
                <div id="verifyEmailOTP" style="display: none;">
                    <div class="form-heading">
                        <strong>Verify OTP</strong> Please enter it below to verify.
                    </div>
                    <form id="login" autocomplete="off">
                        <div class="form-group">
                            <input type="number" class="form-control input-with-label" name="otp" id="otpEmailValue"
                                required placeholder="123456" value="<?php echo ((isset($email)) ? $email : '') ?>">
                        </div>
                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                        <a href="#" id="resendEmailOTPBtn" class="disabled"
                            style="pointer-events: none; float: left; margin-bottom: 10px; text-decoration: none;">Resend
                            OTP
                            in
                            30s</a>
                        <button type="submit" id="verifyEmailOTPBtn" class="button theme-button">Verify OTP</button>
                    </form>
                </div>
                <div id="registrationForm" style="display: none;">
                    <div class="form-heading">
                        <strong>Create Your Profile</strong> Complete your profile to get started.
                    </div>
                    <form id="registration" autocomplete="off">
                        <div class="form-group">
                            <input type="text" class="form-control input-with-label" name="full_name" id="fullName" required
                                placeholder="Enter your full name*">
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control input-with-label" name="mobile" id="mobile" required
                                placeholder="Enter your mobile number">
                        </div>
                        <button type="submit" id="registrationeBtn" class="button theme-button">Complete
                            registration</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <?php

        require_once 'footer.php';

        ?>