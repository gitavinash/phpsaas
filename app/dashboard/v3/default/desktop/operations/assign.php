<?php

/**
 * Operations Management - Assign / Reassign Lead (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/assign.php
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
            <p class="text-muted">You do not have operations permissions to assign leads.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$prefilledLeadId = isset($_GET['lead_id']) ? htmlspecialchars(trim($_GET['lead_id']), ENT_QUOTES) : '';
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="assign" data-lead-id="<?php echo $prefilledLeadId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-user-add"></i>
            </div>
            <div class="package-header-titles">
                <h2>Allocate Lead to Sales Executive</h2>
                <p>Assign or reassign traveler inquiries to designated sales agents and consultants with audit notes.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/unassigned/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Unassigned Desk
            </a>
            <a href="/dashboard/operations/list/" class="btn-v3-action">
                <i class="fi fi-rr-badge-check"></i> Confirmed Leads
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Assign Form Panel -->
    <div class="package-card-panel p-24 max-w-600 mx-auto">
        <div class="v3-form-group">
            <label class="v3-form-label">Target Lead ID(s) *</label>
            <input type="text" id="assignTargetLeadManual" class="v3-form-input" placeholder="e.g. 1024 or comma-separated IDs" value="<?php echo $prefilledLeadId; ?>">
            <small class="text-muted mt-4 d-block">You can enter a single lead ID or multiple IDs separated by commas.</small>
        </div>

        <!-- Hidden input utilized by operations.js submit handler -->
        <input type="hidden" id="assignTargetLeadIds" value="<?php echo $prefilledLeadId; ?>">

        <div class="v3-form-group">
            <label class="v3-form-label">Select Sales Executive *</label>
            <select id="assignAgentSelect" class="v3-form-input">
                <option value="">-- Choose Sales Agent --</option>
            </select>
        </div>

        <div class="v3-form-group">
            <label class="v3-form-label">Assignment Note / Reason</label>
            <textarea id="assignReasonText" class="v3-form-input" rows="3" placeholder="e.g. Assigned for Kashmir honeymoon package consultation and follow-up"></textarea>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-20 border-top mt-16">
            <a href="/dashboard/operations/unassigned/" class="btn-v3-action btn-outline">Back to Queue</a>
            <button type="button" class="btn-v3-action btn-blue font-14 py-10 px-20" id="btnConfirmAssignLead">
                <i class="fi fi-rr-check"></i> Confirm Assignment
            </button>
        </div>
    </div>
</div>
