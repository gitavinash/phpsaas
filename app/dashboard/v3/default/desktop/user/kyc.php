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
            <div class="package-header-icon emerald">
                <i class="fi fi-rr-assessment"></i>
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
                        <div class="kyc-status-alert-box v3-kyc-alert-mb" id="kycStatusBox">
                            <div class="kyc-alert-icon" id="kycStatusIcon"><i class="fi fi-rr-info"></i></div>
                            <div class="kyc-alert-body">
                                <h4 id="kycStatusTitle">Checking KYC Status...</h4>
                                <p id="kycStatusDesc">Please wait while user verification records are retrieved.</p>
                            </div>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="kyc-actions-row v3-kyc-actions-mb">
                            <button type="button" class="kyc-email-btn" id="btnSendKycEmail">
                                <i class="fi fi-rr-envelope"></i> <span id="btnEmailText">Send Email Reminder</span>
                            </button>
                            <a href="#" target="_blank" class="kyc-whatsapp-btn" id="btnSendKycWhatsapp">
                                <i class="fi fi-rr-comment-alt"></i> <span>Send WhatsApp Reminder</span>
                            </a>
                        </div>

                        <!-- Verification Link Box -->
                        <div class="v3-form-group v3-kyc-group-mt">
                            <label class="v3-form-label">
                                Direct KYC Verification URL
                            </label>
                            <div class="v3-flex-gap-8">
                                <div class="v3-input-icon-wrap v3-flex-1">
                                    <i class="fi fi-rr-link-alt input-icon"></i>
                                    <input type="text" id="kycPublicUrlInput" class="v3-form-input" readonly placeholder="Generating KYC URL...">
                                </div>
                                <button type="button" id="btnCopyKycUrl" class="v3-btn-secondary v3-btn-copy-url">
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
                                    Aadhaar Document File <span class="opt v3-label-opt">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycAadharFile" name="aadhar_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="aadharFilePreview" class="v3-preview-file-mt is-hidden"></div>
                            </div>

                            <!-- PAN Card Section -->
                            <div class="v3-form-group">
                                <label for="kycPanNumber" class="v3-form-label">
                                    PAN Card Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-credit-card input-icon"></i>
                                    <input type="text" id="kycPanNumber" name="pan_number" class="v3-form-input text-uppercase" placeholder="e.g. ABCDE1234F" maxlength="10">
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label for="kycPanFile" class="v3-form-label">
                                    PAN Card File <span class="opt v3-label-opt">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycPanFile" name="pan_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="panFilePreview" class="v3-preview-file-mt is-hidden"></div>
                            </div>

                            <!-- GST / Business Registration Section -->
                            <div class="v3-form-group">
                                <label for="kycGstNumber" class="v3-form-label">
                                    GST / Business Registration No.
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="kycGstNumber" name="gst_number" class="v3-form-input text-uppercase" placeholder="15-digit GSTIN number" maxlength="15">
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label for="kycGstFile" class="v3-form-label">
                                    GST Certificate File <span class="opt v3-label-opt">(PDF, JPG, PNG)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-cloud-upload input-icon"></i>
                                    <input type="file" id="kycGstFile" name="gst_file" class="v3-form-input" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div id="gstFilePreview" class="v3-preview-file-mt is-hidden"></div>
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
                                    Compliance Notes / Remarks <span class="opt v3-label-opt">(Optional)</span>
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
                <div class="v3-user-preview-card">
                    <div class="v3-user-preview-header">
                        <span class="v3-user-preview-title">Member Profile</span>
                        <span id="kycSidebarBadge" class="v3-status-badge status-warning badge-xs">Pending</span>
                    </div>

                    <div class="v3-user-preview-hero">
                        <div class="transport-avatar-badge v3-user-preview-avatar" id="kycUserAvatar">
                            U
                        </div>
                        <h3 id="kycUserName" class="v3-user-preview-name">Loading Member...</h3>
                        <span id="kycUserRole" class="badge-service-state badge-role-agent v3-user-preview-role">
                            <i class="fi fi-rr-headset"></i> Sales Agent
                        </span>
                    </div>

                    <div class="v3-user-preview-details">
                        <div class="v3-user-preview-row">
                            <i class="fi fi-rr-phone-call icon-phone-emerald"></i>
                            <span id="kycUserMobile" class="v3-user-preview-val">+91 &mdash;</span>
                        </div>
                        <div class="v3-user-preview-row">
                            <i class="fi fi-rr-envelope icon-mail-indigo"></i>
                            <span id="kycUserEmail" class="v3-user-preview-val">-</span>
                        </div>
                    </div>
                </div>

                <!-- KYC Verification Guidelines Box -->
                <div class="v3-tips-box emerald">
                    <div class="tips-icon"><i class="fi fi-rr-info"></i></div>
                    <div class="tips-content">
                        <strong>Document Guidelines</strong>
                        Ensure uploaded files are clear, legible, and match government databases. Accepted formats: PDF, JPG, PNG (Max 10MB per file).
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>