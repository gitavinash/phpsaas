<div class="package-page-wrapper integration-plugin-wrapper" id="pluginUpiApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-upi">
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
                        <div class="ms-auto">
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
                                    <input type="text" id="upiDetails" name="upi_details" class="v3-form-input" placeholder="e.g. blumox@sbi" required>
                                </div>
                            </div>

                            <!-- Payee Legal Name -->
                            <div class="v3-form-group">
                                <label for="upiPayeeName" class="v3-form-label">
                                    Payee Business Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="upiPayeeName" name="bank_ac_name" class="v3-form-input" placeholder="e.g. Company Name Pvt Ltd">
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
                <div class="v3-card-section v3-card-center-p22">
                    <span class="v3-qr-title">Live Invoice QR Preview</span>

                    <div class="v3-qr-dashed-box">
                        <img id="previewUpiQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi%3A%2F%2Fpay%3Fpa%3Dcompany%40upi" alt="UPI QR" class="v3-qr-preview-img">
                    </div>

                    <div id="previewUpiVpaText" class="v3-qr-vpa-text">company@upi</div>
                    <p class="v3-qr-subtext">Zero gateway fees &bull; Instant direct bank settlement</p>
                </div>
            </div>
        </div>
    </div>
</div>