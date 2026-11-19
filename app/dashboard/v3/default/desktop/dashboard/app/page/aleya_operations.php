<?php

/**
 * Operations Management - Dashboard Overview
 * Page Component: app/dashboard/v3/default/desktop/dashboard/app/page/aleya_operations.php
 * Workspace Compliant: External CSS/JS, REST API integration, SPA asynchronous state, Zero inline CSS/JS.
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

$displayDateRange = 'Last 30 Days';
$displayDuration  = '30 Days';

if (!isset($daterange) || empty($daterange)) {
    $defaultStart = date('d-M-Y', strtotime('-29 days'));
    $defaultEnd   = date('d-M-Y');
    $daterange    = $defaultStart . ' - ' . $defaultEnd;
}

if (!empty($daterange)) {
    $dates = explode(' - ', trim($daterange));
    if (count($dates) === 2) {
        $t1 = strtotime($dates[0]);
        $t2 = strtotime($dates[1]);
        if ($t1 && $t2) {
            $days = round(abs($t2 - $t1) / 86400) + 1;
            if ($days == 1) {
                $displayDateRange = (date('Y-m-d', $t1) === date('Y-m-d')) ? 'Today' : '1 Day';
                $displayDuration  = '1 Day';
            } elseif ($days == 7) {
                $displayDateRange = 'Last 7 Days';
                $displayDuration  = '7 Days';
            } elseif ($days == 30) {
                $displayDateRange = 'Last 30 Days';
                $displayDuration  = '30 Days';
            } else {
                $displayDateRange = 'Custom Range';
                $displayDuration  = "{$days} Days";
            }
        }
    }
}
?>

<div class="package-page-wrapper ops-page-wrapper ops-dashboard-overview" id="operationsApp" data-page="dashboard">
    <!-- 1. Header Bar: Title, Quick Actions & Refresh -->
    <div class="package-header-bar ops-header-bar mb-20">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-compass-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2>Operations Command Center</h2>
                <p>Live traveler logistics, vendor booking requests, confirmation vouchers &amp; daily on-ground fulfillments.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Quick Action CTA Buttons -->
            <a href="/dashboard/operations/booking-request/" class="btn-v3-action btn-blue" title="Send booking request to hotel or cab operator">
                <i class="fi fi-rr-paper-plane"></i> <span>Send Request</span>
            </a>
            <a href="/dashboard/operations/voucher/" class="btn-v3-action btn-green" title="Make confirmation voucher for guest">
                <i class="fi fi-rr-ticket"></i> <span>Issue Voucher</span>
            </a>
            <a href="/dashboard/operations/dispatch/" class="btn-v3-action btn-purple" title="Assign driver and cab details to guest">
                <i class="fi fi-rr-car"></i> <span>Assign Cab / Driver</span>
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Open full operations options">
                <i class="fi fi-rr-apps"></i> <span>Operations Hub</span>
            </a>

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshOps" class="btn-v3-action" title="Refresh operational data">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- 2. Operations Executive KPI Metric Cards -->
    <div class="package-kpi-row ops-kpi-row mb-24">
        <!-- Confirmed Bookings -->
        <a href="/dashboard/operations/list/" class="pkg-mini-kpi indigo text-inherit" title="View all confirmed bookings">
            <div class="kpi-icon"><i class="fi fi-rr-bookmark"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Bookings</p>
                <h3 id="statConfirmedTotal">0</h3>
                <small>Quotation approved &amp; onwards</small>
            </div>
        </a>

        <!-- Quotation Approved -->
        <a href="/dashboard/operations/list/?status=10" class="pkg-mini-kpi amber text-inherit" title="Quotation approved - ready for proforma">
            <div class="kpi-icon"><i class="fi fi-rr-time-check"></i></div>
            <div class="kpi-meta">
                <p>Quotation Approved</p>
                <h3 id="statQuoteApproved">0</h3>
                <small>Ready for booking &amp; proforma</small>
            </div>
        </a>

        <!-- Confirmed Queue -->
        <a href="/dashboard/operations/list/?status=1" class="pkg-mini-kpi emerald text-inherit" title="Confirmed bookings in queue">
            <div class="kpi-icon"><i class="fi fi-rr-badge-check"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Queue</p>
                <h3 id="statPaidBookings">0</h3>
                <small>Ready for booking &amp; vouchers</small>
            </div>
        </a>

        <!-- Vouchers Issued -->
        <a href="/dashboard/operations/list/?status=11" class="pkg-mini-kpi cyan text-inherit" title="Hotels & cabs verified">
            <div class="kpi-icon"><i class="fi fi-rr-ticket"></i></div>
            <div class="kpi-meta">
                <p>Vouchers Issued</p>
                <h3 id="statVouchersIssued">0</h3>
                <small>Hotel &amp; fleet confirmed</small>
            </div>
        </a>

        <!-- Trips In Progress -->
        <a href="/dashboard/operations/trips/" class="pkg-mini-kpi purple text-inherit" title="Active on-ground travelers">
            <div class="kpi-icon"><i class="fi fi-rr-plane"></i></div>
            <div class="kpi-meta">
                <p>Trips In Progress</p>
                <h3 id="statTripsInProgress">0</h3>
                <small>Active on-ground travelers</small>
            </div>
        </a>
    </div>

    <!-- 3. Operations Workspace Quick Navigation Strip -->
    <div class="ops-filter-toolbar mb-24">
        <div class="ops-status-pills">
            <a href="/dashboard/operations/list/" class="btn-ops-pill active">
                <i class="fi fi-rr-badge-check"></i> Confirmed Fulfillment Desk <span class="badge-count-indigo" id="badgeConfirmedCount">0</span>
            </a>
            <a href="/dashboard/operations/trips/" class="btn-ops-pill">
                <i class="fi fi-rr-plane-departure"></i> Trip Planner Board <span class="badge-count-amber" id="badgeTripsCount">0</span>
            </a>
            <a href="/dashboard/operations/requests/" class="btn-ops-pill">
                <i class="fi fi-rr-paper-plane"></i> Booking Requests Ledger
            </a>
            <a href="/dashboard/operations/unassigned/" class="btn-ops-pill">
                <i class="fi fi-rr-user-add"></i> Assign Leads <span class="badge-count-red" id="badgeUnassignedCount">0</span>
            </a>
            <a href="/dashboard/operations/templates/" class="btn-ops-pill">
                <i class="fi fi-rr-file-edit"></i> Request Templates
            </a>
        </div>
    </div>

    <!-- 4. SECTION: Daily Trip Planner & Logistics Board -->
    <div class="package-card-panel mb-28 p-20">
        <div class="d-flex justify-content-between align-items-center mb-16 flex-wrap gap-12">
            <div class="d-flex align-items-center gap-10">
                <h3 class="v3-subheading m-0"><i class="fi fi-rr-plane-departure text-amber"></i> Daily Trip Logistics Board</h3>
                <span class="ops-status-badge badge-approved" id="opsTotalActiveToursWrap">Active Tours: <strong id="opsTotalActiveTours">0</strong></span>
            </div>

            <!-- Date Selector -->
            <div class="ops-date-selector m-0">
                <label><i class="fi fi-rr-calendar"></i> Operations Date:</label>
                <input type="date" id="opsTripDateInput" value="<?php echo date('Y-m-d'); ?>">
                <button type="button" class="btn-ops-pill active" id="btnTripDateToday">Today</button>
                <button type="button" class="btn-ops-pill" id="btnTripDateTomorrow">Tomorrow</button>
                <a href="/dashboard/operations/trips/" class="btn-v3-action btn-outline ml-8 font-12" title="Open Full Trip Planner">
                    <i class="fi fi-rr-expand"></i> Full Board
                </a>
            </div>
        </div>

        <!-- 4-Column Board -->
        <div class="ops-trip-board">
            <!-- Col 0: Tomorrow's Trips (0th Position) -->
            <div class="ops-board-col">
                <div class="ops-board-header tomorrow">
                    <div class="col-title"><i class="fi fi-rr-calendar-clock"></i> Tomorrow's Trips</div>
                    <span class="col-badge" id="badgeTomorrowTrips">0</span>
                </div>
                <div class="ops-board-cards" id="cardsTomorrowTrips">
                    <div class="ops-empty-col">No departures scheduled for tomorrow.</div>
                </div>
            </div>

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
        </div>
    </div>

    <!-- 5. SECTION: Confirmed Bookings Fulfillment Queue -->
    <div class="package-card-panel mb-28 p-20">
        <div class="d-flex justify-content-between align-items-center mb-16 flex-wrap gap-12">
            <h3 class="v3-subheading m-0"><i class="fi fi-rr-badge-check text-emerald"></i> Confirmed Bookings Fulfillment Queue</h3>
            
            <div class="d-flex align-items-center gap-10">
                <div class="ops-search-wrap m-0">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="opsLeadSearchInput" placeholder="Search traveler, mobile, destination, ID...">
                </div>
                <a href="/dashboard/operations/list/" class="btn-v3-action btn-outline font-12" title="Open Full Fulfillment Desk">
                    <i class="fi fi-rr-expand"></i> View All
                </a>
            </div>
        </div>

        <div class="ops-filter-toolbar mb-16 p-0 border-0">
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

        <!-- Confirmed Leads Data Table -->
        <div class="table-responsive">
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
                            <i class="fi fi-rr-spinner fi-spin"></i> Loading operational leads...
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

    <!-- 6. SECTION: Recent Dispatched Booking Requests Ledger -->
    <div class="package-card-panel p-20">
        <div class="d-flex justify-content-between align-items-center mb-16 flex-wrap gap-12">
            <h3 class="v3-subheading m-0"><i class="fi fi-rr-paper-plane text-blue"></i> Dispatched Vendor Booking Requests Ledger</h3>

            <div class="d-flex align-items-center gap-10">
                <div class="ops-vendor-pills m-0" id="bkgLedgerVendorFilter">
                    <button type="button" class="btn-ops-pill active" data-vendor="all">All Services</button>
                    <button type="button" class="btn-ops-pill" data-vendor="hotel"><i class="fi fi-rr-bed"></i> Hotels</button>
                    <button type="button" class="btn-ops-pill" data-vendor="transport"><i class="fi fi-rr-car"></i> Transporters</button>
                    <button type="button" class="btn-ops-pill" data-vendor="activity"><i class="fi fi-rr-flag"></i> Activities</button>
                </div>
                <a href="/dashboard/operations/requests/" class="btn-v3-action btn-outline font-12" title="Open Full Requests Ledger">
                    <i class="fi fi-rr-expand"></i> View All
                </a>
            </div>
        </div>

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

<!-- =========================================================================
     OPERATIONAL ACTION MODALS
     ========================================================================= -->

<!-- Modal 1: Send Vendor Booking Request -->
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
            
            <!-- Lead Traveler Context Ribbon -->
            <div class="ops-bkg-traveler-ribbon">
                <div class="ops-bkg-traveler-left">
                    <i class="fi fi-rr-user text-blue"></i>
                    <span class="traveler-label">Traveler:</span>
                    <span id="bkgLeadGuestName" class="ops-bkg-traveler-name">Guest Traveler</span>
                    <span id="bkgLeadIdBadge" class="ops-bkg-tag id-tag">#--</span>
                </div>
                <div class="ops-bkg-traveler-tags">
                    <span id="bkgLeadDestBadge" class="ops-bkg-tag"><i class="fi fi-rr-map-marker"></i> Package</span>
                    <span id="bkgLeadPaxBadge" class="ops-bkg-tag"><i class="fi fi-rr-users"></i> Pax</span>
                </div>
            </div>

            <div class="ops-modal-grid">
                <!-- Left Column: Logistics Configuration -->
                <div class="ops-modal-col-left">
                    <h5 class="ops-modal-section-title"><i class="fi fi-rr-settings-sliders"></i> Reservation Details</h5>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Vendor Service Type *</label>
                        <select id="bkgVendorType" class="v3-form-input selectJS2" data-placeholder="Choose Service Type">
                            <option value="hotel" selected>Hotel / Property</option>
                            <option value="transport">Transporter / Cab</option>
                            <option value="activity">Activity / Tour Guide</option>
                        </select>
                    </div>

                    <div class="v3-form-group" id="bkgSelectVendorWrap">
                        <label class="v3-form-label">Select Vendor / Property *</label>
                        <select id="bkgVendorSelect" class="v3-form-input selectJS2" data-placeholder="-- Choose Vendor / Property --">
                            <option value="">-- Choose Vendor --</option>
                        </select>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Phone / WhatsApp *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-brands-whatsapp text-emerald"></i></span>
                                <input type="text" id="bkgVendorPhone" class="v3-form-input" placeholder="e.g. 9876543210">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Email</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-envelope text-blue"></i></span>
                                <input type="email" id="bkgVendorEmail" class="v3-form-input" placeholder="e.g. booking@hotel.com">
                            </div>
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-In / Pickup Date *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-calendar"></i></span>
                                <input type="date" id="bkgCheckIn" class="v3-form-input">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-Out / End Date *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-calendar"></i></span>
                                <input type="date" id="bkgCheckOut" class="v3-form-input">
                            </div>
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Room / Vehicle Type</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-bed"></i></span>
                                <input type="text" id="bkgRoomCategory" class="v3-form-input" placeholder="e.g. Deluxe Room / Innova">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Meal Plan</label>
                            <select id="bkgMealPlan" class="v3-form-input selectJS2" data-placeholder="Select Meal Plan">
                                <option value="CP" selected>CP (Breakfast only)</option>
                                <option value="MAP">MAP (Breakfast + Dinner)</option>
                                <option value="AP">AP (All Meals)</option>
                                <option value="EP">EP (Room only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Special Remarks / Instructions</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-comment-alt"></i></span>
                            <input type="text" id="bkgSpecialNotes" class="v3-form-input" placeholder="e.g. Early check-in requested, high floor">
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Dispatch Channel *</label>
                        <div class="ops-segmented-channel-toggle">
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="both" checked>
                                <div class="ops-chip-card">
                                    <span class="chip-icon"><i class="fi fi-rr-shuffle"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">Both Channels</span>
                                        <span class="chip-subtitle">WhatsApp &amp; Email</span>
                                    </div>
                                    <span class="chip-badge">Best</span>
                                </div>
                            </label>
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="whatsapp">
                                <div class="ops-chip-card">
                                    <span class="chip-icon text-emerald"><i class="fi fi-brands-whatsapp"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">WhatsApp</span>
                                        <span class="chip-subtitle">Direct Message</span>
                                    </div>
                                </div>
                            </label>
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="email">
                                <div class="ops-chip-card">
                                    <span class="chip-icon text-blue"><i class="fi fi-rr-envelope"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">Email</span>
                                        <span class="chip-subtitle">Reservation Email</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Fulfillments & Delivery Toggles -->
                    <div class="ops-form-toggles-box">
                        <label class="ops-switch-item">
                            <div class="ops-switch-content">
                                <span class="ops-switch-title"><i class="fi fi-rr-document-signed"></i> Auto-Attach Itinerary Details</span>
                                <span class="ops-switch-desc">Includes stay duration, meal plan &amp; guest count</span>
                            </div>
                            <div class="ops-toggle-switch">
                                <input type="checkbox" id="bkgOptAttachGuest" checked>
                                <span class="ops-toggle-slider"></span>
                            </div>
                        </label>
                        <label class="ops-switch-item">
                            <div class="ops-switch-content">
                                <span class="ops-switch-title"><i class="fi fi-rr-bell-ring"></i> Operations Team Notification</span>
                                <span class="ops-switch-desc">Log dispatch alert in internal operations history</span>
                            </div>
                            <div class="ops-toggle-switch">
                                <input type="checkbox" id="bkgOptNotifyOps" checked>
                                <span class="ops-toggle-slider"></span>
                            </div>
                        </label>
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
                            <select id="bkgTemplatePicker" class="v3-form-input-sm selectJS2" data-placeholder="Choose Template">
                                <option value="">Default Template</option>
                            </select>
                        </div>
                    </div>

                    <!-- WhatsApp Preview Box -->
                    <div class="ops-preview-box active" id="previewBoxWhatsApp">
                        <div class="ops-wa-preview-card">
                            <div class="ops-wa-top-bar">
                                <div class="ops-wa-profile">
                                    <div class="ops-wa-avatar">
                                        <i class="fi fi-rr-building" id="bkgWaVendorAvatarIcon"></i>
                                    </div>
                                    <div class="ops-wa-meta">
                                        <span class="ops-wa-vendor-name" id="bkgWaLiveVendorName">Vendor Partner</span>
                                        <span class="ops-wa-vendor-status"><span class="ops-wa-status-dot"></span> WhatsApp Business &bull; Verified</span>
                                    </div>
                                </div>
                                <div class="ops-wa-badge-pill">
                                    <i class="fi fi-brands-whatsapp"></i> Live Chat Preview
                                </div>
                            </div>
                            
                            <div class="ops-wa-chat-canvas">
                                <div class="ops-wa-bubble">
                                    <div class="ops-wa-bubble-prompt">
                                        <span><i class="fi fi-rr-edit text-emerald"></i> Message Content (You can modify this text before sending):</span>
                                    </div>
                                    <textarea id="bkgWhatsAppContent" class="ops-wa-textarea" rows="11" placeholder="Type WhatsApp message here..."></textarea>
                                    <div class="ops-wa-bubble-footer">
                                        <span class="ops-wa-char-count" id="bkgWaCharCount">0 characters</span>
                                        <span class="ops-wa-time-meta">Live &bull; Ready <span class="ops-wa-ticks">&#10003;&#10003;</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ops-preview-actions-bar">
                            <button type="button" class="btn-ops-mini" id="btnResetWaPreview"><i class="fi fi-rr-refresh"></i> Reset Text</button>
                            <button type="button" class="btn-ops-mini" id="btnCopyWaPreview"><i class="fi fi-rr-copy"></i> Copy Text</button>
                        </div>
                    </div>

                    <!-- Email Preview Box -->
                    <div class="ops-preview-box" id="previewBoxEmail">
                        <div class="ops-email-preview-card">
                            <div class="ops-email-client-header">
                                <div class="ops-email-meta-row">
                                    <span class="ops-email-meta-label">To:</span>
                                    <span class="ops-email-recipient-chip" id="bkgEmailRecipientPreview">vendor@hotel.com</span>
                                </div>
                                <div class="ops-email-meta-row">
                                    <span class="ops-email-meta-label">Subject:</span>
                                    <input type="text" id="bkgEmailSubject" class="v3-form-input ops-email-subject-input" placeholder="Subject...">
                                </div>
                            </div>

                            <!-- Rich Text Toolbar -->
                            <div class="ops-email-editor-toolbar">
                                <div class="editor-tool-group">
                                    <button type="button" class="btn-editor-tool" data-cmd="bold" title="Bold"><i class="fi fi-rr-bold"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="italic" title="Italic"><i class="fi fi-rr-italic"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="underline" title="Underline"><i class="fi fi-rr-underline"></i></button>
                                </div>
                                <div class="editor-tool-separator"></div>
                                <div class="editor-tool-group">
                                    <button type="button" class="btn-editor-tool" data-cmd="insertUnorderedList" title="Bullet List"><i class="fi fi-rr-list"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="insertOrderedList" title="Numbered List"><i class="fi fi-rr-list-number"></i></button>
                                </div>
                                <div class="editor-mode-toggle">
                                    <button type="button" class="btn-mode-tab active" data-mode="visual" id="btnEmailModeVisual"><i class="fi fi-rr-eye"></i> Visual Preview</button>
                                    <button type="button" class="btn-mode-tab" data-mode="code" id="btnEmailModeCode"><i class="fi fi-rr-code-simple"></i> HTML Source</button>
                                </div>
                            </div>

                            <!-- Visual Rich HTML Viewport -->
                            <div class="ops-email-visual-viewport" id="bkgEmailVisualEditor" contenteditable="true">
                                <p>Loading preview...</p>
                            </div>

                            <!-- Raw HTML Code Editor (toggled via button) -->
                            <div class="ops-email-code-wrap" id="bkgEmailCodeWrap">
                                <textarea id="bkgEmailContent" class="ops-email-textarea" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="ops-preview-actions-bar">
                            <button type="button" class="btn-ops-mini" id="btnResetEmailPreview"><i class="fi fi-rr-refresh"></i> Reset Content</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <div class="ops-modal-footer-info">
                <i class="fi fi-rr-shield-check text-emerald"></i>
                <span>Direct Dispatch via Integrated WhatsApp &amp; Email Gateway</span>
            </div>
            <div class="ops-modal-footer-actions">
                <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
                <button type="button" class="btn-v3-action btn-blue" id="btnConfirmSendBookingRequest">
                    <i class="fi fi-rr-paper-plane"></i> Send Booking Request Now
                </button>
            </div>
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

            <!-- Direct Supplementary / Manual Reference Numbers -->
            <div class="ops-voucher-direct-section" id="voucherDirectSection">
                <div class="ops-voucher-direct-header">
                    <span class="direct-title"><i class="fi fi-rr-edit text-emerald"></i> Quick / Direct Confirmation Details</span>
                    <span class="direct-subtitle">Enter property confirmation numbers or driver details directly for the voucher</span>
                </div>
                <div class="ops-form-row">
                    <div class="v3-form-group">
                        <label class="v3-form-label">Hotel / Resort Confirmation Ref</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-building text-blue"></i></span>
                            <input type="text" id="voucherDirectHotelRef" class="v3-form-input" placeholder="e.g. HTL-2026-9812">
                        </div>
                    </div>
                    <div class="v3-form-group">
                        <label class="v3-form-label">Cab / Driver Contact &amp; Cab No.</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-car text-emerald"></i></span>
                            <input type="text" id="voucherDirectDriverRef" class="v3-form-input" placeholder="e.g. Innova (Tariq - 9876543210)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dispatch Options Toggles -->
            <div class="ops-form-toggles-box">
                <label class="ops-switch-item">
                    <div class="ops-switch-content">
                        <span class="ops-switch-title"><i class="fi fi-brands-whatsapp text-emerald"></i> WhatsApp Voucher Alert to Traveler</span>
                        <span class="ops-switch-desc">Send official confirmation voucher link directly to traveler</span>
                    </div>
                    <div class="ops-toggle-switch">
                        <input type="checkbox" id="voucherOptSendWa" checked>
                        <span class="ops-toggle-slider"></span>
                    </div>
                </label>
                <label class="ops-switch-item">
                    <div class="ops-switch-content">
                        <span class="ops-switch-title"><i class="fi fi-rr-shield-check text-blue"></i> Allow Voucher Issuance Override</span>
                        <span class="ops-switch-desc">Manager authorization for post-trip or corporate payment terms</span>
                    </div>
                    <div class="ops-toggle-switch">
                        <input type="checkbox" id="voucherOptManagerOverride">
                        <span class="ops-toggle-slider"></span>
                    </div>
                </label>
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
            <input type="hidden" id="invRawPackageTotal" value="0">

            <div class="v3-form-group">
                <label class="v3-form-label">Document Type *</label>
                <div class="ops-segmented-invoice-toggle">
                    <label class="ops-doctype-chip">
                        <input type="radio" name="invDocType" value="proforma" checked>
                        <div class="ops-doctype-card">
                            <i class="fi fi-rr-document text-amber"></i>
                            <span>Proforma Invoice (Pre-trip)</span>
                        </div>
                    </label>
                    <label class="ops-doctype-chip">
                        <input type="radio" name="invDocType" value="tax_invoice">
                        <div class="ops-doctype-card">
                            <i class="fi fi-rr-receipt text-emerald"></i>
                            <span>Tax Invoice (Settled)</span>
                        </div>
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
                <div class="ops-inv-row text-muted">
                    <span>GST (5% Included):</span>
                    <span id="invGstAmount">₹ 0</span>
                </div>
                <!-- Editable Advance Payment Input -->
                <div class="ops-inv-row ops-inv-advance-input-row">
                    <label for="invAdvanceInput" class="ops-inv-advance-label">
                        <i class="fi fi-rr-wallet text-emerald"></i> Advance / Received Payment (₹):
                    </label>
                    <div class="ops-inv-advance-field-wrap">
                        <span class="currency-tag">₹</span>
                        <input type="number" id="invAdvanceInput" class="v3-form-input inv-editable-advance" placeholder="0" min="0" step="100">
                    </div>
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

<!-- Modal 4: Driver & Vehicle Allocation Modal -->
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

<!-- Modal 5: Quick Record Vendor Response Modal -->
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
