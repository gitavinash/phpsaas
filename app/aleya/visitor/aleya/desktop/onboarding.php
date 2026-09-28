<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />

    <!-- Meta Tags -->
    <title>Get Started — Launch Your Aleya Workspace | Travel Agency CRM & ERP</title>
    <meta name="title" content="Get Started — Launch Your Aleya Workspace" />
    <meta name="description" content="Launch your travel business on autopilot. Set up your agency CRM, itinerary builder, and automated lead follow-ups in minutes." />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>onboarding.css?v=<?php echo time(); ?>" />
</head>

<body class="onboarding-page">

    <!-- Minimal Header -->
    <header class="onboarding-header">
        <div class="onboarding-header-container">
            <a href="/" class="onboarding-logo" aria-label="Aleya Homepage">
                <img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt="Aleya" />
            </a>

            <div class="onboarding-header-right">
                <div class="trust-badge-pill">
                    <i class="fas fa-shield-halved"></i>
                    <span>256-Bit SSL Encrypted</span>
                </div>
                <div>
                    Already using Aleya? <a href="/login/" class="onboarding-login-link">Sign In</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="onboarding-main">
        <div class="onboarding-container">

            <!-- Progress Tracker -->
            <div class="onboarding-progress-wrapper">
                <div class="onboarding-progress-meta">
                    <span id="stepLabel" class="step-label">Step 1 of 4</span>
                    <span id="stepPercent" class="step-percent">25% Complete</span>
                </div>
                <div class="progress-track">
                    <div id="progressBarFill" class="progress-bar-fill"></div>
                </div>
                <div class="step-indicators">
                    <div class="step-indicator-item active" data-step="1">
                        <div class="step-num">1</div>
                        <span>Plan & Brand</span>
                    </div>
                    <div class="step-indicator-item" data-step="2">
                        <div class="step-num">2</div>
                        <span>Tour Destinations</span>
                    </div>
                    <div class="step-indicator-item" data-step="3">
                        <div class="step-num">3</div>
                        <span>Automation</span>
                    </div>
                    <div class="step-indicator-item" data-step="4">
                        <div class="step-num">4</div>
                        <span>Launch</span>
                    </div>
                </div>
            </div>

            <!-- Form Card Shell -->
            <div class="onboarding-card">

                <!-- Alert Message Banner -->
                <div id="onboardingAlert" class="onboarding-alert" role="alert"></div>

                <!-- Form -->
                <form id="onboardingForm" novalidate>

                    <!-- ==============================================
                         STEP 1: Brand Identity & Plan Selection
                         ============================================== -->
                    <div id="step-1" class="onboarding-step active">
                        <div class="step-header">
                            <h1>Name Your Travel Workspace</h1>
                            <p>Choose your workspace name and select the Aleya edition tailored for your business scale.</p>
                        </div>

                        <!-- Brand / Agency Name -->
                        <div class="form-group">
                            <label for="businessName" class="form-label">Agency / Business Name</label>
                            <input type="text" id="businessName" name="business_name" class="form-input"
                                placeholder="e.g. Skyline Holidays & Travel" required
                                data-error="Please enter your travel agency or business name." />
                            <div class="subdomain-preview-box">
                                <div class="preview-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="preview-details">
                                    <div class="preview-text-label">Your Secure Workspace URL</div>
                                    <div id="subdomainPreview" class="preview-url">youragency.aleya.in</div>
                                </div>
                                <div id="subdomainAvailabilityBadge" class="subdomain-status-badge hidden">
                                    <span id="subdomainStatusIcon"><i class="fas fa-circle-check"></i></span>
                                    <span id="subdomainStatusText">Available</span>
                                </div>
                            </div>
                            <input type="hidden" id="subdomainInput" name="subdomain" value="" />
                        </div>

                        <!-- Selected Plan Status Bar (Shown when High Plan ERP is selected) -->
                        <div id="step1ActivePlanBar" class="step1-active-plan-bar">
                            <div class="active-plan-badge-left">
                                <div class="active-plan-icon"><i class="fas fa-crown"></i></div>
                                <div class="active-plan-text">
                                    <div class="active-plan-title">
                                        <span class="active-plan-name">ERP Complete Operating System</span>
                                        <span class="active-plan-tag">All Automations Included</span>
                                    </div>
                                    <p class="active-plan-sub">Full-suite travel OS, AI itineraries, supplier vouchers, invoicing & UPI links.</p>
                                </div>
                            </div>
                            <button type="button" id="btnSwitchToCrm" class="btn-switch-plan">Switch to Starter</button>
                        </div>

                        <!-- Upgrade / Plan Price Section (Shown ONLY when lower plan CRM is selected) -->
                        <div id="step1PlanPriceSection" class="step1-plan-price-section hidden">
                            <div class="upgrade-section-header">
                                <div class="upgrade-pill-tag"><i class="fas fa-sparkles"></i> Recommended Upgrade</div>
                                <h3>Upgrade to Full ERP Operating System</h3>
                                <p>You selected CRM Starter. Upgrade to ERP Complete to unlock AI multi-day itinerary builder, supplier vouchers, and payment links.</p>
                            </div>

                            <!-- Billing Toggle -->
                            <div class="plan-toggle-row">
                                <button type="button" id="cycleYearly" class="plan-cycle-btn active">
                                    Annual <span class="save-badge">Save 25%</span>
                                </button>
                                <button type="button" id="cycleMonthly" class="plan-cycle-btn">
                                    Monthly
                                </button>
                            </div>

                            <!-- Plan Selection Cards -->
                            <div class="plan-cards-grid">
                                <!-- CRM Starter -->
                                <div id="planCardCrm" class="plan-card-item">
                                    <input type="radio" id="planRadioCrm" name="plan" value="crm" class="plan-radio-input" />
                                    <span class="plan-badge-tag basic">Current Selection</span>
                                    <h3 class="plan-title">CRM Starter</h3>
                                    <p class="plan-desc">For emerging agencies seeking organized lead management & follow-ups.</p>

                                    <div class="plan-price-wrap">
                                        <span class="plan-price-currency">₹</span>
                                        <span id="priceDisplayCrm" class="plan-price-number">1,499</span>
                                        <span id="periodDisplayCrm" class="plan-price-period">/mo (billed annually)</span>
                                    </div>

                                    <ul class="plan-features-list">
                                        <li><i class="fas fa-check"></i> Meta (FB/IG) & Google lead auto-capture</li>
                                        <li><i class="fas fa-check"></i> Smart follow-up pipeline & notifications</li>
                                        <li><i class="fas fa-check"></i> Executive performance dashboard</li>
                                        <li><i class="fas fa-check"></i> Up to 10 users included (+₹149/user after)</li>
                                    </ul>

                                    <div id="crmSelectIndicator" class="plan-select-indicator">Selected (Current Plan)</div>
                                </div>

                                <!-- ERP Complete (Featured Upgrade Option) -->
                                <div id="planCardErp" class="plan-card-item upgrade-card">
                                    <input type="radio" id="planRadioErp" name="plan" value="erp" class="plan-radio-input" />
                                    <span class="plan-badge-tag popular"><i class="fas fa-sparkles"></i> Recommended Upgrade</span>
                                    <h3 class="plan-title">ERP Complete</h3>
                                    <p class="plan-desc">Full-suite travel agency operating system with AI itinerary creation & billing.</p>

                                    <div class="plan-price-wrap">
                                        <span class="plan-price-currency">₹</span>
                                        <span id="priceDisplayErp" class="plan-price-number">3,999</span>
                                        <span id="periodDisplayErp" class="plan-price-period">/mo (billed annually)</span>
                                    </div>

                                    <ul class="plan-features-list">
                                        <li><i class="fas fa-check"></i> Everything in CRM Starter</li>
                                        <li><i class="fas fa-check"></i> AI-powered lead scoring & priority rank</li>
                                        <li><i class="fas fa-check"></i> Dynamic multi-day itinerary builder</li>
                                        <li><i class="fas fa-check"></i> Automated supplier vouchers & invoice generation</li>
                                        <li><i class="fas fa-check"></i> Instant payment links & UPI collect</li>
                                        <li><i class="fas fa-check"></i> Up to 10 users included (+₹349/user after)</li>
                                    </ul>

                                    <div id="erpSelectIndicator" class="plan-select-indicator upgrade-indicator">Upgrade to ERP Complete &rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         STEP 2: Tour Package Destinations
                         ============================================== -->
                    <div id="step-2" class="onboarding-step">
                        <div class="step-header">
                            <h2>Select Your Tour Destinations</h2>
                            <p>Select the domestic states and international destinations you cater to. Aleya will automatically migrate master tour packages, day-wise itineraries, and rates into your client website!</p>
                        </div>

                        <!-- Destination Tabs & Action Toolbar -->
                        <div class="dest-controls-bar">
                            <div class="dest-tabs-wrap">
                                <button type="button" id="tabDomestic" class="dest-tab-btn active">
                                    <i class="fas fa-map-location-dot"></i>
                                    <span>Domestic</span>
                                    <span class="dest-tab-count" id="countDomesticBadge">12</span>
                                </button>
                                <button type="button" id="tabInternational" class="dest-tab-btn">
                                    <i class="fas fa-globe"></i>
                                    <span>International</span>
                                    <span class="dest-tab-count" id="countIntlBadge">10</span>
                                </button>
                            </div>

                            <div class="dest-actions-wrap">
                                <div class="dest-stats-text">
                                    <span id="destSelectedCount" class="dest-selected-num">11</span> destinations selected (<span id="destTotalPackagesCount">88</span> master packages)
                                </div>
                                <div class="dest-quick-btns">
                                    <button type="button" id="btnSelectAllTab" class="dest-action-btn">Select All in Tab</button>
                                    <button type="button" id="btnDeselectAllTab" class="dest-action-btn">Clear Tab</button>
                                </div>
                            </div>
                        </div>

                        <!-- Domestic Destination Cards Grid -->
                        <div id="gridDomestic" class="dest-cards-grid active">
                            <label class="destination-card selected" data-dest-key="himachal-pradesh" data-category="domestic" data-packages="31">
                                <input type="checkbox" name="destinations[]" value="himachal-pradesh" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-mountain"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 31 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Himachal Pradesh</h3>
                                    <p class="dest-highlights">Manali, Shimla, Spiti Valley, Kasol</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="rajasthan" data-category="domestic" data-packages="27">
                                <input type="checkbox" name="destinations[]" value="rajasthan" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-fort-awesome"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 27 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Rajasthan</h3>
                                    <p class="dest-highlights">Jaipur, Udaipur, Jodhpur, Jaisalmer</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="kerala" data-category="domestic" data-packages="20">
                                <input type="checkbox" name="destinations[]" value="kerala" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-water"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 20 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Kerala</h3>
                                    <p class="dest-highlights">Munnar, Alleppey, Thekkady, Kovalam</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="uttarakhand" data-category="domestic" data-packages="6">
                                <input type="checkbox" name="destinations[]" value="uttarakhand" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-person-hiking"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 6 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Uttarakhand</h3>
                                    <p class="dest-highlights">Nainital, Mussoorie, Rishikesh, Corbett</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="goa" data-category="domestic" data-packages="4">
                                <input type="checkbox" name="destinations[]" value="goa" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-umbrella-beach"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 4 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Goa</h3>
                                    <p class="dest-highlights">North & South Goa Beaches, Cruises</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="ladakh-kashmir" data-category="domestic" data-packages="3">
                                <input type="checkbox" name="destinations[]" value="ladakh-kashmir" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-snowflake"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 3 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Ladakh & Kashmir</h3>
                                    <p class="dest-highlights">Leh, Pangong, Gulmarg, Srinagar</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="gujarat" data-category="domestic" data-packages="3">
                                <input type="checkbox" name="destinations[]" value="gujarat" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-shield-halved"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 3 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Gujarat</h3>
                                    <p class="dest-highlights">Rann of Kutch, Dwarka, Somnath</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="sikkim-darjeeling" data-category="domestic" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="sikkim-darjeeling" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-mountain-sun"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Sikkim & Darjeeling</h3>
                                    <p class="dest-highlights">Gangtok, Pelling, Tsomgo Lake</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="andaman-nicobar" data-category="domestic" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="andaman-nicobar" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-anchor"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Andaman & Nicobar</h3>
                                    <p class="dest-highlights">Havelock, Neil Island, Port Blair</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="meghalaya-northeast" data-category="domestic" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="meghalaya-northeast" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-leaf"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Meghalaya & Northeast</h3>
                                    <p class="dest-highlights">Shillong, Cherrapunji, Dawki</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="punjab" data-category="domestic" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="punjab" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-monument"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Punjab</h3>
                                    <p class="dest-highlights">Amritsar, Golden Temple, Wagah Border</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="golden-triangle-delhi" data-category="domestic" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="golden-triangle-delhi" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-landmark"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Golden Triangle / Delhi</h3>
                                    <p class="dest-highlights">Delhi, Agra, Fatehpur Sikri</p>
                                </div>
                            </label>
                        </div>

                        <!-- International Destination Cards Grid -->
                        <div id="gridInternational" class="dest-cards-grid">
                            <label class="destination-card selected" data-dest-key="vietnam" data-category="international" data-packages="3">
                                <input type="checkbox" name="destinations[]" value="vietnam" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-plane-departure"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 3 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Vietnam</h3>
                                    <p class="dest-highlights">Hanoi, Da Nang, Ha Long Bay</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="bali" data-category="international" data-packages="2">
                                <input type="checkbox" name="destinations[]" value="bali" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-sun"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 2 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Bali (Indonesia)</h3>
                                    <p class="dest-highlights">Ubud, Kuta, Seminyak, Nusa Penida</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="dubai" data-category="international" data-packages="2">
                                <input type="checkbox" name="destinations[]" value="dubai" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-city"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 2 Packages</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Dubai (UAE)</h3>
                                    <p class="dest-highlights">Burj Khalifa, Desert Safari, Marina</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="thailand" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="thailand" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-umbrella-beach"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Thailand</h3>
                                    <p class="dest-highlights">Phuket, Bangkok, Pattaya, Krabi</p>
                                </div>
                            </label>

                            <label class="destination-card selected" data-dest-key="maldives" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="maldives" checked class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-water"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Maldives</h3>
                                    <p class="dest-highlights">Overwater Villas, Male, Water Sports</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="japan" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="japan" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-torii-gate"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Japan</h3>
                                    <p class="dest-highlights">Tokyo, Kyoto, Mount Fuji, Osaka</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="singapore" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="singapore" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-building-columns"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Singapore</h3>
                                    <p class="dest-highlights">Marina Bay, Sentosa, Universal Studios</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="malaysia" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="malaysia" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-tower-observation"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Malaysia</h3>
                                    <p class="dest-highlights">Kuala Lumpur, Langkawi, Genting</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="nepal" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="nepal" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-mountain"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Nepal</h3>
                                    <p class="dest-highlights">Kathmandu, Pokhara, Nagarkot</p>
                                </div>
                            </label>

                            <label class="destination-card" data-dest-key="mauritius" data-category="international" data-packages="1">
                                <input type="checkbox" name="destinations[]" value="mauritius" class="dest-checkbox" />
                                <div class="dest-card-top">
                                    <div class="dest-icon-wrap"><i class="fas fa-compass"></i></div>
                                    <span class="dest-pkg-pill"><i class="fas fa-route"></i> 1 Package</span>
                                    <span class="dest-check-indicator"><i class="fas fa-check"></i></span>
                                </div>
                                <div class="dest-card-content">
                                    <h3>Mauritius</h3>
                                    <p class="dest-highlights">Port Louis, Belle Mare, Chamarel</p>
                                </div>
                            </label>
                        </div>

                        <!-- Active Team Size -->
                        <div class="form-group team-size-section">
                            <div class="team-size-header-wrap">
                                <div class="team-size-title-row">
                                    <label class="form-label team-size-heading">Active Team Size</label>
                                    <span class="team-size-plan-badge">
                                        <i class="fas fa-check-circle"></i> 10 Licenses Included in Plan
                                    </span>
                                </div>
                                <p class="team-size-subtext">Both CRM and ERP plans come standard with 10 full-featured agent seats. Need more? Easily add extra seats for your agency.</p>
                            </div>

                            <div class="team-size-group">
                                <label class="team-size-card selected">
                                    <input type="radio" name="team_size" value="1-10" checked />
                                    <div class="team-size-card-inner">
                                        <div class="team-size-card-top">
                                            <span class="team-size-icon-pill"><i class="fas fa-users"></i></span>
                                            <span class="team-size-status-pill included">10 Included</span>
                                        </div>
                                        <div class="team-size-card-info">
                                            <div class="team-size-card-title">Up to 10 Agents</div>
                                            <div class="team-size-card-desc">Standard plan covers up to 10 team seats with zero extra fee.</div>
                                        </div>
                                    </div>
                                </label>
                                <label class="team-size-card">
                                    <input type="radio" name="team_size" value="10+" />
                                    <div class="team-size-card-inner">
                                        <div class="team-size-card-top">
                                            <span class="team-size-icon-pill"><i class="fas fa-user-plus"></i></span>
                                            <span class="team-size-status-pill expandable">Expandable</span>
                                        </div>
                                        <div class="team-size-card-info">
                                            <div class="team-size-card-title">More than 10 Agents</div>
                                            <div class="team-size-card-desc">For larger travel agencies needing extra seats beyond the 10 included.</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Custom Team Size Input for 10+ -->
                            <div id="customTeamSizeWrap" class="custom-team-size-wrap hidden">
                                <label for="customTeamSizeInput" class="custom-team-size-label">
                                    <span>Exact Team Size (Total Agents)</span>
                                    <span class="custom-team-size-hint">10 included in plan + additional seats</span>
                                </label>
                                <div class="custom-team-size-field">
                                    <span class="custom-team-size-icon"><i class="fas fa-users"></i></span>
                                    <input type="number" id="customTeamSizeInput" name="custom_team_size" min="11" max="500" placeholder="e.g. 15" class="form-input custom-team-size-input" />
                                    <span class="custom-team-size-suffix">Agents</span>
                                </div>
                                <p id="customTeamSizeHelp" class="custom-team-size-help">
                                    <i class="fas fa-info-circle"></i> 10 licenses are included in your plan. Additional licenses will be configured.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         STEP 3: Core Features Activation
                         ============================================== -->
                    <div id="step-3" class="onboarding-step">
                        <div class="step-header">
                            <h2>Activate Core Automations</h2>
                            <p>Aleya will pre-load these integrations and templates into your workspace upon provisioning.</p>
                        </div>

                        <div class="feature-toggle-list">
                            <label class="feature-toggle-card">
                                <div class="feature-toggle-left">
                                    <div class="feature-toggle-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="feature-toggle-text">
                                        <h4>Instant Meta & Google Lead Ingestion</h4>
                                        <p>Automatically captures Facebook, Instagram, and Google leads into your pipeline within seconds.</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="module_lead_sync" checked class="switch-checkbox" />
                            </label>

                            <label class="feature-toggle-card">
                                <div class="feature-toggle-left">
                                    <div class="feature-toggle-icon">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div class="feature-toggle-text">
                                        <h4>WhatsApp & Email Quick-Responder</h4>
                                        <p>Trigger automated quotation acknowledgments and smart follow-up reminders to prospects.</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="module_whatsapp" checked class="switch-checkbox" />
                            </label>

                            <label class="feature-toggle-card">
                                <div class="feature-toggle-left">
                                    <div class="feature-toggle-icon">
                                        <i class="fas fa-map-location-dot"></i>
                                    </div>
                                    <div class="feature-toggle-text">
                                        <h4>Pre-Built Destination & Itinerary Templates</h4>
                                        <p>Includes ready-to-use day-wise templates for Kashmir, Goa, Kerala, Dubai, Thailand, and Europe.</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="module_templates" checked class="switch-checkbox" />
                            </label>

                            <label class="feature-toggle-card">
                                <div class="feature-toggle-left">
                                    <div class="feature-toggle-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="feature-toggle-text">
                                        <h4>GST Invoices & Instant UPI Payment Links</h4>
                                        <p>Generate professional client vouchers and collect bookings seamlessly via online links.</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="module_billing" checked class="switch-checkbox" />
                            </label>
                        </div>
                    </div>

                    <!-- ==============================================
                         STEP 4: Admin Account & Instant Launch
                         ============================================== -->
                    <div id="step-4" class="onboarding-step">
                        <div class="step-header">
                            <h2>Review & Launch Workspace</h2>
                            <p>Verify your workspace setup and enter administrator credentials to proceed with activation.</p>
                        </div>

                        <div class="step-4-grid">
                            <!-- Account Fields -->
                            <div class="account-fields-col">
                                <div class="account-section-header">
                                    <h3>Administrator Access</h3>
                                    <p>Your master login credentials to manage your travel CRM.</p>
                                </div>

                                <div class="form-row-2col">
                                    <div class="form-group">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" id="firstName" name="first_name" class="form-input"
                                            placeholder="e.g. Rahul" required
                                            data-error="Please enter your first name." />
                                    </div>

                                    <div class="form-group">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" id="lastName" name="last_name" class="form-input"
                                            placeholder="e.g. Sharma" required
                                            data-error="Please enter your last name." />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">Work Email Address</label>
                                    <input type="email" id="email" name="email" class="form-input"
                                        placeholder="rahul@agency.com" required
                                        data-error="Please enter a valid work email address." />
                                    <div class="form-hint">Your login credentials & verification link will be sent here.</div>
                                </div>

                                <div class="form-group">
                                    <label for="mobile" class="form-label">Mobile / WhatsApp Number</label>
                                    <input type="tel" id="mobile" name="mobile" class="form-input"
                                        placeholder="9876543210" maxlength="10" required
                                        data-error="Please enter your 10-digit mobile number." />
                                    <div class="form-hint">Used for 2-factor authentication & instant client lead alerts.</div>
                                </div>

                            </div>

                            <!-- Live Summary Review -->
                            <div class="account-summary-col">
                                <div class="order-summary-box">
                                    <div class="summary-header">
                                        <div class="summary-header-main">
                                            <div class="summary-header-icon">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <h3 class="summary-title">Workspace Overview</h3>
                                                <p class="summary-subtitle">Configuration & investment</p>
                                            </div>
                                        </div>
                                        <span id="summaryPlanName" class="summary-edition-pill">CRM Starter</span>
                                    </div>

                                    <!-- Clean Specs Panel -->
                                    <div class="summary-specs-panel">
                                        <div class="spec-row">
                                            <span class="spec-label">
                                                <i class="fas fa-globe"></i> Portal URL
                                            </span>
                                            <span id="summarySubdomain" class="spec-domain-pill">youragency.aleya.in</span>
                                        </div>

                                        <div class="spec-row">
                                            <span class="spec-label">
                                                <i class="fas fa-calendar-check"></i> Billing Cycle
                                            </span>
                                            <span id="summaryBillingCycle" class="cycle-badge">Monthly</span>
                                        </div>

                                        <div class="spec-row">
                                            <span class="spec-label">
                                                <i class="fas fa-user-group"></i> Team Seats
                                            </span>
                                            <span id="summaryUsersCount" class="spec-value">10 Agent Licenses (Included)</span>
                                        </div>

                                        <div class="spec-row spec-row-top-align">
                                            <span class="spec-label">
                                                <i class="fas fa-route"></i> Tour Database
                                            </span>
                                            <div class="spec-value spec-value-stacked">
                                                <strong id="summaryPackagesBadge" class="spec-packages-highlight">108 Packages</strong>
                                                <span id="summaryPackagesDest" class="spec-packages-sub">17 Selected Destinations</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Upgrade to ERP Option (Visible only if Lower Plan CRM is selected) -->
                                    <div id="upgradeToErpCard" class="upgrade-to-erp-card hidden">
                                        <div class="upgrade-card-head">
                                            <div class="upgrade-card-title-group">
                                                <span class="upgrade-tag"><i class="fas fa-sparkles"></i> Recommended</span>
                                                <span class="upgrade-card-title">Upgrade to Full ERP</span>
                                            </div>
                                            <button type="button" id="btnUpgradeToErp" class="btn-upgrade-action">Upgrade &rarr;</button>
                                        </div>
                                        <p class="upgrade-card-desc">Unlock AI itinerary builder, dynamic supplier vouchers & automated billing.</p>
                                    </div>

                                    <!-- Payment Summary -->
                                    <div id="summaryPricingCard" class="summary-pricing-card">
                                        <div class="pricing-card-title">Payment Summary</div>
                                        <div class="pricing-breakdown-rows">
                                            <div class="pricing-row">
                                                <span id="summaryPlanChargeLabel" class="pricing-row-label">CRM Starter (1st Month Subscription)</span>
                                                <span id="summaryPlanChargeVal" class="pricing-row-val">₹1,874</span>
                                            </div>
                                            <div class="pricing-row" id="summarySetupFeeRow">
                                                <span class="pricing-row-label">
                                                    One-Time Setup Fee
                                                    <span id="summarySetupBadge" class="setup-badge-paid">ONE-TIME</span>
                                                </span>
                                                <span id="summarySetupFeeVal" class="pricing-row-val">₹5,000</span>
                                            </div>
                                            <div class="pricing-row">
                                                <span class="pricing-row-label">GST (18%)</span>
                                                <span id="summaryGstVal" class="pricing-row-val">₹1,237</span>
                                            </div>
                                        </div>

                                        <div class="summary-total-divider"></div>

                                        <div class="summary-total-header">
                                            <div>
                                                <div class="summary-total-label">Total Payable Today</div>
                                                <div id="summaryBillingBreakdown" class="summary-total-subtext">Billed monthly (includes setup fee) + 18% GST</div>
                                            </div>
                                            <div class="summary-price-wrap">
                                                <span class="summary-currency">₹</span>
                                                <span id="summaryPriceNumber" class="summary-amount">8,111</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step Controls -->
                    <div class="onboarding-controls">
                        <button type="button" id="btnPrev" class="btn-prev hidden">
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous</span>
                        </button>

                        <button type="button" id="btnNext" class="btn-next">
                            <span>Next Step</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>

                        <button type="submit" id="btnSubmit" class="btn-submit hidden">
                            <span>Setup My Travel CRM</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>

                <!-- Loading Animation Overlay -->
                <div id="loadingOverlay" class="onboarding-loading-overlay">
                    <div class="loading-spinner"></div>
                    <h3 class="loading-title">Configuring Your Travel Workspace</h3>
                    <p id="loadingStatusText" class="loading-status-text">Setting up your secure travel workspace...</p>
                </div>

            </div>

        </div>
    </main>

    <!-- Razorpay Checkout & External Scripts -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="<?php echo USER_JS ?>onboarding.js?v=<?php echo time(); ?>"></script>

</body>

</html>