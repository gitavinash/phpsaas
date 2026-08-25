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
                        <div style="margin-left: auto;">
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
                                <div style="display: flex; gap: 14px; margin-top: 4px;">
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="phonepe_mode" id="ppModeLive" value="live" checked>
                                        <span><strong style="color: #10b981;">Live Production</strong> (api.phonepe.com)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="phonepe_mode" id="ppModeTest" value="test">
                                        <span><strong style="color: #ea580c;">UAT / Sandbox</strong> (api-preprod.phonepe.com)</span>
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
                <div class="v3-card-section" style="padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Server Webhook</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Automated</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Server Callback URL for payment response status webhook:
                    </p>
                    
                    <div class="v3-code-snippet-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-family: monospace; font-size: 11.5px; color: #334155; word-break: break-all; margin-bottom: 12px;">
                        https://app.aleya.in/webhook/phonepe/
                    </div>

                    <h5 style="font-size: 12px; font-weight: 700; color: #334155; margin: 0 0 6px 0;">Integration Highlights:</h5>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.6;">
                        <li>Zero-redirect UPI Intent flow on mobile</li>
                        <li>High success rate dynamic UPI QR on desktop</li>
                        <li>Cryptographic SHA-256 HMAC checksum validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
