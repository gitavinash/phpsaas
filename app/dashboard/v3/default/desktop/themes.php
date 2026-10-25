<?php

/**
 * Blumox Control Panel: Dedicated Themes Matrix Page
 * Path: /app/dashboard/v3/default/desktop/themes.php
 * Route: /dashboard/themes/
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

    <!-- Themes Main Content Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Theme Catalog</h3>
                <p>Frontend templates available for client site provisioning</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchThemes" placeholder="Search themes by title or slug...">
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select" id="cpFilterThemeApp">
                    <option value="">All Applications</option>
                </select>
            </div>
        </div>

        <div class="cp-grid-cards" id="cpThemesGrid">
            <!-- Populated asynchronously via /api/v3/control_panel/themes/ -->
            <div class="cp-empty-state">
                <i class="fi fi-rr-spinner-alt cp-spin"></i>
                <p>Loading themes matrix...</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Register Theme -->
<div class="cp-modal-backdrop" id="modalNewTheme">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3 id="themeModalHeading">Register New Theme</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewTheme">
            <input type="hidden" id="themeIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="themeAppSelect">Parent Application *</label>
                    <select class="cp-form-select" id="themeAppSelect" required>
                        <option value="">Select App</option>
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
                <button type="submit" class="cp-btn-primary">Save Theme</button>
            </div>
        </form>
    </div>
</div>
