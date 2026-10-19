<div class="package-page-wrapper integration-plugin-wrapper" id="pluginSabpaisaApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-sabpaisa">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>SabPaisa Payment Gateway</h2>
                <p>Unified payment aggregator supporting online checkout, net banking, UPI, and 1.4M+ physical cash collection counters.</p>
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
            <form id="pluginSabpaisaForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Credentials -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>SabPaisa API Credentials</h3>
                            <p>Enter your Client Code, Merchant Username, Password, and AES Auth Keys.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle SabPaisa Integration">
                                <input type="checkbox" id="spActiveToggle" name="enable_sabpaisa" value="1" checked>
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
                                        <input type="radio" name="sabpaisa_mode" id="spModeLive" value="live" checked>
                                        <span><strong class="color-emerald">Live Production</strong> (Real customer payments)</span>
                                    </label>
                                    <label class="v3-env-radio-card">
                                        <input type="radio" name="sabpaisa_mode" id="spModeTest" value="test">
                                        <span><strong class="color-orange">Test Sandbox</strong> (Stage environment)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Client Code -->
                            <div class="v3-form-group">
                                <label for="spClientCode" class="v3-form-label">
                                    Client Code <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-hashtag input-icon"></i>
                                    <input type="text" id="spClientCode" name="sabpaisa_client_code" class="v3-form-input" placeholder="e.g. SP001" required>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="v3-form-group">
                                <label for="spUsername" class="v3-form-label">
                                    Merchant Username <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="spUsername" name="sabpaisa_username" class="v3-form-input" placeholder="e.g. merchant_live" required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="v3-form-group span-2">
                                <label for="spPassword" class="v3-form-label">
                                    Merchant Password <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="spPassword" name="sabpaisa_password" class="v3-form-input" placeholder="Enter SabPaisa Password" autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Auth Key -->
                            <div class="v3-form-group">
                                <label for="spAuthKey" class="v3-form-label">
                                    AES Auth Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="password" id="spAuthKey" name="sabpaisa_auth_key" class="v3-form-input" placeholder="AES 128-bit key" autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Auth IV -->
                            <div class="v3-form-group">
                                <label for="spAuthIv" class="v3-form-label">
                                    AES Auth IV <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="password" id="spAuthIv" name="sabpaisa_auth_iv" class="v3-form-input" placeholder="AES initialization vector" autocomplete="new-password" required>
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
                    <button type="submit" id="btnSaveSabpaisaPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save SabPaisa Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Callback Endpoint</span>
                        <span class="v3-status-badge status-active status-sm">Automated</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        SabPaisa Server-to-Server return URL:
                    </p>
                    
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/sabpaisa/
                    </div>

                    <h5 class="v3-events-title">Key Capabilities:</h5>
                    <ul class="v3-events-list">
                        <li>Multi-bank netbanking aggregator</li>
                        <li>Cash counter payment vouchers (e-Challan)</li>
                        <li>UPI QR and dynamic UPI collect</li>
                        <li>AES encrypted transaction verification</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
