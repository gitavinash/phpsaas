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
            <div class="package-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <i class="fi fi-rr-bank" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2 style="margin: 0;">Bank Accounts &amp; UPI Details</h2>
                <p style="margin-top: 4px;">Configure official bank accounts and UPI payment addresses displayed on customer invoices, vouchers, and payment receipts.</p>
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
                                    <input type="text" id="bankIfsc" name="bank_ifsc" class="v3-form-input" placeholder="e.g. HDFC0001234" maxlength="11" style="text-transform: uppercase;" required>
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
                                    <input type="text" id="upiDetails" name="upi_details" class="v3-form-input" placeholder="e.g. yourcompany@hdfcbank or 9958719998@upi" required>
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
                <div class="v3-live-preview-card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 16px; padding: 22px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25); margin-bottom: 18px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8;">Official Remittance Account</span>
                            <h4 id="previewBankName" style="margin: 2px 0 0 0; font-size: 16px; font-weight: 700; color: #ffffff;">Bank Name</h4>
                        </div>
                        <i class="fi fi-rr-credit-card" style="font-size: 24px; color: #38bdf8;"></i>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <span style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Account Number</span>
                        <div id="previewBankAcNumber" style="font-family: monospace; font-size: 17px; font-weight: 700; letter-spacing: 0.12em; color: #f8fafc; margin-top: 2px;">
                            &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 12px; font-size: 12px;">
                        <div>
                            <span style="font-size: 9.5px; color: #94a3b8; text-transform: uppercase;">Beneficiary</span>
                            <div id="previewBankAcName" style="font-weight: 600; color: #f1f5f9;">Company Name</div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 9.5px; color: #94a3b8; text-transform: uppercase;">IFSC Code</span>
                            <div id="previewBankIfsc" style="font-weight: 700; font-family: monospace; color: #38bdf8;">IFSC0000000</div>
                        </div>
                    </div>
                </div>

                <!-- Live UPI QR Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Instant UPI QR</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Ready</span>
                    </div>

                    <div style="background: #f8fafc; border-radius: 12px; padding: 14px; display: inline-block; margin-bottom: 10px; border: 1px solid #e2e8f0;">
                        <img id="previewUpiQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&amp;data=upi://pay?pa=company@upi" alt="UPI QR" style="width: 130px; height: 130px; display: block; border-radius: 6px;">
                    </div>

                    <div style="font-size: 13px; font-weight: 700; color: #0f172a; word-break: break-all;" id="previewUpiVpa">
                        company@upi
                    </div>
                    <p style="font-size: 11.5px; color: #64748b; margin: 4px 0 0 0;">Scan with GPay, PhonePe, Paytm, or BHIM</p>
                </div>
            </div>
        </div>
    </div>
</div>
