<div class="package-page-wrapper integration-plugin-wrapper" id="pluginInstamojoApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-instamojo">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>Instamojo Payment Gateway</h2>
                <p>Accept cards, netbanking, and digital wallets with instant automated receipt dispatch and payout reconciliations.</p>
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
            <form id="pluginInstamojoForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Instamojo API Credentials</h3>
                            <p>Configure your API Key, Auth Token, and Private Salt from Instamojo Developers portal.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle Instamojo Integration">
                                <input type="checkbox" id="imActiveToggle" name="enable_instamojo" value="1" checked>
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
                                        <input type="radio" name="instamojo_mode" id="imModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (Real customer payments)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="instamojo_mode" id="imModeTest" value="test">
                                        <span><strong class="color-orange">Test Sandbox</strong> (test.instamojo.com)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Private API Key -->
                            <div class="v3-form-group span-2">
                                <label for="imApiKey" class="v3-form-label">
                                    Private API Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="text" id="imApiKey" name="instamojo_api_key" class="v3-form-input" placeholder="e.g. 3a7f829d81e04b9..." required>
                                </div>
                            </div>

                            <!-- Private Auth Token -->
                            <div class="v3-form-group span-2">
                                <label for="imAuthToken" class="v3-form-label">
                                    Private Auth Token <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="imAuthToken" name="instamojo_auth_token" class="v3-form-input" placeholder="e.g. 7c91a0248f..." autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Private Salt Key -->
                            <div class="v3-form-group span-2">
                                <label for="imSaltKey" class="v3-form-label">
                                    Private Salt Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="password" id="imSaltKey" name="instamojo_salt_key" class="v3-form-input" placeholder="e.g. e8192a74..." autocomplete="new-password" required>
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
                    <button type="submit" id="btnSaveInstamojoPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Instamojo Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Webhook Listener</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Instamojo Webhook URL for payment completion and refund notifications:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/instamojo/
                    </div>

                    <h5 class="v3-events-title">Supported Channels:</h5>
                    <ul class="v3-events-list">
                        <li>Debit / Credit Cards (Mastercard, Visa, RuPay)</li>
                        <li>Net Banking across 58+ banks</li>
                        <li>UPI (GPay, PhonePe, Paytm, BHIM)</li>
                        <li>Convenience fee calculation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
