<?php

/**
 * Operations Management - Chauffeur & Vehicle Dispatch (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/dispatch.php
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
            <p class="text-muted">You do not have operations permissions to dispatch drivers.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$prefilledLeadId = isset($_GET['lead_id']) ? htmlspecialchars(trim($_GET['lead_id']), ENT_QUOTES) : '';
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="dispatch" data-lead-id="<?php echo $prefilledLeadId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon primary">
                <i class="fi fi-rr-car"></i>
            </div>
            <div class="package-header-titles">
                <h2>Chauffeur &amp; Vehicle Allocation</h2>
                <p>Assign on-ground drivers, record vehicle plate registration, and dispatch 24-hr pre-trip logistics details directly to traveler WhatsApp.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/trips/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Trip Planner
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Dispatch Form Panel -->
    <div class="package-card-panel p-24 max-w-700 mx-auto">
        <!-- Lead Lookup Box if not prefilled -->
        <div class="ops-lead-picker-box mb-20 <?php echo !empty($prefilledLeadId) ? 'd-none' : ''; ?>" id="opsDispatchPickerWrap">
            <div class="v3-form-group">
                <label class="v3-form-label">Enter Lead ID or Traveler Code *</label>
                <div class="d-flex gap-10">
                    <input type="text" id="dispatchLeadIdManualInput" class="v3-form-input max-w-300" placeholder="e.g. 1024 or hash code" value="<?php echo $prefilledLeadId; ?>">
                    <button type="button" class="btn-v3-action btn-blue" id="btnLoadDispatchLead">
                        <i class="fi fi-rr-search"></i> Select Trip Lead
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden lead ID container used by operations.js -->
        <input type="hidden" id="dispatchLeadId" value="<?php echo $prefilledLeadId; ?>">

        <div class="ops-form-row">
            <div class="v3-form-group">
                <label class="v3-form-label">Driver / Chauffeur Name *</label>
                <input type="text" id="dispatchDriverName" class="v3-form-input" placeholder="e.g. Tariq Ahmad">
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Driver Mobile / WhatsApp *</label>
                <input type="text" id="dispatchDriverPhone" class="v3-form-input" placeholder="e.g. 9876543210">
            </div>
        </div>

        <div class="ops-form-row">
            <div class="v3-form-group">
                <label class="v3-form-label">Vehicle Model / Category</label>
                <input type="text" id="dispatchVehicleType" class="v3-form-input" placeholder="e.g. Innova Crysta">
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Vehicle Registration Number *</label>
                <input type="text" id="dispatchVehicleNo" class="v3-form-input" placeholder="e.g. JK-01-AB-1234">
            </div>
        </div>

        <div class="ops-form-row">
            <div class="v3-form-group">
                <label class="v3-form-label">Scheduled Pickup Time</label>
                <input type="text" id="dispatchPickupTime" class="v3-form-input" placeholder="e.g. 08:30 AM">
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Pickup Point / Airport / Hotel</label>
                <input type="text" id="dispatchPickupLoc" class="v3-form-input" placeholder="e.g. Srinagar Airport Gate 2">
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-20 border-top mt-16">
            <a href="/dashboard/operations/trips/" class="btn-v3-action btn-outline">Back to Trip Planner</a>
            <button type="button" class="btn-v3-action btn-blue font-14 py-10 px-20" id="btnConfirmDriverDispatch">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Driver to Traveler WhatsApp
            </button>
        </div>
    </div>
</div>
