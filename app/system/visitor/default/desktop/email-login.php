<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

if (isset($_COOKIE['isLogin'])) {

    header("Location: {$redirect_url}");
    exit();
}

require_once 'header.php';

?>

<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
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
                    <a href="#" id="resendEmailOTPBtn" class="disabled" style="pointer-events: none;">Resend OTP in
                        30s</a>
                    <button type="submit" id="verifyEmailOTPBtn" class="button theme-button">Verify OTP</button>
                </form>
            </div>
            <div id="registrationForm" style="display: none;">
                <div class="form-heading">
                    <strong>Create Your Profile</strong> Complete your profile to get started.
                </div>
                <form id="login" autocomplete="off">
                    <div class="form-group">

                    </div>
                    <button type="submit" id="emailRegistrationeBtn" class="button theme-button">Complete
                        registration</button>
                </form>
            </div>
        </div>
        <?php

        require_once 'footer.php';

        ?>