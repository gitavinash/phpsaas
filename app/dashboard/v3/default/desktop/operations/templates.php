<?php

/**
 * Operations Management - Booking Request Templates Manager
 * Path: app/dashboard/v3/default/desktop/operations/templates.php
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
            <p class="text-muted">You do not have operations permissions to access the Request Templates Manager.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="templates">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon purple">
                <i class="fi fi-rr-file-edit"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Booking Request Templates Manager</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Standardize vendor reservation communications across WhatsApp and Email with dynamic merge variables.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Create Template CTA -->
            <button type="button" class="btn-v3-action btn-purple ops-action-btn" id="btnOpenNewTemplateModal">
                <i class="fi fi-rr-plus"></i> <span>Create Template</span>
            </button>

            <!-- Hub Link -->
            <a href="/dashboard/operations/" class="btn-v3-action ops-action-btn" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> <span>Hub</span>
            </a>

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshOps" class="btn-v3-action ops-action-btn" title="Refresh templates">
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

    <!-- 2. Templates Section -->
    <div class="ops-tab-section active" id="tabSectionTemplates">
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

            <button type="button" class="btn-v3-action btn-blue" id="btnOpenNewTemplateModalAlt">
                <i class="fi fi-rr-plus"></i> New Template
            </button>
        </div>

        <div class="ops-templates-grid" id="opsTemplatesGrid">
            <!-- Populated dynamically via operations.js -->
        </div>
    </div>
</div>

<!-- Modal: Booking Request Template Editor Modal -->
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
