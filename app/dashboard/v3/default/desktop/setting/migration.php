<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
$isAdmin = (isset($site_access_type) && in_array($site_access_type, ['administrator', 'admin'], true)) || in_array($currentUserType, ['administrator', 'admin', 'superadmin'], true);

if (!$isAdmin) {
    echo "<script>window.location.href = '/dashboard/setting/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingMigrationApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-cloud-download"></i>
            </div>
            <div class="package-header-titles">
                <h2>Default Data Migration</h2>
                <p>Import curated master tour packages, holiday destinations, classified hotels &amp; tariffs, fleet vehicles, and transporter profiles into your workspace.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/setting/" class="btn-v3-action" data-tooltip="Back to Settings">
                <i class="fi fi-rr-settings"></i> <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Stepper Navigation Bar -->
    <div class="v3-stepper-container migration-stepper">
        <div class="v3-step-item active" data-step="1">
            <div class="v3-step-number">1</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 1</span>
                <span class="v3-step-title">Workspace Status</span>
            </div>
        </div>
        <div class="v3-step-divider"></div>
        <div class="v3-step-item" data-step="2">
            <div class="v3-step-number">2</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 2</span>
                <span class="v3-step-title">Select Datasets</span>
            </div>
        </div>
        <div class="v3-step-divider"></div>
        <div class="v3-step-item" data-step="3">
            <div class="v3-step-number">3</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 3</span>
                <span class="v3-step-title">Sync Options</span>
            </div>
        </div>
        <div class="v3-step-divider"></div>
        <div class="v3-step-item" data-step="4">
            <div class="v3-step-number">4</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 4</span>
                <span class="v3-step-title">Run Migration</span>
            </div>
        </div>
    </div>

    <!-- Two-Column Layout -->
    <div class="v3-form-split-layout">
        <!-- Main Column -->
        <div class="v3-form-main-col">
            <div class="v3-form-container">

                <!-- =========================================================
                     Step 1: Workspace Status & Overview
                     ========================================================= -->
                <div class="migration-step-panel active" id="migrationStep1">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-left">
                                <div class="v3-card-header-icon indigo">
                                    <i class="fi fi-rr-database"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Workspace Status &amp; Master Catalog</h3>
                                    <p>Real-time comparison between currently installed workspace records and available master templates.</p>
                                </div>
                            </div>
                        </div>

                        <div class="v3-card-body migration-overview-body">
                            <div class="migration-overview-grid">
                                <!-- Stat 1: Tour Packages -->
                                <div class="migration-stat-card">
                                    <div class="migration-stat-top">
                                        <div class="migration-stat-icon-mini indigo">
                                            <i class="fi fi-rr-plane-alt"></i>
                                        </div>
                                        <span class="migration-stat-status-chip ready" id="statBadgePackages">
                                            <i class="fi fi-rr-check"></i> Ready
                                        </span>
                                    </div>
                                    <div class="migration-stat-main">
                                        <span class="migration-stat-label">Tour Packages</span>
                                        <div class="migration-stat-metric-row">
                                            <span class="migration-stat-installed" id="statInstalledPackages">-</span>
                                            <span class="migration-stat-installed-sub">in workspace</span>
                                        </div>
                                    </div>
                                    <div class="migration-stat-footer">
                                        <span class="migration-stat-footer-text">
                                            <i class="fi fi-rr-layers"></i> <strong class="migration-stat-total" id="statTotalPackages">155 Master</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Stat 2: Destinations -->
                                <div class="migration-stat-card">
                                    <div class="migration-stat-top">
                                        <div class="migration-stat-icon-mini teal">
                                            <i class="fi fi-rr-map-marker"></i>
                                        </div>
                                        <span class="migration-stat-status-chip ready" id="statBadgeDestinations">
                                            <i class="fi fi-rr-check"></i> Ready
                                        </span>
                                    </div>
                                    <div class="migration-stat-main">
                                        <span class="migration-stat-label">Destinations</span>
                                        <div class="migration-stat-metric-row">
                                            <span class="migration-stat-installed" id="statInstalledDestinations">-</span>
                                            <span class="migration-stat-installed-sub">in workspace</span>
                                        </div>
                                    </div>
                                    <div class="migration-stat-footer">
                                        <span class="migration-stat-footer-text">
                                            <i class="fi fi-rr-layers"></i> <strong class="migration-stat-total" id="statTotalDestinations">17 Master</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Stat 3: Fleet Vehicles -->
                                <div class="migration-stat-card">
                                    <div class="migration-stat-top">
                                        <div class="migration-stat-icon-mini purple">
                                            <i class="fi fi-rr-car-alt"></i>
                                        </div>
                                        <span class="migration-stat-status-chip ready" id="statBadgeVehicles">
                                            <i class="fi fi-rr-check"></i> Ready
                                        </span>
                                    </div>
                                    <div class="migration-stat-main">
                                        <span class="migration-stat-label">Fleet Vehicles</span>
                                        <div class="migration-stat-metric-row">
                                            <span class="migration-stat-installed" id="statInstalledVehicles">-</span>
                                            <span class="migration-stat-installed-sub">in workspace</span>
                                        </div>
                                    </div>
                                    <div class="migration-stat-footer">
                                        <span class="migration-stat-footer-text">
                                            <i class="fi fi-rr-layers"></i> <strong class="migration-stat-total" id="statTotalVehicles">11 Master</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Stat 4: Hotels & Rooms -->
                                <div class="migration-stat-card">
                                    <div class="migration-stat-top">
                                        <div class="migration-stat-icon-mini amber">
                                            <i class="fi fi-rr-building"></i>
                                        </div>
                                        <span class="migration-stat-status-chip ready" id="statBadgeHotels">
                                            <i class="fi fi-rr-check"></i> Ready
                                        </span>
                                    </div>
                                    <div class="migration-stat-main">
                                        <span class="migration-stat-label">Hotels &amp; Tariffs</span>
                                        <div class="migration-stat-metric-row">
                                            <span class="migration-stat-installed" id="statInstalledHotels">-</span>
                                            <span class="migration-stat-installed-sub">in workspace</span>
                                        </div>
                                    </div>
                                    <div class="migration-stat-footer">
                                        <span class="migration-stat-footer-text">
                                            <i class="fi fi-rr-layers"></i> <strong class="migration-stat-total" id="statTotalHotels">315 Master</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Stat 5: Transporters -->
                                <div class="migration-stat-card">
                                    <div class="migration-stat-top">
                                        <div class="migration-stat-icon-mini rose">
                                            <i class="fi fi-rr-shipping-fast"></i>
                                        </div>
                                        <span class="migration-stat-status-chip ready" id="statBadgeTransporters">
                                            <i class="fi fi-rr-check"></i> Ready
                                        </span>
                                    </div>
                                    <div class="migration-stat-main">
                                        <span class="migration-stat-label">Transporters</span>
                                        <div class="migration-stat-metric-row">
                                            <span class="migration-stat-installed" id="statInstalledTransporters">-</span>
                                            <span class="migration-stat-installed-sub">in workspace</span>
                                        </div>
                                    </div>
                                    <div class="migration-stat-footer">
                                        <span class="migration-stat-footer-text">
                                            <i class="fi fi-rr-layers"></i> <strong class="migration-stat-total" id="statTotalTransporters">1 Master</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="migration-wizard-nav">
                            <div></div>
                            <button type="button" class="v3-btn-primary btn-wizard-next" data-next="2">
                                <span>Continue to Select Datasets</span> <i class="fi fi-rr-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- =========================================================
                     Step 2: Select Master Datasets
                     ========================================================= -->
                <div class="migration-step-panel" id="migrationStep2">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-left">
                                <div class="v3-card-header-icon emerald">
                                    <i class="fi fi-rr-apps-add"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Select Master Datasets to Import</h3>
                                    <p>Choose which default catalogues to copy into your dashboard workspace.</p>
                                </div>
                            </div>
                            <div class="v3-card-header-actions">
                                <button type="button" class="btn-v3-header-action primary" id="btnSelectAllDatasets">
                                    <i class="fi fi-rr-check-double"></i> <span>Select All</span>
                                </button>
                                <button type="button" class="btn-v3-header-action secondary" id="btnDeselectAllDatasets">
                                    <i class="fi fi-rr-cross-small"></i> <span>Deselect All</span>
                                </button>
                            </div>
                        </div>

                        <div class="v3-card-body">
                            <!-- Datasets Cards Grid -->
                            <div class="v3-dataset-grid">
                                <!-- Dataset 1: Tour Packages -->
                                <div class="v3-dataset-card is-selected" data-type="tour_packages">
                                    <div class="v3-dataset-icon indigo">
                                        <i class="fi fi-rr-plane-alt"></i>
                                    </div>
                                    <div class="v3-dataset-body">
                                        <div class="v3-dataset-title-row">
                                            <div class="v3-dataset-title-group">
                                                <h4 class="v3-dataset-title">Tour Packages</h4>
                                                <span class="v3-dataset-badge" id="badgeCountPackages">155 Packages</span>
                                            </div>
                                            <label class="v3-switch-label" for="chkDatasetPackages" title="Toggle Tour Packages">
                                                <input type="checkbox" id="chkDatasetPackages" class="master-dataset-checkbox" value="tour_packages" checked>
                                                <span class="v3-switch-slider"></span>
                                            </label>
                                        </div>
                                        <p class="v3-dataset-desc">
                                            Complete holiday packages with day-by-day sightseeing, inclusions, exclusions, and multi-tier pricing slabs.
                                        </p>
                                    </div>
                                </div>

                                <!-- Dataset 2: Destinations -->
                                <div class="v3-dataset-card is-selected" data-type="destinations">
                                    <div class="v3-dataset-icon teal">
                                        <i class="fi fi-rr-map-marker"></i>
                                    </div>
                                    <div class="v3-dataset-body">
                                        <div class="v3-dataset-title-row">
                                            <div class="v3-dataset-title-group">
                                                <h4 class="v3-dataset-title">Travel Destinations</h4>
                                                <span class="v3-dataset-badge" id="badgeCountDestinations">17 Hubs</span>
                                            </div>
                                            <label class="v3-switch-label" for="chkDatasetDestinations" title="Toggle Travel Destinations">
                                                <input type="checkbox" id="chkDatasetDestinations" class="master-dataset-checkbox" value="destinations" checked>
                                                <span class="v3-switch-slider"></span>
                                            </label>
                                        </div>
                                        <p class="v3-dataset-desc">
                                            Popular tourist hubs across Himachal, Kashmir, Uttarakhand, Rajasthan, Kerala, Goa, Andaman, and more.
                                        </p>
                                    </div>
                                </div>

                                <!-- Dataset 3: Vehicles -->
                                <div class="v3-dataset-card is-selected" data-type="vehicles">
                                    <div class="v3-dataset-icon purple">
                                        <i class="fi fi-rr-car-alt"></i>
                                    </div>
                                    <div class="v3-dataset-body">
                                        <div class="v3-dataset-title-row">
                                            <div class="v3-dataset-title-group">
                                                <h4 class="v3-dataset-title">Fleet Vehicles</h4>
                                                <span class="v3-dataset-badge" id="badgeCountVehicles">11 Models</span>
                                            </div>
                                            <label class="v3-switch-label" for="chkDatasetVehicles" title="Toggle Fleet Vehicles">
                                                <input type="checkbox" id="chkDatasetVehicles" class="master-dataset-checkbox" value="vehicles" checked>
                                                <span class="v3-switch-slider"></span>
                                            </label>
                                        </div>
                                        <p class="v3-dataset-desc">
                                            Sedans, SUVs, and luxury coaches (Dzire, Etios, Ertiga, Innova Crysta, Tempo Traveller, Urbania).
                                        </p>
                                    </div>
                                </div>

                                <!-- Dataset 4: Hotels -->
                                <div class="v3-dataset-card is-selected" data-type="hotels">
                                    <div class="v3-dataset-icon amber">
                                        <i class="fi fi-rr-building"></i>
                                    </div>
                                    <div class="v3-dataset-body">
                                        <div class="v3-dataset-title-row">
                                            <div class="v3-dataset-title-group">
                                                <h4 class="v3-dataset-title">Hotels &amp; Accommodations</h4>
                                                <span class="v3-dataset-badge" id="badgeCountHotels">315 Hotels</span>
                                            </div>
                                            <label class="v3-switch-label" for="chkDatasetHotels" title="Toggle Hotels & Accommodations">
                                                <input type="checkbox" id="chkDatasetHotels" class="master-dataset-checkbox" value="hotels" checked>
                                                <span class="v3-switch-slider"></span>
                                            </label>
                                        </div>
                                        <p class="v3-dataset-desc">
                                            Classified budget to luxury properties with 276 room tariff tiers, meal plans, and destination locations.
                                        </p>
                                    </div>
                                </div>

                                <!-- Dataset 5: Transporters -->
                                <div class="v3-dataset-card is-selected" data-type="transporters">
                                    <div class="v3-dataset-icon rose">
                                        <i class="fi fi-rr-shipping-fast"></i>
                                    </div>
                                    <div class="v3-dataset-body">
                                        <div class="v3-dataset-title-row">
                                            <div class="v3-dataset-title-group">
                                                <h4 class="v3-dataset-title">Transporters &amp; Operators</h4>
                                                <span class="v3-dataset-badge" id="badgeCountTransporters">1 Operator</span>
                                            </div>
                                            <label class="v3-switch-label" for="chkDatasetTransporters" title="Toggle Transporters & Operators">
                                                <input type="checkbox" id="chkDatasetTransporters" class="master-dataset-checkbox" value="transporters" checked>
                                                <span class="v3-switch-slider"></span>
                                            </label>
                                        </div>
                                        <p class="v3-dataset-desc">
                                            Verified taxi union and fleet operator profiles for seamless quote generation and dispatch.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="migration-wizard-nav">
                            <button type="button" class="v3-btn-secondary btn-wizard-prev" data-prev="1">
                                <i class="fi fi-rr-arrow-left"></i> <span>Back: Workspace Status</span>
                            </button>
                            <button type="button" class="v3-btn-primary btn-wizard-next" data-next="3">
                                <span>Continue to Sync Options</span> <i class="fi fi-rr-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- =========================================================
                     Step 3: Migration Sync Strategy & Mode
                     ========================================================= -->
                <div class="migration-step-panel" id="migrationStep3">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-left">
                                <div class="v3-card-header-icon sky">
                                    <i class="fi fi-rr-settings-sliders"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Migration Strategy &amp; Safety Mode</h3>
                                    <p>Select how the migration engine handles records already existing in your workspace.</p>
                                </div>
                            </div>
                        </div>

                        <div class="v3-card-body">
                            <div class="v3-sync-modes-grid">
                                <!-- Mode 1: Safe Sync (Recommended) -->
                                <div class="v3-sync-mode-card is-selected" data-mode="safe">
                                    <div class="v3-sync-mode-header">
                                        <div class="v3-sync-mode-icon indigo">
                                            <i class="fi fi-rr-shield-check"></i>
                                        </div>
                                        <div class="v3-sync-mode-radio">
                                            <input type="radio" name="migration_mode" id="modeSafe" value="safe" checked>
                                        </div>
                                    </div>
                                    <h4 class="v3-sync-mode-title">Safe Sync (Skip Existing)</h4>
                                    <p class="v3-sync-mode-desc">Only copies master templates that do not already exist in your workspace. Preserves all customized prices, tariffs, and itineraries you have previously created.</p>
                                    <span class="v3-sync-mode-tag recommended"><i class="fi fi-rr-star"></i> Recommended for Active Sites</span>
                                </div>

                                <!-- Mode 2: Full Sync & Refresh -->
                                <div class="v3-sync-mode-card" data-mode="overwrite">
                                    <div class="v3-sync-mode-header">
                                        <div class="v3-sync-mode-icon amber">
                                            <i class="fi fi-rr-refresh"></i>
                                        </div>
                                        <div class="v3-sync-mode-radio">
                                            <input type="radio" name="migration_mode" id="modeOverwrite" value="overwrite">
                                        </div>
                                    </div>
                                    <h4 class="v3-sync-mode-title">Full Sync &amp; Overwrite</h4>
                                    <p class="v3-sync-mode-desc">Refreshes existing matching records with the latest master catalog data. Helpful if you want to reset your workspace templates to fresh default values.</p>
                                    <span class="v3-sync-mode-tag warning"><i class="fi fi-rr-exclamation"></i> Updates Existing Records</span>
                                </div>
                            </div>
                        </div>

                        <div class="migration-wizard-nav">
                            <button type="button" class="v3-btn-secondary btn-wizard-prev" data-prev="2">
                                <i class="fi fi-rr-arrow-left"></i> <span>Back: Select Datasets</span>
                            </button>
                            <button type="button" class="v3-btn-primary btn-wizard-next" data-next="4">
                                <span>Review &amp; Run Migration</span> <i class="fi fi-rr-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- =========================================================
                     Step 4: Confirm, Run & Verification
                     ========================================================= -->
                <div class="migration-step-panel" id="migrationStep4">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-left">
                                <div class="v3-card-header-icon indigo">
                                    <i class="fi fi-rr-cloud-download"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Confirm &amp; Execute Migration</h3>
                                    <p>Review your selected configuration and start the default data import process.</p>
                                </div>
                            </div>
                        </div>

                        <div class="v3-card-body">
                            <!-- Pre-flight Summary Card -->
                            <div class="migration-preflight-card" id="migrationPreflightCard">
                                <div class="migration-preflight-header">
                                    <div class="migration-preflight-header-left">
                                        <h4><i class="fi fi-rr-list-check"></i> Ready to Import</h4>
                                        <p class="migration-preflight-subtitle">Review selected master catalogues and sync strategy before executing migration.</p>
                                    </div>
                                    <span class="v3-preflight-badge" id="preflightSummaryBadge">
                                        <i class="fi fi-rr-layers"></i> 5 Datasets Selected
                                    </span>
                                </div>
                                <div class="migration-preflight-items" id="preflightItemsList">
                                    <!-- Populated dynamically via JS -->
                                </div>
                                <div class="migration-preflight-mode-info mode-safe" id="preflightModeInfo">
                                    <div class="migration-preflight-mode-icon safe">
                                        <i class="fi fi-rr-shield-check"></i>
                                    </div>
                                    <div class="migration-preflight-mode-text">
                                        <div class="migration-preflight-mode-title">Strategy: Safe Sync (Duplicate Protection Active)</div>
                                        <p class="migration-preflight-mode-desc">Existing customized packages, hotels, and tariffs in your workspace are preserved. Only new or missing master records will be imported.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Progress Card -->
                            <div class="migration-progress-card" id="migrationProgressCard">
                                <div class="migration-progress-header">
                                    <div class="migration-progress-title">
                                        <i class="fi fi-rr-spinner fi-spin"></i>
                                        <span id="migrationProgressTitle">Importing Master Data...</span>
                                    </div>
                                    <span class="migration-progress-pct" id="migrationProgressPct">0%</span>
                                </div>
                                <div class="migration-progress-bar-wrap">
                                    <div class="migration-progress-bar-fill" id="migrationProgressBar"></div>
                                </div>
                                <p class="migration-progress-status-msg" id="migrationProgressMsg">Initializing workspace data migration...</p>
                            </div>

                            <!-- Result Summary Banner -->
                            <div class="migration-result-banner" id="migrationResultBanner">
                                <div class="migration-result-icon">
                                    <i class="fi fi-rr-check"></i>
                                </div>
                                <div class="migration-result-content">
                                    <h4 id="migrationResultTitle">Default Data Migration Completed Successfully!</h4>
                                    <p id="migrationResultText">Your workspace is synchronized with the master catalogues.</p>
                                    <div class="migration-result-badges" id="migrationResultBadges"></div>
                                    <div class="migration-result-actions">
                                        <a href="/dashboard/packages/domestic/list/" class="v3-btn-primary btn-sm">
                                            <i class="fi fi-rr-plane-alt"></i> View Packages
                                        </a>
                                        <a href="/dashboard/vendors/property/list/" class="v3-btn-secondary btn-sm">
                                            <i class="fi fi-rr-building"></i> View Hotels
                                        </a>
                                        <button type="button" class="v3-btn-secondary btn-sm" id="btnRestartWizard">
                                            <i class="fi fi-rr-refresh"></i> Run Another Migration
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="migration-wizard-nav" id="step4ActionsBar">
                            <button type="button" class="v3-btn-secondary btn-wizard-prev" data-prev="3" id="btnStep4Back">
                                <i class="fi fi-rr-arrow-left"></i> <span>Back: Sync Options</span>
                            </button>
                            <button type="button" id="btnStartMigration" class="v3-btn-primary">
                                <i class="fi fi-rr-cloud-download"></i> <span id="btnStartMigrationText">Start Default Data Migration</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Live Wizard Summary Card -->
                <div class="v3-card-section v3-assistant-card">
                    <div class="v3-assistant-header">
                        <div class="v3-assistant-title-group">
                            <span class="v3-assistant-icon-badge">
                                <i class="fi fi-rr-workflow"></i>
                            </span>
                            <div>
                                <h4 class="v3-assistant-title">Migration Assistant</h4>
                                <p class="v3-assistant-subtitle">Automated Catalog Sync</p>
                            </div>
                        </div>
                        <span class="v3-assistant-live-pill">
                            <span class="v3-pulse-dot"></span> Live
                        </span>
                    </div>

                    <!-- Mini Visual Step Bar -->
                    <div class="v3-assistant-stepper">
                        <div class="v3-assistant-stepper-meta">
                            <span class="v3-stepper-stage-label" id="sidebarStepBadge">Step 1 of 4</span>
                            <span class="v3-stepper-pct-label" id="sidebarStepPct">25%</span>
                        </div>
                        <div class="v3-assistant-progress-track">
                            <div class="v3-assistant-progress-bar" id="sidebarStepProgressBar"></div>
                        </div>
                    </div>

                    <!-- Parameter Rows -->
                    <div class="v3-assistant-metrics">
                        <div class="v3-assistant-metric-item">
                            <div class="v3-metric-label-group">
                                <span class="v3-metric-icon"><i class="fi fi-rr-flag"></i></span>
                                <span class="v3-metric-label">Current Stage</span>
                            </div>
                            <span class="v3-assistant-badge stage-badge" id="sidebarStepText">Workspace Status</span>
                        </div>

                        <div class="v3-assistant-metric-item">
                            <div class="v3-metric-label-group">
                                <span class="v3-metric-icon"><i class="fi fi-rr-database"></i></span>
                                <span class="v3-metric-label">Datasets</span>
                            </div>
                            <span class="v3-assistant-badge dataset-badge" id="sidebarDatasetsText">5 of 5 Selected</span>
                        </div>

                        <div class="v3-assistant-metric-item">
                            <div class="v3-metric-label-group">
                                <span class="v3-metric-icon"><i class="fi fi-rr-refresh"></i></span>
                                <span class="v3-metric-label">Strategy</span>
                            </div>
                            <span class="v3-assistant-badge mode-badge" id="sidebarModeText">Safe Sync</span>
                        </div>
                    </div>

                    <!-- Dynamic Tip Callout -->
                    <div class="v3-assistant-tip-box">
                        <i class="fi fi-rr-info"></i>
                        <span id="sidebarStepTip">Review current counts to compare active workspace items against master catalog records.</span>
                    </div>
                </div>

                <!-- Info Card: Workspace Safety Guarantees -->
                <div class="v3-card-section v3-safety-card">
                    <div class="v3-safety-header">
                        <div class="v3-safety-title-group">
                            <span class="v3-safety-icon-badge">
                                <i class="fi fi-rr-shield-check"></i>
                            </span>
                            <div>
                                <h4 class="v3-safety-title">Workspace Safety</h4>
                                <p class="v3-safety-subtitle">Non-destructive guarantees</p>
                            </div>
                        </div>
                        <span class="v3-safety-guaranteed-pill">
                            <i class="fi fi-rr-badge-check"></i> Verified
                        </span>
                    </div>

                    <div class="v3-safety-list">
                        <div class="v3-safety-item">
                            <span class="v3-safety-item-icon blue">
                                <i class="fi fi-rr-box-alt"></i>
                            </span>
                            <div class="v3-safety-item-content">
                                <h6>Workspace Scoped</h6>
                                <p>Master data is safely cloned into your active website without altering master templates.</p>
                            </div>
                        </div>

                        <div class="v3-safety-item">
                            <span class="v3-safety-item-icon emerald">
                                <i class="fi fi-rr-check-double"></i>
                            </span>
                            <div class="v3-safety-item-content">
                                <h6>Conflict Protection</h6>
                                <p>Duplicate detection ensures previously imported packages or hotels are not overwritten.</p>
                            </div>
                        </div>

                        <div class="v3-safety-item">
                            <span class="v3-safety-item-icon amber">
                                <i class="fi fi-rr-bolt"></i>
                            </span>
                            <div class="v3-safety-item-content">
                                <h6>Instant Readiness</h6>
                                <p>Imported items are immediately available for quotation generation and client itineraries.</p>
                            </div>
                        </div>

                        <div class="v3-safety-item">
                            <span class="v3-safety-item-icon purple">
                                <i class="fi fi-rr-edit"></i>
                            </span>
                            <div class="v3-safety-item-content">
                                <h6>Fully Editable</h6>
                                <p>After copying, you can freely edit prices, itineraries, vehicle rates, and descriptions anytime.</p>
                            </div>
                        </div>
                    </div>

                    <div class="v3-safety-footer-badge">
                        <i class="fi fi-rr-lock"></i>
                        <span>Zero Downtime • Isolated Tenant Execution</span>
                    </div>
                </div>

                <!-- Post-Migration Navigation Card -->
                <div class="v3-card-section v3-checklist-card">
                    <h4 class="v3-checklist-title">
                        <i class="fi fi-rr-navigation"></i> Quick Access After Migration
                    </h4>
                    <ul class="migration-sidebar-nav-list">
                        <li class="migration-sidebar-nav-item">
                            <a href="/dashboard/packages/domestic/list/">
                                <span><i class="fi fi-rr-plane-alt"></i> Tour Packages</span>
                                <i class="fi fi-rr-arrow-small-right"></i>
                            </a>
                        </li>
                        <li class="migration-sidebar-nav-item">
                            <a href="/dashboard/vendors/property/list/">
                                <span><i class="fi fi-rr-building"></i> Hotels &amp; Properties</span>
                                <i class="fi fi-rr-arrow-small-right"></i>
                            </a>
                        </li>
                        <li class="migration-sidebar-nav-item">
                            <a href="/dashboard/vendors/transporter/vehicle/">
                                <span><i class="fi fi-rr-car-alt"></i> Fleet Vehicles</span>
                                <i class="fi fi-rr-arrow-small-right"></i>
                            </a>
                        </li>
                        <li class="migration-sidebar-nav-item">
                            <a href="/dashboard/vendors/transporter/list/">
                                <span><i class="fi fi-rr-shipping-fast"></i> Transporters</span>
                                <i class="fi fi-rr-arrow-small-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
