<div class="package-page-wrapper integration-plugin-wrapper" id="pluginGoogleAdsApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-google">
                <svg viewBox="0 0 24 24" width="26" height="26">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
            </div>
            <div class="package-header-titles">
                <h2>Google Ads &amp; Lead Extensions</h2>
                <p>Ingest Google Ads Lead Form assets and track conversion ROI with GCLID offline attribution.</p>
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
            <form id="pluginGoogleAdsForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Account Identification -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon amber">
                            <i class="fi fi-rr-bullseye-arrow"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Google Ads Campaign Tracking</h3>
                            <p>Configure your 10-digit Google Ads account identifier and conversion tags.</p>
                        </div>
                        <div class="ms-auto">
                            <label class="v3-toggle-switch" title="Toggle Google Ads Integration">
                                <input type="checkbox" id="gadActiveToggle" name="enable_google_ads" value="1">
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Customer ID -->
                            <div class="v3-form-group span-2">
                                <label for="gadCustomerId" class="v3-form-label">
                                    Google Ads Customer ID (CID) <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="gadCustomerId" name="google_ads_customer_id" class="v3-form-input" placeholder="e.g. 123-456-7890">
                                </div>
                            </div>

                            <!-- Conversion Action ID -->
                            <div class="v3-form-group">
                                <label for="gadConversionId" class="v3-form-label">
                                    Conversion Action ID
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-tags input-icon"></i>
                                    <input type="text" id="gadConversionId" name="google_ads_conversion_id" class="v3-form-input" placeholder="e.g. AW-123456789">
                                </div>
                            </div>

                            <!-- Conversion Label -->
                            <div class="v3-form-group">
                                <label for="gadConversionLabel" class="v3-form-label">
                                    Conversion Label
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-label input-icon"></i>
                                    <input type="text" id="gadConversionLabel" name="google_ads_conversion_label" class="v3-form-input" placeholder="e.g. AbC_dEfGhIjKlMnOp">
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
                    <button type="submit" id="btnSaveGoogleAdsPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Google Ads Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Lead Form Extension Webhook Details -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Webhook Details Box -->
                <div class="v3-card-section v3-card-p20">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Lead Form Webhook</span>
                        <span class="v3-status-badge status-active status-sm">Webhook Active</span>
                    </div>
                    <p class="v3-sidebar-desc">
                        Copy these into your Google Ads Lead Form Asset &gt; <strong>Export leads with a webhook</strong>:
                    </p>
                    
                    <label class="v3-form-label v3-label-sm">Webhook URL</label>
                    <div class="v3-code-snippet-box mb-12">
                        https://app.aleya.in/webhook/google_ads/
                    </div>

                    <label class="v3-form-label v3-label-sm">Key / Secret</label>
                    <input type="text" id="gadWebhookKey" class="v3-form-input v3-mono-input" readonly value="gad_secret_key">
                </div>
            </div>
        </div>
    </div>
</div>
