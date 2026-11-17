<?php

/**
 * Operations Management - Vendor Confirmation & Travel Vouchers (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/voucher.php
 */

$userRole = '';
if (!empty($site_access_type)) {
    $userRole = strtolower(trim($site_access_type));
} elseif (defined('USER_TYPE') && !empty(USER_TYPE)) {
    $userRole = strtolower(trim(USER_TYPE));
} elseif (!empty($_SESSION['user_type'])) {
    $userRole = strtolower(trim($_SESSION['user_type']));
} elseif (!empty($_COOKIE['user_type'])) {
    $userRole = strtolower(trim(base64_decode($_COOKIE['user_type'])));
}

$canAccessOps = in_array($userRole, ['administrator', 'operations', 'super_administrator', 'admin', 'superadmin']);

if (!$canAccessOps) {
    echo '<div class="v3-unauthorized-box p-40 text-center">
            <i class="fi fi-rr-lock font-48 text-red"></i>
            <h3 class="mt-16">Access Restricted</h3>
            <p class="text-muted">You do not have operations permissions to issue travel vouchers.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$prefilledLeadId = isset($_GET['lead_id']) ? htmlspecialchars(trim($_GET['lead_id']), ENT_QUOTES) : '';
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="voucher" data-lead-id="<?php echo $prefilledLeadId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon emerald">
                <i class="fi fi-rr-ticket"></i>
            </div>
            <div class="package-header-titles">
                <h2>Vendor Confirmation &amp; Travel Vouchers</h2>
                <p>Record hotel confirmation reference numbers, driver &amp; fleet contact details, and issue live guest vouchers.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/list/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Confirmed Leads
            </a>
            <a href="/dashboard/operations/requests/" class="btn-v3-action">
                <i class="fi fi-rr-paper-plane"></i> Booking Requests
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Voucher Panel -->
    <div class="package-card-panel p-24 max-w-900 mx-auto">
        <!-- Lead Lookup Box if not prefilled -->
        <div class="ops-lead-picker-box mb-20 <?php echo !empty($prefilledLeadId) ? 'd-none' : ''; ?>" id="opsVoucherPickerWrap">
            <div class="v3-form-group">
                <label class="v3-form-label">Enter Lead ID or Traveler Code *</label>
                <div class="d-flex gap-10">
                    <input type="text" id="voucherLeadIdManualInput" class="v3-form-input max-w-300" placeholder="e.g. 1024 or hash code" value="<?php echo $prefilledLeadId; ?>">
                    <button type="button" class="btn-v3-action btn-green" id="btnLoadVoucherLead">
                        <i class="fi fi-rr-search"></i> Fetch Voucher Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden lead ID container used by operations.js -->
        <input type="hidden" id="voucherLeadId" value="<?php echo $prefilledLeadId; ?>">

        <div class="ops-voucher-guest-banner mb-16" id="voucherGuestBanner">
            <div class="text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Loading traveler details...</div>
        </div>

        <div class="ops-voucher-payment-notice mb-20" id="voucherPaymentNotice">
            <!-- Payment status notice injected dynamically -->
        </div>

        <h5 class="ops-modal-section-title"><i class="fi fi-rr-bed"></i> Booked Services &amp; Confirmation Numbers</h5>
        
        <div class="ops-voucher-items-list mb-24" id="voucherItemsList">
            <div class="text-center p-30 text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Loading booked services...</div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-16 border-top">
            <a href="/dashboard/operations/list/" class="btn-v3-action btn-outline">Back to Confirmed List</a>
            <button type="button" class="btn-v3-action btn-green font-14 py-10 px-20" id="btnConfirmIssueVouchers">
                <i class="fi fi-rr-badge-check"></i> Save Reference IDs &amp; Issue Vouchers
            </button>
        </div>
    </div>
</div>
