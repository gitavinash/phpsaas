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
                        <div style="margin-left: auto;">
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
                                <div style="display: flex; gap: 14px; margin-top: 4px;">
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="instamojo_mode" id="imModeLive" value="live" checked>
                                        <span><strong style="color: #10b981;">Live Production</strong> (Real customer payments)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="instamojo_mode" id="imModeTest" value="test">
                                        <span><strong style="color: #ea580c;">Test Sandbox</strong> (test.instamojo.com)</span>
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
                <div class="v3-card-section" style="padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Webhook Listener</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Automated</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Instamojo Webhook URL for payment completion and refund notifications:
                    </p>
                    
                    <div class="v3-code-snippet-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-family: monospace; font-size: 11.5px; color: #334155; word-break: break-all; margin-bottom: 12px;">
                        https://app.aleya.in/webhook/instamojo/
                    </div>

                    <h5 style="font-size: 12px; font-weight: 700; color: #334155; margin: 0 0 6px 0;">Supported Channels:</h5>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.6;">
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
