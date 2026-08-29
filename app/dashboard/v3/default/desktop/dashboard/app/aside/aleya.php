<?php

/**
 * V3 Sidebar Navigation Menu
 * Component: app/dashboard/v3/default/desktop/dashboard/app/aside/aleya.php
 * Comprehensive travel management system navigation.
 */

$currentUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : '');
$isDashboardActive = ($currentUrl === '/dashboard/' || $currentUrl === '/dashboard' || $currentUrl === '' || $currentUrl === '/');

// 1. CORE & SALES CRM
?>
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
            <li><a href="/dashboard/leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="/dashboard/leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
            <li><a href="/dashboard/leads/add/"><i class="fi fi-rr-add"></i> Add New Lead</a></li>
            <li><a href="/dashboard/leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed Leads</a></li>
            <li><a href="/dashboard/leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
            <li><a href="/dashboard/leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow-ups</a></li>
            <li><a href="/dashboard/leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
        </ul>
    </div>
</li>

<li class="aside-menu-disabled">
    <a href="javascript:void(0)"><i class="fi fi-rr-receipt"></i> Bookings &amp; Vouchers <span class="aside-badge-soon">Soon</span></a>
</li>

<?php
// 2. PACKAGES & ITINERARIES
if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>

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

    <!-- 6. SYSTEM & SETTINGS -->
    <li class="header-section-name">
        <p>System &amp; Settings<span>Team, Channels &amp; Policies</span></p>
    </li>

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

    <?php
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
    ?>
    <li <?php echo (strpos($currentUrl, '/integration/') !== false ? 'class="active"' : ''); ?>>
        <a href="/dashboard/integration/"><i class="fi fi-rr-plug-connection"></i> Plugins &amp; Integrations</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/integration/"><i class="fi-rr-apps-sort"></i> All Plugins</a></li>
                <li data-aside-plugin="facebook" <?php echo ((!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/facebook/"><i class="fi-rr-bullseye-arrow"></i> Facebook Ads</a></li>
                <li data-aside-plugin="whatsapp" <?php echo ((!defined('ENABLE_WHATSAPP_BUSINESS_API') || ENABLE_WHATSAPP_BUSINESS_API == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/whatsapp/"><i class="fi-rr-comment-alt-middle"></i> WhatsApp API</a></li>
                <li data-aside-plugin="smtp" <?php echo ((!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/smtp/"><i class="fi-rr-envelope"></i> SMTP Email</a></li>
                <li data-aside-plugin="google_ads" <?php echo ((defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/google_ads/"><i class="fi-rr-search-alt"></i> Google Ads</a></li>
                <li data-aside-plugin="razorpay" <?php echo ($activeAsidePg === 'razorpay' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/razorpay/"><i class="fi-rr-credit-card"></i> Razorpay</a></li>
                <li data-aside-plugin="instamojo" <?php echo ($activeAsidePg === 'instamojo' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/instamojo/"><i class="fi-rr-credit-card"></i> Instamojo</a></li>
                <li data-aside-plugin="sabpaisa" <?php echo ($activeAsidePg === 'sabpaisa' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/sabpaisa/"><i class="fi-rr-credit-card"></i> SabPaisa</a></li>
                <li data-aside-plugin="phonepe" <?php echo ($activeAsidePg === 'phonepe' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/phonepe/"><i class="fi-rr-credit-card"></i> PhonePe</a></li>
                <li data-aside-plugin="worldline" <?php echo ($activeAsidePg === 'worldline' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/worldline/"><i class="fi-rr-credit-card"></i> Worldline</a></li>
                <li data-aside-plugin="payu" <?php echo ($activeAsidePg === 'payu' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/payu/"><i class="fi-rr-credit-card"></i> PayU</a></li>
                <li data-aside-plugin="phi_commerce" <?php echo ($activeAsidePg === 'phi_commerce' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/phi_commerce/"><i class="fi-rr-credit-card"></i> Phi Commerce</a></li>
                <li data-aside-plugin="cashfree" <?php echo ($activeAsidePg === 'cashfree' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/cashfree/"><i class="fi-rr-credit-card"></i> Cashfree</a></li>
                <li data-aside-plugin="upi" <?php echo ((!defined('ENABLE_UPI') || ENABLE_UPI == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/upi/"><i class="fi-rr-qr-scan"></i> UPI &amp; QR</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/integration/"><i class="fi-rr-apps-sort"></i> All Plugins</a></li>
                <li data-aside-plugin="facebook" <?php echo ((!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/facebook/"><i class="fi-rr-bullseye-arrow"></i> Facebook Ads</a></li>
                <li data-aside-plugin="whatsapp" <?php echo ((!defined('ENABLE_WHATSAPP_BUSINESS_API') || ENABLE_WHATSAPP_BUSINESS_API == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/whatsapp/"><i class="fi-rr-comment-alt-middle"></i> WhatsApp API</a></li>
                <li data-aside-plugin="smtp" <?php echo ((!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/smtp/"><i class="fi-rr-envelope"></i> SMTP Email</a></li>
                <li data-aside-plugin="google_ads" <?php echo ((defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/google_ads/"><i class="fi-rr-search-alt"></i> Google Ads</a></li>
                <li data-aside-plugin="razorpay" <?php echo ($activeAsidePg === 'razorpay' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/razorpay/"><i class="fi-rr-credit-card"></i> Razorpay</a></li>
                <li data-aside-plugin="instamojo" <?php echo ($activeAsidePg === 'instamojo' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/instamojo/"><i class="fi-rr-credit-card"></i> Instamojo</a></li>
                <li data-aside-plugin="sabpaisa" <?php echo ($activeAsidePg === 'sabpaisa' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/sabpaisa/"><i class="fi-rr-credit-card"></i> SabPaisa</a></li>
                <li data-aside-plugin="phonepe" <?php echo ($activeAsidePg === 'phonepe' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/phonepe/"><i class="fi-rr-credit-card"></i> PhonePe</a></li>
                <li data-aside-plugin="worldline" <?php echo ($activeAsidePg === 'worldline' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/worldline/"><i class="fi-rr-credit-card"></i> Worldline</a></li>
                <li data-aside-plugin="payu" <?php echo ($activeAsidePg === 'payu' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/payu/"><i class="fi-rr-credit-card"></i> PayU</a></li>
                <li data-aside-plugin="phi_commerce" <?php echo ($activeAsidePg === 'phi_commerce' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/phi_commerce/"><i class="fi-rr-credit-card"></i> Phi Commerce</a></li>
                <li data-aside-plugin="cashfree" <?php echo ($activeAsidePg === 'cashfree' ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/cashfree/"><i class="fi-rr-credit-card"></i> Cashfree</a></li>
                <li data-aside-plugin="upi" <?php echo ((!defined('ENABLE_UPI') || ENABLE_UPI == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/upi/"><i class="fi-rr-qr-scan"></i> UPI &amp; QR</a></li>
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

<?php
}
?>