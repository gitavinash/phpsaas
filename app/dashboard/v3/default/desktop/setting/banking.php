<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingBankingApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-bank"></i>
            </div>
            <div class="package-header-titles">
                <h2>Bank Accounts &amp; UPI Details</h2>
                <p>Configure official bank accounts and UPI payment addresses displayed on customer invoices, vouchers, and payment receipts.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/setting/" class="btn-v3-action" data-tooltip="Back to Settings">
                <i class="fi fi-rr-settings"></i> <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="settingBankingForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Official Bank Account -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-bank"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Primary Bank Account</h3>
                            <p>Direct NEFT / RTGS / IMPS bank account details for invoice remittances.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Bank Name -->
                            <div class="v3-form-group">
                                <label for="bankName" class="v3-form-label">
                                    Bank Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="bankName" name="bank_name" class="v3-form-input" placeholder="e.g. HDFC Bank, ICICI Bank" required>
                                </div>
                                <span class="v3-field-error" id="errBankName"></span>
                            </div>

                            <!-- Account Holder Name -->
                            <div class="v3-form-group">
                                <label for="bankAcName" class="v3-form-label">
                                    Account Holder / Beneficiary Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="bankAcName" name="bank_ac_name" class="v3-form-input" placeholder="e.g. Acme Holidays Pvt Ltd" required>
                                </div>
                                <span class="v3-field-error" id="errBankAcName"></span>
                            </div>

                            <!-- Account Number -->
                            <div class="v3-form-group">
                                <label for="bankAcNumber" class="v3-form-label">
                                    Bank Account Number <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-credit-card input-icon"></i>
                                    <input type="text" id="bankAcNumber" name="bank_ac_number" class="v3-form-input" placeholder="e.g. 50200012345678" required>
                                </div>
                                <span class="v3-field-error" id="errBankAcNumber"></span>
                            </div>

                            <!-- IFSC Code -->
                            <div class="v3-form-group">
                                <label for="bankIfsc" class="v3-form-label">
                                    IFSC Code <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="text" id="bankIfsc" name="bank_ifsc" class="v3-form-input text-uppercase" placeholder="e.g. HDFC0001234" maxlength="11" required>
                                </div>
                                <span class="v3-field-error" id="errBankIfsc"></span>
                            </div>

                            <!-- Branch Address -->
                            <div class="v3-form-group span-2">
                                <label for="bankAddress" class="v3-form-label">
                                    Bank Branch &amp; Address
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-marker input-icon"></i>
                                    <input type="text" id="bankAddress" name="bank_address" class="v3-form-input" placeholder="e.g. Connaught Place Branch, New Delhi">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: UPI Payment ID & QR Settings -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-qr-scan"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>UPI Payment Address (VPA)</h3>
                            <p>Enable instant UPI payments via Google Pay, PhonePe, Paytm, and BHIM QR.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- UPI VPA ID -->
                            <div class="v3-form-group span-2">
                                <label for="upiDetails" class="v3-form-label">
                                    Official UPI ID / VPA <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-at input-icon"></i>
                                    <input type="text" id="upiDetails" name="upi_details" class="v3-form-input" placeholder="e.g. yourcompany@hdfcbank or 9990026008@upi" required>
                                </div>
                                <span class="v3-field-error" id="errUpiDetails"></span>
                            </div>

                            <!-- Custom Banking Instructions / Footer Note -->
                            <div class="v3-form-group span-2">
                                <label for="bankDetails" class="v3-form-label">
                                    Additional Payment Instructions &amp; Invoice Note
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <textarea id="bankDetails" name="bank_details" class="v3-form-textarea" rows="3" placeholder="e.g. Please share payment screenshot on WhatsApp after transfer for instant confirmation."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveBanking" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveBankingText">Save Bank &amp; UPI Details</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Card Preview -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Virtual Bank Remittance Preview Card -->
                <div class="v3-bank-debit-card">
                    <div class="v3-bank-card-top">
                        <div>
                            <span class="v3-bank-card-top-label">Official Remittance Account</span>
                            <h4 id="previewBankName" class="v3-bank-card-name">Bank Name</h4>
                        </div>
                        <i class="fi fi-rr-credit-card v3-bank-card-icon"></i>
                    </div>

                    <div class="v3-bank-card-middle">
                        <span class="v3-bank-card-middle-label">Account Number</span>
                        <div id="previewBankAcNumber" class="v3-bank-card-number">
                            &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;
                        </div>
                    </div>

                    <div class="v3-bank-card-bottom">
                        <div>
                            <span class="v3-bank-card-bottom-label">Beneficiary</span>
                            <div id="previewBankAcName" class="v3-bank-card-beneficiary">Company Name</div>
                        </div>
                        <div class="text-end">
                            <span class="v3-bank-card-bottom-label">IFSC Code</span>
                            <div id="previewBankIfsc" class="v3-bank-card-ifsc">IFSC0000000</div>
                        </div>
                    </div>
                </div>

                <!-- Live UPI QR Card -->
                <div class="v3-live-preview-card v3-upi-qr-card">
                    <div class="v3-live-preview-header">
                        <span class="v3-live-preview-label">Instant UPI QR</span>
                        <span class="v3-status-badge status-active badge-xs">Ready</span>
                    </div>

                    <div class="v3-upi-qr-frame">
                        <img id="previewUpiQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&amp;data=upi://pay?pa=company@upi" alt="UPI QR" class="v3-upi-qr-img">
                    </div>

                    <div class="v3-upi-vpa-text" id="previewUpiVpa">
                        company@upi
                    </div>
                    <p class="v3-upi-help-text">Scan with GPay, PhonePe, Paytm, or BHIM</p>
                </div>
            </div>
        </div>
    </div>
</div>