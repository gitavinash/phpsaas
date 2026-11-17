<?php

/**
 * Operations Management - Dispatched Booking Requests Ledger
 * Path: app/dashboard/v3/default/desktop/operations/requests.php
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
            <p class="text-muted">You do not have operations permissions to access the Booking Requests Ledger.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="requests">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-paper-plane"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Dispatched Booking Requests Ledger</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Track live confirmation statuses of reservation requests dispatched to hoteliers, fleet operators, and activity guides.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- New Booking Request CTA -->
            <a href="/dashboard/operations/booking-request/" class="btn-v3-action btn-blue ops-action-btn" title="Dispatch New Vendor Booking Request">
                <i class="fi fi-rr-plus"></i> <span>Send Request</span>
            </a>

            <!-- Hub Link -->
            <a href="/dashboard/operations/" class="btn-v3-action ops-action-btn" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> <span>Hub</span>
            </a>

            <!-- Refresh Button -->
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

    <!-- 2. Dispatched Requests Ledger Section -->
    <div class="ops-tab-section active" id="tabSectionRequests">
        <div class="ops-filter-toolbar">
            <div class="ops-vendor-pills" id="bkgLedgerVendorFilter">
                <button type="button" class="btn-ops-pill active" data-vendor="all">All Services</button>
                <button type="button" class="btn-ops-pill" data-vendor="hotel"><i class="fi fi-rr-bed"></i> Hotels</button>
                <button type="button" class="btn-ops-pill" data-vendor="transport"><i class="fi fi-rr-car"></i> Transporters</button>
                <button type="button" class="btn-ops-pill" data-vendor="activity"><i class="fi fi-rr-flag"></i> Activities</button>
            </div>

            <div class="ops-status-pills" id="bkgLedgerStatusFilter">
                <button type="button" class="btn-ops-pill active" data-status="">All Statuses</button>
                <button type="button" class="btn-ops-pill" data-status="0">Pending Response</button>
                <button type="button" class="btn-ops-pill" data-status="1">Vendor Confirmed</button>
                <button type="button" class="btn-ops-pill" data-status="2">Voucher Issued</button>
                <button type="button" class="btn-ops-pill" data-status="3">Declined</button>
            </div>

            <div class="ops-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="opsRequestsSearchInput" placeholder="Filter by vendor, guest, lead ID...">
            </div>
        </div>

        <div class="package-card-panel ops-table-panel">
            <div class="table-responsive">
                <table class="package-table ops-table" id="opsRequestsTable">
                    <thead>
                        <tr>
                            <th>Booking ID &amp; Vendor</th>
                            <th>Service</th>
                            <th>Lead &amp; Guest</th>
                            <th>Dates / Pickup</th>
                            <th>Room / Fleet</th>
                            <th>Confirmation Ref</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="opsRequestsTableBody">
                        <tr>
                            <td colspan="8" class="text-center p-30 text-muted">
                                <i class="fi fi-rr-spinner fi-spin"></i> Loading dispatched booking requests...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ops-pagination-bar" id="opsRequestsPagination">
                <div class="ops-pagination-info" id="opsRequestsPaginationInfo">Showing 0 of 0 requests</div>
                <div class="ops-pagination-controls" id="opsRequestsPaginationControls"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Quick Record Vendor Response / Confirmation Ref ID -->
<div class="v3-modal-overlay" id="modalOpsUpdateBookingStatus">
    <div class="v3-modal-dialog v3-modal-dialog-sm">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-emerald">
                    <i class="fi fi-rr-check"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Record Vendor Response</h4>
                    <span class="v3-modal-subtitle">Update vendor confirmation status and reference ID</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="quickBookingId" value="0">

            <div class="v3-form-group">
                <label class="v3-form-label">Vendor Status *</label>
                <select id="quickBookingStatus" class="v3-form-input">
                    <option value="1">Confirmed by Vendor</option>
                    <option value="3">Declined / Sold Out</option>
                    <option value="0">Pending Awaiting Response</option>
                </select>
            </div>

            <div class="v3-form-group">
                <label class="v3-form-label">Confirmation / Booking Reference ID *</label>
                <input type="text" id="quickBookingRefId" class="v3-form-input" placeholder="e.g. HTL-2026-9901">
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnConfirmQuickBookingUpdate">
                <i class="fi fi-rr-check"></i> Save Response
            </button>
        </div>
    </div>
</div>
