<?php

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
$isWorkflowsActive = (!defined('ENABLE_WORKFLOWS') || ENABLE_WORKFLOWS == '1');
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
            <li data-aside-plugin="workflows" <?php echo ($isWorkflowsActive ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/workflows/"><i class="fi fi-rr-workflow"></i> Workflows</a></li>
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
            <li data-aside-plugin="workflows" <?php echo ($isWorkflowsActive ? '' : 'style="display:none;"') ?>><a href="/dashboard/integration/workflows/"><i class="fi fi-rr-workflow"></i> Workflows</a></li>
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