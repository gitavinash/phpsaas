<div class="package-page-wrapper integration-plugin-wrapper" id="pluginPayuApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-payu">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>PayU Gateway</h2>
                <p>Seamless checkout experience supporting Credit/Debit cards, Net Banking across 50+ banks, UPI QR, and PayLater.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Details Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Configuration Form -->
        <div class="v3-form-main-col">
            <form id="pluginPayuForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>PayU Merchant Credentials</h3>
                            <p>Enter your Merchant Key and Merchant Salt from PayU Merchant Dashboard.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle PayU Integration">
                                <input type="checkbox" id="payuActiveToggle" name="enable_payu" value="1" checked>
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Mode Selection -->
                            <div class="v3-form-group span-2">
                                <label class="v3-form-label">Environment Mode</label>
                                <div class="v3-env-mode-wrap">
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="payu_mode" id="payuModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (secure.payu.in)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="payu_mode" id="payuModeTest" value="test">
                                        <span><strong class="color-orange">Test Sandbox</strong> (test.payu.in)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Merchant Key -->
                            <div class="v3-form-group span-2">
                                <label for="payuMerchantKey" class="v3-form-label">
                                    Merchant Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="text" id="payuMerchantKey" name="payu_merchant_key" class="v3-form-input" placeholder="e.g. JB189gX" required>
                                </div>
                            </div>

                            <!-- Merchant Salt -->
                            <div class="v3-form-group span-2">
                                <label for="payuMerchantSalt" class="v3-form-label">
                                    Merchant Salt <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="payuMerchantSalt" name="payu_merchant_salt" class="v3-form-input" placeholder="Enter PayU Merchant Salt" autocomplete="new-password" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/integration/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSavePayuPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save PayU Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Webhook &amp; Sururl</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Configure success &amp; failure return URLs in PayU Webhook panel:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/payu/
                    </div>

                    <h5 class="v3-events-title">Platform Advantages:</h5>
                    <ul class="v3-events-list">
                        <li>Highest transaction conversion rates</li>
                        <li>Saved cards &amp; native OTP assistance</li>
                        <li>PayLater and EMI payment options</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
