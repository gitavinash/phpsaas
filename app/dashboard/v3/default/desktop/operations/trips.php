<?php

/**
 * Operations Management - Daily Trip Planner & Logistics Board
 * Path: app/dashboard/v3/default/desktop/operations/trips.php
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
            <p class="text-muted">You do not have operations permissions to access the Trip Planner.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="trips">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon amber">
                <i class="fi fi-rr-plane-departure"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Daily Trip Planner &amp; Logistics Board</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Real-time on-ground itinerary coordination, daily arrivals, departure transfers, and driver allocations.</p>
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

    <!-- 2. Trip Board Section -->
    <div class="ops-tab-section active" id="tabSectionTrips">
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

        <!-- 4-Column Logistics Kanban Board -->
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
</div>

<!-- Modal: Driver & Vehicle Allocation + 24hr Pre-Trip WhatsApp Dispatch -->
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
