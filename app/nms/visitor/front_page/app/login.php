<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

if (isset($responseMsg)) {
    echo "<script>alert('{$responseMsg}')</script>";
}

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

if (defined('GOOGLE_RECAPTCHA_GENERATOR') && file_exists(GOOGLE_RECAPTCHA_GENERATOR)) {
    require_once GOOGLE_RECAPTCHA_GENERATOR;
}

?>
<style>
    .auth-portal-box {
        max-width: 440px;
        margin: 24px auto;
        padding: 28px 24px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid #eaeaea;
        text-align: center;
        box-sizing: border-box;
    }

    .auth-logo-header {
        margin-bottom: 20px;
    }

    .auth-logo-header img {
        max-height: 42px;
        width: auto;
    }

    .auth-form-title {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 6px 0;
    }

    .auth-form-subtitle {
        font-size: 0.88rem;
        color: #666666;
        margin: 0 0 22px 0;
        line-height: 1.4;
    }

    .auth-notice-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 18px;
        text-align: left;
        transition: all 0.25s ease;
    }

    .auth-notice-banner.error {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .auth-notice-banner.success {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .auth-notice-banner.warning {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .auth-form-section {
        margin-bottom: 16px;
        text-align: left;
    }

    .auth-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
        text-align: left;
    }

    .auth-input-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #333333;
    }

    .auth-input-group input {
        width: 100%;
        padding: 12px 14px;
        font-size: 0.95rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background-color: #f9fafb;
        color: #111827;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .auth-input-group input:focus {
        background-color: #ffffff;
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.06);
    }

    .auth-btn-primary {
        width: 100%;
        padding: 12px 20px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #ffffff;
        background-color: #000000;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .auth-btn-primary:hover {
        background-color: #222222;
    }

    .auth-back-btn {
        background: none;
        border: none;
        color: #666666;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0 0 12px 0;
        display: inline-block;
    }

    .auth-back-btn:hover {
        color: #000000;
        text-decoration: underline;
    }

    .otp-resend-wrapper {
        margin: 8px 0 14px 0;
        text-align: left;
    }

    .resend-otp-btn {
        color: #d93025;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
    }

    .resend-otp-btn.disabled {
        color: #999999 !important;
        pointer-events: none;
        opacity: 0.7;
    }

    .auth-divider {
        position: relative;
        text-align: center;
        margin: 22px 0 18px 0;
    }

    .auth-divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: #e5e7eb;
        z-index: 1;
    }

    .auth-divider span {
        position: relative;
        background-color: #ffffff;
        padding: 0 12px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #9ca3af;
        text-transform: uppercase;
        z-index: 2;
    }

    .auth-google-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 11px 16px;
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.925rem;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        transition: background-color 0.2s, border-color 0.2s;
        box-sizing: border-box;
    }

    .auth-google-btn:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .login-footer-legal {
        margin-top: 22px;
        padding-top: 14px;
        border-top: 1px solid #f3f4f6;
        font-size: 0.775rem;
        color: #9ca3af;
        line-height: 1.5;
        text-align: center;
    }

    .login-footer-legal a {
        color: #4b5563;
        text-decoration: underline;
    }

    .login-footer-legal a:hover {
        color: #111827;
    }
</style>

<main class="auth-portal-box">
    <div class="auth-logo-header">
        <a href="/"><img src="<?php echo SITE_LOGO; ?>" alt="<?php echo SITE_NAME; ?>" /></a>
    </div>

    <h1 class="auth-form-title">Sign In</h1>
    <p class="auth-form-subtitle"><?php echo ($siteAuthMethod === 'PASSWORD') ? 'Enter your email and password to log in.' : 'Enter your email to receive a secure one-time verification code.'; ?></p>

    <!-- Inline Notice Banner -->
    <div id="authNotice" class="auth-notice-banner" style="display: none;">
        <i id="authNoticeIcon" class="ph ph-warning-circle"></i>
        <span id="authNoticeText"></span>
    </div>

    <form id="login" autocomplete="off" onsubmit="return false;">
        <?php if ($siteAuthMethod === 'PASSWORD'): ?>
            <div id="passwordLoginSection" class="auth-form-section">
                <div class="auth-input-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="name@company.com" required />
                </div>
                <div class="auth-input-group">
                    <label for="pass">Password</label>
                    <input type="password" id="pass" name="pass" placeholder="Password" required />
                </div>
                <div style="text-align: right; margin-top: -6px; margin-bottom: 16px;">
                    <a href="/forgot-password/" style="font-size: 0.85rem; color: #666666; text-decoration: none;">Forgot password?</a>
                </div>
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="button" id="loginBtn" class="auth-btn-primary">
                    Sign In &rarr;
                </button>
            </div>
        <?php else: ?>
            <input type="hidden" name="reg_type" value="subscriber">

            <!-- Step 1: Email Input -->
            <div id="enterEmail" class="auth-form-section">
                <div class="auth-input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@company.com" required />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="button" id="emailLoginContinueBtn" class="auth-btn-primary">
                    Continue &rarr;
                </button>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="verifyEmailOTP" class="auth-form-section" style="display: none;">
                <button type="button" class="auth-back-btn" id="backToEmailBtn">
                    &larr; Back
                </button>
                <div class="auth-input-group">
                    <label for="otpEmailValue">Enter 6-Digit OTP</label>
                    <input type="text" inputmode="numeric" pattern="^\d{6}$" maxlength="6" name="otp" id="otpEmailValue" placeholder="123456" disabled autocomplete="one-time-code" />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <div class="otp-resend-wrapper">
                    <a href="#" id="resendEmailOTPBtn" class="disabled resend-otp-btn" style="pointer-events: none;">Resend OTP in 30s</a>
                </div>
                <button type="button" id="verifyEmailOTPBtn" class="auth-btn-primary">Verify OTP &rarr;</button>
            </div>

            <!-- Step 3: User Registration (New Users Only) -->
            <div id="userRegistration" class="auth-form-section" style="display: none;">
                <button type="button" class="auth-back-btn" id="backToOTPBtn">
                    &larr; Back
                </button>
                <div class="auth-input-group">
                    <label for="userName">Full Name</label>
                    <input type="text" name="name" id="userName" placeholder="Your Name" autocomplete="name" />
                </div>
                <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                <button type="button" id="userRegistrationBtn" class="auth-btn-primary">Complete Registration &rarr;</button>
            </div>
        <?php endif; ?>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        function showNotice(msg, type = "error") {
            const banner = document.getElementById("authNotice");
            const text = document.getElementById("authNoticeText");
            const icon = document.getElementById("authNoticeIcon");
            if (!banner || !text) return;

            text.textContent = msg;
            banner.className = `auth-notice-banner ${type}`;
            if (icon) {
                icon.className = type === "error" ? "ph ph-warning-circle" : (type === "success" ? "ph ph-check-circle" : "ph ph-info");
            }
            banner.style.display = "flex";
        }

        function hideNotice() {
            const banner = document.getElementById("authNotice");
            if (banner) banner.style.display = "none";
        }

        function startResendTimer() {
            const btn = document.getElementById("resendEmailOTPBtn");
            if (!btn) return;
            let countdown = 30;
            btn.textContent = `Resend OTP in ${countdown}s`;
            btn.classList.add("disabled");
            btn.style.pointerEvents = "none";

            const timer = setInterval(() => {
                countdown--;
                btn.textContent = `Resend OTP in ${countdown}s`;
                if (countdown <= 0) {
                    clearInterval(timer);
                    btn.textContent = "Resend OTP";
                    btn.classList.remove("disabled");
                    btn.style.pointerEvents = "auto";
                }
            }, 1000);
        }

        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            let val = urlParams.get(name);
            if (val) {
                try {
                    val = decodeURIComponent(decodeURIComponent(val));
                } catch (e) {}
                if (val.startsWith("/")) return val;
            }
            return null;
        }

        // Restrict OTP input to 6 digits only
        const otpInput = document.getElementById("otpEmailValue");
        if (otpInput) {
            otpInput.addEventListener("input", function() {
                this.value = this.value.replace(/[^0-9]/g, "").slice(0, 6);
            });
        }

        // Back button navigation
        const backToEmailBtn = document.getElementById("backToEmailBtn");
        if (backToEmailBtn) {
            backToEmailBtn.addEventListener("click", function(e) {
                e.preventDefault();
                hideNotice();
                document.getElementById("verifyEmailOTP").style.display = "none";
                document.getElementById("enterEmail").style.display = "block";
                const socialSec = document.getElementById("socialLoginSection");
                if (socialSec) socialSec.style.display = "block";
                const emailInp = document.getElementById("email");
                if (emailInp) emailInp.focus();
            });
        }

        const backToOTPBtn = document.getElementById("backToOTPBtn");
        if (backToOTPBtn) {
            backToOTPBtn.addEventListener("click", function(e) {
                e.preventDefault();
                hideNotice();
                document.getElementById("userRegistration").style.display = "none";
                document.getElementById("verifyEmailOTP").style.display = "block";
                if (otpInput) otpInput.focus();
            });
        }

        const emailBtn = document.getElementById("emailLoginContinueBtn");
        const emailInput = document.getElementById("email");
        const enterEmailSec = document.getElementById("enterEmail");
        const verifyEmailSec = document.getElementById("verifyEmailOTP");
        const socialLoginSec = document.getElementById("socialLoginSection");

        let isSubmittingEmail = false;

        async function sendEmailOTP() {
            if (isSubmittingEmail) return;
            hideNotice();
            const email = emailInput ? emailInput.value.trim().replace(/^(\+91[\-\s]?|0)/, "") : "";
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!regex.test(email)) {
                showNotice("Kindly enter a valid email address.", "warning");
                return;
            }

            isSubmittingEmail = true;
            if (emailBtn) emailBtn.style.display = "none";

            try {
                const res = await fetch(`/api/auth/auth-by-email/?email=${encodeURIComponent(email)}&reg_type=subscriber`);
                const data = await res.json();
                if (emailBtn) emailBtn.style.display = "flex";
                isSubmittingEmail = false;

                if (data.status === "success") {
                    if (enterEmailSec) enterEmailSec.style.display = "none";
                    if (socialLoginSec) socialLoginSec.style.display = "none";
                    if (verifyEmailSec) {
                        verifyEmailSec.classList.remove("display-none");
                        verifyEmailSec.style.display = "block";
                    }
                    if (otpInput) {
                        otpInput.disabled = false;
                        otpInput.value = "";
                        otpInput.focus();
                    }
                    startResendTimer();
                    localStorage.setItem("email", email);
                    if (data.otpRef) localStorage.setItem("otpRef", data.otpRef);
                    showNotice(`OTP sent to ${email}`, "success");
                } else {
                    showNotice(data.msg || "Failed to send OTP", "error");
                }
            } catch (err) {
                console.error(err);
                if (emailBtn) emailBtn.style.display = "flex";
                isSubmittingEmail = false;
                showNotice("Something went wrong. Please try again.", "error");
            }
        }

        if (emailBtn) {
            emailBtn.addEventListener("click", sendEmailOTP);
        }
        if (emailInput) {
            emailInput.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    sendEmailOTP();
                }
            });
        }

        const verifyBtn = document.getElementById("verifyEmailOTPBtn");
        let isVerifying = false;

        async function verifyOTP() {
            if (isVerifying) return;
            hideNotice();
            const otpValue = otpInput ? otpInput.value.trim() : "";
            if (!/^\d{6}$/.test(otpValue)) {
                showNotice("Invalid OTP. Please enter a 6-digit code.", "warning");
                return;
            }

            isVerifying = true;
            if (verifyBtn) verifyBtn.style.display = "none";
            const otpRef = localStorage.getItem("otpRef") || "";

            try {
                const res = await fetch(`/api/auth/verify-otp/?otp_ref_id=${encodeURIComponent(otpRef)}&otp_code=${encodeURIComponent(otpValue)}&reg_type=subscriber&otp_mode=email`);
                const data = await res.json();
                if (verifyBtn) verifyBtn.style.display = "flex";
                isVerifying = false;

                if (data.status === "success") {
                    showNotice("Login successful! Redirecting...", "success");
                    if (data.existingUser) {
                        setTimeout(() => {
                            const redirect = getQueryParam("redirect_url") || "<?php echo addslashes($redirect_url); ?>";
                            window.location.href = redirect;
                        }, 600);
                    } else {
                        if (verifyEmailSec) verifyEmailSec.style.display = "none";
                        const userRegSec = document.getElementById("userRegistration");
                        if (userRegSec) userRegSec.style.display = "block";
                        const nameInput = document.getElementById("userName");
                        if (nameInput) nameInput.focus();
                    }
                } else {
                    showNotice(data.msg || "Invalid OTP code", "error");
                }
            } catch (err) {
                console.error(err);
                if (verifyBtn) verifyBtn.style.display = "flex";
                isVerifying = false;
                showNotice("Something went wrong verifying OTP.", "error");
            }
        }

        if (verifyBtn) {
            verifyBtn.addEventListener("click", verifyOTP);
        }
        if (otpInput) {
            otpInput.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    verifyOTP();
                }
            });
        }

        const resendBtn = document.getElementById("resendEmailOTPBtn");
        if (resendBtn) {
            resendBtn.addEventListener("click", async function(e) {
                e.preventDefault();
                if (resendBtn.classList.contains("disabled")) return;
                startResendTimer();
                const otpRef = localStorage.getItem("otpRef") || "";
                try {
                    const res = await fetch(`/api/auth/resend-otp/?otp_ref_id=${encodeURIComponent(otpRef)}&otp_mode=email`);
                    const data = await res.json();
                    showNotice(data.msg || "OTP resent to your email", "success");
                } catch (err) {
                    console.error(err);
                }
            });
        }

        const regBtn = document.getElementById("userRegistrationBtn");
        const nameInput = document.getElementById("userName");

        if (regBtn && nameInput) {
            regBtn.addEventListener("click", async function() {
                hideNotice();
                const userName = nameInput.value.trim();
                if (!userName) {
                    showNotice("Please enter your name.", "warning");
                    return;
                }
                regBtn.style.display = "none";
                try {
                    const res = await fetch("/api/auth/user-profile-update/", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "name=" + encodeURIComponent(userName)
                    });
                    const data = await res.json();
                    if (data.status === "success") {
                        showNotice("Profile set up! Redirecting...", "success");
                        setTimeout(() => {
                            const redirect = getQueryParam("redirect_url") || "<?php echo addslashes($redirect_url); ?>";
                            window.location.href = redirect;
                        }, 600);
                    } else {
                        regBtn.style.display = "flex";
                        showNotice(data.msg || "Failed to update profile", "error");
                    }
                } catch (err) {
                    console.error(err);
                    regBtn.style.display = "flex";
                    showNotice("Error updating profile.", "error");
                }
            });
        }

        const loginBtn = document.getElementById("loginBtn");
        if (loginBtn) {
            async function submitPasswordLogin() {
                hideNotice();
                const email = (document.getElementById("email")?.value || "").trim();
                const pass = (document.getElementById("pass")?.value || "").trim();
                if (!email) {
                    showNotice("Please enter your email", "warning");
                    return;
                }
                if (!pass) {
                    showNotice("Please enter your password", "warning");
                    return;
                }
                loginBtn.style.display = "none";
                try {
                    const formData = new URLSearchParams();
                    formData.append("type", "auth");
                    formData.append("action", "login");
                    formData.append("email", email);
                    formData.append("pass", pass);
                    formData.append("redirect_url", "<?php echo addslashes($redirect_url); ?>");

                    const res = await fetch("/", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: formData.toString()
                    });
                    const data = await res.json();
                    if (data.status === "success") {
                        showNotice("Login successful! Redirecting...", "success");
                        setTimeout(() => {
                            const redirect = getQueryParam("redirect_url") || data.url || "<?php echo addslashes($redirect_url); ?>";
                            window.location.href = redirect;
                        }, 600);
                    } else {
                        showNotice(data.msg || "Invalid credentials", "error");
                        loginBtn.style.display = "flex";
                    }
                } catch (err) {
                    console.error(err);
                    showNotice("An error occurred during login. Please try again.", "error");
                    loginBtn.style.display = "flex";
                }
            }

            loginBtn.addEventListener("click", submitPasswordLogin);

            const passInput = document.getElementById("pass");
            if (passInput) {
                passInput.addEventListener("keypress", function(e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        submitPasswordLogin();
                    }
                });
            }
        }
    });
</script>
<?php

require_once 'footer.php';

?>