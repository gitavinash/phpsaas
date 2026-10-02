<?php

/**
 * V3 Executive Onboarding & Workspace Setup
 * Path: /app/dashboard/v3/default/desktop/website-setup.php
 * Strictly adheres to V3 Agent Rules: External CSS, External JS, API-based DB communications
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

$bizName = '';
if (!empty($_GET['biz'])) {
    $decodedBiz = base64_decode($_GET['biz']);
    $bizName = ($decodedBiz !== false && strlen($decodedBiz) > 0) ? $decodedBiz : trim($_GET['biz']);
} elseif (!empty($_SESSION['business_name'])) {
    $bizName = $_SESSION['business_name'];
} elseif (!empty($_SESSION['site_name'])) {
    $bizName = $_SESSION['site_name'];
}

// Active App Determination
$appParam = isset($_GET['app']) ? strtolower(trim($_GET['app'])) : (defined('SITE_APP') ? strtolower(SITE_APP) : 'aleya');
if (!in_array($appParam, ['aleya', 'ecom', 'ecommerce', 'ecom_gift', 'e_com_gift', 'spa', 'travel'])) {
    $appParam = 'aleya';
}
if ($appParam === 'ecommerce') $appParam = 'ecom';
if ($appParam === 'e_com_gift') $appParam = 'ecom_gift';

$appConfig = [
    'aleya' => [
        'name'        => 'Aleya Travel CRM & ERP',
        'badge'       => 'Travel Agency, Tour Operator & Transporter',
        'icon'        => 'fi fi-rr-plane-departure',
        'placeholder' => 'my-travels',
        'subdomain'   => '.aleya.in'
    ],
    'ecom' => [
        'name'        => 'E-Commerce Storefront',
        'badge'       => 'Online Store & Products',
        'icon'        => 'fi fi-rr-shopping-bag',
        'placeholder' => 'my-store',
        'subdomain'   => '.isw.one'
    ],
    'ecom_gift' => [
        'name'        => 'Gift & Bakery Studio',
        'badge'       => 'Custom Gifting & Deliveries',
        'icon'        => 'fi fi-rr-gift',
        'placeholder' => 'my-giftstudio',
        'subdomain'   => '.isw.one'
    ]
];

$activeApp = $appConfig[$appParam] ?? $appConfig['aleya'];
$jqueryJs = defined('JS_LIBRARY') ? JS_LIBRARY . 'jquery.min.js' : 'https://code.jquery.com/jquery-3.7.1.min.js';
$appVersion = defined('ALEYA_APP_VERSION') ? ALEYA_APP_VERSION : '3.0';

$cssRelPath = '/app/dashboard/v3/default/desktop/assets/css/website-setup.css';
$jsRelPath = '/app/dashboard/v3/default/desktop/assets/js/website-setup.js';
$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 5);
$cssVer = file_exists($docRoot . $cssRelPath) ? filemtime($docRoot . $cssRelPath) : time();
$jsVer = file_exists($docRoot . $jsRelPath) ? filemtime($docRoot . $jsRelPath) : time();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Workspace Onboarding &bull; <?php echo htmlspecialchars($activeApp['name']); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssRelPath . '?v=' . $cssVer); ?>">

    <?php
    if (defined('PWA') && file_exists(PWA . 'header.php')) {
        require_once PWA . 'header.php';
    }
    ?>
</head>

<body class="setup-page-wrapper">

    <!-- Header Bar -->
    <header class="setup-header-bar">
        <a href="/" class="setup-brand-link" aria-label="Homepage">
            <img src="/app/aleya/visitor/aleya/desktop/assets/images/aleya_logo.png" alt="Aleya Logo" class="setup-brand-logo">
        </a>
        <div class="setup-header-meta">
            <span>Already have a workspace?</span>
            <a href="/login/" class="setup-auth-link">Sign In</a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="setup-container">

        <!-- Stepper Progress Tracker -->
        <div class="setup-stepper-wrap">
            <div class="setup-stepper-meta">
                <span id="stepLabel" class="setup-step-count">Step 1 of 3</span>
                <span class="setup-app-badge"><?php echo htmlspecialchars($activeApp['name']); ?></span>
            </div>
            <div class="setup-progress-track">
                <div id="progressBarFill" class="setup-progress-fill"></div>
            </div>
            <div class="setup-step-pills">
                <div class="setup-step-pill active" id="pillStep1">
                    <span class="pill-num">1</span>
                    <span>Brand & Domain</span>
                </div>
                <div class="setup-step-pill" id="pillStep2">
                    <span class="pill-num">2</span>
                    <span>Tour Packages</span>
                </div>
                <div class="setup-step-pill" id="pillStep3">
                    <span class="pill-num">3</span>
                    <span>Plan & Launch</span>
                </div>
            </div>
        </div>

        <!-- Main Card Shell -->
        <div class="setup-main-card">

            <!-- Live Alert Banner -->
            <div id="setupAlert" class="setup-alert-banner" role="alert">
                <i class="fi fi-rr-exclamation"></i>
                <span id="setupAlertText"></span>
            </div>

            <!-- Form -->
            <form id="formWebsiteSetup" autocomplete="off" novalidate>
                <input type="hidden" id="setupEmail" name="setup_email" value="<?php echo htmlspecialchars($setupEmail); ?>">
                <input type="hidden" id="setupAppType" name="app_type" value="<?php echo htmlspecialchars($appParam); ?>">

                <!-- ==========================================
                     STEP 1: Brand & Domain Configuration
                     ========================================== -->
                <div class="setup-step-panel active" id="setupStep1">
                    <div class="setup-panel-header">
                        <h2 class="setup-panel-title">Configure Your Workspace Domain</h2>
                        <p class="setup-panel-desc" id="step1Desc">
                            <?php if (!empty($bizName)): ?>
                                Setting up workspace for <strong id="agencyNameDisplay" class="setup-highlight-text"><?php echo htmlspecialchars($bizName); ?></strong>. Choose your unique subdomain and operating preferences.
                            <?php else: ?>
                                Setting up workspace for <strong id="agencyNameDisplay" class="setup-highlight-text">your agency</strong>. Choose your unique subdomain and operating preferences.
                            <?php endif; ?>
                        </p>
                    </div>

                    <input type="hidden" id="setupBizName" name="business_name" value="<?php echo htmlspecialchars($bizName); ?>">

                    <div class="setup-field-group">
                        <div class="setup-label-row">
                            <label class="setup-label" for="setupSubdomain">Workspace Subdomain URL</label>
                            <span class="setup-label-hint">Live availability checker</span>
                        </div>
                        <div class="setup-domain-input-group" id="domainInputGroup">
                            <i class="fi fi-rr-globe setup-input-icon"></i>
                            <input
                                type="text"
                                class="setup-domain-text-input"
                                id="setupSubdomain"
                                name="subdomain"
                                placeholder="<?php echo htmlspecialchars($activeApp['placeholder']); ?>"
                                required
                                autofocus
                                autocomplete="off"
                                spellcheck="false">
                            <span class="setup-domain-suffix"><?php echo htmlspecialchars($activeApp['subdomain']); ?></span>
                        </div>
                        <!-- Live Subdomain Availability Status Container -->
                        <div id="subdomainFeedback" class="subdomain-feedback-wrap">
                            <i id="subdomainFeedbackIcon" class="feedback-icon fi fi-rr-info"></i>
                            <span id="subdomainFeedbackText" class="feedback-text">Enter 3+ characters to verify URL availability</span>
                        </div>
                    </div>

                    <div class="setup-field-group">
                        <label class="setup-label" for="setupCurrency">Operating Currency</label>
                        <div class="setup-input-wrap">
                            <i class="fi fi-rr-coins setup-input-icon"></i>
                            <select class="setup-select-input" id="setupCurrency" name="currency">
                                <option value="INR" selected>₹ INR - Indian Rupee</option>
                                <option value="USD">$ USD - US Dollar</option>
                                <option value="EUR">€ EUR - Euro</option>
                                <option value="AED">د.إ AED - UAE Dirham</option>
                                <option value="GBP">£ GBP - British Pound</option>
                            </select>
                        </div>
                    </div>

                    <div class="setup-field-group">
                        <div class="setup-label-row">
                            <label class="setup-label">Primary Brand Theme Color</label>
                            <span class="setup-label-hint">Click a palette to choose from curated color shades</span>
                        </div>

                        <!-- Hidden input storing selected hex for form submission -->
                        <input type="hidden" id="selectedThemeColor" name="theme_color" value="#00647a">

                        <div class="setup-color-picker-box">
                            <!-- Base Color Family Selector -->
                            <div class="setup-family-picker-row" id="colorFamilyPicker" role="radiogroup" aria-label="Brand Color Families">
                                <button type="button" class="color-family-btn active" data-family="teal" title="Aleya Teal">
                                    <span class="family-swatch-circle swatch-family-teal"></span>
                                    <span class="family-label">Teal</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="blue" title="Ocean Blue">
                                    <span class="family-swatch-circle swatch-family-blue"></span>
                                    <span class="family-label">Blue</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="indigo" title="Royal Indigo">
                                    <span class="family-swatch-circle swatch-family-indigo"></span>
                                    <span class="family-label">Indigo</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="emerald" title="Emerald Green">
                                    <span class="family-swatch-circle swatch-family-emerald"></span>
                                    <span class="family-label">Emerald</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="rose" title="Rose Red">
                                    <span class="family-swatch-circle swatch-family-rose"></span>
                                    <span class="family-label">Rose</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="amber" title="Sunset Amber">
                                    <span class="family-swatch-circle swatch-family-amber"></span>
                                    <span class="family-label">Amber</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="purple" title="Violet Purple">
                                    <span class="family-swatch-circle swatch-family-purple"></span>
                                    <span class="family-label">Purple</span>
                                </button>
                                <button type="button" class="color-family-btn" data-family="slate" title="Charcoal Slate">
                                    <span class="family-swatch-circle swatch-family-slate"></span>
                                    <span class="family-label">Slate</span>
                                </button>
                            </div>

                            <!-- Color Shades Selector Section -->
                            <div class="setup-shades-section">
                                <div class="shades-header-meta">
                                    <span class="shades-header-title">Color Shades</span>
                                    <div class="active-shade-pill" id="activeShadePill">
                                        <span class="active-shade-dot" id="activeShadeDot"></span>
                                        <span id="activeShadeName">Teal 700</span>
                                        <span class="active-shade-hex" id="activeShadeHex">#00647a</span>
                                    </div>
                                </div>
                                <div class="setup-shades-palette-grid" id="shadesPaletteGrid" role="radiogroup" aria-label="Color Shades">
                                    <!-- Dynamic shades populated by JS -->
                                </div>
                            </div>

                            <!-- Custom Hex & Spectrum Color Picker -->
                            <div class="setup-custom-color-row">
                                <label for="nativeColorPicker" class="btn-custom-color" id="btnCustomColorPicker" title="Fine-tune with color spectrum">
                                    <i class="fi fi-rr-palette"></i>
                                    <span>Custom Spectrum</span>
                                    <input type="color" id="nativeColorPicker" class="setup-hidden-native-picker" value="#00647a">
                                </label>
                                <div class="setup-hex-box" title="Direct Hex Code Input">
                                    <span class="setup-hex-prefix">#</span>
                                    <input type="text" id="setupHexInput" class="setup-hex-field" maxlength="7" value="00647a" placeholder="00647a" spellcheck="false">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     STEP 2: Tour Package Catalog (Aley                <!-- ==========================================
                     STEP 2: Tour Package Catalog (Aleya App)
                     ========================================== -->
                <div class="setup-step-panel" id="setupStep2">
                    <div class="setup-panel-header">
                        <h2 class="setup-panel-title">Master Tour Packages & Itineraries</h2>
                        <p class="setup-panel-desc">Aleya provides ready-to-sell master tour packages with day-wise itineraries, hotel classifications, and pricing. You can customize them anytime in your dashboard.</p>
                    </div>

                    <!-- Copy Option Toggle Card -->
                    <label class="tour-copy-toggle-card checked" for="toggleCopyTourPackages">
                        <div class="tour-copy-icon">
                            <i class="fi fi-rr-route"></i>
                        </div>
                        <div class="tour-copy-info">
                            <div class="tour-copy-title-row">
                                <h3 class="tour-copy-title">Copy Master Tour Package Catalog to My Site</h3>
                                <span class="tour-copy-badge">122+ Itineraries Ready</span>
                            </div>
                            <p class="tour-copy-sub">Instantly clone popular domestic and international itineraries, sightseeing points, and rates into your workspace.</p>
                            <div class="tour-features-row">
                                <span class="tour-feature-pill"><i class="fi fi-rr-check"></i> Day-wise Itineraries</span>
                                <span class="tour-feature-pill"><i class="fi fi-rr-check"></i> Hotel Classifications</span>
                                <span class="tour-feature-pill"><i class="fi fi-rr-check"></i> Instant WhatsApp Quotes</span>
                            </div>
                        </div>
                        <div class="tour-copy-checkbox-wrap">
                            <input type="checkbox" id="toggleCopyTourPackages" name="copy_tour_packages" value="1" checked class="tour-checkbox">
                        </div>
                    </label>

                    <!-- Interactive Destination Selector -->
                    <div id="destinationsContainer" class="destinations-wrapper">
                        <div class="destinations-toolbar">
                            <div class="dest-tabs-group">
                                <button type="button" id="tabDomestic" class="dest-tab-btn active">
                                    <i class="fi fi-rr-map-marker"></i>
                                    <span>Domestic</span>
                                    <span class="dest-count-pill" id="domesticBadge">12</span>
                                </button>
                                <button type="button" id="tabInternational" class="dest-tab-btn">
                                    <i class="fi fi-rr-plane"></i>
                                    <span>International</span>
                                    <span class="dest-count-pill" id="intlBadge">10</span>
                                </button>
                            </div>

                            <!-- Live Search Input -->
                            <div class="dest-search-wrap">
                                <i class="fi fi-rr-search dest-search-icon"></i>
                                <input type="text" id="destSearchInput" class="dest-search-input" placeholder="Search destination (e.g. Manali, Bali, Goa)..." autocomplete="off">
                            </div>

                            <div class="dest-actions-group">
                                <button type="button" id="btnSelectAllDest" class="dest-btn-action">Select All</button>
                                <button type="button" id="btnClearAllDest" class="dest-btn-action">Clear</button>
                            </div>
                        </div>

                        <!-- Active Selection Status Bar -->
                        <div class="dest-selection-status-bar">
                            <span class="dest-selected-count-label">
                                <i class="fi fi-rr-check-circle"></i>
                                <span id="destSelectedCountText">15 destinations selected</span>
                            </span>
                            <span class="dest-catalog-stats" id="destCatalogStats">122 ready tour packages</span>
                        </div>

                        <div id="destinationsGrid" class="destinations-grid">
                            <!-- Populated dynamically via JS from /api/v3/packages/destinations/ -->
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     STEP 3: Plan Selection & Payment
                     ========================================== -->
                <div class="setup-step-panel" id="setupStep3">
                    <div class="setup-panel-header">
                        <h2 class="setup-panel-title">Choose Your Workspace Subscription</h2>
                        <p class="setup-panel-desc">Plans are loaded dynamically from the platform. Select an edition to activate your administrator dashboard.</p>
                    </div>

                    <!-- Dynamic Packages Grid from isw_package -->
                    <div id="packagesGrid" class="packages-grid">
                        <!-- Populated dynamically via JS from /api/v3/packages/app-packages/ -->
                    </div>

                    <!-- Order Summary Box -->
                    <div class="setup-order-summary-box">
                        <div class="order-summary-head">
                            <h3 class="order-summary-title">Payment & Subscription Summary</h3>
                            <span class="setup-app-badge"><i class="fi fi-rr-shield-check"></i> 100% Secure Checkout</span>
                        </div>
                        <div class="summary-rows">
                            <div class="summary-row">
                                <span>Selected Edition:</span>
                                <strong id="summaryPlanTitle">CRM Starter</strong>
                            </div>
                            <div class="summary-row">
                                <span>Plan Subscription Fee:</span>
                                <span id="summaryPlanFee">₹1,499</span>
                            </div>
                            <div class="summary-row">
                                <span>GST (18%):</span>
                                <span id="summaryGst">₹270</span>
                            </div>
                            <div class="summary-row total-row">
                                <span>Total Payable Today:</span>
                                <span class="total-amount" id="summaryTotal">₹1,769</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls Bar -->
                <div class="setup-controls-bar">
                    <button type="button" id="btnPrevStep" class="setup-btn-prev hidden">
                        <i class="fi fi-rr-arrow-left"></i>
                        <span>Previous</span>
                    </button>

                    <button type="button" id="btnNextStep" class="setup-btn-next">
                        <span>Continue</span>
                        <i class="fi fi-rr-arrow-right"></i>
                    </button>

                    <button type="submit" id="btnSubmitSetup" class="setup-btn-submit hidden">
                        <i class="fi fi-rr-credit-card"></i>
                        <span>Pay Now &bull; <span id="btnSubmitAmount"></span></span>
                    </button>
                </div>

            </form>

            <!-- Loading & Provisioning Animation Overlay -->
            <div id="loadingOverlay" class="setup-loading-overlay">
                <div class="setup-spinner"></div>
                <h3 class="setup-loading-title">Setting Up Your Workspace</h3>
                <p id="loadingMsg" class="setup-loading-msg">Provisioning your new tenant site and assigning administrator privileges...</p>
            </div>

        </div>

    </main>

    <!-- External jQuery & Controller Script (No Inline JS) -->
    <script src="<?php echo htmlspecialchars($jqueryJs); ?>"></script>
    <script src="<?php echo htmlspecialchars($jsRelPath . '?v=' . $jsVer); ?>"></script>

    <?php
    if (defined('PWA') && file_exists(PWA . 'footer.php')) {
        require_once PWA . 'footer.php';
    }
    ?>
</body>

</html>