<?php

/**
 * V3 Sidebar Navigation Menu
 * Component: app/dashboard/v3/default/desktop/dashboard/app/aside/aleya.php
 * Comprehensive travel management system navigation.
 */

$currentUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : '');
$isDashboardActive = ($currentUrl === '/dashboard/' || $currentUrl === '/dashboard' || $currentUrl === '' || $currentUrl === '/');

// 1. CORE & SALES CRM / DIGITAL MARKETING
?>
<?php if ($site_access_type === 'digital_marketing' || $isDigitalMarketingPackage) { ?>
    <!-- 1. DIGITAL MARKETING SUITE (Package ID 6: Dashboard, Leads) -->
    <li class="header-section-name">
        <p>Digital Marketing<span>Campaigns &amp; Leads</span></p>
    </li>

    <li <?php echo ($isDashboardActive ? 'class="active"' : ''); ?>>
        <a href="/dashboard/"><i class="fi fi-rr-apps"></i> Dashboard</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/"><i class="fi-rr-apps"></i> Overview</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/"><i class="fi-rr-apps"></i> Overview</a></li>
            </ul>
        </div>
    </li>

    <li <?php echo (strpos($currentUrl, '/leads/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/leads/"><i class="fi fi-rr-chart-network"></i> Leads &amp; Inquiries</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li><a href="/dashboard/leads/add/"><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=16"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li><a href="/dashboard/leads/add/"><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=16"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
    </li>

<?php } else { ?>
    <!-- 1. SALES CRM (Standard ERP Package) -->
    <li class="header-section-name">
        <p>Sales CRM<span>Daily Operations &amp; Leads</span></p>
    </li>

    <li <?php echo ($isDashboardActive ? 'class="active"' : ''); ?>>
        <a href="/dashboard/"><i class="fi fi-rr-apps"></i> Dashboard</a>
    </li>

    <li <?php echo (strpos($currentUrl, '/leads/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/leads/"><i class="fi fi-rr-chart-network"></i> Leads &amp; Inquiries</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li><a href="/dashboard/leads/add/"><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=16"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li><a href="/dashboard/leads/add/"><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li><a href="/dashboard/leads/list/?leadStatus=16"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
    </li>

<?php if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>
    <li <?php echo (strpos($currentUrl, '/operations') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/operations/"><i class="fi fi-rr-compass-alt"></i> Operations Hub</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/operations/#confirmed"><i class="fi-rr-badge-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/operations/#trips"><i class="fi-rr-plane-departure"></i> Trip Planner</a></li>
                <li><a href="/dashboard/operations/#unassigned"><i class="fi-rr-user-add"></i> Assign Leads</a></li>
                <li><a href="/dashboard/operations/#templates"><i class="fi-rr-file-edit"></i> Request Templates</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/operations/#confirmed"><i class="fi-rr-badge-check"></i> Confirmed Leads</a></li>
                <li><a href="/dashboard/operations/#trips"><i class="fi-rr-plane-departure"></i> Trip Planner</a></li>
                <li><a href="/dashboard/operations/#unassigned"><i class="fi-rr-user-add"></i> Assign Leads</a></li>
                <li><a href="/dashboard/operations/#templates"><i class="fi-rr-file-edit"></i> Request Templates</a></li>
            </ul>
        </div>
    </li>
<?php } ?>
<?php } ?>

<?php
// 1.5 ACCOUNTS & FINANCE (Administrator & Accountant) - Hidden in Digital Marketing Package (ID 6)
if (!$isDigitalMarketingPackage && ($site_access_type == 'administrator' || $site_access_type == 'accountant')) { ?>
    <li <?php echo (strpos($currentUrl, '/accounts') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/accounts/"><i class="fi fi-rr-wallet"></i> Accounts &amp; Ledger</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/accounts/"><i class="fi-rr-apps-sort"></i> All Transactions</a></li>
                <li><a href="/dashboard/accounts/?status=pending"><i class="fi-rr-clock"></i> Pending Verification</a></li>
                <li><a href="/dashboard/accounts/?status=verified"><i class="fi-rr-badge-check"></i> Verified Collections</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/accounts/"><i class="fi-rr-apps-sort"></i> All Transactions</a></li>
                <li><a href="/dashboard/accounts/?status=pending"><i class="fi-rr-clock"></i> Pending Verification</a></li>
                <li><a href="/dashboard/accounts/?status=verified"><i class="fi-rr-badge-check"></i> Verified Collections</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 2. PACKAGES, VENDORS & CMS - Hidden in Digital Marketing Package (ID 6)
if (!$isDigitalMarketingPackage && ($site_access_type == 'administrator' || $site_access_type == 'operations')) {
    $asideNatureVal = defined('COMPANY_NATURE') ? strtolower(COMPANY_NATURE) : 'travel_agency';
    $isDefaultNaturePackagesVendors = in_array($asideNatureVal, ['travel_agency', 'dmc']);

    $showTourPackages = defined('ASIDE_MENU_SHOW_PACKAGES')
        ? (ASIDE_MENU_SHOW_PACKAGES == '1' || ASIDE_MENU_SHOW_PACKAGES === 1)
        : $isDefaultNaturePackagesVendors;

    $showSupplyVendors = defined('ASIDE_MENU_SHOW_VENDORS')
        ? (ASIDE_MENU_SHOW_VENDORS == '1' || ASIDE_MENU_SHOW_VENDORS === 1)
        : $isDefaultNaturePackagesVendors;

    $showMarketingCms = defined('ASIDE_MENU_SHOW_CMS')
        ? (ASIDE_MENU_SHOW_CMS == '1' || ASIDE_MENU_SHOW_CMS === 1)
        : false;
?>

    <?php if ($showTourPackages) { ?>
    <li class="header-section-name" data-aside-group="packages">
        <p>Tour Packages<span>Smart Itineraries &amp; Catalog</span></p>
    </li>

    <li data-aside-group="packages" <?php echo (strpos($currentUrl, '/packages/domestic/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/packages/domestic/"><i class="fi fi-rr-suitcase-alt"></i> Domestic Packages</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                <li><a href="/dashboard/packages/domestic/add/"><i class="fi fi-rr-add"></i> Add New Package</a></li>
                <li><a href="/dashboard/packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                <li><a href="/dashboard/packages/domestic/add/"><i class="fi fi-rr-add"></i> Add New Package</a></li>
                <li><a href="/dashboard/packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a></li>
            </ul>
        </div>
    </li>

    <li data-aside-group="packages" <?php echo (strpos($currentUrl, '/packages/international/') !== false || (strpos($currentUrl, '/international/') !== false && strpos($currentUrl, '/packages/') !== false) ? 'class="active"' : ''); ?>>
        <a href="/dashboard/packages/international/"><i class="fi fi-rr-globe"></i> International Packages</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/packages/international/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                <li><a href="/dashboard/packages/international/add/"><i class="fi fi-rr-add"></i> Add New Package</a></li>
                <li><a href="/dashboard/packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/packages/international/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                <li><a href="/dashboard/packages/international/add/"><i class="fi fi-rr-add"></i> Add New Package</a></li>
                <li><a href="/dashboard/packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a></li>
            </ul>
        </div>
    </li>
    <?php } ?>

    <?php if ($showSupplyVendors) { ?>
    <!-- 3. SUPPLY & VENDORS -->
    <li class="header-section-name" data-aside-group="vendors">
        <p>Supply &amp; Vendors<span>Inventory, Hotels &amp; Logistics</span></p>
    </li>

    <li data-aside-group="vendors" <?php echo (strpos($currentUrl, '/vendors/property/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/vendors/property/"><i class="fi fi-rr-city"></i> Properties &amp; Hotels</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/vendors/property/list/"><i class="fi-rr-apps-sort"></i> All Properties</a></li>
                <li><a href="/dashboard/vendors/property/add/"><i class="fi fi-rr-add"></i> Add New Property</a></li>
                <li><a href="/dashboard/vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage Room Types</a></li>
                <li><a href="/dashboard/vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage Amenities</a></li>
                <li><a href="/dashboard/vendors/property/upload/"><i class="fi fi-rr-file-import"></i> Import Properties CSV</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/vendors/property/list/"><i class="fi-rr-apps-sort"></i> All Properties</a></li>
                <li><a href="/dashboard/vendors/property/add/"><i class="fi fi-rr-add"></i> Add New Property</a></li>
                <li><a href="/dashboard/vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage Room Types</a></li>
                <li><a href="/dashboard/vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage Amenities</a></li>
                <li><a href="/dashboard/vendors/property/upload/"><i class="fi fi-rr-file-import"></i> Import Properties CSV</a></li>
            </ul>
        </div>
    </li>

    <li data-aside-group="vendors" <?php echo (strpos($currentUrl, '/vendors/transporter/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/vendors/transporter/"><i class="fi fi-rr-bus"></i> Transporters &amp; Fleet</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All Transporters</a></li>
                <li><a href="/dashboard/vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add New Transporter</a></li>
                <li><a href="/dashboard/vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage Fleet Vehicles</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All Transporters</a></li>
                <li><a href="/dashboard/vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add New Transporter</a></li>
                <li><a href="/dashboard/vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage Fleet Vehicles</a></li>
            </ul>
        </div>
    </li>

    <li data-aside-group="vendors" class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-building"></i> DMC Partners <span class="aside-badge-soon">Soon</span></a>
    </li>
    <?php } ?>

    <?php if ($showMarketingCms) { ?>
    <!-- 4. MARKETING & CONTENT (CMS) -->
    <li class="header-section-name" data-aside-group="cms">
        <p>Marketing &amp; CMS<span>Blogs, Reviews &amp; Website</span></p>
    </li>

    <li data-aside-group="cms" class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-document-signed"></i> Travel Blogs <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li data-aside-group="cms" class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-comment-quote"></i> Client Testimonials <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li data-aside-group="cms" class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-picture"></i> Hero Sliders <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li data-aside-group="cms" class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-interrogation"></i> FAQs <span class="aside-badge-soon">Soon</span></a>
    </li>
    <?php } ?>

<?php
}

// 5. ANALYTICS & REPORTS (Administrator Only)
if ($site_access_type == 'administrator') { ?>

    <li class="header-section-name">
        <p>Analytics<span>Performance &amp; Reports</span></p>
    </li>

    <li <?php echo (strpos($currentUrl, '/report/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/report/"><i class="fi fi-rr-chart-histogram"></i> Performance Reports</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees Performance</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees Performance</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 7. PLUGINS & INTEGRATIONS (Dedicated Section with Active/Available Indicators)
if ($site_access_type == 'administrator' || $site_access_type == 'digital_marketing') {
    $activeAsidePg = 'razorpay';
    if (defined('PG_NAME') && !empty(PG_NAME)) {
        $activeAsidePg = strtolower(PG_NAME);
    } elseif (defined('ENABLE_INSTAMOJO') && ENABLE_INSTAMOJO == '1') {
        $activeAsidePg = 'instamojo';
    } elseif (defined('ENABLE_SABPAISA') && ENABLE_SABPAISA == '1') {
        $activeAsidePg = 'sabpaisa';
    } elseif (defined('ENABLE_PHONEPE') && ENABLE_PHONEPE == '1') {
        $activeAsidePg = 'phonepe';
    } elseif (defined('ENABLE_WORLDLINE') && ENABLE_WORLDLINE == '1') {
        $activeAsidePg = 'worldline';
    } elseif (defined('ENABLE_PAYU') && ENABLE_PAYU == '1') {
        $activeAsidePg = 'payu';
    } elseif (defined('ENABLE_PHI_COMMERCE') && ENABLE_PHI_COMMERCE == '1') {
        $activeAsidePg = 'phi_commerce';
    } elseif (defined('ENABLE_CASHFREE') && ENABLE_CASHFREE == '1') {
        $activeAsidePg = 'cashfree';
    } elseif (defined('ENABLE_RAZORPAY') && ENABLE_RAZORPAY == '1') {
        $activeAsidePg = 'razorpay';
    }

    $isFbActive    = ($isDigitalMarketingPackage || !defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1' || ENABLE_FACEBOOK_LEAD_ADS === 1);
    $isGAdsActive  = ($isDigitalMarketingPackage || (defined('ENABLE_GOOGLE_ADS') && (ENABLE_GOOGLE_ADS == '1' || ENABLE_GOOGLE_ADS === 1)));
    $isWfActive    = ($isDigitalMarketingPackage || !defined('ENABLE_WEB_FORMS') || ENABLE_WEB_FORMS == '1' || ENABLE_WEB_FORMS === 1);
    $isWaApiActive = (!defined('ENABLE_WHATSAPP_BUSINESS_API') || ENABLE_WHATSAPP_BUSINESS_API == '1' || ENABLE_WHATSAPP_BUSINESS_API === 1);
    $isSmtpActive  = (!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1' || ENABLE_SMTP_EMAIL === 1);
    $isUpiActive   = (!defined('ENABLE_UPI') || ENABLE_UPI == '1' || ENABLE_UPI === 1);
    $isWhActive    = (!defined('ENABLE_WEBHOOKS') || ENABLE_WEBHOOKS == '1' || ENABLE_WEBHOOKS === 1);
    $isWorkflowsActive = (!defined('ENABLE_WORKFLOWS') || ENABLE_WORKFLOWS == '1' || ENABLE_WORKFLOWS === 1);

    $activePluginCount = 0;
    if ($isWorkflowsActive) $activePluginCount++;
    if ($isFbActive) $activePluginCount++;
    if ($isGAdsActive) $activePluginCount++;
    if ($isWfActive) $activePluginCount++;
    if ($isWaApiActive) $activePluginCount++;
    if ($isSmtpActive) $activePluginCount++;
    if ($isWhActive) $activePluginCount++;
    if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') {
        if (!empty($activeAsidePg)) $activePluginCount++;
        if ($isUpiActive) $activePluginCount++;
    }
?>
    <!-- 7. PLUGINS & INTEGRATIONS -->
    <li class="header-section-name">
        <p>Plugins &amp; Integrations<span>Active &amp; Available Addons</span></p>
    </li>

    <li class="aside-item-plugins<?php echo (strpos($currentUrl, '/integration/') !== false && strpos($currentUrl, '/integration/whatsapp_web') === false ? ' active' : ''); ?>">
        <a href="/dashboard/integration/" class="aside-plugins-main-link">
            <i class="fi fi-rr-plug-connection"></i> 
            <span class="aside-menu-text">Plugins &amp; Integrations</span> 
            <span class="aside-badge-plugin-count" id="asideActivePluginBadge"><?php echo $activePluginCount; ?> Active</span>
        </a>
        <div class="side-menu aside-menu-plugins">
            <!-- Header with Quick Directory Shortcut -->
            <div class="aside-plugin-drawer-header">
                <div class="aside-plugin-drawer-title">
                    <i class="fi fi-rr-apps-sort"></i>
                    <span>Plugin Center</span>
                    <span class="aside-plugin-active-pill" id="asideActivePluginCountPill"><?php echo $activePluginCount; ?> Active</span>
                </div>
                <a href="/dashboard/integration/" class="aside-plugin-directory-btn" title="Open Full Plugins Directory">
                    Directory <i class="fi fi-rr-arrow-up-right-from-square"></i>
                </a>
            </div>

            <!-- Instant Live Search Bar -->
            <div class="aside-plugin-search-box">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="asidePluginSearchInput" class="aside-plugin-search-input" placeholder="Search active plugins..." autocomplete="off">
                <button type="button" id="asidePluginSearchClear" class="aside-plugin-search-clear is-hidden" aria-label="Clear Search"><i class="fi fi-rr-cross-small"></i></button>
            </div>

            <!-- Panel 1: Connected & Active Plugins (Direct Settings Access) -->
            <div class="aside-plugin-panel is-active" id="asidePluginsActivePanel">
                <div class="aside-plugin-list-scroll">
                    <ul class="aside-plugin-items-list">
                        <li data-aside-plugin="google_ads" class="aside-plugin-item <?php echo ($isGAdsActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="google ads adwords sem search marketing">
                            <a href="/dashboard/integration/google_ads/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-google"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/google_ads.svg" alt="Google Ads"></span>
                                <span class="aside-plugin-name">Google Ads</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="facebook" class="aside-plugin-item <?php echo ($isFbActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="facebook lead ads meta instagram advertising">
                            <a href="/dashboard/integration/facebook/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-facebook"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook"></span>
                                <span class="aside-plugin-name">Facebook Ads</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="web_forms" class="aside-plugin-item <?php echo ($isWfActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="web forms lead capture landing page builder embed">
                            <a href="/dashboard/integration/web_forms/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-web-forms"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/web_forms.svg" alt="Web Forms"></span>
                                <span class="aside-plugin-name">Web Forms</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="whatsapp" class="aside-plugin-item <?php echo ($isWaApiActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="whatsapp api business meta official cloud">
                            <a href="/dashboard/integration/whatsapp/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-whatsapp"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp"></span>
                                <span class="aside-plugin-name">WhatsApp API</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="smtp" class="aside-plugin-item <?php echo ($isSmtpActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="smtp email mail server notifications transactional">
                            <a href="/dashboard/integration/smtp/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-smtp"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/smtp.svg" alt="SMTP Email"></span>
                                <span class="aside-plugin-name">SMTP Email</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <?php if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') { ?>
                            <li data-aside-plugin="razorpay" class="aside-plugin-item <?php echo ($activeAsidePg === 'razorpay' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="razorpay payment gateway online payments">
                                <a href="/dashboard/integration/razorpay/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-razorpay"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/razorpay.svg" alt="Razorpay"></span>
                                    <span class="aside-plugin-name">Razorpay</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="phonepe" class="aside-plugin-item <?php echo ($activeAsidePg === 'phonepe' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="phonepe payment gateway upi qr">
                                <a href="/dashboard/integration/phonepe/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-phonepe"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/phonepe.svg" alt="PhonePe"></span>
                                    <span class="aside-plugin-name">PhonePe</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="cashfree" class="aside-plugin-item <?php echo ($activeAsidePg === 'cashfree' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="cashfree payment gateway auto collection">
                                <a href="/dashboard/integration/cashfree/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-cashfree"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/cashfree.svg" alt="Cashfree" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Cashfree</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="payu" class="aside-plugin-item <?php echo ($activeAsidePg === 'payu' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="payu payment gateway">
                                <a href="/dashboard/integration/payu/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-payu"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/payu.svg" alt="PayU" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">PayU</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="instamojo" class="aside-plugin-item <?php echo ($activeAsidePg === 'instamojo' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="instamojo payment gateway">
                                <a href="/dashboard/integration/instamojo/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-instamojo"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/instamojo.png" alt="Instamojo" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Instamojo</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="sabpaisa" class="aside-plugin-item <?php echo ($activeAsidePg === 'sabpaisa' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="sabpaisa payment gateway">
                                <a href="/dashboard/integration/sabpaisa/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-sabpaisa"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/sabpaisa.png" alt="SabPaisa" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">SabPaisa</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="worldline" class="aside-plugin-item <?php echo ($activeAsidePg === 'worldline' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="worldline payment gateway">
                                <a href="/dashboard/integration/worldline/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-worldline"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/worldline.svg" alt="Worldline" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Worldline</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="phi_commerce" class="aside-plugin-item <?php echo ($activeAsidePg === 'phi_commerce' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="phi commerce payment gateway">
                                <a href="/dashboard/integration/phi_commerce/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-phicommerce"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/phicommerce.png" alt="Phi Commerce" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Phi Commerce</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="upi" class="aside-plugin-item <?php echo ($isUpiActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="upi qr static dynamic scan code payment">
                                <a href="/dashboard/integration/upi/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-upi"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/upi.svg" alt="UPI"></span>
                                    <span class="aside-plugin-name">UPI &amp; QR</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                        <?php } ?>
                        <li data-aside-plugin="webhooks" class="aside-plugin-item <?php echo ($isWhActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="webhooks developer rest api callbacks events">
                            <a href="/dashboard/integration/webhooks/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-webhooks"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/webhooks.svg" alt="Webhooks"></span>
                                <span class="aside-plugin-name">Webhooks</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="workflows" class="aside-plugin-item <?php echo ($isWorkflowsActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="automation workflows flowchart make visual studio pipelines lead distribution">
                            <a href="/dashboard/integration/workflows/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-workflows"><i class="fi fi-rr-workflow"></i></span>
                                <span class="aside-plugin-name">Workflows</span>
                                <span class="aside-plugin-setting-pill" title="Configure Flowcharts"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                    </ul>
                    <div class="aside-plugin-empty-state is-hidden" id="asideActiveEmptyState">
                        <i class="fi fi-rr-search"></i>
                        <span>No matching active plugin found</span>
                    </div>
                </div>
                <div class="aside-plugin-drawer-footer">
                    <a href="/dashboard/integration/" class="aside-plugin-explore-link">
                        <span><i class="fi fi-rr-apps"></i> All Plugins Directory</span>
                        <i class="fi fi-rr-angle-small-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/integration/"><i class="fi fi-rr-apps-sort"></i> All Plugins</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 8. WHATSAPP CRM (Displayed above Setting section)
$waAsideSectionFile = __DIR__ . '/whatsapp_section.php';
if (file_exists($waAsideSectionFile)) {
    require_once $waAsideSectionFile;
}
?>

<!-- 9. SYSTEM & SETTINGS (Always at end of menus) -->
<?php if ($site_access_type == 'administrator' || $site_access_type == 'digital_marketing') { ?>
    <li class="header-section-name">
        <p>System &amp; Settings<span>Team, Policies &amp; Configuration</span></p>
    </li>

    <?php if ($site_access_type == 'administrator') { ?>
        <li <?php echo (strpos($currentUrl, '/user/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/user/list/"><i class="fi fi-rr-users-alt"></i> Team &amp; Users</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/user/list/"><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li><a href="/dashboard/user/add/"><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/user/list/"><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li><a href="/dashboard/user/add/"><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo (strpos($currentUrl, '/company/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/company/list/"><i class="fi fi-rr-shop"></i> Companies &amp; Branches</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li><a href="/dashboard/company/add/"><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li><a href="/dashboard/company/add/"><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo (strpos($currentUrl, '/policy/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/policy/"><i class="fi fi-rr-memo"></i> Company Policies</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li><a href="/dashboard/policy/add/"><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li><a href="/dashboard/policy/add/"><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>

    <?php if (USER_TYPE == 'administrator') { ?>
        <li <?php echo (strpos($currentUrl, '/setting/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/setting/"><i class="fi fi-rr-settings"></i> Settings</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/setting/"><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li><a href="/dashboard/setting/app/"><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li><a href="/dashboard/setting/configuration/"><i class="fi fi-rr-settings-sliders"></i> Configuration</a></li>
                    <li><a href="/dashboard/setting/contact/"><i class="fi fi-rr-phone-call"></i> Contact Details</a></li>
                    <li><a href="/dashboard/setting/notifications/"><i class="fi fi-rr-bell"></i> Notifications</a></li>
                    <li><a href="/dashboard/setting/bank/"><i class="fi fi-rr-bank"></i> Bank &amp; Payments</a></li>
                    <li><a href="/dashboard/setting/seo/"><i class="fi fi-rr-search-alt"></i> SEO Settings</a></li>
                    <li><a href="/dashboard/setting/profile/"><i class="fi fi-rr-user"></i> My Profile</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/setting/"><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li><a href="/dashboard/setting/app/"><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li><a href="/dashboard/setting/configuration/"><i class="fi fi-rr-settings-sliders"></i> Configuration</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>
<?php } ?>