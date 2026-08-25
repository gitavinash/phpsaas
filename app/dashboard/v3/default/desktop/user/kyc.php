<?php
$encodedId = isset($_GET['id']) ? trim($_GET['id']) : '';
?>
<div class="package-page-wrapper user-form-wrapper" id="userKycApp" data-user-id="<?php echo htmlspecialchars($encodedId); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon user-header-badge">
                <i class="fi fi-rr-assessment"></i>
            </div>
            <div class="package-header-titles">
                <h2>User KYC Verification</h2>
                <p>Verify user identification documents, compliance status, and send verification reminders.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/user/" class="btn-v3-action" data-tooltip="Back to Users directory">
                <i class="fi fi-rr-arrow-left"></i> <span>Team Users</span>
            </a>
        </div>
    </div>

    <!-- KYC Card -->
    <div class="package-table-card form-container-card">
        <div class="kyc-review-container" id="kycReviewBox">
            <div class="table-loading-state" id="kycLoadingState">
                <div class="table-loading-spinner"></div>
                <span>Loading KYC status...</span>
            </div>

            <div class="kyc-content-wrap" id="kycContentWrap" style="display: none;">
                <div class="kyc-user-header">
                    <div class="transport-avatar-badge" id="kycUserAvatar" style="width: 48px; height: 48px; font-size: 18px;">U</div>
                    <div class="kyc-user-meta">
                        <h3 id="kycUserName">-</h3>
                        <p><span id="kycUserEmail">-</span> &bull; <span id="kycUserMobile">-</span></p>
                    </div>
                </div>

                <div class="kyc-status-alert-box" id="kycPendingAlert">
                    <div class="kyc-alert-icon"><i class="fi fi-rr-info"></i></div>
                    <div class="kyc-alert-body">
                        <h4>KYC Documents Pending</h4>
                        <p>This user has not yet submitted their identification and verification documents.</p>
                    </div>
                </div>

                <div class="kyc-actions-row">
                    <button type="button" class="kyc-email-btn" id="btnSendKycEmail">
                        <i class="fi fi-rr-envelope"></i> <span>Send Email Reminder</span>
                    </button>
                    <a href="#" target="_blank" class="kyc-whatsapp-btn" id="btnSendKycWhatsapp">
                        <i class="fi fi-rr-comment-alt"></i> <span>Send WhatsApp Reminder</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>