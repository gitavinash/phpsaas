<?php

/**
 * V3 Executive Login Page - Left Showcase & Right Passwordless Form Split Layout
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
$jqueryJs = defined('JS_LIBRARY') ? JS_LIBRARY . 'jquery.min.js' : 'https://code.jquery.com/jquery-3.7.1.min.js';

// Authentication Method: OTP or PASSWORD (defaults to OTP if not defined or empty)
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

// Default Aleya Brand Assets
$aleyaLogo = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_logo.png';
$aleyaWhiteLogo = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_white_logo.png';
$aleyaFavicon = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png';

// Determine App Model for tailored left promotional showcase
$rawApp = defined('SITE_APP') ? strtolower(SITE_APP) : 'aleya';
if (in_array($rawApp, ['ecommerce', 'ecom', 'store', 'shop'])) {
    $appKey = 'ecom';
} elseif (in_array($rawApp, ['ecom_gift', 'e_com_gift', 'gift', 'bakery'])) {
    $appKey = 'ecom_gift';
} else {
    $appKey = 'aleya';
}

$isAleya = ($appKey === 'aleya');
$panelLogo = !empty($siteLogo) ? $siteLogo : $aleyaLogo;
$pageIcon = !empty($siteIcon) ? $siteIcon : ($isAleya ? $aleyaFavicon : '');
$brandTitle = !empty($siteName) && $siteName !== 'ISW Platform' ? $siteName : ($isAleya ? 'Aleya' : $siteName);

$appShowcaseData = [
    'aleya' => [
        'badge'       => 'AI-Powered Business Suite',
        'heading'     => 'Your Business, On Autopilot.',
        'subheading'  => 'Aleya captures leads from Facebook, Instagram & Google — then automates follow-ups, tracks bookings, and grows your business.',
        'features'    => [
            ['icon' => 'fi fi-rr-bolt', 'title' => 'Multi-Channel Lead Capture', 'desc' => 'Instant capture from Meta Ads, Google campaigns & website forms.'],
            ['icon' => 'fi fi-rr-paper-plane', 'title' => 'Automated Follow-ups', 'desc' => 'Smart WhatsApp & email reminders that keep leads engaged.'],
            ['icon' => 'fi fi-rr-chart-line-up', 'title' => 'Bookings & Revenue Tracking', 'desc' => 'Live pipeline tracking, invoices, vouchers & conversion insights.']
        ],
        'stat_value'  => '100+',
        'stat_label'  => 'Businesses already growing with Aleya'
    ],
    'ecom' => [
        'badge'       => 'E-Commerce Cloud OS',
        'heading'     => 'Scale your storefront with speed & intelligence',
        'subheading'  => 'Manage product catalog, high-frequency customer orders, automated billing, and live store analytics.',
        'features'    => [
            ['icon' => 'fi fi-rr-shopping-bag', 'title' => 'Instant Checkout', 'desc' => 'High-conversion checkout with lightning-fast catalog search.'],
            ['icon' => 'fi fi-rr-chart-line-up', 'title' => 'Real-Time Insights', 'desc' => 'Live revenue metrics, order velocity, and customer cohorts.'],
            ['icon' => 'fi fi-rr-boxes', 'title' => 'Inventory & Billing', 'desc' => 'Automated stock management and instant digital tax invoices.']
        ],
        'stat_value'  => '99.99%',
        'stat_label'  => 'Uptime &bull; Powers modern digital storefronts'
    ],
    'ecom_gift' => [
        'badge'       => 'Gift & Bakery Studio',
        'heading'     => 'Craft unforgettable orders, cakes & celebration gifts',
        'subheading'  => 'Empower custom cake configurations, bespoke gift hampers, date-slot delivery, and greeting cards.',
        'features'    => [
            ['icon' => 'fi fi-rr-gift', 'title' => 'Hamper Customizer', 'desc' => 'Bespoke item mixing, packaging themes, and occasion selectors.'],
            ['icon' => 'fi fi-rr-clock-three', 'title' => 'Delivery Slot Dispatch', 'desc' => 'Pinpoint date and time slot scheduling with rider routing.'],
            ['icon' => 'fi fi-rr-cake-slice', 'title' => 'Flavor & Custom Modifiers', 'desc' => 'Eggless choices, weight options, and custom message cards.']
        ],
        'stat_value'  => '250,000+',
        'stat_label'  => 'Festive celebrations and hampers delivered'
    ]
];

$activeShowcase = $appShowcaseData[$appKey] ?? $appShowcaseData['aleya'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign In &bull; <?php echo htmlspecialchars($brandTitle); ?></title>
    <?php if (!empty($pageIcon)): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($pageIcon); ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="/app/dashboard/v3/default/desktop/assets/css/auth.css?v=<?php echo file_exists(__DIR__ . '/assets/css/auth.css') ? filemtime(__DIR__ . '/assets/css/auth.css') : '3.3'; ?>">

    <?php
    if (defined('GOOGLE_RECAPTCHA_GENERATOR') && file_exists(GOOGLE_RECAPTCHA_GENERATOR)) {
        require_once GOOGLE_RECAPTCHA_GENERATOR;
    }
    if (defined('PWA') && file_exists(PWA . 'header.php')) {
        require_once PWA . 'header.php';
    }
    ?>
</head>

<body class="v3-split-auth-body">
    <div class="v3-split-auth-layout">
        <!-- ======================================================= -->
        <!-- LEFT SHOWCASE HERO SECTION (App-Tailored & Animated) -->
        <!-- ======================================================= -->
        <aside class="auth-showcase-panel">
            <!-- Animated Ambient Gradient Spheres -->
            <div class="ambient-glow ambient-glow-1"></div>
            <div class="ambient-glow ambient-glow-2"></div>
            <div class="ambient-glow ambient-glow-3"></div>
            <div class="ambient-grid-overlay"></div>

            <div class="showcase-content-wrapper">
                <!-- Top Brand Showcase Header with Aleya Logo -->
                <div class="showcase-header">
                    <a href="https://aleya.in" class="showcase-brand-link" title="Aleya - Travel ERP Software">
                        <img src="<?php echo htmlspecialchars($aleyaWhiteLogo); ?>" alt="Aleya" class="showcase-brand-logo" height="28">
                    </a>
                    <div class="showcase-brand-pill">
                        <span class="live-status-dot"></span>
                        <span class="showcase-badge-text"><?php echo htmlspecialchars($activeShowcase['badge']); ?></span>
                    </div>
                </div>

                <!-- Main Promotional Copy -->
                <div class="showcase-hero-text">
                    <h1 class="showcase-title"><?php echo htmlspecialchars($activeShowcase['heading']); ?></h1>
                    <p class="showcase-description"><?php echo htmlspecialchars($activeShowcase['subheading']); ?></p>
                </div>

                <!-- Feature Highlighting Cards -->
                <div class="showcase-feature-list">
                    <?php foreach ($activeShowcase['features'] as $feat): ?>
                        <div class="showcase-feature-item">
                            <div class="feature-icon-box">
                                <i class="<?php echo htmlspecialchars($feat['icon']); ?>"></i>
                            </div>
                            <div class="feature-text-group">
                                <strong><?php echo htmlspecialchars($feat['title']); ?></strong>
                                <span><?php echo htmlspecialchars($feat['desc']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Stat Bar / Social Proof Pill -->
                <div class="showcase-footer-stat">
                    <div class="stat-highlight">
                        <strong class="stat-number"><?php echo htmlspecialchars($activeShowcase['stat_value']); ?></strong>
                        <span class="stat-caption"><?php echo $activeShowcase['stat_label']; ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ======================================================= -->
        <!-- RIGHT FORM SECTION (Minimal & Pixel-Perfect) -->
        <!-- ======================================================= -->
        <main class="auth-form-panel">
            <div class="auth-form-container">

                <!-- Form Card -->
                <div class="v3-auth-card">
                    <!-- Heading -->
                    <div class="form-heading">
                        <strong>Sign In</strong>
                        <span><?php echo ($siteAuthMethod === 'PASSWORD') ? 'Enter your credentials to access your workspace' : 'Enter your work email to access your workspace'; ?></span>
                    </div>

                    <!-- Dynamic Live Alert Banner -->
                    <div id="authAlertBox" class="v3-auth-alert">
                        <i id="authAlertIcon" class="fi fi-rr-exclamation"></i>
                        <span id="authAlertText"></span>
                    </div>

                    <?php if ($siteAuthMethod === 'PASSWORD'): ?>
                        <!-- Password Login Workflow -->
                        <div id="authPasswordSection" class="auth-section">
                            <form id="formPasswordLogin" autocomplete="on">
                                <div class="form-group">
                                    <label class="auth-label" for="loginIdentifier">Email or Username</label>
                                    <div class="input-icon-wrap">
                                        <i class="fi fi-rr-envelope input-icon"></i>
                                        <input
                                            type="text"
                                            class="form-control input-with-label"
                                            name="identifier"
                                            id="loginIdentifier"
                                            placeholder="name@company.com or username"
                                            required
                                            autofocus>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="label-link-row">
                                        <label class="auth-label" for="loginPassword">Password</label>
                                        <a href="/forgot-password/" class="auth-forgot-link" tabindex="-1">Forgot password?</a>
                                    </div>
                                    <div class="input-icon-wrap">
                                        <i class="fi fi-rr-lock input-icon"></i>
                                        <input
                                            type="password"
                                            class="form-control input-with-label input-with-toggle"
                                            name="password"
                                            id="loginPassword"
                                            placeholder="Enter your password"
                                            required
                                            autocomplete="current-password">
                                        <button type="button" class="btn-toggle-password" id="btnTogglePassword" aria-label="Toggle password visibility">
                                            <i class="fi fi-rr-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                                <button type="submit" id="btnLoginPassword" class="button theme-button">
                                    <span class="btn-spinner"></span>
                                    <span class="btn-text">Sign In</span>
                                    <i class="fi fi-rr-arrow-right btn-arrow"></i>
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Passwordless Email OTP Workflow -->
                        <div id="authOtpSection" class="auth-section">
                            <!-- Step A: Enter Email Address -->
                            <div id="paneOtpStepEmail" class="otp-pane">
                                <form id="formSendOtp" autocomplete="off">
                                    <div class="form-group">
                                        <label class="auth-label" for="otpEmail">Email Address</label>
                                        <div class="input-icon-wrap">
                                            <i class="fi fi-rr-envelope input-icon"></i>
                                            <input
                                                type="email"
                                                class="form-control input-with-label"
                                                name="email"
                                                id="otpEmail"
                                                placeholder="name@company.com"
                                                required
                                                autofocus>
                                        </div>
                                    </div>

                                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                                    <button type="submit" id="btnSendOtp" class="button theme-button">
                                        <span class="btn-spinner"></span>
                                        <span class="btn-text">Continue with Email</span>
                                        <i class="fi fi-rr-arrow-right btn-arrow"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Step B: Verify 6-Digit OTP Code -->
                            <div id="paneOtpStepVerify" class="otp-pane">
                                <div class="otp-sent-info">
                                    <div class="otp-sent-meta">
                                        <span class="otp-sent-label">Code sent to</span>
                                        <strong id="displayOtpTarget" class="otp-sent-email"></strong>
                                    </div>
                                    <a href="#" id="btnChangeOtpEmail" class="otp-edit-link">
                                        <i class="fi fi-rr-edit"></i> Edit
                                    </a>
                                </div>

                                <form id="formVerifyOtp" autocomplete="off">
                                    <div class="form-group">
                                        <label class="auth-label" for="otpCodeInput">6-Digit Verification Code</label>
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
                                                autocomplete="one-time-code"
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group form-link-row">
                                        <span class="otp-countdown-text">
                                            Didn't receive code?
                                            <a href="#" id="btnResendOtp" class="resend-link is-disabled">Resend in <span id="resendTimer">30</span>s</a>
                                        </span>
                                    </div>

                                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                                    <button type="submit" id="btnVerifyOtp" class="button theme-button">
                                        <span class="btn-spinner"></span>
                                        <span class="btn-text">Verify & Continue</span>
                                        <i class="fi fi-rr-check btn-arrow"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Legal & Version -->
                <div class="auth-footer-meta">
                    <p class="auth-legal-text">
                        By continuing, you agree to our <a href="https://blumox.com/terms-and-conditions/" target="_blank">Terms</a> &amp; <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy Policy</a>
                    </p>
                    <span class="auth-version-text">version 3.0</span>
                </div>
            </div>
        </main>
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