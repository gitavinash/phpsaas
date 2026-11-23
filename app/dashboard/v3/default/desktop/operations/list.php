<?php

/**
 * Operations Management - Confirmed Leads Fulfillment Desk
 * Path: app/dashboard/v3/default/desktop/operations/list.php
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
            <p class="text-muted">You do not have operations permissions to access the Operations Fulfillment Desk.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="list">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Confirmed Bookings Fulfillment</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Live quotation approvals, advance payments, voucher dispatches, and traveler fulfillments.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Header Quick Action Buttons -->
            <a href="/dashboard/operations/" class="btn-v3-action ops-action-btn" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> <span>Hub</span>
            </a>
            <button type="button" id="btnRefreshOps" class="btn-v3-action ops-action-btn" title="Refresh operational data">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- 2. Operations Sub-Navigation Tabs Strip -->
    <div class="ops-subnav-strip" id="opsSubNavTabs">
        <a href="/dashboard/operations/list/" class="ops-subnav-tab" title="Confirmed Bookings Fulfillment Desk">
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

    <!-- 2. KPI Metric Cards (5 columns balanced across desktop >= 1280px) -->
    <div class="package-kpi-row ops-kpi-row">
        <div class="pkg-mini-kpi indigo active" data-filter-status="all" title="Click to view all confirmed bookings">
            <div class="kpi-icon"><i class="fi fi-rr-bookmark"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Bookings</p>
                <h3 id="statConfirmedTotal">0</h3>
                <small>Approved &amp; onwards</small>
            </div>
        </div>

        <div class="pkg-mini-kpi amber" data-filter-status="10" title="Click to filter Quotation Approved">
            <div class="kpi-icon"><i class="fi fi-rr-time-check"></i></div>
            <div class="kpi-meta">
                <p>Quotation Approved</p>
                <h3 id="statQuoteApproved">0</h3>
                <small>Ready for booking</small>
            </div>
        </div>

        <div class="pkg-mini-kpi emerald" data-filter-status="1" title="Click to filter Confirmed Bookings">
            <div class="kpi-icon"><i class="fi fi-rr-badge-check"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Queue</p>
                <h3 id="statPaidBookings">0</h3>
                <small>Ready for booking</small>
            </div>
        </div>

        <div class="pkg-mini-kpi cyan" data-filter-status="11" title="Click to filter Vouchers Issued">
            <div class="kpi-icon"><i class="fi fi-rr-ticket"></i></div>
            <div class="kpi-meta">
                <p>Vouchers Issued</p>
                <h3 id="statVouchersIssued">0</h3>
                <small>Hotels &amp; cabs ready</small>
            </div>
        </div>

        <div class="pkg-mini-kpi purple" data-href="/dashboard/operations/trips/" title="Click to open Daily Trip Planner">
            <div class="kpi-icon"><i class="fi fi-rr-plane"></i></div>
            <div class="kpi-meta">
                <p>Trips In Progress</p>
                <h3 id="statTripsInProgress">0</h3>
                <small>Active on tour</small>
            </div>
        </div>
    </div>

    <!-- 3. Confirmed Leads Fulfillment Desk Section -->
    <div class="ops-tab-section active" id="tabSectionConfirmed">
        <div class="ops-filter-toolbar">
            <div class="ops-filter-left">
                <span class="ops-filter-label"><i class="fi fi-rr-filter"></i> Stage:</span>
                <div class="ops-status-pills" id="confirmedStatusFilter">
                    <button type="button" class="btn-ops-pill active" data-status="">
                        <span class="pill-dot all"></span> All Confirmed (<span class="ops-pill-count pill-count-all" id="pillCountAll">0</span>)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="10">
                        <span class="pill-dot approved"></span> Approved (<span class="ops-pill-count pill-count-approved" id="pillCountApproved">0</span>)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="1">
                        <span class="pill-dot confirmed"></span> Confirmed (<span class="ops-pill-count pill-count-confirmed" id="pillCountConfirmed">0</span>)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="11">
                        <span class="pill-dot voucher"></span> Vouchers (<span class="ops-pill-count pill-count-voucher" id="pillCountVoucher">0</span>)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="13">
                        <span class="pill-dot progress"></span> In Progress (<span class="ops-pill-count pill-count-progress" id="pillCountProgress">0</span>)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="14">
                        <span class="pill-dot completed"></span> Completed (<span class="ops-pill-count pill-count-completed" id="pillCountCompleted">0</span>)
                    </button>
                </div>
            </div>

            <div class="ops-filter-right">
                <div class="ops-search-wrap">
                    <i class="fi fi-rr-search ops-search-icon"></i>
                    <input type="text" id="opsLeadSearchInput" placeholder="Search traveler, mobile, destination, #ID..." autocomplete="off">
                    <button type="button" id="opsClearLeadSearchBtn" class="ops-clear-search-btn" title="Clear search">&times;</button>
                </div>
            </div>
        </div>

        <!-- Confirmed Leads Data Table -->
        <div class="package-card-panel ops-table-panel">
            <div class="table-responsive ops-table-responsive">
                <table class="package-table ops-table" id="opsConfirmedTable">
                    <thead>
                        <tr>
                            <th class="th-lead">Traveler &amp; Lead</th>
                            <th class="th-tour">Tour &amp; Dates</th>
                            <th class="th-financials">Payment</th>
                            <th class="th-status">Status</th>
                            <th class="th-actions text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="opsConfirmedTableBody">
                        <tr>
                            <td colspan="5" class="text-center p-30 text-muted">
                                <i class="fi fi-rr-spinner fi-spin"></i> Loading confirmed leads...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="ops-pagination-bar" id="opsConfirmedPagination">
                <div class="ops-pagination-info" id="opsPaginationInfo">Showing 0 of 0 leads</div>
                <div class="ops-pagination-controls" id="opsPaginationControls"></div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     OPERATIONAL ACTION MODALS
     ========================================================================= -->

<?php require_once __DIR__ . '/modals-partial.php'; ?>

<!-- Modal 5: Assign Lead Modal -->
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
