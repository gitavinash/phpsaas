<?php

/**
 * Operations Management - Create / Edit Booking Template (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/template-add.php
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
            <p class="text-muted">You do not have operations permissions to configure templates.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$templateId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="template-add" data-template-id="<?php echo $templateId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon purple">
                <i class="fi fi-rr-file-edit"></i>
            </div>
            <div class="package-header-titles">
                <h2><?php echo $templateId > 0 ? 'Edit Booking Request Template' : 'Create New Booking Request Template'; ?></h2>
                <p>Compose vendor reservation formats with dynamic merge variables for instant WhatsApp and Email dispatch.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/templates/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> All Templates
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Template Form Panel -->
    <div class="package-card-panel p-24 max-w-800 mx-auto">
        <input type="hidden" id="tplEditorId" value="<?php echo $templateId; ?>">

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
        <div class="ops-merge-tags-box mb-16">
            <label><i class="fi fi-rr-magic-wand"></i> Click any merge tag below to insert into the template body:</label>
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
            <label class="v3-form-label">Template Body Content *</label>
            <textarea id="tplEditorBody" class="v3-form-input" rows="10" placeholder="Type template message here..."></textarea>
        </div>

        <div class="v3-form-group">
            <label class="ops-checkbox-label">
                <input type="checkbox" id="tplEditorIsDefault" value="1">
                <span>Set as default template for this channel and vendor type</span>
            </label>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-20 border-top mt-16">
            <a href="/dashboard/operations/templates/" class="btn-v3-action btn-outline">Back to Templates</a>
            <button type="button" class="btn-v3-action btn-purple font-14 py-10 px-20" id="btnConfirmSaveTemplate">
                <i class="fi fi-rr-disk"></i> Save Template
            </button>
        </div>
    </div>
</div>
