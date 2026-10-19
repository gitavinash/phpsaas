<div class="package-page-wrapper integration-plugin-wrapper" id="pluginPhiCommerceApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-phicommerce">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>Phi Commerce (PayPhi) Gateway</h2>
                <p>Omni-channel payment gateway solution providing unified checkout across POS, Payment Links, UPI, and Cards.</p>
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
            <form id="pluginPhiCommerceForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>PayPhi API Credentials</h3>
                            <p>Enter your Merchant ID and Secret Encryption Key from Phi Commerce portal.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle Phi Commerce Integration">
                                <input type="checkbox" id="phiActiveToggle" name="enable_phi_commerce" value="1" checked>
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
                                        <input type="radio" name="phi_commerce_mode" id="phiModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (api.payphi.com)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="phi_commerce_mode" id="phiModeTest" value="test">
                                        <span><strong class="color-orange">QA Sandbox</strong> (qa.payphi.com)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Merchant ID -->
                            <div class="v3-form-group span-2">
                                <label for="phiMerchantId" class="v3-form-label">
                                    Merchant ID <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="phiMerchantId" name="phi_commerce_merchant_id" class="v3-form-input" placeholder="e.g. PHI_M_99214" required>
                                </div>
                            </div>

                            <!-- Secret Encryption Key -->
                            <div class="v3-form-group span-2">
                                <label for="phiEncryptionKey" class="v3-form-label">
                                    Secret Encryption Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="phiEncryptionKey" name="phi_commerce_encryption_key" class="v3-form-input" placeholder="Enter Secret Encryption Key" autocomplete="new-password" required>
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
                    <button type="submit" id="btnSavePhiCommercePlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Phi Commerce Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Webhook Return</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Server-to-Server postback URL for Phi Commerce:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/phi_commerce/
                    </div>

                    <h5 class="v3-events-title">Highlights:</h5>
                    <ul class="v3-events-list">
                        <li>Omni-channel link and dynamic QR generation</li>
                        <li>Dynamic multi-currency conversion (DCC)</li>
                        <li>Automated fraud detection engine</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
