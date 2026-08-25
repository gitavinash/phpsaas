<div class="package-page-wrapper integration-plugin-wrapper" id="pluginWorldlineApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-worldline">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>Worldline (Ingenico) Gateway</h2>
                <p>Enterprise multi-currency global payments gateway supporting Cards, Net Banking, e-Mandates, and SHA-512 validation.</p>
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
            <form id="pluginWorldlineForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Worldline Merchant Credentials</h3>
                            <p>Configure your Merchant Code, SHA Encryption Salt, and Scheme Parameters.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle Worldline Integration">
                                <input type="checkbox" id="wlActiveToggle" name="enable_worldline" value="1" checked>
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
                                        <input type="radio" name="worldline_mode" id="wlModeLive" value="live" checked>
                                        <span><strong style="color: #10b981;">Live Production</strong> (ipg.in.worldline.com)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="worldline_mode" id="wlModeTest" value="test">
                                        <span><strong style="color: #ea580c;">Test Sandbox</strong> (test.worldline.com)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Merchant Code -->
                            <div class="v3-form-group span-2">
                                <label for="wlMerchantCode" class="v3-form-label">
                                    Merchant Code <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="wlMerchantCode" name="worldline_merchant_code" class="v3-form-input" placeholder="e.g. L93821" required>
                                </div>
                            </div>

                            <!-- Salt Key -->
                            <div class="v3-form-group span-2">
                                <label for="wlSaltKey" class="v3-form-label">
                                    SHA-512 Encryption Salt <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="password" id="wlSaltKey" name="worldline_salt_key" class="v3-form-input" placeholder="Enter Worldline Salt Key" autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Scheme Code -->
                            <div class="v3-form-group">
                                <label for="wlScheme" class="v3-form-label">
                                    Scheme Identifier <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-hashtag input-icon"></i>
                                    <input type="text" id="wlScheme" name="worldline_scheme" class="v3-form-input" placeholder="e.g. FIRST" value="FIRST" required>
                                </div>
                            </div>

                            <!-- Currency -->
                            <div class="v3-form-group">
                                <label for="wlCurrency" class="v3-form-label">
                                    Default Currency <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-dollar input-icon"></i>
                                    <input type="text" id="wlCurrency" name="worldline_currency" class="v3-form-input" placeholder="e.g. INR" value="INR" required>
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
                    <button type="submit" id="btnSaveWorldlinePlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Worldline Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section" style="padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Response URL</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Automated</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Server Response Return URL for Worldline merchant account:
                    </p>
                    
                    <div class="v3-code-snippet-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-family: monospace; font-size: 11.5px; color: #334155; word-break: break-all; margin-bottom: 12px;">
                        https://app.aleya.in/webhook/worldline/
                    </div>

                    <h5 style="font-size: 12px; font-weight: 700; color: #334155; margin: 0 0 6px 0;">Enterprise Capabilities:</h5>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.6;">
                        <li>Multi-currency transaction processing</li>
                        <li>SHA-512 cryptographic request &amp; response verification</li>
                        <li>High volume enterprise throughput</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
