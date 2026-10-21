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

    <!-- Two-Column Layout -->
    <div class="v3-form-split-layout">
        <!-- Main Column -->
        <div class="v3-form-main-col">
            <div class="v3-form-container">

                <!-- Section 1: Workspace Status & Overview -->
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
                </div>

                <!-- Section 2: Dataset Selection -->
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
                                <i class="fi fi-rr-check"></i> <span>Select All</span>
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
                </div>

                <!-- Section 3: Migration Safety & Deduplication -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-left">
                            <div class="v3-card-header-icon sky">
                                <i class="fi fi-rr-shield-check"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Workspace Safety &amp; Deduplication</h3>
                                <p>Control workspace overwrite and duplicate protection rules before import.</p>
                            </div>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-feature-toggle-row">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-shield-check"></i>
                                </div>
                                <div>
                                    <h4 class="v3-feature-toggle-title">Skip duplicate items in workspace</h4>
                                    <p class="v3-feature-toggle-desc">Prevents re-importing identical packages, hotels, and vehicles if you already have them in your dashboard.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="chkSkipExisting">
                                <input type="checkbox" id="chkSkipExisting" value="1" checked>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Live Progress Card -->
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

                <!-- Section 5: Result Summary Banner -->
                <div class="migration-result-banner" id="migrationResultBanner">
                    <div class="migration-result-icon">
                        <i class="fi fi-rr-check"></i>
                    </div>
                    <div class="migration-result-content">
                        <h4>Default Data Migration Completed Successfully!</h4>
                        <p id="migrationResultText">Your workspace has been seeded with master catalogues. You can now customize packages, adjust tariffs, and build itineraries immediately.</p>
                        <div class="migration-result-badges" id="migrationResultBadges"></div>
                    </div>
                </div>

                <!-- Section 6: Standalone Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="button" id="btnStartMigration" class="v3-btn-primary">
                        <i class="fi fi-rr-cloud-download"></i> <span id="btnStartMigrationText">Start Default Data Migration</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Info Card -->
                <div class="v3-card-section v3-checklist-card">
                    <h4 class="v3-checklist-title">
                        <i class="fi fi-rr-info"></i> How Migration Works
                    </h4>
                    <ul class="v3-checklist-list">
                        <li><strong>Workspace Scoped:</strong> Master data is safely cloned into your active website without altering master templates.</li>
                        <li><strong>Conflict-Free:</strong> Duplicate detection ensures previously imported packages or hotels are not overwritten.</li>
                        <li><strong>Instant Readiness:</strong> Imported items are immediately ready for quotation generation and client itineraries.</li>
                        <li><strong>Fully Editable:</strong> After copying, you can edit prices, itineraries, and descriptions at any time.</li>
                    </ul>
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
