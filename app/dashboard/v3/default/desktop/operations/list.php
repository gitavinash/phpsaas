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

        <div class="pkg-mini-kpi emerald" data-filter-status="12" title="Click to filter Payments Recorded">
            <div class="kpi-icon"><i class="fi fi-rr-wallet"></i></div>
            <div class="kpi-meta">
                <p>Payments Recorded</p>
                <h3 id="statPaidBookings">0</h3>
                <small>Advance / Full paid</small>
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
                        <span class="pill-dot all"></span> All Confirmed
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="10">
                        <span class="pill-dot approved"></span> Approved (10)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="6">
                        <span class="pill-dot payment-sent"></span> Payment Sent (6)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="8">
                        <span class="pill-dot upay"></span> Upay (8)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="12">
                        <span class="pill-dot paid"></span> Paid (12)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="11">
                        <span class="pill-dot voucher"></span> Vouchers (11)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="13">
                        <span class="pill-dot progress"></span> In Progress (13)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="14">
                        <span class="pill-dot completed"></span> Completed (14)
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
                            <th class="th-lead">Lead &amp; Traveler</th>
                            <th class="th-dest">Destination</th>
                            <th class="th-dates">Travel Dates</th>
                            <th class="th-pax">Pax</th>
                            <th class="th-financials">Package &amp; Payments</th>
                            <th class="th-agent">Sales Agent</th>
                            <th class="th-status">Ops Status</th>
                            <th class="th-actions text-right">Operational Actions</th>
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
