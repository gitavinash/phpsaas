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
                        <div class="ms-auto">
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
                                <div class="v3-env-mode-wrap">
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="worldline_mode" id="wlModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (ipg.in.worldline.com)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="worldline_mode" id="wlModeTest" value="test">
                                        <span><strong class="color-orange">Test Sandbox</strong> (test.worldline.com)</span>
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
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Response URL</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Server Response Return URL for Worldline merchant account:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/worldline/
                    </div>

                    <h5 class="v3-events-title">Enterprise Capabilities:</h5>
                    <ul class="v3-events-list">
                        <li>Multi-currency transaction processing</li>
                        <li>SHA-512 cryptographic request &amp; response verification</li>
                        <li>High volume enterprise throughput</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
