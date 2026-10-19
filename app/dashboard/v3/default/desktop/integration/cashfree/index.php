<div class="package-page-wrapper integration-plugin-wrapper" id="pluginCashfreeApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-cashfree">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>Cashfree Payments Gateway</h2>
                <p>High-speed payments suite offering Seamless Checkout, Auto-collect virtual accounts, and Instant Vendor Payouts.</p>
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
            <form id="pluginCashfreeForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Cashfree API Credentials</h3>
                            <p>Enter your App ID and Secret Key from Cashfree Merchant Dashboard &gt; API Keys.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle Cashfree Integration">
                                <input type="checkbox" id="cfActiveToggle" name="enable_cashfree" value="1" checked>
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
                                        <input type="radio" name="cashfree_mode" id="cfModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (api.cashfree.com)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="cashfree_mode" id="cfModeTest" value="test">
                                        <span><strong class="color-orange">Test Sandbox</strong> (sandbox.cashfree.com)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- App ID -->
                            <div class="v3-form-group span-2">
                                <label for="cfAppId" class="v3-form-label">
                                    Cashfree App ID <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="cfAppId" name="cashfree_app_id" class="v3-form-input" placeholder="e.g. CF39218A932..." required>
                                </div>
                            </div>

                            <!-- Secret Key -->
                            <div class="v3-form-group span-2">
                                <label for="cfSecretKey" class="v3-form-label">
                                    Cashfree Secret Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="cfSecretKey" name="cashfree_secret_key" class="v3-form-input" placeholder="Enter Cashfree Secret Key" autocomplete="new-password" required>
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
                    <button type="submit" id="btnSaveCashfreePlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Cashfree Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Webhook Endpoint</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Server Webhook URL for Cashfree payment status updates:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/cashfree/
                    </div>

                    <h5 class="v3-events-title">Capabilities:</h5>
                    <ul class="v3-events-list">
                        <li>Seamless drop-in checkout SDK</li>
                        <li>Auto-collect dynamic virtual bank accounts</li>
                        <li>Instant automated vendor commission split</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
