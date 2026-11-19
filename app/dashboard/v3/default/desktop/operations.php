<?php

/**
 * Operations Management Hub - Main Landing & Operations Navigation Desk
 * Path: app/dashboard/v3/default/desktop/operations.php
 * Workspace Compliant: Zero inline CSS/JS, modular card navigation, backward compatible query/hash handling.
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
            <p class="text-muted">You do not have operations permissions to access the Operations Hub.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

// -------------------------------------------------------------------------
// Backward-compatibility query parameter routing
// -------------------------------------------------------------------------
$reqAction = isset($_GET['action']) ? trim($_GET['action']) : '';
$reqTab    = isset($_GET['tab']) ? trim($_GET['tab']) : '';
$reqLeadId = isset($_GET['lead_id']) ? urlencode(trim($_GET['lead_id'])) : '';

$forwardUrl = '';
if ($reqAction === 'booking') {
    $forwardUrl = '/dashboard/operations/booking-request/' . (!empty($reqLeadId) ? '?lead_id=' . $reqLeadId : '');
} elseif ($reqAction === 'voucher') {
    $forwardUrl = '/dashboard/operations/voucher/' . (!empty($reqLeadId) ? '?lead_id=' . $reqLeadId : '');
} elseif ($reqAction === 'invoice') {
    $forwardUrl = '/dashboard/operations/invoice/' . (!empty($reqLeadId) ? '?lead_id=' . $reqLeadId : '');
} elseif ($reqTab === 'trips') {
    $forwardUrl = '/dashboard/operations/trips/';
} elseif ($reqTab === 'requests') {
    $forwardUrl = '/dashboard/operations/requests/';
} elseif ($reqTab === 'unassigned') {
    $forwardUrl = '/dashboard/operations/unassigned/';
} elseif ($reqTab === 'templates') {
    $forwardUrl = '/dashboard/operations/templates/';
} elseif ($reqTab === 'confirmed') {
    $forwardUrl = '/dashboard/operations/list/';
}

if (!empty($forwardUrl)) {
    echo '<script>window.location.replace(' . json_encode($forwardUrl) . ');</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($forwardUrl, ENT_QUOTES) . '"></noscript>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper ops-hub-page" id="operationsApp" data-page="hub">
    <!-- Header Heading -->
    <h2 class="heading">
        <?php echo ucwords($postType); ?>
        <span>Vendor reservation requests, confirmation vouchers, daily trip logistics, and traveler fulfillments</span>
    </h2>

    <!-- Quick Metric KPI Row -->
    <div class="package-kpi-row ops-kpi-row mb-24">
        <a href="/dashboard/operations/list/" class="pkg-mini-kpi indigo text-inherit">
            <div class="kpi-icon"><i class="fi fi-rr-bookmark"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Bookings</p>
                <h3 id="statConfirmedTotal">0</h3>
                <small>Quotation approved &amp; onwards</small>
            </div>
        </a>

        <a href="/dashboard/operations/list/?status=10" class="pkg-mini-kpi amber text-inherit">
            <div class="kpi-icon"><i class="fi fi-rr-time-check"></i></div>
            <div class="kpi-meta">
                <p>Quotation Approved</p>
                <h3 id="statQuoteApproved">0</h3>
                <small>Ready for booking &amp; proforma</small>
            </div>
        </a>

        <a href="/dashboard/operations/list/?status=12" class="pkg-mini-kpi emerald text-inherit">
            <div class="kpi-icon"><i class="fi fi-rr-wallet"></i></div>
            <div class="kpi-meta">
                <p>Payments Recorded</p>
                <h3 id="statPaidBookings">0</h3>
                <small>Advance or full payment verified</small>
            </div>
        </a>

        <a href="/dashboard/operations/list/?status=11" class="pkg-mini-kpi cyan text-inherit">
            <div class="kpi-icon"><i class="fi fi-rr-ticket"></i></div>
            <div class="kpi-meta">
                <p>Vouchers Issued</p>
                <h3 id="statVouchersIssued">0</h3>
                <small>Hotel &amp; cab confirmed</small>
            </div>
        </a>

        <a href="/dashboard/operations/trips/" class="pkg-mini-kpi purple text-inherit">
            <div class="kpi-icon"><i class="fi fi-rr-plane"></i></div>
            <div class="kpi-meta">
                <p>Trips In Progress</p>
                <h3 id="statTripsInProgress">0</h3>
                <small>Active on-ground travelers</small>
            </div>
        </a>
    </div>

    <!-- Section 1: Operations Lists & Desks -->
    <h3 class="v3-subheading mb-16"><i class="fi fi-rr-apps-sort"></i> Operational Desks &amp; Ledgers</h3>
    <div class="main-menu-list mb-32">
        <!-- Card 1: Confirmed Leads List -->
        <div class="widget box-2-5">
            <a href="operations/list/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-confirmed">
                        <i class="fi-rr-badge-check"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Confirmed Leads</h3>
                        <p>Fulfillment desk, payment verifications &amp; proformas</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: Daily Trip Planner Board -->
        <div class="widget box-2-5">
            <a href="operations/trips/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-pending">
                        <i class="fi-rr-plane-departure"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Trip Planner Board</h3>
                        <p>Daily departures, on-tour tracking &amp; arrival logistics</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 3: Dispatched Booking Requests Ledger -->
        <div class="widget box-2-5">
            <a href="operations/requests/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-followup">
                        <i class="fi-rr-paper-plane"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Booking Requests</h3>
                        <p>Dispatched vendor reservations &amp; live confirmations</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 4: Unassigned Leads Desk -->
        <div class="widget box-2-5">
            <a href="operations/unassigned/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-cancelled">
                        <i class="fi-rr-user-add"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Assign Leads</h3>
                        <p>Allocate incoming customer inquiries to sales agents</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 5: Booking Request Templates -->
        <div class="widget box-2-5">
            <a href="operations/templates/">
                <div class="widget-setting-info">
                    <div class="feature-icon">
                        <i class="fi-rr-file-edit"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Request Templates</h3>
                        <p>Dynamic WhatsApp &amp; Email vendor reservation templates</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Section 2: Direct Actions & CTAs -->
    <h3 class="v3-subheading mb-16"><i class="fi fi-rr-bolt"></i> Operational CTAs &amp; Quick Actions</h3>
    <div class="main-menu-list">
        <!-- CTA 1: Send Booking Request -->
        <div class="widget box-2-5">
            <a href="operations/booking-request/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-followup">
                        <i class="fi-rr-paper-plane"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Send Booking Request</h3>
                        <p>Dispatch hotel, cab or tour reservation with live preview</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- CTA 2: Confirmation Vouchers -->
        <div class="widget box-2-5">
            <a href="operations/voucher/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-confirmed">
                        <i class="fi-rr-ticket"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Issue Travel Vouchers</h3>
                        <p>Record vendor ref IDs and issue verified vouchers</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- CTA 3: Generate Invoice -->
        <div class="widget box-2-5">
            <a href="operations/invoice/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-pending">
                        <i class="fi-rr-document"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Generate Invoice</h3>
                        <p>Calculate proforma, 5% GST, balance due &amp; dispatch</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- CTA 4: Assign Cab / Driver -->
        <div class="widget box-2-5">
            <a href="operations/dispatch/">
                <div class="widget-setting-info">
                    <div class="feature-icon">
                        <i class="fi-rr-car"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Assign Cab / Driver</h3>
                        <p>Save driver details and send vehicle info to guest on WhatsApp</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- CTA 5: Lead Allocation -->
        <div class="widget box-2-5">
            <a href="operations/assign/">
                <div class="widget-setting-info">
                    <div class="feature-icon is-cancelled">
                        <i class="fi-rr-user-time"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Allocate Lead</h3>
                        <p>Single or bulk assign inquiries to sales representatives</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- CTA 6: Create Booking Template -->
        <div class="widget box-2-5">
            <a href="operations/template-add/">
                <div class="widget-setting-info">
                    <div class="feature-icon">
                        <i class="fi-rr-plus"></i>
                    </div>
                    <div class="about-feature">
                        <h3>Create Template</h3>
                        <p>Design a new WhatsApp or Email vendor booking format</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
