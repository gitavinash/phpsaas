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
<li <?php echo ((strpos(ADMIN_BASE_URL, '/integration/') !== false) ? 'class="active"' : '') ?>>
    <a href="/dashboard/integration/"><i class="fi fi-rr-plug-connection"></i> Plugins</a>
    <div class="side-menu">
        <ul>
            <li><a href="/dashboard/integration/"><i class="fi-rr-apps-sort"></i> All Plugins</a></li>
            <li data-aside-plugin="facebook" <?php echo ((!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/facebook/"><i class="fi-rr-bullseye-arrow"></i> Facebook Ads</a></li>
            <li data-aside-plugin="whatsapp_web" <?php echo ((defined('ENABLE_WHATSAPP_WEB_API') && ENABLE_WHATSAPP_WEB_API == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/whatsapp_web/"><i class="fi-rr-comment-alt-middle"></i> WhatsApp Web</a></li>
            <li data-aside-plugin="smtp" <?php echo ((!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/smtp/"><i class="fi-rr-envelope"></i> SMTP Email</a></li>
            <li data-aside-plugin="google_ads" <?php echo ((defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/google_ads/"><i class="fi-rr-search-alt"></i> Google Ads</a></li>
            <li data-aside-plugin="web_forms" <?php echo ((!defined('ENABLE_WEB_FORMS') || ENABLE_WEB_FORMS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/web_forms/"><i class="fi-rr-browser"></i> Web Forms</a></li>
            <li data-aside-plugin="webhooks" <?php echo ((!defined('ENABLE_WEBHOOKS') || ENABLE_WEBHOOKS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/webhooks/"><i class="fi-rr-code-compare"></i> Webhooks</a></li>
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
            <li data-aside-plugin="whatsapp_web" <?php echo ((defined('ENABLE_WHATSAPP_WEB_API') && ENABLE_WHATSAPP_WEB_API == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/whatsapp_web/"><i class="fi-rr-comment-alt-middle"></i> WhatsApp Web</a></li>
            <li data-aside-plugin="smtp" <?php echo ((!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/smtp/"><i class="fi-rr-envelope"></i> SMTP Email</a></li>
            <li data-aside-plugin="google_ads" <?php echo ((defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/google_ads/"><i class="fi-rr-search-alt"></i> Google Ads</a></li>
            <li data-aside-plugin="web_forms" <?php echo ((!defined('ENABLE_WEB_FORMS') || ENABLE_WEB_FORMS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/web_forms/"><i class="fi-rr-browser"></i> Web Forms</a></li>
            <li data-aside-plugin="webhooks" <?php echo ((!defined('ENABLE_WEBHOOKS') || ENABLE_WEBHOOKS == '1') ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/webhooks/"><i class="fi-rr-code-compare"></i> Webhooks</a></li>
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