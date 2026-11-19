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
                        <span class="pill-dot all"></span> All Confirmed
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="10">
                        <span class="pill-dot approved"></span> Approved (10)
                    </button>
                    <button type="button" class="btn-ops-pill" data-status="1">
                        <span class="pill-dot confirmed"></span> Confirmed (1)
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
