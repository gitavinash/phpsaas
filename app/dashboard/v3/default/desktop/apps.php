<?php

/**
 * Blumox Control Panel: Dedicated Apps Hub Page
 * Path: /app/dashboard/v3/default/desktop/apps.php
 * Route: /dashboard/apps/
 * Strict V3 Rule: External CSS and External JS only, no inline code
 */

$userRole = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$roleTitles = [
    'administrator' => 'System Administrator',
    'partner' => 'Partner / Referral',
    'sales' => 'Sales Executive',
    'support' => 'Customer Support',
    'on_ground_sales' => 'On Ground Sales'
];
$displayRole = $roleTitles[$userRole] ?? 'Administrator';

?>

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="apps" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-apps"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Application Catalog &amp; Software Hub</h1>
                <p>
                    <span>Core Software Modules, Vertical Products &amp; Platform Offerings</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenNewAppModal">
                    <i class="fi fi-rr-add"></i> <span>Register New App</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Applications">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Apps Hub Main Content Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Registered Applications</h3>
                <p>Multi-tenant application modules available for provisioning</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchApps" placeholder="Search applications by title or slug...">
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select" id="cpFilterAppStatus">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="cp-grid-cards" id="cpAppsGrid">
            <!-- Populated asynchronously via /api/v3/control_panel/apps/ -->
            <div class="cp-empty-state">
                <i class="fi fi-rr-spinner-alt cp-spin"></i>
                <p>Loading application catalog...</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Register App -->
<div class="cp-modal-backdrop" id="modalNewApp">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3 id="appModalHeading">Register Application</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewApp">
            <input type="hidden" id="appIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="appTitleInput">Application Title *</label>
                    <input type="text" class="cp-form-input" id="appTitleInput" required placeholder="e.g. Flight Booking Portal">
                </div>
                <div class="cp-form-group">
                    <label for="appPathInput">App Path Slug (Unique) *</label>
                    <input type="text" class="cp-form-input" id="appPathInput" required placeholder="e.g. flight_booking">
                </div>
                <div class="cp-form-group">
                    <label for="appDescInput">Description</label>
                    <textarea class="cp-form-textarea" id="appDescInput" rows="2" placeholder="Brief intro to the application..."></textarea>
                </div>
                <div class="cp-form-group">
                    <label for="appColorInput">Primary Brand Color</label>
                    <input type="text" class="cp-form-input" id="appColorInput" value="#2563eb" placeholder="#2563eb">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Application</button>
            </div>
        </form>
    </div>
</div>
