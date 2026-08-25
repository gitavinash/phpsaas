<?php
$encodedId = $id ?? ($_GET['id'] ?? '');
if (empty($encodedId) && !empty($_GET['user_id'])) {
    $encodedId = base64_encode((string)$_GET['user_id']);
}
?>
<div class="package-page-wrapper user-form-wrapper" id="userKycApp" data-user-id="<?php echo htmlspecialchars($encodedId); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="fi fi-rr-assessment" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2>User KYC &amp; Compliance Verification</h2>
                <p>Verify user identification documents, compliance clearance, and dispatch verification reminders.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/user/edit/?id=<?php echo htmlspecialchars($encodedId); ?>" class="btn-v3-action" data-tooltip="Edit User Profile">
                <i class="fi fi-rr-edit"></i> <span>Edit Profile</span>
            </a>
            <a href="/dashboard/user/" class="btn-v3-action" data-tooltip="Back to Team Directory">
                <i class="fi fi-rr-arrow-left"></i> <span>Team Directory</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="userKycUploadForm" class="v3-form-container" autocomplete="off" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="kycUserId" name="id" value="<?php echo htmlspecialchars($encodedId); ?>">

                <!-- Card 1: Verification Status & Reminder Actions -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Compliance Status &amp; Direct Reminders</h3>
                            <p>Send one-click notification reminders containing the secure KYC upload URL.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <!-- Status Alert Box -->
                        <div class="kyc-status-alert-box" id="kycStatusBox" style="margin-bottom: 18px;">
                            <div class="kyc-alert-icon" id="kycStatusIcon"><i class="fi fi-rr-info"></i></div>
                            <div class="kyc-alert-body">
                                <h4 id="kycStatusTitle">Checking KYC Status...</h4>
                                <p id="kycStatusDesc">Please wait while user verification records are retrieved.</p>
                            </div>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="kyc-actions-row" style="margin-bottom: 16px;">
                            <button type="button" class="kyc-email-btn" id="btnSendKycEmail">
                                <i class="fi fi-rr-envelope"></i> <span id="btnEmailText">Send Email Reminder</span>
                            </button>
                            <a href="#" target="_blank" class="kyc-whatsapp-btn" id="btnSendKycWhatsapp">
                                <i class="fi fi-rr-comment-alt"></i> <span>Send WhatsApp Reminder</span>
                            </a>
                        </div>

                        <!-- Verification Link Box -->
                        <div class="v3-form-group" style="margin-top: 14px;">
                            <label class="v3-form-label">
                                Direct KYC Verification URL
                            </label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <div class="v3-input-icon-wrap" style="flex: 1;">
                                    <i class="fi fi-rr-link-alt input-icon"></i>
                                    <input type="text" id="kycPublicUrlInput" class="v3-form-input" readonly placeholder="Generating KYC URL...">
                                </div>
                                <button type="button" id="btnCopyKycUrl" class="v3-btn-secondary" style="height: 38px; padding: 0 16px; white-space: nowrap;">
                                    <i class="fi fi-rr-copy"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Document Upload & Identification Numbers -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-document-signed"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Identification Documents</h3>
                            <p>Upload government-approved identity cards and business registration certificates.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Aadhaar Card Section -->
                            <div class="v3-form-group">
                                <label for="kycAadharNumber" class="v3-form-label">
                                    Aadhaar Card Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="kycAadharNumber" name="aadhar_number" class="v3-form-input" placeholder="12-digit Aadhaar number" maxlength="16">
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label for="kycAadharFile" class="v3-form-label">
                                    Aadhaar Document File <span class="opt" style="font-weight: normal; color: #94a3b8;">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycAadharFile" name="aadhar_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="aadharFilePreview" style="margin-top: 6px; display: none;"></div>
                            </div>

                            <!-- PAN Card Section -->
                            <div class="v3-form-group">
                                <label for="kycPanNumber" class="v3-form-label">
                                    PAN Card Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-credit-card input-icon"></i>
                                    <input type="text" id="kycPanNumber" name="pan_number" class="v3-form-input" placeholder="e.g. ABCDE1234F" maxlength="10" style="text-transform: uppercase;">
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label for="kycPanFile" class="v3-form-label">
                                    PAN Card File <span class="opt" style="font-weight: normal; color: #94a3b8;">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycPanFile" name="pan_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="panFilePreview" style="margin-top: 6px; display: none;"></div>
                            </div>

                            <!-- GST / Business Registration Section -->
                            <div class="v3-form-group">
                                <label for="kycGstNumber" class="v3-form-label">
                                    GST / Business Registration No.
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="kycGstNumber" name="gst_number" class="v3-form-input" placeholder="15-digit GSTIN number" maxlength="15" style="text-transform: uppercase;">
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label for="kycGstFile" class="v3-form-label">
                                    GST Certificate File <span class="opt" style="font-weight: normal; color: #94a3b8;">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycGstFile" name="gst_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="gstFilePreview" style="margin-top: 6px; display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Verification Clearance & Notes -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-check-circle"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Verification Decision &amp; Audit Notes</h3>
                            <p>Update verification clearance status and maintain compliance history.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- KYC Clearance Status -->
                            <div class="v3-form-group">
                                <label for="kycStatusSelect" class="v3-form-label">
                                    KYC Clearance Decision <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <select id="kycStatusSelect" name="kyc_status" class="v3-form-select" required>
                                        <option value="0">Pending Verification (Documents Submitted / Awaiting Review)</option>
                                        <option value="1">Approved &amp; Verified (All Documents Valid)</option>
                                        <option value="2">Rejected / Incomplete (Requires Resubmission)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="v3-form-group">
                                <label for="kycRemarks" class="v3-form-label">
                                    Compliance Notes / Remarks <span class="opt" style="font-weight: normal; color: #94a3b8;">(Optional)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <input type="text" id="kycRemarks" name="remarks" class="v3-form-input" placeholder="e.g. Identity verified on Govt portal">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/user/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveKyc" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveKycText">Save &amp; Update KYC Documents</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Member Profile & Guidelines Sidebar -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Member Live Preview Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Member Profile</span>
                        <span id="kycSidebarBadge" class="v3-status-badge status-warning" style="font-size: 11px;">Pending</span>
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 16px;">
                        <div class="transport-avatar-badge" id="kycUserAvatar" style="width: 64px; height: 64px; font-size: 22px; font-weight: 700; border-radius: 16px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                            U
                        </div>
                        <h3 id="kycUserName" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Loading Member...</h3>
                        <span id="kycUserRole" class="badge-service-state badge-role-agent" style="font-size: 11.5px; margin-top: 2px;">
                            <i class="fi fi-rr-headset"></i> Sales Agent
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; background: #f8fafc; border-radius: 10px; padding: 12px; border: 1px solid #edf2f7; font-size: 12.5px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155;">
                            <i class="fi fi-rr-phone-call" style="color: #10b981; font-size: 12px; width: 14px;"></i>
                            <span id="kycUserMobile" style="font-weight: 500;">+91 &mdash;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <i class="fi fi-rr-envelope" style="color: #6366f1; font-size: 12px; width: 14px;"></i>
                            <span id="kycUserEmail" style="font-weight: 500; overflow: hidden; text-overflow: ellipsis;">-</span>
                        </div>
                    </div>
                </div>

                <!-- KYC Verification Guidelines Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-info"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">Document Guidelines</strong>
                        Ensure uploaded files are clear, legible, and match government databases. Accepted formats: PDF, JPG, PNG (Max 10MB per file).
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>