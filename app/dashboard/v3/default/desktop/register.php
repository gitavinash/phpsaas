<?php

/**
 * V3 Executive App-Tailored Registration Page
 * Path: /app/dashboard/v3/default/desktop/register.php
 */

$redirect_url = isset($redirect_url) && !empty($redirect_url) ? $redirect_url : '/dashboard/';

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

// Current or Requested App Type
$defaultApp = isset($_GET['app']) ? strtolower(trim($_GET['app'])) : (defined('SITE_APP') ? strtolower(SITE_APP) : 'ecom');
if (!in_array($defaultApp, ['ecom', 'ecommerce', 'aleya', 'ecom_gift', 'e_com_gift'])) {
    $defaultApp = 'ecom';
}
if ($defaultApp === 'ecommerce') $defaultApp = 'ecom';
if ($defaultApp === 'e_com_gift') $defaultApp = 'ecom_gift';

// Google OAuth URL
$googleOAuthUrl = '/oauth/google/?redirect_url=' . urlencode($redirect_url);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Create Your Workspace &bull; <?php echo htmlspecialchars($siteName); ?></title>
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
        <div class="v3-auth-box v3-auth-box-wide">
            <!-- Auth Card -->
            <div class="v3-auth-card">
                <!-- Brand Logo Section -->
                <div class="logo">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>">
                    <?php else: ?>
                        <div class="logo-monogram">
                            <i class="fi fi-rr-rocket-lunch"></i>
                            <span><?php echo htmlspecialchars($siteName); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <!-- Heading -->
                    <div class="form-heading">
                        <strong>Launch Your Workspace</strong>
                        <span>Select your business model and create your account instantly</span>
                    </div>

                    <!-- Dynamic Live Alert Banner -->
                    <div id="authAlertBox" class="v3-auth-alert">
                        <i id="authAlertIcon" class="fi fi-rr-exclamation"></i>
                        <span id="authAlertText"></span>
                    </div>

                    <!-- Google Quick Sign-Up -->
                    <div class="auth-social-row">
                        <a href="<?php echo htmlspecialchars($googleOAuthUrl); ?>" id="btnGoogleSignIn" class="btn-google-login">
                            <svg class="google-svg-icon" viewBox="0 0 24 24" width="18" height="18">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" />
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" />
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.04 0 12s.45 3.82 1.25 5.42l4.03-3.15z" />
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
                            </svg>
                            <span>Fast-track with Google</span>
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>or customize your workspace</span>
                    </div>

                    <!-- App Choice Selector Grid -->
                    <div class="app-choice-grid" role="radiogroup" aria-label="Select Application Type">
                        <!-- 1. E-Commerce -->
                        <div class="app-choice-card <?php echo ($defaultApp === 'ecom') ? 'active' : ''; ?>" data-app="ecom">
                            <div class="app-choice-icon app-icon-ecom">
                                <i class="fi fi-rr-shopping-bag"></i>
                            </div>
                            <div class="app-choice-content">
                                <strong>E-Commerce</strong>
                                <span>Online Store & Orders</span>
                            </div>
                            <i class="fi fi-rr-check-circle app-choice-check"></i>
                        </div>

                        <!-- 2. Aleya (Salon & Spa) -->
                        <div class="app-choice-card <?php echo ($defaultApp === 'aleya') ? 'active' : ''; ?>" data-app="aleya">
                            <div class="app-choice-icon app-icon-aleya">
                                <i class="fi fi-rr-sparkles"></i>
                            </div>
                            <div class="app-choice-content">
                                <strong>Aleya</strong>
                                <span>Salon, Spa & Wellness</span>
                            </div>
                            <i class="fi fi-rr-check-circle app-choice-check"></i>
                        </div>

                        <!-- 3. E-Commerce Gift -->
                        <div class="app-choice-card <?php echo ($defaultApp === 'ecom_gift') ? 'active' : ''; ?>" data-app="ecom_gift">
                            <div class="app-choice-icon app-icon-gift">
                                <i class="fi fi-rr-gift"></i>
                            </div>
                            <div class="app-choice-content">
                                <strong>Gift Studio</strong>
                                <span>Bakery, Florist & Gifts</span>
                            </div>
                            <i class="fi fi-rr-check-circle app-choice-check"></i>
                        </div>
                    </div>

                    <!-- Registration Form (Passwordless) -->
                    <form id="register" autocomplete="off">
                        <input type="hidden" name="app_type" id="selectedAppType" value="<?php echo htmlspecialchars($defaultApp); ?>">

                        <!-- App-Tailored Business Name Field -->
                        <div class="form-group">
                            <label class="auth-label" id="lblBusinessName" for="business_name">
                                <?php
                                if ($defaultApp === 'aleya') {
                                    echo 'Salon / Spa Brand Name';
                                } elseif ($defaultApp === 'ecom_gift') {
                                    echo 'Gift Studio / Bakery Name';
                                } else {
                                    echo 'Store / Brand Name';
                                }
                                ?>
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-shop input-icon" id="iconBusiness"></i>
                                <input
                                    type="text"
                                    class="form-control input-with-label"
                                    name="business_name"
                                    id="business_name"
                                    placeholder="e.g. Acme Studio"
                                    required
                                    autofocus>
                            </div>
                        </div>

                        <!-- App-Tailored Category / Speciality Selector -->
                        <div class="form-group">
                            <label class="auth-label" id="lblCategory" for="category">Business Category</label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-apps input-icon"></i>
                                <select class="form-control input-with-label" name="category" id="category">
                                    <!-- Options populated dynamically via JS based on app selection -->
                                    <option value="fashion">Fashion & Apparel</option>
                                    <option value="electronics">Electronics & Gadgets</option>
                                    <option value="beauty">Beauty & Cosmetics</option>
                                    <option value="home">Home Decor & Living</option>
                                    <option value="general">General Merchandise</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contact Details Row (Name) -->
                        <div class="form-row-2col">
                            <div class="form-group">
                                <label class="auth-label" for="first_name">First Name</label>
                                <div class="input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input
                                        type="text"
                                        class="form-control input-with-label"
                                        name="first_name"
                                        id="first_name"
                                        placeholder="First name"
                                        required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="auth-label" for="last_name">Last Name</label>
                                <div class="input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input
                                        type="text"
                                        class="form-control input-with-label"
                                        name="last_name"
                                        id="last_name"
                                        placeholder="Last name"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Work Email & Mobile -->
                        <div class="form-group">
                            <label class="auth-label" for="email">Work Email Address</label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-envelope input-icon"></i>
                                <input
                                    type="email"
                                    class="form-control input-with-label"
                                    name="email"
                                    id="email"
                                    placeholder="name@company.com"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="auth-label" for="mobile">Mobile Number (For WhatsApp / SMS Notifications)</label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-phone-call input-icon"></i>
                                <input
                                    type="tel"
                                    class="form-control input-with-label"
                                    name="mobile"
                                    id="mobile"
                                    placeholder="10-digit mobile number"
                                    required>
                            </div>
                        </div>

                        <!-- Terms & Agreement Checkbox -->
                        <div class="form-group form-link-row">
                            <label class="remember-wrap">
                                <input type="checkbox" id="agreeTerms" required checked>
                                <span class="remember-label">I agree to the <a href="https://blumox.com/terms-and-conditions/" target="_blank">Terms of Service</a> and <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy Policy</a></span>
                            </label>
                        </div>

                        <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="">
                        <input type="hidden" name="type" value="auth">
                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="userType" value="administrator">
                        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                        <button type="submit" id="registerBtn" class="button theme-button">
                            <span class="btn-spinner"></span>
                            <span class="btn-text">Continue to Workspace Setup</span>
                            <i class="fi fi-rr-arrow-right btn-arrow"></i>
                        </button>

                        <div class="form-group register-row">
                            <p>Already have an account? <a href="/login/">Sign In</a></p>
                        </div>
                    </form>
                </div>

                <!-- Legal Notice -->
                <div class="legal">
                    <p>Protected by enterprise bank-grade security protocols &bull; 256-bit SSL Encryption</p>
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