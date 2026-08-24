<?php

$rawId = $_GET['id'] ?? 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $id = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $id = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;
}
$encodedId = base64_encode($id);

?>

<div class="package-page-wrapper package-gallery-wrapper" id="packageGalleryApp" data-package-id="<?php echo $id; ?>" data-encoded-id="<?php echo $encodedId; ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi-rr-gallery"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="pkgHeaderTitle">Tour Package — Gallery</h2>
                <div class="pkg-meta-row" style="margin-top: 4px;">
                    <span class="pkg-theme-pill pkg-theme-standard" id="pkgMetaTheme">Standard</span>
                    <span class="pkg-duration-badge" id="pkgMetaDuration">
                        <i class="fi-rr-moon" style="color: #6366f1;"></i> 0N / <i class="fi-rr-sun" style="color: #f59e0b;"></i> 1D
                    </span>
                    <span class="pkg-duration-badge" id="pkgMetaLocation" style="background: #f8fafc; border-color: #cbd5e1;">
                        <i class="fi-rr-marker" style="color: #0284c7;"></i> Domestic
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="#" target="_blank" class="btn-gallery-action" id="btnViewLivePackage" style="background: #f8fafc; color: #0284c7; border: 1px solid #cbd5e1; display: none;" data-tooltip="View Live Package Itinerary" title="View Live Package Itinerary">
                <i class="fi fi-rr-eye"></i> View Package
            </a>
            <a href="/dashboard/packages/domestic/list/" class="btn-gallery-action" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;" data-tooltip="Back to Domestic Packages List">
                <i class="fi fi-rr-arrow-left"></i> Packages
            </a>
            <a href="/dashboard/packages/domestic/edit/?id=<?php echo $encodedId; ?>" class="btn-gallery-action" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;" data-tooltip="Edit Package Details">
                <i class="fi fi-rr-edit"></i> Edit Details
            </a>
        </div>
    </div>

    <!-- Quick Metric KPI Row -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi-rr-picture"></i></div>
            <div class="kpi-meta">
                <p>Total Photos</p>
                <h3 id="statTotalImages">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi-rr-calendar-check"></i></div>
            <div class="kpi-meta">
                <p>Days Covered</p>
                <h3 id="statDaysCovered">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi-rr-exclamation"></i></div>
            <div class="kpi-meta">
                <p>Days Missing Photo</p>
                <h3 id="statDaysMissing">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi-rr-star"></i></div>
            <div class="kpi-meta">
                <p>Featured Cover</p>
                <h3 id="statFeaturedCover">-</h3>
            </div>
        </div>
    </div>

    <!-- Sub Header Single Line Toolbar -->
    <div class="gallery-sub-header">
        <div class="gallery-filter-tabs">
            <button type="button" class="theme-filter-btn active" data-filter="all" data-tooltip="Show all gallery photos">
                <i class="fi fi-rr-apps"></i> All (<span id="tabCountAll">0</span>)
            </button>
            <button type="button" class="theme-filter-btn" data-filter="assigned" data-tooltip="Show day-assigned photos">
                <i class="fi fi-rr-calendar-check"></i> Assigned (<span id="tabCountAssigned">0</span>)
            </button>
            <button type="button" class="theme-filter-btn" data-filter="unassigned" data-tooltip="Show unassigned photos">
                <i class="fi fi-rr-clock"></i> Unassigned (<span id="tabCountUnassigned">0</span>)
            </button>
            <button type="button" class="theme-filter-btn btn-suggestions-tab" id="btnToggleSuggestions" data-tooltip="Discover recommended stock photos">
                <i class="fi fi-rr-magic-wand"></i> Image Suggestions
            </button>
        </div>

        <div class="gallery-quick-actions">
            <input type="text" id="galleryLocalSearch" class="gallery-search-input" placeholder="Search photos..." data-tooltip="Filter photos by day or name">
            <button type="button" class="btn-gallery-action upload open-gallery-upload-modal" id="btnOpenGalleryUpload" data-tooltip="Upload new image from your device">
                <i class="fi fi-rr-cloud-upload"></i> Upload Image
            </button>
        </div>
    </div>

    <!-- Bulk Delete Toolbar -->
    <div class="gallery-bulk-bar" id="galleryBulkBar" style="display: none;">
        <div class="bulk-bar-info">
            <label class="gallery-select-all-label">
                <input type="checkbox" id="chkSelectAllGallery">
                <span class="select-all-text">Select All</span>
            </label>
            <span class="bulk-count-badge" id="selectedGalleryCount">0 selected</span>
        </div>
        <div class="bulk-bar-actions">
            <button type="button" class="btn-bulk-action btn-bulk-unselect" id="btnUnselectAllGallery" data-tooltip="Clear current selection">
                <i class="fi fi-rr-cross-small"></i> Unselect All
            </button>
            <button type="button" class="btn-bulk-action btn-bulk-delete" id="btnBulkDeleteGallery" data-tooltip="Permanently delete selected photos">
                <i class="fi fi-rr-trash"></i> Delete Selected (<span id="btnDeleteCount">0</span>)
            </button>
        </div>
    </div>

    <!-- Pexels Stock Suggestions Panel -->
    <div class="pexels-suggestion-panel" id="pexelsSuggestionPanel" style="display: none;">
        <div class="pexels-suggestion-header">
            <div>
                <h3><i class="fi fi-rr-magic-wand"></i> Recommended Stock Photos</h3>
                <p>1-Click Auto Assignment: Automatically searches & assigns the best destination photo for Day 1, Day 2, etc.</p>
            </div>
            <div class="pexels-search-wrap">
                <input type="text" id="pexelsSearchQuery" class="pexels-search-input" placeholder="Search city or destination...">
                <button type="button" class="btn-pexels-action" id="btnSearchPexels" data-tooltip="Search Pexels stock photos"><i class="fi fi-rr-search"></i> Search</button>
                <button type="button" class="btn-pexels-action btn-add-all-suggested" id="btnAddAllSuggested" data-tooltip="Auto-assign destination photos for all itinerary days">
                    <i class="fi fi-rr-magic-wand"></i> Auto-Assign All Day Photos
                </button>
            </div>
        </div>

        <!-- Quick Filters for Image Suggestions -->
        <div class="pexels-quick-filters">
            <span style="font-size: 12px; font-weight: 700; color: #0369a1;">Categories:</span>
            <button type="button" class="pexels-tag-btn active" data-tag="">All Day Suggestions</button>
            <button type="button" class="pexels-tag-btn" data-tag="landmarks">Landmarks & Sightseeing</button>
            <button type="button" class="pexels-tag-btn" data-tag="nature landscape">Nature & Mountains</button>
            <button type="button" class="pexels-tag-btn" data-tag="resort hotel">Hotels & Resorts</button>
            <button type="button" class="pexels-tag-btn" data-tag="culture street">Culture & Markets</button>
        </div>

        <div class="pexels-grid" id="pexelsGrid">
            <div class="pexels-loading-state">
                <div class="pexels-loading-icon-wrap">
                    <div class="pexels-loading-pulse-ring"></div>
                    <i class="fi fi-rr-spinner"></i>
                </div>
                <div class="pexels-loading-text">Fetching day-wise stock photos from Pexels...</div>
            </div>
        </div>

        <!-- Infinite Load More Button Container -->
        <div style="text-align: center; margin-top: 20px;" id="pexelsLoadMoreWrap">
            <button type="button" class="btn-pexels-action" id="btnLoadMorePexels" style="display: none; background: #0284c7; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 20px;" data-tooltip="Load additional stock photos">
                <i class="fi fi-rr-refresh"></i> Load More Stock Photos
            </button>
        </div>
    </div>

    <!-- Gallery Cards Grid -->
    <div class="dashboard-gallery-grid" id="galleryGrid">
        <div class="pexels-loading-state" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #6366f1;">
            <i class="fi fi-rr-spinner spinner" style="font-size: 24px;"></i>
            <p style="margin-top: 8px; font-weight: 600;">Loading gallery photos...</p>
        </div>
    </div>

    <!-- Upload Gallery Modal -->
    <div class="popUp galleryBox">
        <div class="smallPopUpBox" style="max-width: 600px;">
            <div class="widget box-7" style="border: none; padding: 0;">
                <div class="contentArea">
                    <form method="POST" id="addGalleryForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                                    <h4 class="heading" style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">
                                        <i class="fi fi-rr-cloud-upload"></i> Upload Gallery Photos
                                    </h4>
                                </div>
                                <div class="form-group">
                                    <div id="dropZone" class="gallery-drop-zone">
                                        <div class="defaultText">
                                            <i class="fi fi-rr-cloud-upload-alt upload-icon"></i>
                                            <span class="upload-title">Drag & Drop photos here or click to browse</span>
                                            <span class="upload-hint">Supports JPG, JPEG, PNG</span>
                                        </div>
                                    </div>
                                    <input type="file" id="fileInput" name="gallery[]" accept=".jpg, .jpeg, .png" multiple style="display: none;" />
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
            <div class="widget box-3" style="border: none; padding-top: 14px;">
                <div class="actionArea" style="display: flex; gap: 8px; justify-content: flex-end;">
                    <a href="#" class="btn popUpBtn" id="btnSubmitGalleryUpload" style="background: var(--brand-700, #4f46e5); color: #fff; border-radius: 6px; font-weight: 600; padding: 7px 16px; font-size: 12.5px; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <i class="fi-rr-check"></i> Upload Now
                    </a>
                    <span class="btn popUpBtn closeGalleryModal" style="background: #f1f5f9; color: #475569; border-radius: 6px; font-weight: 600; padding: 7px 14px; font-size: 12.5px; border: 1px solid #e2e8f0; cursor: pointer;">
                        <i class="fi-rr-cross"></i> Cancel
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Image Preview Modal -->
    <div class="theme-lightbox-modal" id="lightboxModal">
        <div class="theme-lightbox-content">
            <div class="theme-lightbox-header">
                <h3 id="lightboxTitle">Image Preview</h3>
                <button type="button" class="theme-lightbox-close" id="lightboxClose">&times;</button>
            </div>
            <div class="theme-lightbox-body">
                <img id="lightboxImg" src="" alt="Preview">
            </div>
            <div class="theme-lightbox-footer">
                <span class="theme-stat-pill is-green" id="lightboxDayBadge"><i class="fi fi-rr-calendar"></i> Day Status</span>
                <div>
                    <button type="button" class="theme-lightbox-nav-btn" id="lightboxPrev"><i class="fi fi-rr-angle-left"></i> Prev</button>
                    <button type="button" class="theme-lightbox-nav-btn" id="lightboxNext">Next <i class="fi fi-rr-angle-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>