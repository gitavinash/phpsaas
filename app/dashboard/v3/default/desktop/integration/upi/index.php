<div class="package-page-wrapper integration-plugin-wrapper" id="pluginUpiApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-upi" style="background: #e0f2fe; color: #0284c7; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px;">
                <i class="fi fi-rr-qr-scan"></i>
            </div>
            <div class="package-header-titles">
                <h2>UPI &amp; Dynamic QR Remittance</h2>
                <p>Configure official UPI VPA handles and live dynamic QR code generation for vouchers and invoices.</p>
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
            <form id="pluginUpiForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: UPI Details -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-bank"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Merchant UPI VPA Identification</h3>
                            <p>Configure your verified business UPI Virtual Payment Address.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle UPI Integration">
                                <input type="checkbox" id="upiActiveToggle" name="enable_upi" value="1" checked>
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- UPI VPA Handle -->
                            <div class="v3-form-group span-2">
                                <label for="upiDetails" class="v3-form-label">
                                    Primary UPI ID / VPA <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-at input-icon"></i>
                                    <input type="text" id="upiDetails" name="upi_details" class="v3-form-input" placeholder="e.g. musafirindiatrip@icici" required>
                                </div>
                            </div>

                            <!-- Payee Legal Name -->
                            <div class="v3-form-group">
                                <label for="upiPayeeName" class="v3-form-label">
                                    Payee Business Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="upiPayeeName" name="bank_ac_name" class="v3-form-input" placeholder="e.g. Musafir India Trip Pvt Ltd">
                                </div>
                            </div>

                            <!-- Merchant Category Code -->
                            <div class="v3-form-group">
                                <label for="upiMcc" class="v3-form-label">
                                    Merchant Category Code (MCC)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-tags input-icon"></i>
                                    <input type="text" id="upiMcc" name="upi_merchant_code" class="v3-form-input" placeholder="4722 (Travel Agencies)">
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
                    <button type="submit" id="btnSaveUpiPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save UPI Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Dynamic QR Preview -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- QR Card -->
                <div class="v3-card-section" style="padding: 22px; text-align: center;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; display: block; margin-bottom: 12px;">Live Invoice QR Preview</span>
                    
                    <div style="background: #ffffff; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 14px; display: inline-block; margin-bottom: 12px;">
                        <img id="previewUpiQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi%3A%2F%2Fpay%3Fpa%3Dcompany%40upi" alt="UPI QR" style="width: 140px; height: 140px; display: block;">
                    </div>

                    <div id="previewUpiVpaText" style="font-size: 13.5px; font-weight: 700; color: #0f172a; font-family: monospace;">company@upi</div>
                    <p style="font-size: 11.5px; color: #64748b; margin: 4px 0 0 0;">Zero gateway fees &bull; Instant direct bank settlement</p>
                </div>
            </div>
        </div>
    </div>
</div>
