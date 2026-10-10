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

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-receipt"></i> Bookings &amp; Vouchers <span class="aside-badge-soon">Soon</span></a>
    </li>
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
if (!$isDigitalMarketingPackage && ($site_access_type == 'administrator' || $site_access_type == 'operations')) { ?>

    <li class="header-section-name">
        <p>Tour Packages<span>Smart Itineraries &amp; Catalog</span></p>
    </li>

    <li <?php echo (strpos($currentUrl, '/packages/domestic/') !== false ? 'class="active"' : ''); ?>>
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

    <li <?php echo (strpos($currentUrl, '/packages/international/') !== false || (strpos($currentUrl, '/international/') !== false && strpos($currentUrl, '/packages/') !== false) ? 'class="active"' : ''); ?>>
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

    <!-- 3. SUPPLY & VENDORS -->
    <li class="header-section-name">
        <p>Supply &amp; Vendors<span>Inventory, Hotels &amp; Logistics</span></p>
    </li>

    <li <?php echo (strpos($currentUrl, '/vendors/property/') !== false ? 'class="active"' : ''); ?>>
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

    <li <?php echo (strpos($currentUrl, '/vendors/transporter/') !== false ? 'class="active"' : ''); ?>>
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

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-building"></i> DMC Partners <span class="aside-badge-soon">Soon</span></a>
    </li>

    <!-- 4. MARKETING & CONTENT (CMS) -->
    <li class="header-section-name">
        <p>Marketing &amp; CMS<span>Blogs, Reviews &amp; Website</span></p>
    </li>

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-document-signed"></i> Travel Blogs <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-comment-quote"></i> Client Testimonials <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-picture"></i> Hero Sliders <span class="aside-badge-soon">Soon</span></a>
    </li>

    <li class="aside-menu-disabled">
        <a href="javascript:void(0)"><i class="fi fi-rr-interrogation"></i> FAQs <span class="aside-badge-soon">Soon</span></a>
    </li>

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

    $activePluginCount = 0;
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

    <li <?php echo (strpos($currentUrl, '/integration/') !== false && strpos($currentUrl, '/integration/whatsapp_web') === false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/integration/"><i class="fi fi-rr-plug-connection"></i> Plugins &amp; Integrations <span class="aside-badge-plugin-count"><?php echo $activePluginCount; ?> Active</span></a>
        <div class="side-menu aside-menu-plugins">
            <ul>
                <li><a href="/dashboard/integration/"><i class="fi fi-rr-apps-sort"></i> <strong>All Plugins Directory</strong></a></li>

                <li class="aside-submenu-group-title"><span>Connected &amp; Active</span></li>
                <li data-aside-plugin="facebook" class="<?php echo ($isFbActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isFbActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/facebook/"><i class="fi fi-rr-bullseye-arrow"></i> Facebook Ads <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <li data-aside-plugin="google_ads" class="<?php echo ($isGAdsActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isGAdsActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/google_ads/"><i class="fi fi-rr-search-alt"></i> Google Ads <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <li data-aside-plugin="web_forms" class="<?php echo ($isWfActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isWfActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/web_forms/"><i class="fi fi-rr-browser"></i> Web Forms <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <li data-aside-plugin="whatsapp" class="<?php echo ($isWaApiActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isWaApiActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/whatsapp/"><i class="fi fi-rr-comment-alt-middle"></i> WhatsApp API <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <li data-aside-plugin="smtp" class="<?php echo ($isSmtpActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isSmtpActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/smtp/"><i class="fi fi-rr-envelope"></i> SMTP Email <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <?php if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') { ?>
                    <li data-aside-plugin="razorpay" class="<?php echo ($activeAsidePg === 'razorpay' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'razorpay' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/razorpay/"><i class="fi fi-rr-credit-card"></i> Razorpay <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="instamojo" class="<?php echo ($activeAsidePg === 'instamojo' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'instamojo' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/instamojo/"><i class="fi fi-rr-credit-card"></i> Instamojo <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="sabpaisa" class="<?php echo ($activeAsidePg === 'sabpaisa' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'sabpaisa' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/sabpaisa/"><i class="fi fi-rr-credit-card"></i> SabPaisa <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="phonepe" class="<?php echo ($activeAsidePg === 'phonepe' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'phonepe' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/phonepe/"><i class="fi fi-rr-credit-card"></i> PhonePe <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="worldline" class="<?php echo ($activeAsidePg === 'worldline' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'worldline' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/worldline/"><i class="fi fi-rr-credit-card"></i> Worldline <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="payu" class="<?php echo ($activeAsidePg === 'payu' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'payu' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/payu/"><i class="fi fi-rr-credit-card"></i> PayU <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="phi_commerce" class="<?php echo ($activeAsidePg === 'phi_commerce' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'phi_commerce' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/phi_commerce/"><i class="fi fi-rr-credit-card"></i> Phi Commerce <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="cashfree" class="<?php echo ($activeAsidePg === 'cashfree' ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($activeAsidePg === 'cashfree' ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/cashfree/"><i class="fi fi-rr-credit-card"></i> Cashfree <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                    <li data-aside-plugin="upi" class="<?php echo ($isUpiActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isUpiActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/upi/"><i class="fi fi-rr-qr-scan"></i> UPI &amp; QR <span class="aside-plugin-status-pill is-active">Active</span></a></li>
                <?php } ?>
                <li data-aside-plugin="webhooks" class="<?php echo ($isWhActive ? 'plugin-item-active' : 'plugin-item-disabled'); ?>" <?php echo ($isWhActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/webhooks/"><i class="fi fi-rr-code-compare"></i> Webhooks <span class="aside-plugin-status-pill is-active">Active</span></a></li>

                <li class="aside-submenu-group-title"><span>Available Addons</span></li>
                <li data-aside-plugin="facebook_available" <?php echo (!$isFbActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/facebook/"><i class="fi fi-rr-bullseye-arrow"></i> Facebook Ads <span class="aside-plugin-status-pill is-idle">Setup</span></a></li>
                <li data-aside-plugin="google_ads_available" <?php echo (!$isGAdsActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/google_ads/"><i class="fi fi-rr-search-alt"></i> Google Ads <span class="aside-plugin-status-pill is-idle">Setup</span></a></li>
                <li data-aside-plugin="web_forms_available" <?php echo (!$isWfActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/web_forms/"><i class="fi fi-rr-browser"></i> Web Forms <span class="aside-plugin-status-pill is-idle">Setup</span></a></li>
                <li data-aside-plugin="whatsapp_available" <?php echo (!$isWaApiActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/whatsapp/"><i class="fi fi-rr-comment-alt-middle"></i> WhatsApp API <span class="aside-plugin-status-pill is-idle">Setup</span></a></li>
                <li data-aside-plugin="smtp_available" <?php echo (!$isSmtpActive ? '' : 'style="display:none;"'); ?>><a href="/dashboard/integration/smtp/"><i class="fi fi-rr-envelope"></i> SMTP Email <span class="aside-plugin-status-pill is-idle">Setup</span></a></li>
                <?php if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') { ?>
                    <?php if ($activeAsidePg !== 'razorpay') { ?><li data-aside-plugin="razorpay_available"><a href="/dashboard/integration/razorpay/"><i class="fi fi-rr-credit-card"></i> Razorpay <span class="aside-plugin-status-pill is-idle">Setup</span></a></li><?php } ?>
                    <?php if ($activeAsidePg !== 'phonepe') { ?><li data-aside-plugin="phonepe_available"><a href="/dashboard/integration/phonepe/"><i class="fi fi-rr-credit-card"></i> PhonePe <span class="aside-plugin-status-pill is-idle">Setup</span></a></li><?php } ?>
                    <?php if ($activeAsidePg !== 'cashfree') { ?><li data-aside-plugin="cashfree_available"><a href="/dashboard/integration/cashfree/"><i class="fi fi-rr-credit-card"></i> Cashfree <span class="aside-plugin-status-pill is-idle">Setup</span></a></li><?php } ?>
                    <?php if (!$isUpiActive) { ?><li data-aside-plugin="upi_available"><a href="/dashboard/integration/upi/"><i class="fi fi-rr-qr-scan"></i> UPI &amp; QR <span class="aside-plugin-status-pill is-idle">Setup</span></a></li><?php } ?>
                <?php } ?>
                <?php if (!$isWhActive) { ?><li data-aside-plugin="webhooks_available"><a href="/dashboard/integration/webhooks/"><i class="fi fi-rr-code-compare"></i> Webhooks <span class="aside-plugin-status-pill is-idle">Setup</span></a></li><?php } ?>
            </ul>
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