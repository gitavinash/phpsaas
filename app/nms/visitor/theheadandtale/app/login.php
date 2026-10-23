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

?>
<style>
    .auth-notice-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 18px;
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

    .auth-notice-banner i {
        font-size: 1.2rem;
        flex-shrink: 0;
    }
</style>

<!-- AUTH SCREEN CONTENT -->
<div id="view-auth" class="page-view active">
    <div class="auth-body">
        <h2 class="auth-title">
            Welcome to <?php echo SITE_NAME ?>
        </h2>

        <!-- Native App Inline Notice Banner -->
        <div id="authNotice" class="auth-notice-banner" style="display: none;">
            <i id="authNoticeIcon" class="ph ph-warning-circle"></i>
            <span id="authNoticeText"></span>
        </div>

        <form id="login" autocomplete="off" class="auth-form" onsubmit="return false;">
            <?php if ($siteAuthMethod === 'PASSWORD'): ?>
                <div id="passwordLoginSection">
                    <p class="auth-subtitle">
                        Enter your email and password to log in.
                    </p>
                    <div style="margin-bottom: 12px;">
                        <input
                            type="text"
                            id="email"
                            class="auth-input"
                            name="email"
                            placeholder="Email Address"
                            required />
                    </div>
                    <div style="margin-bottom: 8px;">
                        <input
                            type="password"
                            id="pass"
                            class="auth-input"
                            name="pass"
                            placeholder="Password"
                            required />
                    </div>
                    <div style="margin-bottom: 16px; text-align: right;">
                        <a href="/forgot-password/" style="color: var(--accent-primary, #0066cc); font-size: 0.85rem; text-decoration: none;">
                            Forgot Password?
                        </a>
                    </div>
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
                    <button
                        type="button"
                        id="loginBtn"
                        class="auth-btn primary-btn"
                        style="border: none; width: 100%; cursor: pointer;">
                        Log In
                    </button>
                </div>
            <?php else: ?>
                <!-- Step 1: Enter Email -->
                <div id="enterEmail">
                    <p class="auth-subtitle">
                        Enter your email to get started.
                    </p>
                    <input
                        type="email"
                        id="email"
                        class="auth-input"
                        name="email"
                        placeholder="youremail@example.com"
                        required />
                    <button
                        type="button"
                        id="emailLoginContinueBtn"
                        class="auth-btn primary-btn"
                        style="border: none; width: 100%; margin-top: 12px; cursor: pointer;">
                        Continue
                    </button>
                </div>

                <!-- Step 2: Verify OTP -->
                <div id="verifyEmailOTP" style="display: none;">
                    <p class="auth-subtitle">
                        Verify 6-digit OTP sent to your email
                    </p>
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="^\d{6}$"
                        maxlength="6"
                        id="otpEmailValue"
                        class="auth-input"
                        name="otp"
                        placeholder="123456"
                        disabled />

                    <div style="margin: 12px 0 16px; text-align: left;">
                        <a href="#" id="resendEmailOTPBtn" class="disabled" style="color: var(--accent-primary, #0066cc); text-decoration: none; font-size: 0.9rem; pointer-events: none;">
                            Resend OTP in 30s
                        </a>
                    </div>

                    <button
                        type="button"
                        id="verifyEmailOTPBtn"
                        class="auth-btn primary-btn"
                        style="border: none; width: 100%; cursor: pointer;">
                        Verify OTP
                    </button>
                </div>

                <!-- Step 3: Complete Registration -->
                <div id="userRegistration" style="display: none;">
                    <p class="auth-subtitle">
                        Enter your full name to complete setup
                    </p>
                    <input
                        type="text"
                        id="userName"
                        class="auth-input"
                        name="name"
                        placeholder="Full Name (e.g. Avinash Kumar)" />

                    <button
                        type="button"
                        id="userRegistrationBtn"
                        class="auth-btn primary-btn"
                        style="border: none; width: 100%; margin-top: 12px; cursor: pointer;">
                        Complete Registration
                    </button>
                </div>
            <?php endif; ?>
        </form>

        <!-- Social Login Section (hidden when email is entered) -->
        <!-- <div id="socialLoginSection">
            <div class="auth-divider">
                <span>or</span>
            </div>

            <a
                href="/oauth/google/?redirect_url=<?php echo urlencode($redirect_url); ?>"
                class="auth-btn google-btn">
                <i class="ph-fill ph-google-logo"></i> Continue with Google
            </a>
        </div> -->
    </div>
</div>

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

        const emailBtn = document.getElementById("emailLoginContinueBtn");
        const emailInput = document.getElementById("email");
        const enterEmailSec = document.getElementById("enterEmail");
        const verifyEmailSec = document.getElementById("verifyEmailOTP");
        const socialLoginSec = document.getElementById("socialLoginSection");
        const otpInput = document.getElementById("otpEmailValue");

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
                if (emailBtn) emailBtn.style.display = "block";
                isSubmittingEmail = false;

                if (data.status === "success") {
                    if (enterEmailSec) enterEmailSec.style.display = "none";
                    if (socialLoginSec) socialLoginSec.style.display = "none";
                    if (verifyEmailSec) verifyEmailSec.style.display = "block";
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
                if (emailBtn) emailBtn.style.display = "block";
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
                if (verifyBtn) verifyBtn.style.display = "block";
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
                if (verifyBtn) verifyBtn.style.display = "block";
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
                        regBtn.style.display = "block";
                        showNotice(data.msg || "Failed to update profile", "error");
                    }
                } catch (err) {
                    console.error(err);
                    regBtn.style.display = "block";
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