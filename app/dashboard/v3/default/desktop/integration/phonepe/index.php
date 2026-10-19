<div class="package-page-wrapper integration-plugin-wrapper" id="pluginPhonepeApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-phonepe">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>PhonePe Payment Gateway</h2>
                <p>Integrate PhonePe Standard Checkout with zero-friction UPI Intent flow, QR desktop checkout, and card processing.</p>
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
            <form id="pluginPhonepeForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>PhonePe Merchant Credentials</h3>
                            <p>Enter your Merchant ID, Salt Key, and Salt Index from PhonePe Merchant Portal.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle PhonePe Integration">
                                <input type="checkbox" id="ppActiveToggle" name="enable_phonepe" value="1" checked>
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
                                        <input type="radio" name="phonepe_mode" id="ppModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (api.phonepe.com)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="phonepe_mode" id="ppModeTest" value="test">
                                        <span><strong class="color-orange">UAT / Sandbox</strong> (api-preprod.phonepe.com)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Merchant ID -->
                            <div class="v3-form-group span-2">
                                <label for="ppMerchantId" class="v3-form-label">
                                    Merchant ID <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="ppMerchantId" name="phonepe_merchant_id" class="v3-form-input" placeholder="e.g. M2306160483220844..." required>
                                </div>
                            </div>

                            <!-- Salt Key -->
                            <div class="v3-form-group">
                                <label for="ppSaltKey" class="v3-form-label">
                                    Salt Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="password" id="ppSaltKey" name="phonepe_salt_key" class="v3-form-input" placeholder="Enter PhonePe Salt Key" autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Salt Index -->
                            <div class="v3-form-group">
                                <label for="ppSaltIndex" class="v3-form-label">
                                    Salt Index <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-hashtag input-icon"></i>
                                    <input type="text" id="ppSaltIndex" name="phonepe_salt_index" class="v3-form-input" placeholder="e.g. 1" value="1" required>
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
                    <button type="submit" id="btnSavePhonepePlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save PhonePe Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Server Webhook</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Server Callback URL for payment response status webhook:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/phonepe/
                    </div>

                    <h5 class="v3-events-title">Integration Highlights:</h5>
                    <ul class="v3-events-list">
                        <li>Zero-redirect UPI Intent flow on mobile</li>
                        <li>High success rate dynamic UPI QR on desktop</li>
                        <li>Cryptographic SHA-256 HMAC checksum validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
