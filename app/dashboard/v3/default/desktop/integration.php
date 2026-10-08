<?php
$activePg = 'razorpay';
if (defined('PG_NAME') && !empty(PG_NAME)) {
    $activePg = strtolower(PG_NAME);
} elseif (defined('ENABLE_INSTAMOJO') && ENABLE_INSTAMOJO == '1') {
    $activePg = 'instamojo';
} elseif (defined('ENABLE_SABPAISA') && ENABLE_SABPAISA == '1') {
    $activePg = 'sabpaisa';
} elseif (defined('ENABLE_PHONEPE') && ENABLE_PHONEPE == '1') {
    $activePg = 'phonepe';
} elseif (defined('ENABLE_WORLDLINE') && ENABLE_WORLDLINE == '1') {
    $activePg = 'worldline';
} elseif (defined('ENABLE_PAYU') && ENABLE_PAYU == '1') {
    $activePg = 'payu';
} elseif (defined('ENABLE_PHI_COMMERCE') && ENABLE_PHI_COMMERCE == '1') {
    $activePg = 'phi_commerce';
} elseif (defined('ENABLE_CASHFREE') && ENABLE_CASHFREE == '1') {
    $activePg = 'cashfree';
} elseif (defined('ENABLE_RAZORPAY') && ENABLE_RAZORPAY == '1') {
    $activePg = 'razorpay';
}

$isFbActive   = (!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1');
$isGAdsActive = (defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1');
$isWaActive   = (defined('ENABLE_WHATSAPP_WEB_API') && ENABLE_WHATSAPP_WEB_API == '1');
$isSmtpActive = (!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1');
$isWhActive   = (!defined('ENABLE_WEBHOOKS') || ENABLE_WEBHOOKS == '1');
$isUpiActive  = (!defined('ENABLE_UPI') || ENABLE_UPI == '1');
?>
<div class="package-page-wrapper integration-page-wrapper" id="integrationApp">
    <!-- Sticky Top Toolbar Area (Scoped only for integration page) -->
    <div class="integration-sticky-top-bar">
        <!-- Header Banner -->
        <div class="package-header-bar integration-header-bar">
            <div class="package-header-left">
                <div class="package-header-icon integration-header-icon">
                    <i class="fi fi-rr-apps-add"></i>
                </div>
                <div class="package-header-titles">
                    <h2>Integrations &amp; Connected Channels</h2>
                    <p><?php echo ($site_access_type === 'digital_marketing' ? 'Connect and manage your marketing campaigns, lead acquisition ads, and messaging channels.' : 'Connect your advertising campaigns, instant messaging, payment gateways, and automation webhooks.'); ?></p>
                </div>
            </div>
            <div class="package-header-actions">
                <div class="integration-search-box">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="integrationSearchInput" placeholder="Search integrations by name, feature..." class="integration-search-input">
                </div>
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="integration-toolbar-row">
            <div class="integration-category-tabs">
                <button type="button" class="integration-cat-tab active" data-category="all">
                    <i class="fi fi-rr-apps"></i> All Channels <span class="tab-count" id="countAll"><?php echo ($site_access_type === 'digital_marketing' ? '5' : '14'); ?></span>
                </button>
                <button type="button" class="integration-cat-tab" data-category="leadgen">
                    <i class="fi fi-rr-bullseye-arrow"></i> Lead Gen &amp; Ads <span class="tab-count" id="countLeadgen">2</span>
                </button>
                <button type="button" class="integration-cat-tab" data-category="messaging">
                    <i class="fi fi-rr-comment-alt-middle"></i> Messaging &amp; Email <span class="tab-count" id="countMessaging">2</span>
                </button>
                <?php if ($site_access_type !== 'digital_marketing') { ?>
                <button type="button" class="integration-cat-tab" data-category="payments">
                    <i class="fi fi-rr-credit-card"></i> Payments &amp; Banking <span class="tab-count" id="countPayments">9</span>
                </button>
                <?php } ?>
                <button type="button" class="integration-cat-tab" data-category="developer">
                    <i class="fi fi-rr-code-compare"></i> Developer &amp; Webhooks <span class="tab-count" id="countDeveloper">1</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Integration Cards Grid -->
    <div class="integration-cards-grid" id="integrationCardsGrid">

        <!-- 1. Facebook Lead Ads -->
        <div class="integration-card <?php echo ($isFbActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="leadgen" data-slug="facebook">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-facebook">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Facebook Lead Ads</h3>
                    <span class="integration-card-pill">Lead Capture</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Facebook Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="facebook" data-key="enable_facebook_lead_ads" <?php echo ($isFbActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Capture real-time traveler inquiries from Facebook &amp; Instagram ad campaigns with automated webhook synchronization.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Instant Ingestion</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Form Webhooks</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isFbActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isFbActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/facebook/" class="<?php echo ($isFbActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Google Ads Tracking -->
        <div class="integration-card <?php echo ($isGAdsActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="leadgen" data-slug="google_ads">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-google">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/google_ads.svg" alt="Google Ads">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Google Ads Tracking</h3>
                    <span class="integration-card-pill">Ad Tracking &amp; Forms</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Google Ads Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="google_ads" data-key="enable_google_ads" <?php echo ($isGAdsActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Track offline conversion milestones, ingest Google Search lead extension forms, and calculate tour campaign ROI.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> GCLID Attribution</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Lead Form Sync</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isGAdsActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isGAdsActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/google_ads/" class="<?php echo ($isGAdsActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. WhatsApp Web API (QR Code) -->
        <div class="integration-card <?php echo ($isWaActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="messaging" data-slug="whatsapp_web">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-whatsapp">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp Web">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">WhatsApp Web API</h3>
                    <span class="integration-card-pill">Live Chat &amp; QR Sync</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle WhatsApp Web Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="whatsapp_web" data-key="enable_whatsapp_web_api" <?php echo ($isWaActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Connect your existing WhatsApp by scanning a QR code. Sync incoming messages directly into the CRM and chat with travelers from your dashboard.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> QR Code Pairing</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Two-Way CRM Inbox</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isWaActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isWaActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/whatsapp_web/" class="<?php echo ($isWaActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure Device</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. SMTP Email Server -->
        <div class="integration-card <?php echo ($isSmtpActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="messaging" data-slug="smtp">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-smtp">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/smtp.svg" alt="SMTP Email">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">SMTP Email Server</h3>
                    <span class="integration-card-pill">Email Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle SMTP Email Server">
                        <input type="checkbox" class="integration-active-toggle" data-slug="smtp" data-key="enable_smtp_email" <?php echo ($isSmtpActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Route outgoing notifications, quotation PDFs, payment receipts, and team alerts through your custom authenticated SMTP mail server.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Custom Mail Host</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> TLS / SSL Auth</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isSmtpActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isSmtpActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/smtp/" class="<?php echo ($isSmtpActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. Custom CRM Webhooks -->
        <div class="integration-card <?php echo ($isWhActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="developer" data-slug="webhooks">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-webhooks">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/webhooks.svg" alt="Webhooks">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Custom CRM Webhooks</h3>
                    <span class="integration-card-pill">Automation &amp; API</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Webhooks Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="webhooks" data-key="enable_webhooks" <?php echo ($isWhActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Stream real-time lead and booking payloads to external endpoints, Zapier, Make, and backend microservices.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Outbound Triggers</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> HMAC SHA-256</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isWhActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isWhActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/webhooks/" class="<?php echo ($isWhActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php if ($site_access_type !== 'digital_marketing') { ?>
        <!-- 6. Razorpay Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'razorpay' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="razorpay">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-razorpay">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/razorpay.svg" alt="Razorpay">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Razorpay Gateway</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Razorpay Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="razorpay" data-key="enable_razorpay" <?php echo ($activePg === 'razorpay' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Collect online credit/debit card, netbanking, and EMI payments with automatic invoice generation and booking status updates.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Cards &amp; NetBanking</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Auto Invoice PDF</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'razorpay' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'razorpay' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/razorpay/" class="<?php echo ($activePg === 'razorpay' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 7. PhonePe Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'phonepe' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="phonepe">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-phonepe">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/phonepe.svg" alt="PhonePe">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">PhonePe Gateway</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle PhonePe Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="phonepe" data-key="enable_phonepe" <?php echo ($activePg === 'phonepe' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Integrate PhonePe Standard Checkout with zero friction, automated UPI Intent flow, and card processing.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> UPI Intent Flow</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Instant Checkout</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'phonepe' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'phonepe' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/phonepe/" class="<?php echo ($activePg === 'phonepe' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 8. Cashfree Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'cashfree' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="cashfree">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-cashfree">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/cashfree.svg" alt="Cashfree" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Cashfree Payments</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Cashfree Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="cashfree" data-key="enable_cashfree" <?php echo ($activePg === 'cashfree' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">High-speed payments suite offering Seamless Checkout, Auto-collect virtual accounts, and Instant Vendor Payouts.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Auto-Collect Virtual</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> 120+ Payment Modes</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'cashfree' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'cashfree' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/cashfree/" class="<?php echo ($activePg === 'cashfree' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 9. PayU Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'payu' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="payu">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-payu">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/payu.svg" alt="PayU" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">PayU Gateway</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle PayU Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="payu" data-key="enable_payu" <?php echo ($activePg === 'payu' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Seamless checkout experience supporting Credit/Debit cards, Net Banking across 50+ banks, UPI QR, and PayLater.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> 50+ NetBanking</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> 1-Click Cards</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'payu' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'payu' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/payu/" class="<?php echo ($activePg === 'payu' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 10. Instamojo Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'instamojo' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="instamojo">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-instamojo">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/instamojo.png" alt="Instamojo" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Instamojo Gateway</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Instamojo Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="instamojo" data-key="enable_instamojo" <?php echo ($activePg === 'instamojo' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Collect online debit/credit card, netbanking, and UPI payments with instant settlement and automated receipts.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Smart Payment Links</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Fast Settlement</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'instamojo' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'instamojo' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/instamojo/" class="<?php echo ($activePg === 'instamojo' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 11. SabPaisa Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'sabpaisa' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="sabpaisa">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-sabpaisa">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/sabpaisa.png" alt="SabPaisa" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">SabPaisa Gateway</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle SabPaisa Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="sabpaisa" data-key="enable_sabpaisa" <?php echo ($activePg === 'sabpaisa' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Unified payment gateway supporting multi-banking aggregator channels, offline cash counters, UPI, and cards.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Multi-Bank Aggregator</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Offline Cash Counters</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'sabpaisa' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'sabpaisa' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/sabpaisa/" class="<?php echo ($activePg === 'sabpaisa' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 12. Worldline / Ingenico Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'worldline' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="worldline">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-worldline">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/worldline.svg" alt="Worldline" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Worldline (Ingenico)</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Worldline Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="worldline" data-key="enable_worldline" <?php echo ($activePg === 'worldline' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Enterprise multi-currency global payments gateway supporting Cards, Net Banking, e-Mandates, and SHA-512 validation.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Multi-Currency Global</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> SHA-512 Validation</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'worldline' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'worldline' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/worldline/" class="<?php echo ($activePg === 'worldline' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 13. Phi Commerce (PayPhi) Payment Gateway -->
        <div class="integration-card <?php echo ($activePg === 'phi_commerce' ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="phi_commerce">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-phicommerce">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/phicommerce.png" alt="Phi Commerce" class="brand-logo-wide">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">Phi Commerce (PayPhi)</h3>
                    <span class="integration-card-pill">Payment Gateway</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle Phi Commerce Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="phi_commerce" data-key="enable_phi_commerce" <?php echo ($activePg === 'phi_commerce' ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Omni-channel payment gateway solution providing unified checkout across POS, Payment Links, UPI, and Cards.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Omni-Channel Pay</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Fraud Shielding</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($activePg === 'phi_commerce' ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($activePg === 'phi_commerce' ? 'Active Gateway' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/phi_commerce/" class="<?php echo ($activePg === 'phi_commerce' ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 14. UPI & Instant QR Code -->
        <div class="integration-card <?php echo ($isUpiActive ? 'is-active-card' : 'is-disabled'); ?>" data-category="payments" data-slug="upi">
            <div class="integration-card-header">
                <div class="integration-brand-logo brand-upi">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/upi.svg" alt="UPI">
                </div>
                <div class="integration-header-text">
                    <h3 class="integration-title">UPI &amp; Dynamic QR</h3>
                    <span class="integration-card-pill">Direct Remittance</span>
                </div>
                <div class="integration-header-toggle">
                    <label class="v3-toggle-switch" title="Toggle UPI Integration">
                        <input type="checkbox" class="integration-active-toggle" data-slug="upi" data-key="enable_upi" <?php echo ($isUpiActive ? 'checked' : '') ?>>
                        <span class="v3-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="integration-card-body">
                <p class="integration-desc">Generate dynamic NPCI-compliant UPI QR codes on quotations and vouchers for direct zero-surcharge bank remittances.</p>
                <div class="integration-feature-chips">
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> 0% Gateway Fee</span>
                    <span class="feature-chip"><i class="fi fi-rr-check"></i> Dynamic Amount QR</span>
                </div>
            </div>
            <div class="integration-card-footer">
                <div class="integration-status-pill <?php echo ($isUpiActive ? 'is-active' : 'is-idle'); ?>">
                    <span class="status-pulse-dot"></span>
                    <span class="status-text"><?php echo ($isUpiActive ? 'Active' : 'Disabled'); ?></span>
                </div>
                <div class="integration-card-actions">
                    <a href="/dashboard/integration/upi/" class="<?php echo ($isUpiActive ? 'btn-integration-primary' : 'btn-integration-outline'); ?>">
                        <span>Configure</span> <i class="fi fi-rr-arrow-small-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>
</div>