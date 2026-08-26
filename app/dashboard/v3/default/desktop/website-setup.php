<?php

/**
 * V3 Executive Onboarding & Workspace Setup
 * Path: /app/dashboard/v3/default/desktop/website-setup.php
 * Supports E-Commerce, Aleya, and E-Commerce Gift Workspaces
 */

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$setupEmail = '';

if (isset($_GET['setup_id']) && !empty($_GET['setup_id'])) {
    $setupEmail = base64_decode($_GET['setup_id']);
} elseif (isset($_SESSION['user_email'])) {
    $setupEmail = $_SESSION['user_email'];
} elseif (isset($_COOKIE['user_email'])) {
    $setupEmail = base64_decode($_COOKIE['user_email']);
}

// Active App Determination
$appParam = isset($_GET['app']) ? strtolower(trim($_GET['app'])) : (defined('SITE_APP') ? strtolower(SITE_APP) : 'ecom');
if (!in_array($appParam, ['ecom', 'ecommerce', 'aleya', 'ecom_gift', 'e_com_gift'])) {
    $appParam = 'ecom';
}
if ($appParam === 'ecommerce') $appParam = 'ecom';
if ($appParam === 'e_com_gift') $appParam = 'ecom_gift';

$appConfig = [
    'ecom' => [
        'name'        => 'E-Commerce Store',
        'badge'       => 'Online Storefront',
        'icon'        => 'fi fi-rr-shopping-bag',
        'color'       => '#4f46e5',
        'placeholder' => 'my-store',
        'sample_text' => 'Load 5 ready-to-sell demo products and categories',
        'subdomain'   => 'store.isw.one'
    ],
    'aleya' => [
        'name'        => 'Aleya Salon & Spa',
        'badge'       => 'Appointments & Wellness',
        'icon'        => 'fi fi-rr-sparkles',
        'color'       => '#e11d48',
        'placeholder' => 'my-salon',
        'sample_text' => 'Load sample salon services, stylist slots & packages',
        'subdomain'   => 'salon.isw.one'
    ],
    'ecom_gift' => [
        'name'        => 'Gift & Bakery Studio',
        'badge'       => 'Customized Orders & Gifting',
        'icon'        => 'fi fi-rr-gift',
        'color'       => '#059669',
        'placeholder' => 'my-giftstudio',
        'sample_text' => 'Load sample cakes, gift hampers, florist delivery options',
        'subdomain'   => 'gift.isw.one'
    ]
];

$activeAppConfig = $appConfig[$appParam] ?? $appConfig['ecom'];
$siteName = defined('SITE_NAME') ? SITE_NAME : 'ISW Platform';
$appVersion = defined('ALEYA_APP_VERSION') ? ALEYA_APP_VERSION : (defined('SITE_VERSION') ? SITE_VERSION : '3.0');
$jqueryJs = defined('JS_LIBRARY') ? JS_LIBRARY . 'jquery.min.js' : 'https://code.jquery.com/jquery-3.7.1.min.js';
$sweetAlertJs = defined('PLUGIN_LIBRARY') ? PLUGIN_LIBRARY . 'sweetalert/sweetalert.min.js' : 'https://cdn.jsdelivr.net/npm/sweetalert2@11';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Workspace Setup &bull; <?php echo htmlspecialchars($activeAppConfig['name']); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="/app/dashboard/v3/default/desktop/assets/css/auth.css">

    <?php 
    if (defined('PWA') && file_exists(PWA . 'header.php')) {
        require_once PWA . 'header.php';
    }
    ?>
</head>

<body class="v3-auth-body">
    <div class="v3-auth-container">
        <div class="v3-auth-box v3-auth-box-wide">
            <!-- Onboarding Card -->
            <div class="v3-auth-card">
                <!-- App Header Banner -->
                <div class="onboarding-app-banner">
                    <div class="onboarding-app-icon" style="background: <?php echo htmlspecialchars($activeAppConfig['color']); ?>20; color: <?php echo htmlspecialchars($activeAppConfig['color']); ?>;">
                        <i class="<?php echo htmlspecialchars($activeAppConfig['icon']); ?>"></i>
                    </div>
                    <div class="onboarding-app-meta">
                        <span class="onboarding-app-badge"><?php echo htmlspecialchars($activeAppConfig['badge']); ?></span>
                        <strong class="onboarding-app-title"><?php echo htmlspecialchars($activeAppConfig['name']); ?></strong>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Heading -->
                    <div class="form-heading">
                        <strong>Finish Setting Up Your Workspace</strong>
                        <span>Customize your domain, branding, and starter catalog</span>
                    </div>

                    <!-- Dynamic Live Alert Banner -->
                    <div id="authAlertBox" class="v3-auth-alert">
                        <i id="authAlertIcon" class="fi fi-rr-exclamation"></i>
                        <span id="authAlertText"></span>
                    </div>

                    <!-- Onboarding Multi-Step Form -->
                    <form id="formOnboarding" autocomplete="off">
                        <input type="hidden" name="setup_email" value="<?php echo htmlspecialchars($setupEmail); ?>">
                        <input type="hidden" name="app_type" value="<?php echo htmlspecialchars($appParam); ?>">

                        <!-- Business Name & Subdomain -->
                        <div class="form-group">
                            <label class="auth-label" for="onboardingBizName">Business / Brand Name</label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-shop input-icon"></i>
                                <input 
                                    type="text" 
                                    class="form-control input-with-label" 
                                    name="business_name" 
                                    id="onboardingBizName" 
                                    placeholder="e.g. <?php echo ($appParam === 'aleya') ? 'Glow Beauty Lounge' : (($appParam === 'ecom_gift') ? 'Sweet Occasions Bakery' : 'Apex Store'); ?>" 
                                    required 
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Subdomain Input with Domain Suffix Badge -->
                        <div class="form-group">
                            <label class="auth-label" for="onboardingSubdomain">Workspace Subdomain URL</label>
                            <div class="domain-input-group">
                                <input 
                                    type="text" 
                                    class="form-control input-with-label" 
                                    name="subdomain" 
                                    id="onboardingSubdomain" 
                                    placeholder="<?php echo htmlspecialchars($activeAppConfig['placeholder']); ?>" 
                                    required
                                >
                                <span class="domain-suffix-badge">.isw.one</span>
                            </div>
                        </div>

                        <!-- Currency Selector -->
                        <div class="form-group">
                            <label class="auth-label" for="onboardingCurrency">Default Store Currency</label>
                            <div class="input-icon-wrap">
                                <i class="fi fi-rr-dollar input-icon"></i>
                                <select class="form-control input-with-label" name="currency" id="onboardingCurrency">
                                    <option value="INR" selected>₹ INR - Indian Rupee</option>
                                    <option value="USD">$ USD - US Dollar</option>
                                    <option value="EUR">€ EUR - Euro</option>
                                    <option value="GBP">£ GBP - British Pound</option>
                                    <option value="AED">د.إ AED - UAE Dirham</option>
                                </select>
                            </div>
                        </div>

                        <!-- Brand Accent Color Swatches -->
                        <div class="form-group">
                            <label class="auth-label">Brand Primary Theme Color</label>
                            <div class="theme-swatch-picker">
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#4f46e5" checked>
                                    <span class="swatch-circle" style="background: #4f46e5;"></span>
                                </label>
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#059669">
                                    <span class="swatch-circle" style="background: #059669;"></span>
                                </label>
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#e11d48">
                                    <span class="swatch-circle" style="background: #e11d48;"></span>
                                </label>
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#7c3aed">
                                    <span class="swatch-circle" style="background: #7c3aed;"></span>
                                </label>
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#d97706">
                                    <span class="swatch-circle" style="background: #d97706;"></span>
                                </label>
                                <label class="color-swatch-item">
                                    <input type="radio" name="theme_color" value="#0f172a">
                                    <span class="swatch-circle" style="background: #0f172a;"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Pre-load Sample Catalog / Starter Content Checkbox -->
                        <div class="form-group form-link-row">
                            <label class="remember-wrap">
                                <input type="checkbox" name="load_samples" value="1" checked>
                                <span class="remember-label"><?php echo htmlspecialchars($activeAppConfig['sample_text']); ?></span>
                            </label>
                        </div>

                        <!-- Progress Animation Box (Hidden initially) -->
                        <div id="onboardingProgressBox" class="onboarding-progress-box" style="display: none;">
                            <div class="progress-item" id="pStep1">
                                <i class="fi fi-rr-check-circle"></i>
                                <span>Configuring <?php echo htmlspecialchars($activeAppConfig['name']); ?> workspace...</span>
                            </div>
                            <div class="progress-item" id="pStep2">
                                <i class="fi fi-rr-check-circle"></i>
                                <span>Generating security tokens & domain routes...</span>
                            </div>
                            <div class="progress-item" id="pStep3">
                                <i class="fi fi-rr-check-circle"></i>
                                <span>Preparing dashboard environment...</span>
                            </div>
                        </div>

                        <button type="submit" id="btnCompleteSetup" class="button theme-button">
                            <span class="btn-spinner"></span>
                            <span class="btn-text">Launch My Workspace</span>
                            <i class="fi fi-rr-rocket-lunch btn-arrow"></i>
                        </button>
                    </form>
                </div>

                <!-- Footer Legal Notice -->
                <div class="legal">
                    <p>Protected by enterprise bank-grade security protocols &bull; 256-bit SSL Encryption</p>
                </div>
            </div>

            <!-- Version Tag -->
            <p class="version">
                v <?php echo htmlspecialchars($appVersion); ?> &bull; Enterprise V3
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
