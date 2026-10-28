<?php

/**
 * V3 Executive Registration Page
 * Tailored dynamically based on the current domain app (Aleya, E-Commerce, Gift Studio)
 * Path: /app/dashboard/v3/default/desktop/register.php
 */

$redirect_url = isset($redirect_url) && !empty($redirect_url) ? $redirect_url : '/website-setup/';

if (isset($_COOKIE['isLogin']) && isset($_COOKIE['user_data'])) {
    header("Location: /dashboard/");
    exit();
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int)SITE_ID : 1;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Blumox Platform';
$siteLogo = defined('SITE_LOGO') && !empty(SITE_LOGO) ? SITE_LOGO : '';
$siteIcon = defined('SITE_ICON') && !empty(SITE_ICON) ? SITE_ICON : '';
$appVersion = defined('ALEYA_APP_VERSION') ? ALEYA_APP_VERSION : (defined('SITE_VERSION') ? SITE_VERSION : '3.0');
$jqueryJs = defined('JS_LIBRARY') ? JS_LIBRARY . 'jquery.min.js' : 'https://code.jquery.com/jquery-3.7.1.min.js';
$sweetAlertJs = defined('PLUGIN_LIBRARY') ? PLUGIN_LIBRARY . 'sweetalert/sweetalert.min.js' : 'https://cdn.jsdelivr.net/npm/sweetalert2@11';

// Current Domain App
$defaultApp = defined('SITE_APP') ? strtolower(trim(SITE_APP)) : (isset($_GET['app']) ? strtolower(trim($_GET['app'])) : 'aleya');
if (!in_array($defaultApp, ['aleya', 'ecom', 'ecommerce', 'ecom_gift', 'e_com_gift'])) {
    $defaultApp = 'aleya';
}
if ($defaultApp === 'ecommerce') $defaultApp = 'ecom';
if ($defaultApp === 'e_com_gift') $defaultApp = 'ecom_gift';

$isAleya = ($defaultApp === 'aleya');

// Default Aleya Logos
$aleyaLogo = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_logo.png';
$aleyaWhiteLogo = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_white_logo.png';
$aleyaFavicon = '/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png';

$panelLogo = $isAleya ? $aleyaLogo : (!empty($siteLogo) ? $siteLogo : $aleyaLogo);
$pageIcon = $isAleya ? $aleyaFavicon : (!empty($siteIcon) ? $siteIcon : $aleyaFavicon);
$brandTitle = $isAleya ? 'Aleya' : $siteName;

// Showcase content tailored to each app
$appShowcaseData = [
    'aleya' => [
        'badge'       => 'Travel & Hospitality ERP',
        'heading'     => 'The Operating System for Travel, Spa & Transport.',
        'subheading'  => 'Aleya automates day-wise tour proposals, vehicle dispatch, spa bookings, WhatsApp follow-ups, and payment tracking.',
        'features'    => [
            ['icon' => 'fi fi-rr-route', 'title' => 'Tour Packages & Itinerary Engine', 'desc' => 'Generate beautiful day-wise proposals, vouchers and itineraries in seconds.'],
            ['icon' => 'fi fi-rr-taxi', 'title' => 'Transporter & Fleet Operations', 'desc' => 'Manage cabs, drivers, vehicle assignments, duty slips and fuel tracking.'],
            ['icon' => 'fi fi-rr-sparkles', 'title' => 'Spa & Wellness Management', 'desc' => 'Seamless appointment slots, therapist scheduling, treatment packages and guest billing.'],
            ['icon' => 'fi fi-rr-paper-plane', 'title' => 'Multi-Channel Lead Follow-ups', 'desc' => 'Auto-capture leads from Meta Ads, Google & website with instant WhatsApp reminders.']
        ],
        'stat_value'  => '500+',
        'stat_label'  => 'Travel agencies, spas & transporters scaled with Aleya'
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

$activeShowcase = $appShowcaseData[$defaultApp] ?? $appShowcaseData['aleya'];

// Categories map tailored specifically to current domain app
$appCategories = [
    'aleya' => [
        ['val' => 'travel_agency', 'label' => 'Travel Agency & Tour Operator'],
        ['val' => 'transporter', 'label' => 'Transporter & Fleet Operator (Cabs, Coaches, Rentals)'],
        ['val' => 'spa_wellness', 'label' => 'Spa, Wellness & Retreat Center'],
        ['val' => 'dmc', 'label' => 'Destination Management Company (DMC)'],
        ['val' => 'hotel_resort', 'label' => 'Hotel, Resort & Homestay'],
        ['val' => 'adventure_tours', 'label' => 'Adventure, Trekking & Safari Organizer'],
        ['val' => 'sightseeing_excursions', 'label' => 'Day Tours & Sightseeing Excursions'],
        ['val' => 'corporate_mice', 'label' => 'Corporate Travel & MICE Organizers'],
        ['val' => 'other_travel', 'label' => 'Other Travel & Hospitality Business'],
    ],
    'ecom' => [
        ['val' => 'fashion', 'label' => 'Fashion & Apparel'],
        ['val' => 'electronics', 'label' => 'Electronics & Gadgets'],
        ['val' => 'beauty', 'label' => 'Beauty & Cosmetics'],
        ['val' => 'home', 'label' => 'Home Decor & Living'],
        ['val' => 'grocery', 'label' => 'Grocery & Gourmet'],
        ['val' => 'general', 'label' => 'General Merchandise'],
    ],
    'ecom_gift' => [
        ['val' => 'bakery_cakes', 'label' => 'Bakery, Cakes & Desserts'],
        ['val' => 'florist_flowers', 'label' => 'Florist & Fresh Flowers'],
        ['val' => 'custom_hampers', 'label' => 'Personalised Hampers & Gifts'],
        ['val' => 'sweets_chocolates', 'label' => 'Artisanal Chocolates & Sweets'],
        ['val' => 'occasions_decor', 'label' => 'Occasions & Party Supplies'],
    ],
];

$activeCategories = $appCategories[$defaultApp] ?? $appCategories['aleya'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Create Your Workspace &bull; <?php echo htmlspecialchars($brandTitle); ?></title>
    <link rel="icon" href="<?php echo htmlspecialchars($pageIcon); ?>">

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
                <!-- Top Brand Showcase Header -->
                <div class="showcase-header">
                    <div class="showcase-brand-pill">
                        <span class="live-status-dot"></span>
                        <span class="showcase-badge-text" id="showcaseBadge"><?php echo htmlspecialchars($activeShowcase['badge']); ?></span>
                    </div>
                </div>

                <!-- Main Promotional Copy -->
                <div class="showcase-hero-text">
                    <h1 class="showcase-title" id="showcaseTitle"><?php echo htmlspecialchars($activeShowcase['heading']); ?></h1>
                    <p class="showcase-description" id="showcaseDesc"><?php echo htmlspecialchars($activeShowcase['subheading']); ?></p>
                </div>

                <!-- Feature Highlighting Cards -->
                <div class="showcase-feature-list" id="showcaseFeatures">
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
                        <strong class="stat-number" id="showcaseStatValue"><?php echo htmlspecialchars($activeShowcase['stat_value']); ?></strong>
                        <span class="stat-caption" id="showcaseStatLabel"><?php echo $activeShowcase['stat_label']; ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ======================================================= -->
        <!-- RIGHT FORM SECTION (Focused, Clean & App-Tailored) -->
        <!-- ======================================================= -->
        <main class="auth-form-panel">
            <div class="auth-form-container auth-form-container-wide">
                <!-- Brand Logo -->
                <div class="logo">
                    <img src="<?php echo htmlspecialchars($panelLogo); ?>" alt="<?php echo htmlspecialchars($brandTitle); ?>">
                </div>

                <!-- Auth Card -->
                <div class="v3-auth-card">
                    <div class="card-body">
                        <!-- Heading -->
                        <div class="form-heading">
                            <?php if ($defaultApp === 'aleya'): ?>
                                <strong>Create Your Workspace</strong>
                                <span>Set up your travel agency, spa, or fleet in under 2 minutes</span>
                            <?php elseif ($defaultApp === 'ecom_gift'): ?>
                                <strong>Create Your Gift Studio</strong>
                                <span>Set up your bakery, florist, or custom hamper workspace</span>
                            <?php else: ?>
                                <strong>Launch Your Storefront</strong>
                                <span>Set up your online store and inventory in minutes</span>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Live Alert Banner -->
                        <div id="authAlertBox" class="v3-auth-alert">
                            <i id="authAlertIcon" class="fi fi-rr-exclamation"></i>
                            <span id="authAlertText"></span>
                        </div>

                        <!-- Registration Form -->
                        <form id="register" autocomplete="off">
                            <input type="hidden" name="app_type" id="selectedAppType" value="<?php echo htmlspecialchars($defaultApp); ?>">

                            <!-- App-Tailored Business Name Field -->
                            <div class="form-group">
                                <label class="auth-label" id="lblBusinessName" for="business_name">
                                    <?php
                                    if ($defaultApp === 'aleya') {
                                        echo 'Agency / Business Name';
                                    } elseif ($defaultApp === 'ecom_gift') {
                                        echo 'Gift Studio / Bakery Name';
                                    } else {
                                        echo 'Store / Brand Name';
                                    }
                                    ?>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="<?php echo ($defaultApp === 'aleya') ? 'fi fi-rr-briefcase' : (($defaultApp === 'ecom_gift') ? 'fi fi-rr-gift' : 'fi fi-rr-shop'); ?> input-icon" id="iconBusiness"></i>
                                    <input
                                        type="text"
                                        class="form-control input-with-label"
                                        name="business_name"
                                        id="business_name"
                                        placeholder="<?php echo ($defaultApp === 'aleya') ? 'e.g. Skyline Holidays & Travels / Royal Cabs / Lotus Spa' : (($defaultApp === 'ecom_gift') ? 'e.g. Sweet Occasions Studio' : 'e.g. Apex E-Commerce'); ?>"
                                        required
                                        autofocus>
                                </div>
                            </div>

                            <!-- App-Tailored Category Selector -->
                            <div class="form-group">
                                <label class="auth-label" id="lblCategory" for="category">Business Category</label>
                                <div class="input-icon-wrap">
                                    <i class="fi fi-rr-apps input-icon"></i>
                                    <select class="form-control input-with-label" name="category" id="category">
                                        <?php foreach ($activeCategories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat['val']); ?>"><?php echo htmlspecialchars($cat['label']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Contact Details Row (First & Last Name) -->
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
                                <label class="auth-label" for="mobile">Mobile Number (WhatsApp & SMS Notifications)</label>
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
                                <span class="btn-text">Create Free Workspace</span>
                                <i class="fi fi-rr-arrow-right btn-arrow"></i>
                            </button>

                            <div class="form-group register-row">
                                <p>Already have an account? <a href="/login/">Sign In</a></p>
                            </div>
                        </form>
                    </div>

                    <!-- Legal Notice & Version -->
                    <div class="auth-footer-meta">
                        <p class="auth-version-text">v 3.0</p>
                    </div>
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