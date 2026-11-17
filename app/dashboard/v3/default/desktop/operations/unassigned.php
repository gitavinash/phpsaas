<?php

/**
 * Operations Management - Unassigned Leads & Enquiry Allocation Desk
 * Path: app/dashboard/v3/default/desktop/operations/unassigned.php
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
            <p class="text-muted">You do not have operations permissions to access the Unassigned Leads Desk.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="unassigned">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-user-add"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Unassigned Leads &amp; Enquiry Allocation Desk</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Distribute incoming travel inquiries, website leads, and campaign prospects to sales executives and travel consultants.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/" class="btn-v3-action ops-action-btn" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> <span>Hub</span>
            </a>
            <button type="button" id="btnRefreshOps" class="btn-v3-action ops-action-btn" title="Refresh operational data">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Operations Sub-Navigation Tabs Strip -->
    <div class="ops-subnav-strip" id="opsSubNavTabs">
        <a href="/dashboard/operations/list/" class="ops-subnav-tab" title="Confirmed Leads Fulfillment Desk">
            <i class="fi fi-rr-badge-check"></i> <span>Confirmed Leads</span> <span class="badge-count-indigo" id="badgeConfirmedCount">0</span>
        </a>
        <a href="/dashboard/operations/trips/" class="ops-subnav-tab" title="Daily Trip Planner Board">
            <i class="fi fi-rr-plane-departure"></i> <span>Trip Planner</span> <span class="badge-count-amber" id="badgeTripsCount">0</span>
        </a>
        <a href="/dashboard/operations/requests/" class="ops-subnav-tab" title="Dispatched Booking Requests">
            <i class="fi fi-rr-paper-plane"></i> <span>Booking Requests</span>
        </a>
        <a href="/dashboard/operations/unassigned/" class="ops-subnav-tab" title="Unassigned Inbound Leads">
            <i class="fi fi-rr-user-add"></i> <span>Assign Leads</span> <span class="badge-count-red" id="badgeUnassignedCount">0</span>
        </a>
        <a href="/dashboard/operations/templates/" class="ops-subnav-tab" title="Booking Request Templates">
            <i class="fi fi-rr-file-edit"></i> <span>Templates</span>
        </a>
    </div>

    <!-- 2. Unassigned Leads Desk Section -->
    <div class="ops-tab-section active" id="tabSectionUnassigned">
        <div class="ops-filter-toolbar">
            <div class="ops-batch-actions">
                <span id="selectedLeadsCount">0 leads selected</span>
                <button type="button" class="btn-v3-action btn-blue" id="btnOpenBulkAssignModal" disabled>
                    <i class="fi fi-rr-user-add"></i> Assign Selected Leads
                </button>
            </div>
            <div class="ops-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="opsUnassignedSearchInput" placeholder="Filter unassigned leads by guest, mobile, destination...">
            </div>
        </div>

        <div class="package-card-panel ops-table-panel">
            <div class="table-responsive">
                <table class="package-table ops-table" id="opsUnassignedTable">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="chkSelectAllUnassigned"></th>
                            <th>Lead ID &amp; Customer</th>
                            <th>Requirement / Destination</th>
                            <th>Contact Info</th>
                            <th>Source / Campaign</th>
                            <th>Received At</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="opsUnassignedTableBody">
                        <tr>
                            <td colspan="7" class="text-center p-30 text-muted">
                                <i class="fi fi-rr-spinner fi-spin"></i> Loading unassigned leads...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Assign / Reassign Lead Modal -->
<div class="v3-modal-overlay" id="modalOpsAssign">
    <div class="v3-modal-dialog v3-modal-dialog-sm">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-primary">
                    <i class="fi fi-rr-user-add"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title" id="assignModalTitle">Assign Lead to Agent</h4>
                    <span class="v3-modal-subtitle">Select sales executive to allocate enquiry</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="assignTargetLeadIds" value="">
            
            <div class="v3-form-group">
                <label class="v3-form-label">Select Sales Executive *</label>
                <select id="assignAgentSelect" class="v3-form-input">
                    <option value="">-- Choose Agent --</option>
                </select>
            </div>

            <div class="v3-form-group">
                <label class="v3-form-label">Assignment Note / Reason</label>
                <textarea id="assignReasonText" class="v3-form-input" rows="2" placeholder="e.g. Assigned for Kashmir honeymoon package follow-up"></textarea>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-blue" id="btnConfirmAssignLead">
                <i class="fi fi-rr-check"></i> Confirm Assignment
            </button>
        </div>
    </div>
</div>
