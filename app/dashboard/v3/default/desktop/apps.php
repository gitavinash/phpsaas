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
                    <span>Multi-Tenant Core Software Modules &amp; Vertical Platform Products</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenNewAppModal">
                    <i class="fi fi-rr-plus"></i> <span>Register New App</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Applications">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Counters -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-apps"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Modules</p>
                <h3 id="cpAppsKpiTotal">--</h3>
                <span>Registered in catalog</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon emerald">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Active Apps</p>
                <h3 id="cpAppsKpiActive">--</h3>
                <span class="cp-kpi-live"><i class="fi fi-rr-bullet"></i> Operational</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon amber">
                <i class="fi fi-rr-pause-circle"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Inactive / Staged</p>
                <h3 id="cpAppsKpiInactive">--</h3>
                <span>Disabled or drafting</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-palette"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Themes</p>
                <h3 id="cpAppsKpiThemes">--</h3>
                <span>Design variations</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon indigo">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Client Sites</p>
                <h3 id="cpAppsKpiSites">--</h3>
                <span>Tenant instances</span>
            </div>
        </div>
    </div>

    <!-- Apps Hub Main Content Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Application Catalog</h3>
                <p>Browse active modules, modify product branding &amp; details, or deploy new applications</p>
            </div>

            <!-- Quick Segmented Status Tabs -->
            <div class="cp-filter-tabs" id="cpAppStatusTabs">
                <button type="button" class="cp-filter-pill active" data-status="">
                    <span>All Applications</span>
                    <span class="cp-pill-count" id="cpBadgeAllApps">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-status="1">
                    <i class="fi fi-rr-check"></i>
                    <span>Active Only</span>
                    <span class="cp-pill-count" id="cpBadgeActiveApps">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-status="0">
                    <i class="fi fi-rr-cross-circle"></i>
                    <span>Inactive</span>
                    <span class="cp-pill-count" id="cpBadgeInactiveApps">--</span>
                </button>
            </div>
        </div>

        <!-- Toolbar: Search, Sorting, Hidden Status select, and View Switcher -->
        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchApps" placeholder="Search by app title, directory slug, or description...">
                <button type="button" class="cp-search-clear-btn" id="btnClearSearchApps" title="Clear search">&times;</button>
            </div>

            <div class="cp-filters-wrap">
                <!-- Status select kept synced with filter pills -->
                <select class="cp-select v3-form-select cp-select2" id="cpFilterAppStatus" data-placeholder="All Status" data-width="150px">
                    <option value="">Status: All</option>
                    <option value="1">Status: Active</option>
                    <option value="0">Status: Inactive</option>
                </select>

                <select class="cp-select v3-form-select cp-select2" id="cpSortApps" data-placeholder="Sort Applications" data-width="180px">
                    <option value="newest">Sort: Newest First</option>
                    <option value="oldest">Sort: Oldest First</option>
                    <option value="title_asc">Sort: Title (A-Z)</option>
                    <option value="title_desc">Sort: Title (Z-A)</option>
                    <option value="most_sites">Sort: Most Client Sites</option>
                    <option value="most_themes">Sort: Most Themes</option>
                </select>

                <!-- View Switcher: Grid vs Table -->
                <div class="cp-view-toggle">
                    <button type="button" class="cp-view-btn active" id="btnViewAppsGrid" title="Grid View">
                        <i class="fi fi-rr-grid"></i>
                    </button>
                    <button type="button" class="cp-view-btn" id="btnViewAppsTable" title="Table View">
                        <i class="fi fi-rr-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Status Sub-bar -->
        <div class="cp-result-meta">
            <span id="cpAppsCountLabel">Loading applications...</span>
        </div>

        <!-- Grid View Mode Container -->
        <div class="cp-grid-cards" id="cpAppsGrid">
            <div class="cp-empty-state">
                <i class="fi fi-rr-spinner-alt cp-spin"></i>
                <p>Loading application catalog...</p>
            </div>
        </div>

        <!-- Table View Mode Container (hidden by default) -->
        <div class="cp-table-wrap cp-apps-table-wrap" id="cpAppsTableWrap">
            <table class="cp-table package-v3-datatable" id="cpAppsTable">
                <thead>
                    <tr>
                        <th class="col-app-id">ID</th>
                        <th>Application &amp; Slug</th>
                        <th>Description</th>
                        <th>Themes</th>
                        <th>Sites</th>
                        <th>Brand Palette</th>
                        <th>Visibility</th>
                        <th>Operational Status</th>
                        <th class="text-right no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody id="cpAppsTableBody">
                    <tr>
                        <td colspan="9" class="text-center">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading applications...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Register New App OR Modify Existing App -->
<div class="cp-modal-backdrop" id="modalNewApp">
    <div class="cp-modal-box cp-app-modal-box">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <h3 id="appModalHeading">Register Application</h3>
                <p id="appModalSubheading">Deploy a new software product module into the platform</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>

        <form id="formNewApp">
            <input type="hidden" id="appIdInput" value="0">
            <div class="cp-modal-body">
                <!-- Title Field -->
                <div class="cp-form-group">
                    <label for="appTitleInput">Application Title *</label>
                    <input type="text" class="cp-form-input" id="appTitleInput" required placeholder="e.g. Flight Booking Portal">
                    <span class="cp-form-hint">Display name used across client dashboards and provisioning forms.</span>
                </div>

                <!-- Path Slug Field (Protected: editable on create, locked on modify) -->
                <div class="cp-form-group" id="appPathGroup">
                    <label for="appPathInput">
                        <span>Directory Path Slug (Unique) *</span>
                        <span class="cp-locked-badge" id="appPathLockBadge"><i class="fi fi-rr-lock"></i> Locked (Immutable)</span>
                    </label>
                    <div class="cp-input-icon-wrap">
                        <i class="fi fi-rr-folder cp-input-icon"></i>
                        <input type="text" class="cp-form-input cp-input-with-icon" id="appPathInput" required placeholder="e.g. flight_booking">
                    </div>
                    <span class="cp-form-hint" id="appPathCreateHint">Directory folder name under <code>app/</code>. Auto-generated from title, alphanumeric and underscores only.</span>
                    <div class="cp-locked-notice" id="appPathLockedNotice">
                        <i class="fi fi-rr-shield-exclamation"></i>
                        <span>Directory path slug is permanently locked to preserve file bindings, routing, themes, and existing tenant instances.</span>
                    </div>
                </div>

                <!-- Description Field -->
                <div class="cp-form-group">
                    <label for="appDescInput">Application Description</label>
                    <textarea class="cp-form-textarea" id="appDescInput" rows="2" placeholder="Brief summary of the application functionality and target industry..."></textarea>
                </div>

                <!-- Brand Color Palette -->
                <div class="cp-form-row">
                    <div class="cp-form-group cp-form-col">
                        <label for="appColorInput">Primary Brand Color</label>
                        <div class="cp-color-picker-wrap">
                            <input type="color" class="cp-color-swatch-input" id="appColorPicker" value="#2563eb">
                            <input type="text" class="cp-form-input cp-color-text-input" id="appColorInput" value="#2563eb" placeholder="#2563eb" maxlength="10">
                        </div>
                    </div>

                    <div class="cp-form-group cp-form-col">
                        <label for="appSecondaryColorInput">Secondary Color</label>
                        <div class="cp-color-picker-wrap">
                            <input type="color" class="cp-color-swatch-input" id="appSecondaryColorPicker" value="#60a5fa">
                            <input type="text" class="cp-form-input cp-color-text-input" id="appSecondaryColorInput" value="#60a5fa" placeholder="#60a5fa" maxlength="10">
                        </div>
                    </div>
                </div>

                <!-- Color Live Preview Banner -->
                <div class="cp-brand-preview-banner" id="appBrandPreviewBanner">
                    <div class="cp-brand-preview-accent" id="appPreviewAccent"></div>
                    <div class="cp-brand-preview-content">
                        <div class="cp-brand-preview-icon" id="appPreviewIcon">
                            <i class="fi fi-rr-apps"></i>
                        </div>
                        <div class="cp-brand-preview-text">
                            <strong id="appPreviewTitle">Application Preview</strong>
                            <span id="appPreviewPath">/app_path/</span>
                        </div>
                    </div>
                </div>

                <!-- Image / Icon URL -->
                <div class="cp-form-group">
                    <label for="appImageInput">App Icon / Logo Image URL</label>
                    <input type="text" class="cp-form-input" id="appImageInput" placeholder="e.g. /upload/others/logo.png or https://...">
                </div>

                <!-- Status & Visibility Selectors -->
                <div class="cp-form-row">
                    <div class="cp-form-group cp-form-col">
                        <label for="appStatusSelect">Operational Status</label>
                        <select class="cp-form-select cp-select2 cp-modal-select2" id="appStatusSelect" data-placeholder="Select Status">
                            <option value="1">Active (Live &amp; Operational)</option>
                            <option value="0">Inactive (Disabled / Suspended)</option>
                        </select>
                    </div>

                    <div class="cp-form-group cp-form-col">
                        <label for="appVisibilitySelect">Catalog Visibility</label>
                        <select class="cp-form-select cp-select2 cp-modal-select2" id="appVisibilitySelect" data-placeholder="Select Visibility">
                            <option value="1">Visible in Provisioning Catalog</option>
                            <option value="0">Internal / Hidden Module</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary" id="btnSubmitApp">
                    <i class="fi fi-rr-check"></i> <span id="btnSubmitAppText">Save Application</span>
                </button>
            </div>
        </form>
    </div>
</div>
