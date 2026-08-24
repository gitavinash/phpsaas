<?php

$id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));

$query = "
    SELECT tp.tour_package_title, tp.tour_package_city, tp.tour_package_country, tp.tour_package_theme,
           tp.tour_package_duration, tp.tour_package_itinerary,
           g.gallery_image_list, g.gallery_id, g.gallery_image
    FROM " . TABLE_PREFIX . "tour_package AS tp
    LEFT JOIN " . TABLE_PREFIX . "gallery AS g
        ON g.gallery_category = tp.tour_package_id
        AND g.gallery_site_id = " . SITE_ID . "
        AND g.gallery_type = 'tour_package'
        AND g.gallery_status = 1
    WHERE tp.tour_package_id = '{$id}'
    AND tp.tour_package_site_id = " . SITE_ID;

$tour_package_title = 'Tour Package';
$tour_package_city = '';
$tour_package_country = '';
$tour_package_theme = '';
$tour_package_duration = 0;
$tour_package_itinerary = '';
$gallery_image_list = '';
$gallery_id = 0;
$featured_image_path = '';

if ($galleryData = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($galleryData) != 0) {
        $row = mysqli_fetch_assoc($galleryData);
        $tour_package_title = $row['tour_package_title'] ?? 'Tour Package';
        $tour_package_city = $row['tour_package_city'] ?? '';
        $tour_package_country = $row['tour_package_country'] ?? '';
        $tour_package_theme = $row['tour_package_theme'] ?? '';
        $tour_package_duration = (int)($row['tour_package_duration'] ?? 0);
        $tour_package_itinerary = $row['tour_package_itinerary'] ?? '';
        $gallery_image_list = $row['gallery_image_list'] ?? '';
        $gallery_id = (int)($row['gallery_id'] ?? 0);
        $featured_image_path = $row['gallery_image'] ?? '';
    }
}

$galleryImagesList = [];
if (!empty($gallery_image_list)) {
    $galleryImagesList = json_decode($gallery_image_list, TRUE) ?? [];
}

// Calculate Total Days for this Tour Package
$itineraryArr = [];
if (!empty($tour_package_itinerary)) {
    $itineraryArr = json_decode($tour_package_itinerary, TRUE) ?? [];
}
$totalDays = max(count($itineraryArr), $tour_package_duration + 1, 1);

// Build map of assigned days -> image path
$assignedDayMap = [];
foreach ($galleryImagesList as $img) {
    if (isset($img['day']) && (int)$img['day'] > 0) {
        $assignedDayMap[(int)$img['day']] = $img['path'] ?? '';
    }
}

// Count unique days that have an assigned image vs days missing image
$daysAssignedCount = 0;
for ($d = 1; $d <= $totalDays; $d++) {
    if (isset($assignedDayMap[$d]) && !empty($assignedDayMap[$d])) {
        $daysAssignedCount++;
    }
}
$daysMissingCount = max(0, $totalDays - $daysAssignedCount);

// Sort images: Assigned days (1, 2, 3...) first, then unassigned images
usort($galleryImagesList, function ($a, $b) {
    $dayA = isset($a['day']) && (int)$a['day'] > 0 ? (int)$a['day'] : 999999;
    $dayB = isset($b['day']) && (int)$b['day'] > 0 ? (int)$b['day'] : 999999;
    if ($dayA === $dayB) {
        return 0;
    }
    return ($dayA < $dayB) ? -1 : 1;
});

// Image count stats
$assignedImagesCount = 0;
$unassignedImagesCount = 0;
foreach ($galleryImagesList as $img) {
    if (isset($img['day']) && (int)$img['day'] > 0) {
        $assignedImagesCount++;
    } else {
        $unassignedImagesCount++;
    }
}
$totalImagesCount = count($galleryImagesList);

$defaultSearchQuery = !empty($tour_package_city) ? $tour_package_city : (!empty($tour_package_country) ? $tour_package_country : $tour_package_title);

?>

<style>
    /* Theme Integration Styles */
    .dashboard-gallery-container {
        width: 100%;
        padding: 10px 0;
    }

    .gallery-sub-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #ffffff;
        padding: 16px 20px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .gallery-stats-pills {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .theme-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .theme-stat-pill.is-green {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }

    .theme-stat-pill.is-orange {
        background: #fff7ed;
        color: #c2410c;
        border-color: #ffedd5;
    }

    .theme-stat-pill.is-red {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    .gallery-filter-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .theme-filter-btn {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .theme-filter-btn:hover {
        color: #0f172a;
        background: #ffffff;
    }

    .theme-filter-btn.active {
        background: var(--brand-700, #2563eb);
        color: #ffffff !important;
    }

    /* Pexels Suggestions Section */
    .pexels-suggestion-panel {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(14, 165, 233, 0.08);
    }

    .pexels-suggestion-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .pexels-suggestion-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #0369a1;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pexels-search-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pexels-search-input {
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid #7dd3fc;
        font-size: 13px;
        outline: none;
        width: 220px;
        background: #ffffff;
    }

    .btn-pexels-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        background: #0284c7;
        color: #ffffff;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-pexels-action:hover {
        background: #0369a1;
    }

    .btn-add-all-suggested {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
    }

    .btn-add-all-suggested:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        transform: translateY(-1px);
    }

    .pexels-quick-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .pexels-tag-btn {
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        background: #ffffff;
        color: #0284c7;
        border: 1px solid #7dd3fc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pexels-tag-btn:hover,
    .pexels-tag-btn.active {
        background: #0284c7;
        color: #ffffff;
    }

    .pexels-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        min-height: 280px;
    }

    /* Minimal Animated Loader Styling */
    @keyframes dashSpin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulseRing {

        0%,
        100% {
            opacity: 0.3;
            transform: scale(0.92);
        }

        50% {
            opacity: 0.9;
            transform: scale(1.1);
        }
    }

    .spinner {
        display: inline-block;
        animation: dashSpin 0.9s linear infinite;
    }

    .pexels-loading-state {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 280px;
        padding: 50px 20px;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(6px);
        border-radius: 12px;
        border: 1px dashed #7dd3fc;
        color: #0284c7;
        gap: 16px;
    }

    .pexels-loading-icon-wrap {
        position: relative;
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pexels-loading-icon-wrap i {
        font-size: 26px;
        color: #0284c7;
        animation: dashSpin 0.9s linear infinite;
        z-index: 2;
    }

    .pexels-loading-pulse-ring {
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px dashed #38bdf8;
        animation: pulseRing 1.8s ease-in-out infinite;
    }

    .pexels-loading-text {
        font-size: 13px;
        font-weight: 600;
        color: #0369a1;
        letter-spacing: 0.3px;
    }

    .pexels-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .pexels-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
        border-color: #0284c7;
    }

    .pexels-thumb-wrap {
        position: relative;
        width: 100%;
        height: 210px;
        background: #0f172a;
        overflow: hidden;
    }

    .pexels-thumb-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.4s ease;
    }

    .pexels-card:hover .pexels-thumb-wrap img {
        transform: scale(1.06);
    }

    .pexels-card-body {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pexels-day-select-wrap label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
    }

    /* Select2 container tweaks for cards */
    .pexels-day-select-wrap .select2-container {
        width: 100% !important;
    }

    .pexels-day-select-wrap .select2-container--default .select2-selection--single {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        height: 36px;
        display: flex;
        align-items: center;
    }

    .pexels-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding-left: 10px;
    }

    .pexels-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    /* Dashboard Gallery Grid Layout */
    .dashboard-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 10px;
    }

    .dash-gallery-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease-in-out;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .dash-gallery-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .dash-gallery-card.is-assigned {
        border: 2px solid #10b981;
        background: #fafdfb;
    }

    .dash-gallery-card.is-featured {
        border-color: #f59e0b;
    }

    .dash-thumb-container {
        position: relative;
        width: 100%;
        height: 190px;
        background: #0f172a;
        overflow: hidden;
    }

    .dash-thumb-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .dash-gallery-card:hover .dash-thumb-container img {
        transform: scale(1.05);
    }

    .dash-card-badges {
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 6px;
        z-index: 2;
        pointer-events: none;
    }

    .dash-badge-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        backdrop-filter: blur(6px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .dash-badge-chip.badge-day {
        background: rgba(16, 185, 129, 0.95);
        color: #ffffff;
    }

    .dash-badge-chip.badge-none {
        background: rgba(100, 116, 139, 0.85);
        color: #ffffff;
    }

    .dash-badge-chip.badge-star {
        background: rgba(245, 158, 11, 0.95);
        color: #ffffff;
    }

    .dash-thumb-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 3;
    }

    .dash-gallery-card:hover .dash-thumb-overlay,
    .pexels-card:hover .dash-thumb-overlay {
        opacity: 1;
    }

    .dash-preview-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        transition: all 0.2s ease;
    }

    .dash-preview-btn:hover {
        transform: scale(1.1);
        background: var(--brand-700, #2563eb);
        color: #ffffff;
    }

    .dash-card-body {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dash-day-select-wrap label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
    }

    /* Select2 container tweaks for cards */
    .dash-day-select-wrap .select2-container {
        width: 100% !important;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        height: 36px;
        display: flex;
        align-items: center;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding-left: 10px;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .dash-card-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .dash-btn-featured {
        width: 100%;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #334155;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.2s ease;
    }

    .dash-btn-featured:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .dash-btn-featured.is-active {
        background: #fef3c7;
        border-color: #fde68a;
        color: #d97706;
    }

    /* Lightbox Preview Modal */
    .theme-lightbox-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(6px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .theme-lightbox-modal.active {
        display: flex;
    }

    .theme-lightbox-content {
        background: #ffffff;
        border-radius: 14px;
        max-width: 850px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
    }

    .theme-lightbox-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .theme-lightbox-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .theme-lightbox-close {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background: #e2e8f0;
        color: #475569;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .theme-lightbox-close:hover {
        background: #cbd5e1;
        color: #0f172a;
    }

    .theme-lightbox-body {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        padding: 16px;
        max-height: 60vh;
    }

    .theme-lightbox-body img {
        max-width: 100%;
        max-height: 55vh;
        object-fit: contain;
    }

    .theme-lightbox-footer {
        padding: 14px 20px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .theme-lightbox-nav-btn {
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
    }

    .theme-lightbox-nav-btn:hover {
        background: #f1f5f9;
    }
</style>

<h2 class="heading">
    <?php echo ucwords($tour_package_title) ?>'s Gallery
    <span>Tour package image gallery & day-wise assignment</span>
</h2>

<div class="box-1">
    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section class="dashboard-gallery-container">

                <!-- Sub Header Controls & Filters -->
                <div class="gallery-sub-header">
                    <div class="gallery-stats-pills">
                        <div class="theme-stat-pill">
                            <i class="fi fi-rr-picture"></i> <b id="statTotalImages"><?php echo $totalImagesCount; ?></b> Gallery Photos
                        </div>
                        <div class="theme-stat-pill is-green">
                            <i class="fi fi-rr-calendar-check"></i> <b id="statDaysCovered"><?php echo $daysAssignedCount; ?> / <?php echo $totalDays; ?></b> Days Covered
                        </div>
                        <div class="theme-stat-pill <?php echo ($daysMissingCount > 0) ? 'is-red' : 'is-green'; ?>" id="statDaysMissingPill">
                            <?php if ($daysMissingCount > 0) { ?>
                                <i class="fi fi-rr-exclamation"></i> <b><?php echo $daysMissingCount; ?></b> <?php echo ($daysMissingCount === 1) ? 'Day' : 'Days'; ?> Missing Image
                            <?php } else { ?>
                                <i class="fi fi-rr-check-circle"></i> All Days Assigned!
                            <?php } ?>
                        </div>
                    </div>

                    <div class="gallery-filter-tabs">
                        <button class="theme-filter-btn <?php echo ($totalImagesCount > 0) ? 'active' : ''; ?>" data-filter="all">All (<?php echo $totalImagesCount; ?>)</button>
                        <button class="theme-filter-btn" data-filter="assigned">Assigned Days (<?php echo $assignedImagesCount; ?>)</button>
                        <button class="theme-filter-btn" data-filter="unassigned">Unassigned (<?php echo $unassignedImagesCount; ?>)</button>
                        <button class="theme-filter-btn <?php echo ($totalImagesCount === 0) ? 'active' : ''; ?>" id="btnToggleSuggestions" style="color: #0284c7; font-weight: 700;">
                            <i class="fi fi-rr-magic-wand"></i> Images Suggestions
                        </button>
                    </div>
                </div>

                <!-- Pexels Stock Image Suggestions Panel -->
                <div class="pexels-suggestion-panel" id="pexelsSuggestionPanel" style="<?php echo ($totalImagesCount === 0) ? 'display: block;' : 'display: none;'; ?>">
                    <div class="pexels-suggestion-header">
                        <div>
                            <h3><i class="fi fi-rr-magic-wand"></i> Recommended Stock Images</h3>
                            <span style="font-size: 12px; color: #0369a1; display: block; margin-top: 2px;">1-Click Auto Assignment: Automatically searches & assigns the best destination photo for Day 1, Day 2, etc. (and sets featured image if missing)</span>
                        </div>
                        <div class="pexels-search-wrap">
                            <input type="text" id="pexelsSearchQuery" class="pexels-search-input" placeholder="Search city or destination..." value="<?php echo htmlspecialchars($defaultSearchQuery); ?>">
                            <button type="button" class="btn-pexels-action" id="btnSearchPexels"><i class="fi fi-rr-search"></i> Search</button>
                            <button type="button" class="btn-pexels-action btn-add-all-suggested" id="btnAddAllSuggested" title="Automatically searches & assigns destination photos for each day of your itinerary in 1 click">
                                <i class="fi fi-rr-magic-wand"></i> Auto-Assign All Day Photos
                            </button>
                        </div>
                    </div>

                    <!-- Quick Filters for Perfect Image Suggestions -->
                    <div class="pexels-quick-filters">
                        <span style="font-size: 12px; font-weight: 700; color: #0369a1;">Refine Category:</span>
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
                        <button type="button" class="btn-pexels-action" id="btnLoadMorePexels" style="display: none; background: #0284c7; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 20px;">
                            <i class="fi fi-rr-refresh"></i> Load More Stock Photos
                        </button>
                    </div>
                </div>

                <!-- Gallery Cards Grid -->
                <div class="dashboard-gallery-grid" id="galleryGrid" style="<?php echo ($totalImagesCount === 0) ? 'display: none;' : 'display: grid;'; ?>">
                    <?php
                    if (empty($galleryImagesList)) {
                        echo "<div style='grid-column: 1 / -1; padding: 40px; text-align: center; background: #fff; border-radius: 8px; color: #64748b;'>No gallery images uploaded yet. You can pick recommended photos from the <b>Images Suggestions</b> panel above or click the floating <b>+</b> button at bottom right to upload images.</div>";
                    }

                    foreach ($galleryImagesList as $index => $imageData) {
                        $path = $imageData['path'] ?? '';
                        $imgPath = (!empty($path)) ? $path : ADMIN_IMAGES . 'default-picture.png';
                        $assignedDay = isset($imageData['day']) ? (int)$imageData['day'] : 0;
                        $isFeatured = ($featured_image_path !== '' && $featured_image_path === $path);

                        $cardClasses = 'dash-gallery-card';
                        if ($assignedDay > 0) $cardClasses .= ' is-assigned';
                        if ($isFeatured) $cardClasses .= ' is-featured';
                    ?>
                        <div class="<?php echo $cardClasses; ?>"
                            data-index="<?php echo $index; ?>"
                            data-image="<?php echo htmlspecialchars($path); ?>"
                            data-day="<?php echo $assignedDay; ?>"
                            data-featured="<?php echo $isFeatured ? '1' : '0'; ?>">

                            <div class="dash-thumb-container">
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Gallery Image <?php echo $index + 1; ?>" loading="lazy">

                                <div class="dash-card-badges">
                                    <div>
                                        <?php if ($assignedDay > 0) { ?>
                                            <span class="dash-badge-chip badge-day"><i class="fi fi-rr-calendar"></i> Day <?php echo $assignedDay; ?></span>
                                        <?php } else { ?>
                                            <span class="dash-badge-chip badge-none">Unassigned</span>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?php if ($isFeatured) { ?>
                                            <span class="dash-badge-chip badge-star"><i class="fi fi-rr-star"></i> Featured</span>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="dash-thumb-overlay">
                                    <button type="button" class="dash-preview-btn btn-preview-img"
                                        data-image="<?php echo htmlspecialchars($imgPath); ?>"
                                        data-day="<?php echo $assignedDay; ?>"
                                        data-name="<?php echo htmlspecialchars($imageData['name'] ?? 'Image ' . ($index + 1)); ?>"
                                        title="View Preview">
                                        <i class="fi fi-rr-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="dash-card-body">
                                <div class="dash-day-select-wrap">
                                    <label><i class="fi fi-rr-calendar"></i> Assign Day:</label>
                                    <select class="selectJS form-control assign-day-select"
                                        data-image="<?php echo htmlspecialchars($path); ?>"
                                        data-package-id="<?php echo $id; ?>"
                                        data-gallery-id="<?php echo $gallery_id; ?>">
                                        <option value="0">-- Unassigned --</option>
                                        <?php for ($d = 1; $d <= $totalDays; $d++) {
                                            $selected = ($assignedDay === $d) ? 'selected' : '';
                                            $occupiedByOther = isset($assignedDayMap[$d]) && $assignedDayMap[$d] !== $path;
                                            $labelSuffix = $occupiedByOther ? ' (Currently Occupied)' : '';
                                        ?>
                                            <option value="<?php echo $d; ?>" <?php echo $selected; ?>>
                                                Day <?php echo $d . $labelSuffix; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="dash-card-actions">
                                    <a href="#" class="dash-btn-featured setGalleryFeatureImage <?php echo $isFeatured ? 'is-active' : ''; ?>"
                                        data-id="<?php echo $gallery_id; ?>"
                                        data-category="<?php echo $id; ?>"
                                        data-image="<?php echo htmlspecialchars($path); ?>">
                                        <i class="fi fi-rr-star"></i> <?php echo $isFeatured ? 'Featured Image' : 'Set as Featured'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Floating Shortcut Action to Add New Image -->
                <div class="shortcut">
                    <a href="#" class="addNewGallery" title="Add New Image"><i class="fi fi-rr-plus"></i></a>
                </div>

            </section>
        </div>
    </div>

    <!-- Add New Gallery Upload Modal -->
    <div class="popUp galleryBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addGalleryForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new gallery</h4>
                                </div>
                                <div class="form-group">
                                    <div id="dropZone" style="height: 500px;">
                                        <span class="defaultText">Drag & Drop files here or click to upload</span>
                                    </div>
                                    <input type="file" id="fileInput" name="gallery[]" accept=".jpg, .jpeg, .png" multiple />
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addGallery"><i class="fi-rr-check"></i> Add new gallery</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class="fi-rr-cross"></i> Cancel</span>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalDaysCount = <?php echo $totalDays; ?>;
        const currentPackageId = "<?php echo $id; ?>";
        const currentGalleryId = "<?php echo $gallery_id; ?>";
        const isGalleryEmpty = <?php echo ($totalImagesCount === 0) ? 'true' : 'false'; ?>;
        const defaultDestinationQuery = "<?php echo htmlspecialchars($defaultSearchQuery); ?>";
        let pexelsLoaded = false;
        let pexelsSuggestionsData = [];
        let currentPexelsPage = 1;
        let activeSearchQuery = '';

        // Initialize Select2 on day selector dropdowns
        function initDaySelect2() {
            if ($.fn.select2) {
                $(".assign-day-select").select2({
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            }
        }
        initDaySelect2();

        // Update real-time stats pills in sub-header
        function updateGalleryStatsPills() {
            const assignedDaysSet = new Set();
            let totalImages = 0;

            $(".dash-gallery-card").each(function() {
                totalImages++;
                const day = parseInt($(this).data("day")) || 0;
                if (day > 0) {
                    assignedDaysSet.add(day);
                }
            });

            const daysCovered = assignedDaysSet.size;
            const daysMissing = Math.max(0, totalDaysCount - daysCovered);

            $("#statTotalImages").text(totalImages);
            $("#statDaysCovered").text(daysCovered + " / " + totalDaysCount);

            if (daysMissing > 0) {
                $("#statDaysMissingPill")
                    .removeClass("is-green").addClass("is-red")
                    .html('<i class="fi fi-rr-exclamation"></i> <b>' + daysMissing + '</b> ' + (daysMissing === 1 ? 'Day' : 'Days') + ' Missing Image');
            } else {
                $("#statDaysMissingPill")
                    .removeClass("is-red").addClass("is-green")
                    .html('<i class="fi fi-rr-check-circle"></i> All Days Assigned!');
            }
        }

        // Fetch Pexels Suggestions (with Infinite Pagination support)
        function loadPexelsSuggestions(customQuery = '', isAppend = false) {
            if (!isAppend) {
                currentPexelsPage = 1;
                activeSearchQuery = customQuery;
                pexelsSuggestionsData = [];
                const loadingHtml = `
                    <div class="pexels-loading-state">
                        <div class="pexels-loading-icon-wrap">
                            <div class="pexels-loading-pulse-ring"></div>
                            <i class="fi fi-rr-spinner"></i>
                        </div>
                        <div class="pexels-loading-text">Fetching day-wise stock photos from Pexels...</div>
                    </div>
                `;
                $("#pexelsGrid").html(loadingHtml);
                $("#btnLoadMorePexels").hide();
            } else {
                $("#btnLoadMorePexels").prop("disabled", true).html('<i class="fi fi-rr-spinner spinner"></i> Fetching More...');
            }

            $.ajax({
                url: "/api/aleya/gallery/suggest-images/",
                type: "GET",
                data: {
                    packageId: currentPackageId,
                    query: activeSearchQuery,
                    page: currentPexelsPage
                },
                dataType: "json",
                success: function(res) {
                    pexelsLoaded = true;
                    $("#btnLoadMorePexels").prop("disabled", false).html('<i class="fi fi-rr-refresh"></i> Load More Stock Photos');

                    if (res.status === "success" && res.suggestions && res.suggestions.length > 0) {
                        const startIndex = pexelsSuggestionsData.length;
                        pexelsSuggestionsData = pexelsSuggestionsData.concat(res.suggestions);
                        renderPexelsGrid(res.suggestions, isAppend, startIndex);

                        if (res.hasMore) {
                            $("#btnLoadMorePexels").show();
                        } else {
                            $("#btnLoadMorePexels").hide();
                        }
                    } else {
                        if (!isAppend) {
                            $("#pexelsGrid").html('<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">No stock photo suggestions found. Try searching another keyword above.</div>');
                        }
                        $("#btnLoadMorePexels").hide();
                    }
                },
                error: function() {
                    $("#btnLoadMorePexels").prop("disabled", false).html('<i class="fi fi-rr-refresh"></i> Load More Stock Photos');
                    if (!isAppend) {
                        $("#pexelsGrid").html('<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #ef4444;">Failed to fetch Pexels suggestions. Please check internet connection or try again.</div>');
                    }
                }
            });
        }

        function renderPexelsGrid(suggestions, isAppend = false, startIndex = 0) {
            let html = '';
            suggestions.forEach((item, idx) => {
                const dataIndex = startIndex + idx;
                const dayLabel = item.day > 0 ? ('Day ' + item.day) : 'Suggested';
                html += `
                    <div class="pexels-card">
                        <div class="pexels-thumb-wrap">
                            <img src="${item.thumb}" alt="${item.alt}" loading="lazy">
                            <div class="dash-card-badges">
                                <span class="dash-badge-chip badge-day"><i class="fi fi-rr-calendar"></i> ${dayLabel}</span>
                            </div>
                            <div class="dash-thumb-overlay">
                                <button type="button" class="dash-preview-btn btn-preview-pexels-img"
                                        data-url="${item.url}"
                                        data-alt="${item.alt}"
                                        title="View Full Image">
                                    <i class="fi fi-rr-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="pexels-card-body">
                            <div class="dash-day-select-wrap pexels-day-select-wrap">
                                <label><i class="fi fi-rr-calendar"></i> Target Day:</label>
                                <select class="selectJS pexels-day-select" data-idx="${dataIndex}">
                                    <option value="0">-- Unassigned --</option>`;
                for (let d = 1; d <= totalDaysCount; d++) {
                    const sel = (item.day === d) ? 'selected' : '';
                    html += `<option value="${d}" ${sel}>Day ${d}</option>`;
                }
                html += `        </select>
                            </div>
                            <button type="button" class="dash-btn-featured btn-add-single-pexels" data-idx="${dataIndex}">
                                <i class="fi fi-rr-plus"></i> Add to Gallery
                            </button>
                        </div>
                    </div>
                `;
            });

            if (isAppend) {
                $("#pexelsGrid").append(html);
            } else {
                $("#pexelsGrid").html(html);
            }

            if ($.fn.select2) {
                $(".pexels-day-select").select2({
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            }
        }

        // Infinite Load More Button Click Event
        $("#btnLoadMorePexels").click(function() {
            currentPexelsPage++;
            loadPexelsSuggestions(activeSearchQuery, true);
        });

        // Preview suggested Pexels image in Lightbox Modal
        $(document).on("click", ".btn-preview-pexels-img", function(e) {
            e.preventDefault();
            const fullUrl = $(this).data("url");
            const alt = $(this).data("alt") || "Pexels Stock Photo";

            $("#lightboxImg").attr("src", fullUrl);
            $("#lightboxTitle").text(alt);
            $("#lightboxDayBadge").removeClass("is-orange").addClass("is-green").html('<i class="fi fi-rr-magic-wand"></i> Pexels High-Res Stock Photo');
            $("#lightboxModal").addClass("active");
        });

        // Auto load suggestions if gallery is empty
        if (isGalleryEmpty) {
            loadPexelsSuggestions();
        }

        // TAB SWITCHING LOGIC: Hide .dashboard-gallery-grid when "Images Suggestions" is clicked!
        $(".theme-filter-btn").click(function() {
            const btnId = $(this).attr("id");

            if (btnId === "btnToggleSuggestions") {
                // Activate Images Suggestions tab
                $(".theme-filter-btn").removeClass("active");
                $(this).addClass("active");

                $("#galleryGrid").hide();
                $("#pexelsSuggestionPanel").slideDown(200);

                if (!pexelsLoaded) {
                    loadPexelsSuggestions();
                }
            } else {
                // Activate All / Assigned / Unassigned tabs
                $(".theme-filter-btn").removeClass("active");
                $(this).addClass("active");

                $("#pexelsSuggestionPanel").slideUp(200);
                $("#galleryGrid").show();

                const filter = $(this).data("filter");
                $(".dash-gallery-card").each(function() {
                    const day = parseInt($(this).data("day")) || 0;
                    if (filter === "all") {
                        $(this).show();
                    } else if (filter === "assigned") {
                        if (day > 0) $(this).show();
                        else $(this).hide();
                    } else if (filter === "unassigned") {
                        if (day === 0) $(this).show();
                        else $(this).hide();
                    }
                });
            }
        });

        // Pexels Quick Tag Filters
        $(".pexels-tag-btn").click(function() {
            $(".pexels-tag-btn").removeClass("active");
            $(this).addClass("active");
            const tag = $(this).data("tag");

            let searchQuery = defaultDestinationQuery;
            if (tag) {
                searchQuery = defaultDestinationQuery + ' ' + tag;
            }
            $("#pexelsSearchQuery").val(searchQuery);
            loadPexelsSuggestions(searchQuery);
        });

        $("#btnSearchPexels").click(function() {
            const q = $("#pexelsSearchQuery").val().trim();
            loadPexelsSuggestions(q);
        });

        $("#pexelsSearchQuery").keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $("#btnSearchPexels").click();
            }
        });

        // Add single suggested Pexels photo
        $(document).on("click", ".btn-add-single-pexels", function() {
            const $btn = $(this);
            const idx = $btn.data("idx");
            const item = pexelsSuggestionsData[idx];
            const chosenDay = parseInt($btn.closest(".pexels-card-body").find(".pexels-day-select").val()) || 0;

            $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner spinner"></i> Adding...');

            $.ajax({
                url: "/api/aleya/gallery/add-suggested/",
                type: "POST",
                data: {
                    packageId: currentPackageId,
                    galleryId: currentGalleryId,
                    imagePath: item.url,
                    day: chosenDay
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        popUpMessage("center", res.msg, "success");
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        $btn.prop("disabled", false).html('<i class="fi fi-rr-plus"></i> Add to Gallery');
                        popUpMessage("center", res.msg || "Failed to add image", "error");
                    }
                },
                error: function() {
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-plus"></i> Add to Gallery');
                    popUpMessage("center", "Error adding image to gallery", "error");
                }
            });
        });

        // Intelligent Auto-assign: Searches & assigns destination photos for each day (Shimla, Manali, Kullu, etc.)
        $("#btnAddAllSuggested").click(function() {
            const $btn = $(this);
            $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner spinner"></i> Searching & Auto-Assigning Day Photos...');

            $.ajax({
                url: "/api/aleya/gallery/add-suggested/",
                type: "POST",
                data: {
                    packageId: currentPackageId,
                    galleryId: currentGalleryId,
                    autoAssign: 1
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        popUpMessage("center", res.msg, "success");
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        $btn.prop("disabled", false).html('<i class="fi fi-rr-magic-wand"></i> Auto-Assign All Day Photos');
                        popUpMessage("center", res.msg || "Failed to assign suggested images", "error");
                    }
                },
                error: function() {
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-magic-wand"></i> Auto-Assign All Day Photos');
                    popUpMessage("center", "Error assigning suggested images", "error");
                }
            });
        });

        // Day Assignment Change Handler for Existing Images
        $(document).on("change", ".assign-day-select", function() {
            const $select = $(this);
            const imagePath = $select.data("image");
            const newDay = parseInt($select.val()) || 0;
            const $card = $select.closest(".dash-gallery-card");

            $select.prop("disabled", true);

            $.ajax({
                url: "/api/aleya/gallery/assign-day/",
                type: "POST",
                data: {
                    packageId: currentPackageId,
                    galleryId: currentGalleryId,
                    imagePath: imagePath,
                    day: newDay
                },
                dataType: "json",
                success: function(res) {
                    $select.prop("disabled", false);
                    if (res.status === "success") {
                        popUpMessage("center", res.msg, "success");

                        // If newDay > 0, unassign any other card previously assigned to this day
                        if (newDay > 0) {
                            $(".dash-gallery-card").each(function() {
                                const $otherCard = $(this);
                                const otherImg = $otherCard.data("image");
                                if (otherImg !== imagePath) {
                                    const $otherSelect = $otherCard.find(".assign-day-select");
                                    if (parseInt($otherSelect.val()) === newDay) {
                                        $otherSelect.val("0").trigger("change.select2");
                                        $otherCard.data("day", 0).removeClass("is-assigned");
                                        $otherCard.find(".dash-badge-chip.badge-day")
                                            .removeClass("badge-day")
                                            .addClass("badge-none")
                                            .html("Unassigned");
                                    }
                                }
                            });
                        }

                        // Update current card UI
                        $card.data("day", newDay);
                        const $badgeContainer = $card.find(".dash-card-badges > div").first();

                        if (newDay > 0) {
                            $card.addClass("is-assigned");
                            $badgeContainer.html('<span class="dash-badge-chip badge-day"><i class="fi fi-rr-calendar"></i> Day ' + newDay + '</span>');
                        } else {
                            $card.removeClass("is-assigned");
                            $badgeContainer.html('<span class="dash-badge-chip badge-none">Unassigned</span>');
                        }

                        // Update dropdown option labels across all cards
                        updateAllDropdownOccupiedLabels();

                        // Auto-sort cards in DOM: assigned days 1..N first, then unassigned
                        sortGalleryCardsInDOM();

                        // Recalculate stats pills
                        updateGalleryStatsPills();
                    } else {
                        popUpMessage("center", res.msg || "Failed to assign day", "error");
                    }
                },
                error: function(err) {
                    $select.prop("disabled", false);
                    const msg = err.responseJSON && err.responseJSON.msg ? err.responseJSON.msg : "An error occurred";
                    popUpMessage("center", msg, "error");
                }
            });
        });

        // Function to update "Currently Occupied" text in dropdown options & sync Select2
        function updateAllDropdownOccupiedLabels() {
            const occupiedMap = {};
            $(".dash-gallery-card").each(function() {
                const img = $(this).data("image");
                const day = parseInt($(this).data("day")) || 0;
                if (day > 0) {
                    occupiedMap[day] = img;
                }
            });

            $(".dash-gallery-card").each(function() {
                const $card = $(this);
                const currentImg = $card.data("image");
                const $select = $card.find(".assign-day-select");

                $select.find("option").each(function() {
                    const d = parseInt($(this).val());
                    if (d > 0) {
                        const occupiedBy = occupiedMap[d];
                        const isOccupiedByOther = occupiedBy && occupiedBy !== currentImg;
                        $(this).text("Day " + d + (isOccupiedByOther ? " (Currently Occupied)" : ""));
                    }
                });

                if ($.fn.select2) {
                    $select.trigger("change.select2");
                }
            });
        }

        // DOM Card Sorting Function: Assigned days (1, 2, 3...) first, then unassigned
        function sortGalleryCardsInDOM() {
            const $grid = $("#galleryGrid");
            const $cards = $grid.find(".dash-gallery-card").get();

            $cards.sort(function(a, b) {
                const dayA = parseInt($(a).data("day")) || 999999;
                const dayB = parseInt($(b).data("day")) || 999999;
                if (dayA === dayB) return 0;
                return dayA < dayB ? -1 : 1;
            });

            $.each($cards, function(idx, elem) {
                $grid.append(elem);
            });

            initDaySelect2();
        }

        // Lightbox Modal Logic
        let currentLightboxIndex = 0;
        let previewImages = [];

        function collectPreviewImages() {
            previewImages = [];
            $(".dash-gallery-card:visible").each(function() {
                const $btn = $(this).find(".btn-preview-img");
                previewImages.push({
                    src: $btn.data("image"),
                    name: $btn.data("name"),
                    day: parseInt($(this).data("day")) || 0
                });
            });
        }

        $(document).on("click", ".btn-preview-img", function(e) {
            e.preventDefault();
            collectPreviewImages();
            const src = $(this).data("image");
            currentLightboxIndex = previewImages.findIndex(img => img.src === src);
            if (currentLightboxIndex < 0) currentLightboxIndex = 0;
            showLightboxModal();
        });

        function showLightboxModal() {
            if (previewImages.length === 0) return;
            const item = previewImages[currentLightboxIndex];
            $("#lightboxImg").attr("src", item.src);
            $("#lightboxTitle").text(item.name || ("Image " + (currentLightboxIndex + 1)));

            if (item.day > 0) {
                $("#lightboxDayBadge").removeClass("is-orange").addClass("is-green").html('<i class="fi fi-rr-calendar"></i> Day ' + item.day + ' Assigned');
            } else {
                $("#lightboxDayBadge").removeClass("is-green").addClass("is-orange").html('<i class="fi fi-rr-calendar"></i> Unassigned');
            }

            $("#lightboxModal").addClass("active");
        }

        $("#lightboxClose").click(function() {
            $("#lightboxModal").removeClass("active");
        });

        $("#lightboxPrev").click(function() {
            if (previewImages.length === 0) return;
            currentLightboxIndex = (currentLightboxIndex - 1 + previewImages.length) % previewImages.length;
            showLightboxModal();
        });

        $("#lightboxNext").click(function() {
            if (previewImages.length === 0) return;
            currentLightboxIndex = (currentLightboxIndex + 1) % previewImages.length;
            showLightboxModal();
        });

        $(document).keydown(function(e) {
            if ($("#lightboxModal").hasClass("active")) {
                if (e.key === "Escape") $("#lightboxModal").removeClass("active");
                if (e.key === "ArrowLeft") $("#lightboxPrev").click();
                if (e.key === "ArrowRight") $("#lightboxNext").click();
            }
        });
    });
</script>