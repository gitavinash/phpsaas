<?php

if ($site_access_type == 'administrator' || $site_access_type == 'accountant') { ?>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/accounts/') !== false || strpos(ADMIN_BASE_URL, '/accounts') !== false) ? 'class="active"' : '') ?>>
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

<?php
}

if ($site_access_type == 'administrator') { ?>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/report/') !== false) ? 'class="active"' : '') ?>>
    <a href="report/"><i class="fi fi-rr-chart-histogram"></i> Reports</a>
    <div class="side-menu">
        <ul>
            <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                    performance</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                    performance</a>
        </ul>
    </div>
</li>

<?php
}

if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>

<li class="header-section-name">
    <p>Vendors<span>Manage vendor.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/transporter/') !== false) ? 'class="active"' : '') ?>>
    <a href="vendors/transporter/"><i class="fi fi-rr-bus"></i> Transporter vendor</a>
    <div class="side-menu">
        <ul>
            <li><a href="vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All transporters</a>
            </li>
            <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
            </li>
            <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                    vehicles</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All transporters</a></li>
            <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
            </li>
            <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                    vehicles</a>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/property/') !== false) ? 'class="active"' : '') ?>>
    <a href="vendors/property/"><i class="fi fi-rr-city"></i> Property Vendors</a>
    <div class="side-menu">
        <ul>
            <li><a href="vendors/property/list/"><i class="fi-rr-apps-sort"></i> All properties</a></li>
            <li><a href="vendors/property/add/"><i class="fi fi-rr-add"></i> Add new property</a></li>
            <li><a href="vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
            </li>
            <li><a href="vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage room
                    types</a>
            </li>
            <li><a href="vendors/property/upload/"><i class="fi fi-rr-file-upload"></i> Upload properties</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="vendors/property/list/"><i class="fi-rr-apps-sort"></i> All properties</a></li>
            <li><a href="vendors/property/add/"><i class="fi fi-rr-add"></i> Add new property</a></li>
            <li><a href="vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
            </li>
            <li><a href="vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage room
                    types</a>
            </li>
            <li><a href="vendors/property/upload/"><i class="fi fi-rr-file-upload"></i> Upload properties</a></li>
        </ul>
    </div>
</li>
<!--
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/dmc/') !== false) ? 'class="active"' : '') ?>>
                    <a href="vendors/dmc/"><i class="fi fi-rr-shop"></i> DMC Vendor</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                </li> -->

<li class="header-section-name">
    <p>Tour Packages<span>Manage all tour packages.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/packages/domestic/') !== false) ? 'class="active"' : '') ?>>
    <a href="packages/domestic/"><i class="fi fi-rr-suitcase-alt"></i> Domestic packages</a>
    <div class="side-menu">
        <ul>
            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
            </li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/international/') !== false) ? 'class="active"' : '') ?>>
    <a href="packages/international/"><i class="fi fi-rr-earth-asia"></i> International packages</a>
    <div class="side-menu">
        <ul>
            <li><a href="packages/international/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="packages/international/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
            <li><a href="packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="packages/international/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="packages/international/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
            <li><a href="packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
            </li>
        </ul>
    </div>
</li>
<?php
}

?>


<li class="header-section-name">
    <p>Leads<span>Manage leads.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/leads/') !== false) ? 'class="active"' : '') ?>>
    <a href="leads/"><i class="fi fi-rr-chart-network"></i> Leads</a>
    <div class="side-menu">
        <ul>
            <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
            <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
            <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                    leads</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
            <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
            <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
            </li>
            <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                    leads</a>
            </li>
        </ul>
    </div>
</li>

<?php

if ($site_access_type == 'administrator') { ?>

<!-- <li class="header-section-name">
                    <p>Clients<span>Manage clients.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/clients/') !== false) ? 'class="active"' : '') ?>>
                    <a href="clients/"><i class="fi fi-rr-hr-group"></i> Clients</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a>
                        </ul>
                    </div>
                </li> -->

<li class="header-section-name">
    <p>Integration<span>Manage third party resources.</span></p>
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
<?php
    $isFbActive    = (!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1' || ENABLE_FACEBOOK_LEAD_ADS === 1);
    $isGAdsActive  = (defined('ENABLE_GOOGLE_ADS') && (ENABLE_GOOGLE_ADS == '1' || ENABLE_GOOGLE_ADS === 1));
    $isWfActive    = (!defined('ENABLE_WEB_FORMS') || ENABLE_WEB_FORMS == '1' || ENABLE_WEB_FORMS === 1);
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
    if (!empty($activeAsidePg)) $activePluginCount++;
    if ($isUpiActive) $activePluginCount++;
?>
<li class="aside-item-plugins<?php echo ((strpos(ADMIN_BASE_URL, '/integration/') !== false && strpos(ADMIN_BASE_URL, '/integration/whatsapp_web') === false) ? ' active' : ''); ?>">
    <a href="/dashboard/integration/" class="aside-plugins-main-link">
        <i class="fi fi-rr-plug-connection"></i> 
        <span class="aside-menu-text">Plugins &amp; Integrations</span>
        <span class="aside-badge-plugin-count" id="asideActivePluginBadge"><?php echo $activePluginCount; ?> Active</span>
    </a>
    <div class="side-menu aside-menu-plugins">
        <div class="aside-plugin-drawer-header">
            <div class="aside-plugin-drawer-title">
                <i class="fi fi-rr-apps-sort"></i>
                <span>Plugin Center</span>
                <span class="aside-plugin-active-pill" id="asideActivePluginCountPill"><?php echo $activePluginCount; ?> Active</span>
            </div>
            <a href="/dashboard/integration/" class="aside-plugin-directory-btn" title="Open All Plugins Directory">
                Directory <i class="fi fi-rr-arrow-up-right-from-square"></i>
            </a>
        </div>
        <div class="aside-plugin-search-box">
            <i class="fi fi-rr-search"></i>
            <input type="text" id="asidePluginSearchInput" class="aside-plugin-search-input" placeholder="Search active plugins..." autocomplete="off">
            <button type="button" id="asidePluginSearchClear" class="aside-plugin-search-clear is-hidden" aria-label="Clear Search"><i class="fi fi-rr-cross-small"></i></button>
        </div>
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
                    <li data-aside-plugin="upi" class="aside-plugin-item <?php echo ($isUpiActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="upi qr static dynamic scan code payment">
                        <a href="/dashboard/integration/upi/" class="aside-plugin-link">
                            <span class="aside-plugin-icon-wrap brand-upi"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/upi.svg" alt="UPI"></span>
                            <span class="aside-plugin-name">UPI &amp; QR</span>
                            <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                        </a>
                    </li>
                    <li data-aside-plugin="webhooks" class="aside-plugin-item <?php echo ($isWhActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="webhooks developer rest api callbacks events">
                        <a href="/dashboard/integration/webhooks/" class="aside-plugin-link">
                            <span class="aside-plugin-icon-wrap brand-webhooks"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/webhooks.svg" alt="Webhooks"></span>
                            <span class="aside-plugin-name">Webhooks</span>
                            <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
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

<li class="header-section-name">
    <p>Business Hub<span>Control Your Business</span></p>
</li>


<li <?php echo ((strpos(ADMIN_BASE_URL, '/policy/') !== false) ? 'class="active"' : '') ?>>
    <a href="policy/"><i class="fi fi-rr-memo"></i> Policies</a>
    <div class="side-menu">
        <ul>
            <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
            <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add new policy</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
            <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add new policy</a></li>
        </ul>
    </div>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/bank/') !== false) ? 'class="active"' : '') ?>>
    <a href="payment/"><i class="fi fi-rr-indian-rupee-sign"></i> Payments</a>
    <div class="side-menu">
        <ul>
            <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All methods</a></li>
            <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add new method</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All methods</a></li>
            <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add new method</a></li>
        </ul>
    </div>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/company/') !== false) ? 'class="active"' : '') ?>>
    <a href="company/"><i class="fi fi-rr-building"></i> Company</a>
    <div class="side-menu">
        <ul>
            <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All companies</a></li>
            <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add new company</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All companies</a></li>
            <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add new company</a></li>
        </ul>
    </div>
</li>

<?php
}

?>