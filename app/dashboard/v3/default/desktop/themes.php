<?php

/**
 * Blumox Control Panel: Dedicated Themes Matrix Page
 * Path: /app/dashboard/v3/default/desktop/themes.php
 * Route: /dashboard/themes/
 * Strict V3 Rule: External CSS and External JS only, zero inline code
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

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="themes" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-palette"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Theme Matrix &amp; Presentation Engine</h1>
                <p>
                    <span>Visual Themes, UI Layouts &amp; Styling Presets Linked to Applications</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenNewThemeModal">
                    <i class="fi fi-rr-add"></i> <span>Register New Theme</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Themes">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Counters -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-palette"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Themes</p>
                <h3 id="cpThemesKpiTotal">--</h3>
                <span>Catalog variations</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon emerald">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Active Themes</p>
                <h3 id="cpThemesKpiActive">--</h3>
                <span class="cp-kpi-live"><i class="fi fi-rr-bullet"></i> Operational</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon amber">
                <i class="fi fi-rr-pause-circle"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Archived / Staged</p>
                <h3 id="cpThemesKpiInactive">--</h3>
                <span>Inactive presets</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-apps"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Apps Covered</p>
                <h3 id="cpThemesKpiApps">--</h3>
                <span>Parent modules</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon indigo">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Linked Sites</p>
                <h3 id="cpThemesKpiSites">--</h3>
                <span>Running instances</span>
            </div>
        </div>
    </div>

    <!-- Themes Main Content Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Theme Catalog</h3>
                <p>Frontend templates available for client site provisioning</p>
            </div>

            <!-- Quick Segmented Status Tabs -->
            <div class="cp-filter-tabs" id="cpThemeStatusTabs">
                <button type="button" class="cp-filter-pill active" data-filter-status="">
                    <span>All Themes</span>
                    <span class="cp-pill-count" id="cpBadgeAllThemes">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-filter-status="1">
                    <i class="fi fi-rr-check"></i>
                    <span>Active Only</span>
                    <span class="cp-pill-count" id="cpBadgeActiveThemes">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-filter-status="0">
                    <i class="fi fi-rr-cross-circle"></i>
                    <span>Archived</span>
                    <span class="cp-pill-count" id="cpBadgeInactiveThemes">--</span>
                </button>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchThemes" placeholder="Search themes by title, slug, or app...">
                <button type="button" class="cp-search-clear-btn" id="cpClearThemesSearch" title="Clear search">
                    <i class="fi fi-rr-cross-small"></i>
                </button>
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select v3-form-select cp-select2" id="cpFilterThemeApp" data-placeholder="All Applications" data-width="180px">
                    <option value="">All Applications</option>
                </select>
                <select class="cp-select v3-form-select cp-select2" id="cpFilterThemeStatus" data-placeholder="All Status" data-width="145px">
                    <option value="">Status: All</option>
                    <option value="1">Active Only</option>
                    <option value="0">Archived</option>
                </select>
                <select class="cp-select v3-form-select cp-select2" id="cpSortThemes" data-placeholder="Sort Themes" data-width="165px">
                    <option value="newest">Sort: Newest First</option>
                    <option value="oldest">Sort: Oldest First</option>
                    <option value="title_asc">Sort: Title (A-Z)</option>
                    <option value="most_sites">Sort: Most Sites Running</option>
                </select>

                <!-- View Switcher: Grid vs Table -->
                <div class="cp-view-toggle">
                    <button type="button" class="cp-view-btn active" id="btnViewThemesGrid" title="Grid View">
                        <i class="fi fi-rr-grid"></i>
                    </button>
                    <button type="button" class="cp-view-btn" id="btnViewThemesTable" title="Table View">
                        <i class="fi fi-rr-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Status Sub-bar -->
        <div class="cp-result-meta">
            <span id="cpThemesCountLabel">Loading themes matrix...</span>
        </div>

        <!-- Grid View Mode Container -->
        <div class="cp-grid-cards" id="cpThemesGrid">
            <div class="cp-empty-state">
                <i class="fi fi-rr-spinner-alt cp-spin"></i>
                <p>Loading themes matrix...</p>
            </div>
        </div>

        <!-- Table View Mode Container (Hidden by default) -->
        <div class="cp-table-wrap cp-themes-table-wrap" id="cpThemesTableWrap">
            <table class="cp-table package-v3-datatable" id="cpThemesTable">
                <thead>
                    <tr>
                        <th class="col-app-id">ID</th>
                        <th>Theme Title &amp; Slug</th>
                        <th>Parent Application</th>
                        <th>Directory Path</th>
                        <th>Version</th>
                        <th>Client Sites</th>
                        <th>Status</th>
                        <th class="text-right no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody id="cpThemesTableBody">
                    <tr>
                        <td colspan="8" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading themes list...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Register / Edit Theme -->
<div class="cp-modal-backdrop" id="modalNewTheme">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <h3 id="themeModalHeading">Register New Theme</h3>
                <p>Link a visual frontend theme preset to a parent application</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewTheme">
            <input type="hidden" id="themeIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="themeAppSelect">Parent Application *</label>
                    <select class="cp-form-select v3-form-select cp-select2 cp-modal-select2" id="themeAppSelect" required data-placeholder="Select Parent App">
                        <option value="">Select Parent App</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="themeTitleInput">Theme Title *</label>
                    <input type="text" class="cp-form-input" id="themeTitleInput" required placeholder="e.g. Modern Dark Travel">
                </div>
                <div class="cp-form-group">
                    <label for="themePathInput">Theme Directory Path *</label>
                    <input type="text" class="cp-form-input" id="themePathInput" required placeholder="e.g. modern_dark">
                </div>
                <div class="cp-form-group">
                    <label for="themeVersionInput">Version</label>
                    <input type="text" class="cp-form-input" id="themeVersionInput" value="1.0" placeholder="1.0">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">
                    <i class="fi fi-rr-disk"></i> <span>Save Theme</span>
                </button>
            </div>
        </form>
    </div>
</div>
