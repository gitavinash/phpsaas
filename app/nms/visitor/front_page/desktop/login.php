<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET; ?>style.css?ver=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET; ?>login.css?ver=<?php echo time(); ?>">
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
            <input type="hidden" name="reg_type" value="subscriber">

            <!-- Step 1: Email Input -->
            <div id="enterEmail" class="auth-form-section">
                <div class="auth-input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@company.com" required />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="submit" id="emailLoginContinueBtn" class="auth-btn-primary">
                    Continue &rarr;
                </button>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="verifyEmailOTP" class="auth-form-section display-none">
                <button type="button" class="auth-back-btn" id="backToEmailBtn" onclick="goBackToEmail(event)">
                    &larr; Back
                </button>
                <div class="auth-input-group">
                    <label for="otpEmailValue">Enter 6-Digit OTP</label>
                    <input type="text" inputmode="numeric" pattern="^\d{6}$" maxlength="6" name="otp" id="otpEmailValue" placeholder="123456" disabled autocomplete="one-time-code" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <div class="otp-resend-wrapper">
                    <a href="#" id="resendEmailOTPBtn" class="disabled resend-otp-btn">Resend OTP in 30s</a>
                </div>
                <button type="button" id="verifyEmailOTPBtn" class="auth-btn-primary">Verify OTP &rarr;</button>
            </div>

            <!-- Step 3: User Registration (New Users Only) -->
            <div id="userRegistration" class="auth-form-section display-none">
                <button type="button" class="auth-back-btn" id="backToOTPBtn" onclick="goBackToOTP(event)">
                    &larr; Back
                </button>
                <div class="auth-input-group">
                    <label for="userName">Full Name</label>
                    <input type="text" name="name" id="userName" placeholder="Your Name" autocomplete="name" />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="button" id="userRegistrationBtn" class="auth-btn-primary">Complete Registration &rarr;</button>
            </div>
        </form>

        <div id="socialLoginSection">
            <div class="auth-divider">
                <span>or</span>
            </div>

            <a href='/oauth/google/?redirect_url=<?php
                                                    echo ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/')
                                                        . ((isset($method)) ? '&method=' . $method : '')
                                                        . ((isset($id)) ? '&id=' . $id : '')
                                                        . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl  : '');
                                                    ?>' class="auth-google-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                </svg>
                Continue with Google
            </a>
        </div>

        <div class="login-footer-legal">
            By continuing, you agree to our
            <a href="/terms-and-conditions/" target="_blank" rel="noopener noreferrer">Terms &amp; Conditions</a>
            and
            <a href="/privacy-policy/" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
        </div>
    </main>

    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function goBackToEmail(e) {
            if (e) e.preventDefault();
            const verifySec = document.getElementById('verifyEmailOTP');
            const enterSec = document.getElementById('enterEmail');
            const socialSec = document.getElementById('socialLoginSection');
            if (verifySec) verifySec.style.display = 'none';
            if (enterSec) enterSec.style.display = 'block';
            if (socialSec) socialSec.style.display = 'block';
            const emailInp = document.getElementById('email');
            if (emailInp) emailInp.focus();
        }

        function goBackToOTP(e) {
            if (e) e.preventDefault();
            const regSec = document.getElementById('userRegistration');
            const verifySec = document.getElementById('verifyEmailOTP');
            if (regSec) regSec.style.display = 'none';
            if (verifySec) verifySec.style.display = 'block';
            const otpInp = document.getElementById('otpEmailValue');
            if (otpInp) otpInp.focus();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial state
            const verifySec = document.getElementById('verifyEmailOTP');
            const regSec = document.getElementById('userRegistration');
            const enterSec = document.getElementById('enterEmail');
            if (verifySec) verifySec.style.display = 'none';
            if (regSec) regSec.style.display = 'none';
            if (enterSec) enterSec.style.display = 'block';

            // Restrict OTP input to 6 digits only
            const otpInput = document.getElementById("otpEmailValue");
            if (otpInput) {
                otpInput.addEventListener("input", function() {
                    this.value = this.value.replace(/[^0-9]/g, "").slice(0, 6);
                });
            }
        });
    </script>
    <script src="<?php echo USER_JS; ?>login.js"></script>
    <script src="<?php echo USER_JS; ?>script.js"></script>

</body>

</html>