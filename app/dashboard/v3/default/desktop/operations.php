<?php

/**
 * Operations Management Hub - Single Page Application View
 * Path: app/dashboard/v3/default/desktop/operations.php
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

$canAccessOps = in_array($userRole, ['administrator', 'operations', 'super_administrator']);

if (!$canAccessOps) {
    echo '<div class="v3-unauthorized-box p-40 text-center">
            <i class="fi fi-rr-lock font-48 text-red"></i>
            <h3 class="mt-16">Access Restricted</h3>
            <p class="text-muted">You do not have operations permissions to access the Operations Hub.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-compass-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2>Operations Management Hub</h2>
                <p>Vendor booking requests, live confirmation vouchers, daily trip logistics, and traveler fulfillments.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Main Navigation Tabs -->
            <div class="report-preset-btn-group" id="opsNavTabs">
                <button type="button" class="btn-date-preset active" data-tab="confirmed">
                    <i class="fi fi-rr-badge-check"></i> Confirmed Leads <span class="badge-count-indigo" id="badgeConfirmedCount">0</span>
                </button>
                <button type="button" class="btn-date-preset" data-tab="trips">
                    <i class="fi fi-rr-plane-departure"></i> Trip Planner <span class="badge-count-amber" id="badgeTripsCount">0</span>
                </button>
                <button type="button" class="btn-date-preset" data-tab="requests">
                    <i class="fi fi-rr-paper-plane"></i> Booking Requests <span class="badge-count-indigo" id="badgeRequestsCount">0</span>
                </button>
                <button type="button" class="btn-date-preset" data-tab="unassigned">
                    <i class="fi fi-rr-user-add"></i> Assign Leads <span class="badge-count-red" id="badgeUnassignedCount">0</span>
                </button>
                <button type="button" class="btn-date-preset" data-tab="templates">
                    <i class="fi fi-rr-file-edit"></i> Request Templates
                </button>
            </div>

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshOps" class="btn-v3-action" title="Refresh operational data">
                <i class="fi fi-rr-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- 2. KPI Metric Cards -->
    <div class="package-kpi-row ops-kpi-row">
        <div class="pkg-mini-kpi indigo" data-filter-status="all">
            <div class="kpi-icon"><i class="fi fi-rr-bookmark"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Bookings</p>
                <h3 id="statConfirmedTotal">0</h3>
                <small>Quotation approved &amp; onwards</small>
            </div>
        </div>

        <div class="pkg-mini-kpi amber" data-filter-status="10">
            <div class="kpi-icon"><i class="fi fi-rr-time-check"></i></div>
            <div class="kpi-meta">
                <p>Quotation Approved</p>
                <h3 id="statQuoteApproved">0</h3>
                <small>Ready for booking &amp; proforma</small>
            </div>
        </div>

        <div class="pkg-mini-kpi emerald" data-filter-status="12">
            <div class="kpi-icon"><i class="fi fi-rr-wallet"></i></div>
            <div class="kpi-meta">
                <p>Payments Recorded</p>
                <h3 id="statPaidBookings">0</h3>
                <small>Advance or full payment verified</small>
            </div>
        </div>

        <div class="pkg-mini-kpi cyan" data-filter-status="11">
            <div class="kpi-icon"><i class="fi fi-rr-ticket"></i></div>
            <div class="kpi-meta">
                <p>Vouchers Issued</p>
                <h3 id="statVouchersIssued">0</h3>
                <small>Hotel &amp; cab confirmed</small>
            </div>
        </div>

        <div class="pkg-mini-kpi purple" data-tab-target="trips">
            <div class="kpi-icon"><i class="fi fi-rr-plane"></i></div>
            <div class="kpi-meta">
                <p>Trips In Progress</p>
                <h3 id="statTripsInProgress">0</h3>
                <small>Active on-ground travelers</small>
            </div>
        </div>
    </div>

    <!-- 3. TAB 1: Confirmed Leads Fulfillment Desk -->
    <div class="ops-tab-section active" id="tabSectionConfirmed">
        <div class="ops-filter-toolbar">
            <div class="ops-status-pills" id="confirmedStatusFilter">
                <button type="button" class="btn-ops-pill active" data-status="">All Confirmed</button>
                <button type="button" class="btn-ops-pill" data-status="10">Approved (10)</button>
                <button type="button" class="btn-ops-pill" data-status="6">Payment Sent (6)</button>
                <button type="button" class="btn-ops-pill" data-status="8">Upay (8)</button>
                <button type="button" class="btn-ops-pill" data-status="12">Paid (12)</button>
                <button type="button" class="btn-ops-pill" data-status="11">Vouchers (11)</button>
                <button type="button" class="btn-ops-pill" data-status="13">In Progress (13)</button>
                <button type="button" class="btn-ops-pill" data-status="14">Completed (14)</button>
            </div>

            <div class="ops-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="opsLeadSearchInput" placeholder="Search traveler, mobile, destination, ID...">
            </div>
        </div>

        <!-- Confirmed Leads Data Table -->
        <div class="package-card-panel ops-table-panel">
            <div class="table-responsive">
                <table class="package-table ops-table" id="opsConfirmedTable">
                    <thead>
                        <tr>
                            <th>Lead &amp; Traveler</th>
                            <th>Destination</th>
                            <th>Travel Dates</th>
                            <th>Pax</th>
                            <th>Package &amp; Payments</th>
                            <th>Sales Agent</th>
                            <th>Ops Status</th>
                            <th class="text-right">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody id="opsConfirmedTableBody">
                        <tr>
                            <td colspan="8" class="text-center p-30 text-muted">
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

    <!-- 4. TAB 2: Daily Trip Planner & Logistics Board -->
    <div class="ops-tab-section" id="tabSectionTrips">
        <div class="ops-filter-toolbar">
            <div class="ops-date-selector">
                <label><i class="fi fi-rr-calendar"></i> Operations Date:</label>
                <input type="date" id="opsTripDateInput" value="<?php echo date('Y-m-d'); ?>">
                <button type="button" class="btn-ops-pill active" id="btnTripDateToday">Today</button>
                <button type="button" class="btn-ops-pill" id="btnTripDateTomorrow">Tomorrow</button>
            </div>
            <div class="ops-trip-metrics-pill">
                <span>Total Active Tours: <strong id="opsTotalActiveTours">0</strong></span>
            </div>
        </div>

        <!-- 4-Column Board -->
        <div class="ops-trip-board">
            <!-- Col 1: Departing Today -->
            <div class="ops-board-col">
                <div class="ops-board-header departing">
                    <div class="col-title"><i class="fi fi-rr-plane-departure"></i> Departing Today</div>
                    <span class="col-badge" id="badgeDepartingToday">0</span>
                </div>
                <div class="ops-board-cards" id="cardsDepartingToday">
                    <div class="ops-empty-col">No departures scheduled for this date.</div>
                </div>
            </div>

            <!-- Col 2: Active On Tour -->
            <div class="ops-board-col">
                <div class="ops-board-header on-tour">
                    <div class="col-title"><i class="fi fi-rr-route"></i> Active On Tour</div>
                    <span class="col-badge" id="badgeActiveOnTour">0</span>
                </div>
                <div class="ops-board-cards" id="cardsActiveOnTour">
                    <div class="ops-empty-col">No travelers currently mid-itinerary.</div>
                </div>
            </div>

            <!-- Col 3: Returning Today -->
            <div class="ops-board-col">
                <div class="ops-board-header returning">
                    <div class="col-title"><i class="fi fi-rr-plane-arrival"></i> Returning Today</div>
                    <span class="col-badge" id="badgeReturningToday">0</span>
                </div>
                <div class="ops-board-cards" id="cardsReturningToday">
                    <div class="ops-empty-col">No return trips scheduled today.</div>
                </div>
            </div>

            <!-- Col 4: Completed Trips -->
            <div class="ops-board-col">
                <div class="ops-board-header completed">
                    <div class="col-title"><i class="fi fi-rr-badge-check"></i> Completed Trips</div>
                    <span class="col-badge" id="badgeCompletedTrips">0</span>
                </div>
                <div class="ops-board-cards" id="cardsCompletedTrips">
                    <div class="ops-empty-col">No completed trips in current view.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. TAB 3: Unassigned Leads Desk -->
    <div class="ops-tab-section" id="tabSectionUnassigned">
        <div class="ops-filter-toolbar">
            <div class="ops-batch-actions">
                <span id="selectedLeadsCount">0 leads selected</span>
                <button type="button" class="btn-v3-action btn-blue" id="btnOpenBulkAssignModal" disabled>
                    <i class="fi fi-rr-user-add"></i> Assign Selected Leads
                </button>
            </div>
            <div class="ops-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="opsUnassignedSearchInput" placeholder="Filter unassigned leads...">
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

    <!-- 5.5. TAB: Dispatched Vendor Booking Requests Ledger -->
    <div class="ops-tab-section" id="tabSectionRequests">
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

    <!-- 6. TAB 4: Booking Request Templates Manager -->
    <div class="ops-tab-section" id="tabSectionTemplates">
        <div class="ops-filter-toolbar">
            <div class="ops-channel-pills" id="tplChannelFilter">
                <button type="button" class="btn-ops-pill active" data-channel="all">All Channels</button>
                <button type="button" class="btn-ops-pill" data-channel="whatsapp"><i class="fi fi-brands-whatsapp"></i> WhatsApp</button>
                <button type="button" class="btn-ops-pill" data-channel="email"><i class="fi fi-rr-envelope"></i> Email</button>
            </div>

            <div class="ops-vendor-pills" id="tplVendorFilter">
                <button type="button" class="btn-ops-pill active" data-vendor="all">All Vendors</button>
                <button type="button" class="btn-ops-pill" data-vendor="hotel"><i class="fi fi-rr-bed"></i> Hotels</button>
                <button type="button" class="btn-ops-pill" data-vendor="transport"><i class="fi fi-rr-car"></i> Transporters</button>
                <button type="button" class="btn-ops-pill" data-vendor="activity"><i class="fi fi-rr-flag"></i> Activities</button>
            </div>

            <button type="button" class="btn-v3-action btn-blue" id="btnOpenNewTemplateModal">
                <i class="fi fi-rr-plus"></i> Create Template
            </button>
        </div>

        <div class="ops-templates-grid" id="opsTemplatesGrid">
            <!-- Populated dynamically -->
        </div>
    </div>
</div>

<!-- =========================================================================
     MODALS
     ========================================================================= -->

<!-- Modal 1: Send Vendor Booking Request (With Live Preview & In-line Editor) -->
<div class="v3-modal-overlay" id="modalOpsBookingRequest">
    <div class="v3-modal-dialog v3-modal-dialog-xl">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-primary">
                    <i class="fi fi-rr-paper-plane"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Vendor Booking Request &amp; Live Preview</h4>
                    <span class="v3-modal-subtitle">Review, edit content in-line, and dispatch reservation request via WhatsApp or Email</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="bkgModalLeadId" value="">
            
            <div class="ops-modal-grid">
                <!-- Left Column: Logistics Configuration -->
                <div class="ops-modal-col-left">
                    <h5 class="ops-modal-section-title"><i class="fi fi-rr-settings-sliders"></i> Reservation Details</h5>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Vendor Service Type *</label>
                        <select id="bkgVendorType" class="v3-form-input">
                            <option value="hotel">Hotel / Property</option>
                            <option value="transport">Transporter / Cab</option>
                            <option value="activity">Activity / Tour Guide</option>
                        </select>
                    </div>

                    <div class="v3-form-group" id="bkgSelectVendorWrap">
                        <label class="v3-form-label">Select Vendor / Property *</label>
                        <select id="bkgVendorSelect" class="v3-form-input">
                            <option value="">-- Choose Vendor --</option>
                        </select>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Phone / WhatsApp *</label>
                            <input type="text" id="bkgVendorPhone" class="v3-form-input" placeholder="e.g. 9876543210">
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Email</label>
                            <input type="email" id="bkgVendorEmail" class="v3-form-input" placeholder="e.g. booking@hotel.com">
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-In / Pickup Date *</label>
                            <input type="date" id="bkgCheckIn" class="v3-form-input">
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-Out / End Date *</label>
                            <input type="date" id="bkgCheckOut" class="v3-form-input">
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Room / Vehicle Type</label>
                            <input type="text" id="bkgRoomCategory" class="v3-form-input" placeholder="e.g. Deluxe Room / Innova">
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Meal Plan</label>
                            <select id="bkgMealPlan" class="v3-form-input">
                                <option value="CP">CP (Breakfast only)</option>
                                <option value="MAP">MAP (Breakfast + Dinner)</option>
                                <option value="AP">AP (All Meals)</option>
                                <option value="EP">EP (Room only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Special Remarks / Instructions</label>
                        <input type="text" id="bkgSpecialNotes" class="v3-form-input" placeholder="e.g. Early check-in requested, high floor">
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Dispatch Channel *</label>
                        <div class="ops-radio-group">
                            <label class="ops-radio-label">
                                <input type="radio" name="bkgChannel" value="both" checked>
                                <span><i class="fi fi-rr-shuffle"></i> Both (WhatsApp &amp; Email)</span>
                            </label>
                            <label class="ops-radio-label">
                                <input type="radio" name="bkgChannel" value="whatsapp">
                                <span><i class="fi fi-brands-whatsapp"></i> WhatsApp Only</span>
                            </label>
                            <label class="ops-radio-label">
                                <input type="radio" name="bkgChannel" value="email">
                                <span><i class="fi fi-rr-envelope"></i> Email Only</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Live Interactive Preview & In-line Editor -->
                <div class="ops-modal-col-right">
                    <div class="ops-preview-header">
                        <div class="ops-preview-tabs" id="bkgPreviewTabs">
                            <button type="button" class="btn-ops-preview-tab active" data-preview="whatsapp">
                                <i class="fi fi-brands-whatsapp text-emerald"></i> WhatsApp Preview &amp; Editor
                            </button>
                            <button type="button" class="btn-ops-preview-tab" data-preview="email">
                                <i class="fi fi-rr-envelope text-blue"></i> Email Preview &amp; Editor
                            </button>
                        </div>

                        <div class="ops-template-picker">
                            <label><i class="fi fi-rr-file-edit"></i> Template:</label>
                            <select id="bkgTemplatePicker" class="v3-form-input-sm">
                                <option value="">Default Template</option>
                            </select>
                        </div>
                    </div>

                    <!-- WhatsApp Preview Box -->
                    <div class="ops-preview-box active" id="previewBoxWhatsApp">
                        <div class="ops-wa-bubble-wrap">
                            <div class="ops-wa-bubble-header">
                                <i class="fi fi-brands-whatsapp"></i> Message Content (You can modify this text before sending):
                            </div>
                            <textarea id="bkgWhatsAppContent" class="ops-wa-textarea" rows="12"></textarea>
                        </div>
                    </div>

                    <!-- Email Preview Box -->
                    <div class="ops-preview-box" id="previewBoxEmail">
                        <div class="v3-form-group mb-10">
                            <label class="v3-form-label">Email Subject</label>
                            <input type="text" id="bkgEmailSubject" class="v3-form-input" placeholder="Subject...">
                        </div>
                        <div class="ops-email-body-wrap">
                            <label class="v3-form-label">Email HTML Body (Editable)</label>
                            <textarea id="bkgEmailContent" class="ops-email-textarea" rows="10"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-blue" id="btnConfirmSendBookingRequest">
                <i class="fi fi-rr-paper-plane"></i> Send Booking Request Now
            </button>
        </div>
    </div>
</div>

<!-- Modal 2: Confirmation Vouchers & Ref IDs Form -->
<div class="v3-modal-overlay" id="modalOpsVoucher">
    <div class="v3-modal-dialog v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-emerald">
                    <i class="fi fi-rr-ticket"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Vendor Confirmation &amp; Travel Vouchers</h4>
                    <span class="v3-modal-subtitle">Record hotel confirmation reference numbers, driver details, and issue vouchers</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="voucherLeadId" value="">

            <div class="ops-voucher-guest-banner" id="voucherGuestBanner">
                <!-- Traveler details populated dynamically -->
            </div>

            <div class="ops-voucher-payment-notice" id="voucherPaymentNotice">
                <!-- Payment verification warning or green badge -->
            </div>

            <h5 class="ops-modal-section-title"><i class="fi fi-rr-bed"></i> Booked Services &amp; Confirmation Numbers</h5>
            
            <div class="ops-voucher-items-list" id="voucherItemsList">
                <!-- Booking rows with confirmation ref inputs -->
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
            <button type="button" class="btn-v3-action btn-green" id="btnConfirmIssueVouchers">
                <i class="fi fi-rr-badge-check"></i> Save Reference IDs &amp; Issue Vouchers
            </button>
        </div>
    </div>
</div>

<!-- Modal 3: Invoice & Proforma Generator -->
<div class="v3-modal-overlay" id="modalOpsInvoice">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-amber">
                    <i class="fi fi-rr-document"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Generate Invoice / Proforma</h4>
                    <span class="v3-modal-subtitle">Auto-calculated from itinerary quote with tax breakdown &amp; payments</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="invModalLeadId" value="">
            <input type="hidden" id="invGuestPhone" value="">
            <input type="hidden" id="invGuestName" value="">

            <div class="v3-form-group">
                <label class="v3-form-label">Document Type *</label>
                <div class="ops-radio-group">
                    <label class="ops-radio-label">
                        <input type="radio" name="invDocType" value="proforma" checked>
                        <span>Proforma Invoice (Pre-trip)</span>
                    </label>
                    <label class="ops-radio-label">
                        <input type="radio" name="invDocType" value="tax_invoice">
                        <span>Tax Invoice (Settled)</span>
                    </label>
                </div>
            </div>

            <div class="ops-form-row">
                <div class="v3-form-group">
                    <label class="v3-form-label">Document Number</label>
                    <input type="text" id="invNumber" class="v3-form-input" readonly>
                </div>
                <div class="v3-form-group">
                    <label class="v3-form-label">Document Date</label>
                    <input type="text" id="invDate" class="v3-form-input" readonly>
                </div>
            </div>

            <!-- Financials Summary Box -->
            <div class="ops-inv-financials-card">
                <div class="ops-inv-row">
                    <span>Package Itinerary Total:</span>
                    <strong id="invPkgTotal">₹ 0</strong>
                </div>
                <div class="ops-inv-row">
                    <span>GST (5% Included):</span>
                    <span id="invGstAmount">₹ 0</span>
                </div>
                <div class="ops-inv-row text-emerald">
                    <span>Advance / Payments Verified:</span>
                    <strong id="invAdvancePaid">- ₹ 0</strong>
                </div>
                <div class="ops-inv-row highlight">
                    <span>Outstanding Balance Due:</span>
                    <strong id="invBalanceDue" class="text-rose">₹ 0</strong>
                </div>
            </div>

            <!-- Agency Bank Details -->
            <div class="ops-inv-bank-box">
                <div class="bank-title"><i class="fi fi-rr-bank"></i> Remittance &amp; Bank Details:</div>
                <div class="bank-meta" id="invBankMeta">HDFC Bank | A/C: 50200088991122 | IFSC: HDFC0001234 | UPI: aleya@hdfcbank</div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-amber" id="btnConfirmSendInvoice">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Invoice via WhatsApp
            </button>
        </div>
    </div>
</div>

<!-- Modal 4: Assign / Reassign Lead Modal -->
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

<!-- Modal 5: Booking Request Template Editor Modal -->
<div class="v3-modal-overlay" id="modalOpsTemplateEditor">
    <div class="v3-modal-dialog v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-purple">
                    <i class="fi fi-rr-file-edit"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title" id="tplEditorModalTitle">Create Request Template</h4>
                    <span class="v3-modal-subtitle">Design customizable WhatsApp and Email templates with dynamic merge variables</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="tplEditorId" value="0">

            <div class="ops-form-row">
                <div class="v3-form-group">
                    <label class="v3-form-label">Channel *</label>
                    <select id="tplEditorChannel" class="v3-form-input">
                        <option value="whatsapp">WhatsApp Message</option>
                        <option value="email">Email Notification</option>
                    </select>
                </div>
                <div class="v3-form-group">
                    <label class="v3-form-label">Vendor Service Type *</label>
                    <select id="tplEditorVendorType" class="v3-form-input">
                        <option value="hotel">Hotel / Property</option>
                        <option value="transport">Transporter / Cab</option>
                        <option value="activity">Activity / Tour Guide</option>
                    </select>
                </div>
            </div>

            <div class="v3-form-group">
                <label class="v3-form-label">Template Name *</label>
                <input type="text" id="tplEditorTitle" class="v3-form-input" placeholder="e.g. Urgent Reservation Request">
            </div>

            <div class="v3-form-group" id="tplEditorSubjectWrap">
                <label class="v3-form-label">Email Subject (Email channel only)</label>
                <input type="text" id="tplEditorSubject" class="v3-form-input" placeholder="e.g. Booking Request: {guest_name} - {nights}N - {check_in}">
            </div>

            <!-- Merge Tags Quick Inserters -->
            <div class="ops-merge-tags-box">
                <label><i class="fi fi-rr-magic-wand"></i> Click merge tag to insert into template:</label>
                <div class="ops-tag-pills" id="tplMergeTagsWrap">
                    <span class="ops-tag-pill" data-tag="{vendor_name}">{vendor_name}</span>
                    <span class="ops-tag-pill" data-tag="{guest_name}">{guest_name}</span>
                    <span class="ops-tag-pill" data-tag="{guest_phone}">{guest_phone}</span>
                    <span class="ops-tag-pill" data-tag="{check_in}">{check_in}</span>
                    <span class="ops-tag-pill" data-tag="{check_out}">{check_out}</span>
                    <span class="ops-tag-pill" data-tag="{nights}">{nights}</span>
                    <span class="ops-tag-pill" data-tag="{room_category}">{room_category}</span>
                    <span class="ops-tag-pill" data-tag="{room_count}">{room_count}</span>
                    <span class="ops-tag-pill" data-tag="{meal_plan}">{meal_plan}</span>
                    <span class="ops-tag-pill" data-tag="{vehicle_type}">{vehicle_type}</span>
                    <span class="ops-tag-pill" data-tag="{pax_adults}">{pax_adults}</span>
                    <span class="ops-tag-pill" data-tag="{pax_children}">{pax_children}</span>
                    <span class="ops-tag-pill" data-tag="{special_instructions}">{special_instructions}</span>
                    <span class="ops-tag-pill" data-tag="{agency_name}">{agency_name}</span>
                    <span class="ops-tag-pill" data-tag="{ops_manager_name}">{ops_manager_name}</span>
                    <span class="ops-tag-pill" data-tag="{ops_manager_phone}">{ops_manager_phone}</span>
                </div>
            </div>

            <div class="v3-form-group">
                <label class="v3-form-label">Template Body *</label>
                <textarea id="tplEditorBody" class="v3-form-input" rows="8" placeholder="Write template body with merge tags..."></textarea>
            </div>

            <div class="v3-form-group">
                <label class="ops-checkbox-label">
                    <input type="checkbox" id="tplEditorIsDefault" value="1">
                    <span>Set as default template for this channel and vendor type</span>
                </label>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-purple" id="btnConfirmSaveTemplate">
                <i class="fi fi-rr-disk"></i> Save Template
            </button>
        </div>
    </div>
</div>

<!-- Modal 6: Quick Record Vendor Response / Confirmation Ref ID -->
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

<!-- Modal 7: Driver & Vehicle Allocation + 24hr Pre-Trip WhatsApp Dispatch -->
<div class="v3-modal-overlay" id="modalOpsDriverDispatch">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-primary">
                    <i class="fi fi-rr-car"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Chauffeur &amp; Vehicle Dispatch</h4>
                    <span class="v3-modal-subtitle">Assign driver and dispatch pre-trip WhatsApp details to traveler</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="dispatchLeadId" value="">

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
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-blue" id="btnConfirmDriverDispatch">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Driver to Traveler WhatsApp
            </button>
        </div>
    </div>
</div>
