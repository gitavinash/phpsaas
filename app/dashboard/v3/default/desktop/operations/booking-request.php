<?php

/**
 * Operations Management - Send Vendor Booking Request (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/booking-request.php
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
            <p class="text-muted">You do not have operations permissions to dispatch booking requests.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$prefilledLeadId = isset($_GET['lead_id']) ? htmlspecialchars(trim($_GET['lead_id']), ENT_QUOTES) : '';
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="booking-request" data-lead-id="<?php echo $prefilledLeadId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-paper-plane"></i>
            </div>
            <div class="package-header-titles">
                <h2>Dispatch Vendor Booking Request</h2>
                <p>Configure reservation logistics, customize WhatsApp or Email templates in real-time, and dispatch to properties or transporters.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/requests/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Requests Ledger
            </a>
            <a href="/dashboard/operations/list/" class="btn-v3-action">
                <i class="fi fi-rr-badge-check"></i> Confirmed Leads
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Booking Request Card Container -->
    <div class="package-card-panel p-24">
        <!-- Lead Lookup Box if not prefilled -->
        <div class="ops-lead-picker-box mb-20 <?php echo !empty($prefilledLeadId) ? 'd-none' : ''; ?>" id="opsLeadPickerWrap">
            <div class="v3-form-group">
                <label class="v3-form-label">Enter Lead ID or Traveler Code *</label>
                <div class="d-flex gap-10">
                    <input type="text" id="bkgLeadIdManualInput" class="v3-form-input max-w-300" placeholder="e.g. 1024 or hash code" value="<?php echo $prefilledLeadId; ?>">
                    <button type="button" class="btn-v3-action btn-blue" id="btnLoadBookingLead">
                        <i class="fi fi-rr-search"></i> Load Lead Logistics
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden lead ID container used by operations.js -->
        <input type="hidden" id="bkgModalLeadId" value="<?php echo $prefilledLeadId; ?>">

        <!-- Interactive Two-Column Form & Live Preview Grid -->
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

                <div class="mt-20">
                    <button type="button" class="btn-v3-action btn-blue w-100 py-12 font-14" id="btnConfirmSendBookingRequest">
                        <i class="fi fi-rr-paper-plane"></i> Send Booking Request Now
                    </button>
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
                        <textarea id="bkgWhatsAppContent" class="ops-wa-textarea" rows="14"></textarea>
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
                        <textarea id="bkgEmailContent" class="ops-email-textarea" rows="12"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
