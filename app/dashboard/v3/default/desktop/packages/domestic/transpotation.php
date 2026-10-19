<?php

$rawId = $_GET['id'] ?? ($_GET['package_id'] ?? 0);
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;
}
$encodedId = base64_encode((string)$packageId);

?>

<div class="package-page-wrapper package-transport-wrapper" id="packageTransportApp" data-package-id="<?php echo $packageId; ?>" data-encoded-id="<?php echo $encodedId; ?>" data-category="domestic">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-car-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="pkgHeaderTitle">Tour Package</h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard" id="pkgMetaTheme">Standard</span>
                    <span class="pkg-duration-badge" id="pkgMetaDuration">
                        <i class="fi fi-rr-moon pkg-duration-moon"></i> 0N / <i class="fi fi-rr-sun pkg-duration-sun"></i> 1D
                    </span>
                    <span class="pkg-duration-badge pkg-location-badge" id="pkgMetaLocation">
                        <i class="fi fi-rr-marker pkg-duration-marker"></i> Domestic
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="#" target="_blank" class="btn-v3-action" id="btnViewLivePackage" data-tooltip="View Live Package" title="View Live Package">
                <i class="fi fi-rr-eye"></i> View Package
            </a>
            <a href="/dashboard/packages/domestic/list/" class="btn-v3-action" data-tooltip="Back to Domestic Packages List" title="Back to Domestic Packages List">
                <i class="fi fi-rr-arrow-left"></i> Packages
            </a>
            <a href="/dashboard/packages/domestic/edit/?id=<?php echo $encodedId; ?>" class="btn-v3-action" data-tooltip="Edit Package Details" title="Edit Package Details">
                <i class="fi fi-rr-edit"></i> Edit Package
            </a>
        </div>
    </div>

    <!-- Main Content Box -->
    <div class="box-1 mt-16">
        <div class="widget box-1 no-padding">
            <div class="main-box">
                <section class="dashboard-gallery-container">

                    <!-- Vehicle Configuration Section -->
                    <div class="transport-vehicle-config-card">
                        <div class="transport-config-header">
                            <div class="transport-config-title">
                                <i class="fi fi-rr-steering-wheel"></i>
                                <div>
                                    <h4>Package Fleet & Vehicle Configuration</h4>
                                    <p>Configure the default vehicles included in this tour itinerary for outstation transfers and local sightseeing.</p>
                                </div>
                            </div>
                            <button type="button" class="btn-v3-action primary" id="btnSavePackageVehicles">
                                <i class="fi fi-rr-disk"></i> Save Vehicle Options
                            </button>
                        </div>
                        <div class="transport-config-grid">
                            <div class="transport-field-card">
                                <label for="selectOutstationVehicle"><i class="fi fi-rr-road text-indigo"></i> Outstation Transfer Vehicle:</label>
                                <select id="selectOutstationVehicle" class="selectJS2 form-control">
                                    <option value="">-- Select Outstation Vehicle --</option>
                                </select>
                            </div>
                            <div class="transport-field-card">
                                <label for="selectLocalVehicle"><i class="fi fi-rr-marker pkg-duration-marker"></i> Local Sightseeing Vehicle:</label>
                                <select id="selectLocalVehicle" class="selectJS2 form-control">
                                    <option value="">-- Select Local Sightseeing Vehicle --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Header Controls & Filters -->
                    <div class="gallery-sub-header mt-18">
                        <div class="gallery-sub-header-top">
                            <div class="gallery-stats-pills">
                                <div class="theme-stat-pill is-blue">
                                    <i class="fi fi-rr-truck-side"></i> <span><b id="statTotalTransporters">0</b> Available Transporters</span>
                                </div>
                                <div class="theme-stat-pill is-green" id="statAssignedTransportersPill">
                                    <i class="fi fi-rr-check-circle"></i> <span><b id="statAssignedTransporters">0</b> Assigned</span>
                                </div>
                                <div class="theme-stat-pill is-purple" id="statNearbyTransportersPill">
                                    <i class="fi fi-rr-marker"></i> <span><b id="statNearbyTransporters">0</b> In Destination Region</span>
                                </div>
                            </div>

                            <div class="gallery-filter-tabs">
                                <button type="button" class="theme-filter-btn active" data-filter="all">
                                    All <span class="filter-badge" id="filterCountAll">0</span>
                                </button>
                                <button type="button" class="theme-filter-btn" data-filter="assigned">
                                    Assigned <span class="filter-badge" id="filterCountAssigned">0</span>
                                </button>
                                <button type="button" class="theme-filter-btn" data-filter="nearby">
                                    Destination Area <span class="filter-badge" id="filterCountNearby">0</span>
                                </button>
                                <button type="button" class="theme-filter-btn" data-filter="unassigned">
                                    Unassigned <span class="filter-badge" id="filterCountUnassigned">0</span>
                                </button>
                            </div>
                        </div>

                        <!-- Transporters Search & Quick Add -->
                        <div class="transport-search-row">
                            <div class="transport-search-wrap">
                                <i class="fi fi-rr-search"></i>
                                <input type="text" id="transportSearchInput" placeholder="Search transporters by company name, city, phone or service state..." class="transport-search-input" />
                            </div>
                            <a href="/dashboard/vendors/transporter/add/" target="_blank" class="btn-v3-action" data-tooltip="Add new transporter to system directory">
                                <i class="fi fi-rr-plus"></i> Add Transporter
                            </a>
                        </div>
                    </div>

                    <!-- Transporters Cards Grid Layout -->
                    <div class="dash-hotel-grid" id="transportGrid">
                        <div class="hotel-loading-state">
                            <div class="v3-spinner"></div>
                            <p>Loading transporters & vehicles...</p>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </div>
</div>
