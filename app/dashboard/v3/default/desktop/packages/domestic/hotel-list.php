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

<div class="package-page-wrapper package-hotel-list-wrapper" id="packageHotelListApp" data-package-id="<?php echo $packageId; ?>" data-encoded-id="<?php echo $encodedId; ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-building"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="pkgHeaderTitle">Tour Package — Included Hotels</h2>
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

                    <!-- Sub Header Controls & Filters -->
                    <div class="gallery-sub-header">
                        <div class="gallery-sub-header-top">
                            <div class="gallery-stats-pills">
                                <div class="theme-stat-pill is-blue">
                                    <i class="fi fi-rr-building"></i> <span><b id="statTotalHotels">0</b> Nearby Hotels</span>
                                </div>
                                <div class="theme-stat-pill is-purple" id="statMentionedCitiesWrap">
                                    <i class="fi fi-rr-marker"></i> <span><b id="statMentionedCitiesCount">0</b> Mentioned Cities</span>
                                </div>
                                <div class="theme-stat-pill is-green">
                                    <i class="fi fi-rr-moon-stars"></i> <span><b id="statNightsCovered">0 / 0</b> Nights Covered</span>
                                </div>
                                <div class="theme-stat-pill is-green" id="statNightsMissingPill">
                                    <i class="fi fi-rr-check-circle"></i> <span>All Nights Assigned</span>
                                </div>
                            </div>

                            <div class="gallery-filter-tabs">
                                <button type="button" class="theme-filter-btn active" data-filter="all">
                                    All <span class="filter-badge" id="filterCountAll">0</span>
                                </button>
                                <button type="button" class="theme-filter-btn" data-filter="assigned">
                                    Assigned <span class="filter-badge" id="filterCountAssigned">0</span>
                                </button>
                                <button type="button" class="theme-filter-btn" data-filter="unassigned">
                                    Unassigned <span class="filter-badge" id="filterCountUnassigned">0</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mentioned Cities Filter Chips Bar -->
                        <div class="city-filter-bar" id="cityFilterBar">
                            <!-- Injected dynamically via V3PackageHotelList -->
                        </div>
                    </div>

                    <!-- Hotel Cards Grid Layout -->
                    <div class="dashboard-gallery-grid" id="hotelGrid">
                        <!-- Loaded dynamically via V3PackageHotelList from /api/v3/packages/hotels.php -->
                    </div>

                    <!-- Floating Shortcut Action to Add New Hotel -->
                    <div class="shortcut">
                        <a href="/dashboard/vendors/property/add/" title="Add New Hotel Vendor"><i class="fi fi-rr-plus"></i></a>
                    </div>

                </section>
            </div>
        </div>
    </div>

    <!-- Lightbox Image Preview Modal -->
    <div class="theme-lightbox-modal" id="lightboxModal">
        <div class="theme-lightbox-content">
            <div class="theme-lightbox-header">
                <h3 id="lightboxTitle">Hotel Preview</h3>
                <button type="button" class="theme-lightbox-close" id="lightboxClose">&times;</button>
            </div>
            <div class="theme-lightbox-body">
                <img id="lightboxImg" src="" alt="Hotel Preview">
            </div>
            <div class="theme-lightbox-footer">
                <span class="theme-stat-pill is-green" id="lightboxNightBadge"><i class="fi fi-rr-building"></i> Hotel Property</span>
            </div>
        </div>
    </div>
</div>