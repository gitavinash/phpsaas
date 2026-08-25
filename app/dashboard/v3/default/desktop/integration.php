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
                    <p>Connect your advertising campaigns, instant messaging, and payment gateways.</p>
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
                    <i class="fi fi-rr-apps"></i> All Channels <span class="tab-count" id="countAll">13</span>
                </button>
                <button type="button" class="integration-cat-tab" data-category="leadgen">
                    <i class="fi fi-rr-bullseye-arrow"></i> Lead Gen &amp; Ads <span class="tab-count" id="countLeadgen">2</span>
                </button>
                <button type="button" class="integration-cat-tab" data-category="messaging">
                    <i class="fi fi-rr-comment-alt-middle"></i> Messaging &amp; Email <span class="tab-count" id="countMessaging">2</span>
                </button>
                <button type="button" class="integration-cat-tab" data-category="payments">
                    <i class="fi fi-rr-credit-card"></i> Payments &amp; Banking <span class="tab-count" id="countPayments">9</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Integration Cards Grid -->
    <div class="integration-cards-grid" id="integrationCardsGrid">

        <!-- 1. Facebook Lead Ads -->
        <div class="integration-card" data-category="leadgen" data-slug="facebook">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-facebook">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Facebook Lead Ads</h3>
                        <label class="v3-toggle-switch" title="Toggle Facebook Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="facebook" data-key="enable_facebook_lead_ads" <?php echo ((!defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1') ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Capture real-time traveler inquiries from Facebook &amp; Instagram ad campaigns with automated webhook synchronization.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/facebook/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. WhatsApp Cloud API -->
        <div class="integration-card" data-category="messaging" data-slug="whatsapp">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-whatsapp">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">WhatsApp Cloud API</h3>
                        <label class="v3-toggle-switch" title="Toggle WhatsApp Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="whatsapp" data-key="enable_whatsapp_business_api" <?php echo ((!defined('ENABLE_WHATSAPP_BUSINESS_API') || ENABLE_WHATSAPP_BUSINESS_API == '1') ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Deliver interactive travel itineraries, automatic booking confirmations, and quotation PDFs directly to travelers on WhatsApp.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/whatsapp/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. SMTP Email Server -->
        <div class="integration-card" data-category="messaging" data-slug="smtp">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-smtp">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/smtp.svg" alt="SMTP Email">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">SMTP Email Server</h3>
                        <label class="v3-toggle-switch" title="Toggle SMTP Email Server">
                            <input type="checkbox" class="integration-active-toggle" data-slug="smtp" data-key="enable_smtp_email" <?php echo ((!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1') ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Route outgoing notifications, quotation PDFs, payment receipts, and team alerts through your custom authenticated SMTP mail server.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/smtp/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Google Ads Tracking -->
        <div class="integration-card" data-category="leadgen" data-slug="google_ads">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-google">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/google.svg" alt="Google Ads">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Google Ads Tracking</h3>
                        <label class="v3-toggle-switch" title="Toggle Google Ads Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="google_ads" data-key="enable_google_ads" <?php echo ((defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1') ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Track offline conversion milestones, ingest Google Search lead extension forms, and calculate tour campaign ROI.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/google_ads/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Razorpay Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="razorpay">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-razorpay">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/razorpay.svg" alt="Razorpay">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Razorpay Gateway</h3>
                        <label class="v3-toggle-switch" title="Toggle Razorpay Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="razorpay" data-key="enable_razorpay" <?php echo ($activePg === 'razorpay' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Collect online credit/debit card, netbanking, and EMI payments with automatic invoice generation and booking status updates.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/razorpay/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Instamojo Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="instamojo">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-instamojo">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/instamojo.svg" alt="Instamojo">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Instamojo Gateway</h3>
                        <label class="v3-toggle-switch" title="Toggle Instamojo Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="instamojo" data-key="enable_instamojo" <?php echo ($activePg === 'instamojo' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Collect online debit/credit card, netbanking, and UPI payments with instant settlement and automated receipts.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/instamojo/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SabPaisa Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="sabpaisa">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-sabpaisa">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/sabpaisa.svg" alt="SabPaisa">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">SabPaisa Gateway</h3>
                        <label class="v3-toggle-switch" title="Toggle SabPaisa Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="sabpaisa" data-key="enable_sabpaisa" <?php echo ($activePg === 'sabpaisa' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Unified payment gateway supporting multi-banking aggregator channels, offline cash counters, UPI, and cards.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/sabpaisa/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. PhonePe Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="phonepe">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-phonepe">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/phonepe.svg" alt="PhonePe">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">PhonePe Gateway</h3>
                        <label class="v3-toggle-switch" title="Toggle PhonePe Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="phonepe" data-key="enable_phonepe" <?php echo ($activePg === 'phonepe' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Integrate PhonePe Standard Checkout with zero friction, automated UPI Intent flow, and card processing.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/phonepe/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Worldline / Ingenico Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="worldline">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-worldline">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/worldline.svg" alt="Worldline">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Worldline (Ingenico)</h3>
                        <label class="v3-toggle-switch" title="Toggle Worldline Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="worldline" data-key="enable_worldline" <?php echo ($activePg === 'worldline' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Enterprise multi-currency global payments gateway supporting Cards, Net Banking, e-Mandates, and SHA-512 validation.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/worldline/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. PayU Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="payu">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-payu">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/payu.svg" alt="PayU">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">PayU Gateway</h3>
                        <label class="v3-toggle-switch" title="Toggle PayU Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="payu" data-key="enable_payu" <?php echo ($activePg === 'payu' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Seamless checkout experience supporting Credit/Debit cards, Net Banking across 50+ banks, UPI QR, and PayLater.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/payu/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10. Phi Commerce (PayPhi) Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="phi_commerce">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-phicommerce">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/phicommerce.svg" alt="Phi Commerce">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Phi Commerce (PayPhi)</h3>
                        <label class="v3-toggle-switch" title="Toggle Phi Commerce Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="phi_commerce" data-key="enable_phi_commerce" <?php echo ($activePg === 'phi_commerce' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Omni-channel payment gateway solution providing unified checkout across POS, Payment Links, UPI, and Cards.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/phi_commerce/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 11. Cashfree Payment Gateway -->
        <div class="integration-card" data-category="payments" data-slug="cashfree">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-cashfree">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/cashfree.svg" alt="Cashfree">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">Cashfree Payments</h3>
                        <label class="v3-toggle-switch" title="Toggle Cashfree Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="cashfree" data-key="enable_cashfree" <?php echo ($activePg === 'cashfree' ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">High-speed payments suite offering Seamless Checkout, Auto-collect virtual accounts, and Instant Vendor Payouts.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/cashfree/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12. UPI & Instant QR Code -->
        <div class="integration-card" data-category="payments" data-slug="upi">
            <div class="integration-card-main">
                <div class="integration-brand-logo brand-upi">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/upi.svg" alt="UPI">
                </div>
                <div class="integration-card-content">
                    <div class="integration-card-title-bar">
                        <h3 class="integration-title">UPI &amp; Dynamic QR</h3>
                        <label class="v3-toggle-switch" title="Toggle UPI Integration">
                            <input type="checkbox" class="integration-active-toggle" data-slug="upi" data-key="enable_upi" <?php echo ((!defined('ENABLE_UPI') || ENABLE_UPI == '1') ? 'checked' : '') ?>>
                            <span class="v3-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="integration-desc">Generate dynamic NPCI-compliant UPI QR codes on quotations and vouchers for direct zero-surcharge bank remittances.</p>
                    <div class="integration-card-actions">
                        <a href="/dashboard/integration/upi/" class="btn-integration-primary">
                            Configure plugin
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>