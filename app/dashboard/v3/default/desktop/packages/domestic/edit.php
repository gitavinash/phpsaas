<?php

$rawId = $_GET['id'] ?? 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $id = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $id = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;
}
$encodedId = is_numeric($id) ? base64_encode($id) : $rawId;
?>

<div class="package-page-wrapper package-edit-wrapper" id="packageEditApp" data-package-id="<?php echo $id; ?>" data-encoded-id="<?php echo $encodedId; ?>" data-package-type="domestic">
    
    <!-- Loading State -->
    <div id="packageEditLoading" style="text-align: center; padding: 70px 20px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; margin-top: 20px;">
        <div class="table-loading-spinner" style="margin: 0 auto 16px auto; width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #0284c7; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
        <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Loading Package Details...</h4>
        <p style="color: #64748b; font-size: 13px;">Please wait while we retrieve the domestic itinerary.</p>
    </div>

    <!-- Error State -->
    <div id="packageEditError" style="display: none; text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; margin-top: 20px;">
        <i class="fi fi-rr-exclamation" style="font-size: 48px; color: #ef4444; margin-bottom: 16px; display: inline-block;"></i>
        <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Tour Package Not Found</h3>
        <p id="packageErrorText" style="color: #64748b; margin-bottom: 24px;">The requested domestic package could not be retrieved or has been removed.</p>
        <a href="/dashboard/packages/domestic/list/" class="btn-gallery-action" style="background: #0284c7; color: #fff; padding: 10px 22px; display: inline-flex; border-radius: 8px;">
            <i class="fi fi-rr-arrow-left" style="margin-right: 8px;"></i> Return to Packages List
        </a>
    </div>

    <!-- Edit Package Content Body -->
    <div id="packageEditContent" style="display: none;">
        <!-- Header Banner -->
        <div class="package-header-bar">
            <div class="package-header-left">
                <div class="package-header-icon">
                    <i class="fi fi-rr-edit"></i>
                </div>
                <div class="package-header-titles">
                    <h2 id="pkgHeaderTitle">Tour Package</h2>
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
                <a href="#" target="_blank" class="btn-gallery-action" id="btnViewLivePackage" style="background: #f8fafc; color: #0284c7; border: 1px solid #cbd5e1; display: none;" title="View Live Package">
                    <i class="fi fi-rr-eye"></i> View Live
                </a>
                <a href="/dashboard/packages/domestic/gallery/?id=<?php echo $encodedId; ?>" class="btn-gallery-action" id="btnManageGallery" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;" title="Manage Gallery Photos">
                    <i class="fi fi-rr-gallery"></i> Manage Gallery
                </a>
                <a href="/dashboard/packages/domestic/list/" class="btn-gallery-action" id="btnBackToList" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;" title="Back to Domestic List">
                    <i class="fi fi-rr-arrow-left"></i> Packages
                </a>
            </div>
        </div>

        <!-- Edit Navigation Segment Tabs -->
        <div class="edit-nav-tabs-wrapper">
            <div class="edit-nav-tabs">
                <button type="button" class="edit-tab-btn active" data-target="#tabGeneral">
                    <i class="fi fi-rr-settings-sliders"></i>
                    <span>1. General & Logistics</span>
                </button>
                <button type="button" class="edit-tab-btn" data-target="#tabItinerary">
                    <i class="fi fi-rr-calendar"></i>
                    <span>2. Day-Wise Itinerary</span>
                    <span class="tab-badge" id="tabItineraryCountBadge">0 Days</span>
                </button>
                <button type="button" class="edit-tab-btn" data-target="#tabPolicies">
                    <i class="fi fi-rr-list-check"></i>
                    <span>3. Inclusions & Exclusions</span>
                </button>
            </div>
        </div>

        <!-- Edit Package Form -->
        <form action="#" id="createTourPackageForm" method="POST">
            <input type="hidden" name="packageType" id="packageType" value="domestic">
            <input type="hidden" name="tourPackageId" id="hiddenPackageId" value="<?php echo $encodedId; ?>">
            <input type="hidden" name="duration" id="hiddenDuration" value="0">

            <!-- TAB 1: General & Logistics -->
            <div class="edit-tab-pane active" id="tabGeneral">
                <!-- Package Main Hero Card -->
                <div class="v3-edit-card hero-section-card">
                    <div class="v3-edit-card-body">
                        <div class="package-name-hero-field">
                            <label class="v3-form-label" for="name">
                                Package Title / Tour Name <span class="req">*</span>
                            </label>
                            <input id="name" name="packageName" class="v3-hero-input" type="text" value="" required placeholder="e.g. Haridwar & Rishikesh Tour Package 3N4D" />
                            <span class="v3-input-hint"><i class="fi fi-rr-info"></i> This title appears prominently on customer quotes, PDFs, and itineraries.</span>
                        </div>
                    </div>
                </div>

                <!-- Parameters & Logistics 2-Column Grid -->
                <div class="v3-form-dual-cards">
                    <!-- Left: Stay & Experience Parameters -->
                    <div class="v3-edit-card">
                        <div class="v3-edit-card-header">
                            <div class="v3-card-title-wrap">
                                <div class="v3-card-icon indigo"><i class="fi fi-rr-bed"></i></div>
                                <div>
                                    <h3 class="v3-card-title">Stay & Theme Attributes</h3>
                                    <p class="v3-card-subtitle">Duration, travel style, and accommodation quality.</p>
                                </div>
                            </div>
                        </div>
                        <div class="v3-edit-card-body">
                            <div class="v3-stack-fields">
                                <!-- Trip Duration Info Box -->
                                <div class="v3-duration-info-box">
                                    <div class="v3-duration-icon"><i class="fi fi-rr-clock"></i></div>
                                    <div class="v3-duration-text">
                                        <div class="v3-duration-label">Tour Duration</div>
                                        <div class="v3-duration-val" id="durationDisplay">0 Nights / 1 Days</div>
                                    </div>
                                </div>

                                <!-- Travel Style -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="travelStyle">Travel Style / Theme <span class="req">*</span></label>
                                    <select class="selectJS form-control v3-select" name="travelStyle" id="travelStyle">
                                        <option value="select">Select travel style</option>
                                    </select>
                                </div>

                                <!-- Hotel Type -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="hotel">Hotel / Accommodation Standard <span class="req">*</span></label>
                                    <select class="selectJS form-control v3-select" name="hotel" id="hotel">
                                        <option value="select">Select hotel type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Transportation & Vehicles -->
                    <div class="v3-edit-card">
                        <div class="v3-edit-card-header">
                            <div class="v3-card-title-wrap">
                                <div class="v3-card-icon emerald"><i class="fi fi-rr-car"></i></div>
                                <div>
                                    <h3 class="v3-card-title">Transportation & Vehicles</h3>
                                    <p class="v3-card-subtitle">Default vehicles assigned for transit & sightseeing.</p>
                                </div>
                            </div>
                        </div>
                        <div class="v3-edit-card-body">
                            <div class="v3-stack-fields">
                                <!-- Helper Callout in Front Row -->
                                <div class="v3-helper-box">
                                    <i class="fi fi-rr-shield-check"></i>
                                    <span>Vehicles can also be customized dynamically during individual quote generation.</span>
                                </div>

                                <!-- Outstation Vehicle -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="outstationVehicle">
                                        <i class="fi fi-rr-road" style="color: #6366f1; margin-right: 4px;"></i> Outstation Vehicle <span class="req">*</span>
                                    </label>
                                    <select class="selectJS form-control v3-select" name="outstationVehicle" id="outstationVehicle">
                                        <option value="select">Select vehicle</option>
                                    </select>
                                </div>

                                <!-- Local Sightseeing Vehicle -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="localSighseeignVehicle">
                                        <i class="fi fi-rr-camera" style="color: #0284c7; margin-right: 4px;"></i> Local Sightseeing Vehicle <span class="req">*</span>
                                    </label>
                                    <select class="selectJS form-control v3-select" name="localSighseeignVehicle" id="localSighseeignVehicle">
                                        <option value="select">Select vehicle</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overview Rich Editor Card -->
                <div class="v3-edit-card">
                    <div class="v3-edit-card-header">
                        <div class="v3-card-title-wrap">
                            <div class="v3-card-icon sky"><i class="fi fi-rr-document-signed"></i></div>
                            <div>
                                <h3 class="v3-card-title">Tour Overview & Journey Highlights</h3>
                                <p class="v3-card-subtitle">Comprehensive summary giving travelers an enticing preview of their vacation.</p>
                            </div>
                        </div>
                    </div>
                    <div class="v3-edit-card-body">
                        <div class="v3-editor-container">
                            <div class="editor" id="overviewEditor" style="min-height: 180px"></div>
                            <textarea name="overview" id="editor-0" style="display:none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="v3-tab-footer-actions">
                    <button type="button" class="btn-v3-next-tab" data-next="#tabItinerary">
                        Continue to Day-Wise Itinerary <i class="fi fi-rr-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- TAB 2: Day-Wise Itinerary Breakdown -->
            <div class="edit-tab-pane" id="tabItinerary">
                <!-- Sticky Day Quick Switcher & Toolbar -->
                <div class="v3-days-quick-bar">
                    <div class="days-bar-left">
                        <div class="days-bar-title"><i class="fi fi-rr-apps"></i> Jump to:</div>
                        <div class="days-pill-list" id="daysPillList"></div>
                    </div>
                    <div class="days-bar-right">
                        <button type="button" class="btn-days-toggle-all" id="btnExpandAllDays" onclick="if(window.toggleAllDayCards){window.toggleAllDayCards(this); return false;}">
                            <i class="fi fi-rr-compress-alt"></i> Collapse All Days
                        </button>
                    </div>
                </div>

                <!-- Day Cards Stack -->
                <div class="v3-days-container" id="daysContainer"></div>

                <div class="v3-tab-footer-actions">
                    <button type="button" class="btn-v3-prev-tab" data-prev="#tabGeneral">
                        <i class="fi fi-rr-arrow-left"></i> Back to General
                    </button>
                    <button type="button" class="btn-v3-next-tab" data-next="#tabPolicies">
                        Continue to Inclusions & Exclusions <i class="fi fi-rr-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- TAB 3: Inclusions & Exclusions -->
            <div class="edit-tab-pane" id="tabPolicies">
                <div class="v3-edit-card">
                    <div class="v3-edit-card-header">
                        <div class="v3-card-title-wrap">
                            <div class="v3-card-icon amber"><i class="fi fi-rr-list-check"></i></div>
                            <div>
                                <h3 class="v3-card-title">Inclusions & Exclusions</h3>
                                <p class="v3-card-subtitle">Explicitly state what is included in the base pricing and what requires separate payment.</p>
                            </div>
                        </div>
                    </div>
                    <div class="v3-edit-card-body">
                        <div class="v3-split-grid">
                            <!-- Inclusions -->
                            <div class="v3-split-column">
                                <div class="v3-column-header inclusions">
                                    <i class="fi fi-rr-check-circle"></i> What's Included
                                </div>
                                <div class="v3-editor-container">
                                    <div class="editor" id="inclusionsEditor" style="min-height: 200px"></div>
                                    <textarea name="inclusion" id="editor-inclusion" style="display:none"></textarea>
                                </div>
                            </div>

                            <!-- Exclusions -->
                            <div class="v3-split-column">
                                <div class="v3-column-header exclusions">
                                    <i class="fi fi-rr-cross-circle"></i> What's Excluded
                                </div>
                                <div class="v3-editor-container">
                                    <div class="editor" id="exclusionsEditor" style="min-height: 200px"></div>
                                    <textarea name="exclusion" id="editor-exclusion" style="display:none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="v3-tab-footer-actions">
                    <button type="button" class="btn-v3-prev-tab" data-prev="#tabItinerary">
                        <i class="fi fi-rr-arrow-left"></i> Back to Itinerary
                    </button>
                </div>
            </div>

            <!-- Sticky Bottom Action Toolbar -->
            <div class="v3-edit-sticky-bar">
                <div class="v3-sticky-left">
                    <span class="v3-status-pill"><i class="fi fi-rr-info"></i> All updates immediately sync with client itinerary generation</span>
                </div>
                <div class="v3-sticky-right">
                    <a href="/dashboard/packages/domestic/list/" class="btn-v3-secondary" id="btnCancelEdit">
                        Cancel
                    </a>
                    <button type="button" class="btn-v3-primary" id="updateTourPackageBtn">
                        <i class="fi fi-rr-disk"></i> Update Itinerary
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Scoped V3 Package Edit Styles */
.package-edit-wrapper {
    padding-bottom: 100px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Nav Tabs Bar */
.edit-nav-tabs-wrapper {
    margin-bottom: 20px;
}

.edit-nav-tabs {
    display: flex;
    gap: 12px;
    background: #ffffff;
    padding: 8px 12px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
}

.edit-tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    background: transparent;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-tab-btn:hover {
    color: #0284c7;
    background: #f0f9ff;
}

.edit-tab-btn.active {
    color: #0284c7;
    background: #e0f2fe;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.1);
}

.edit-tab-btn .tab-badge {
    background: #0284c7;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

.edit-tab-btn.active .tab-badge {
    background: #0369a1;
}

/* Tab Panes */
.edit-tab-pane {
    display: none;
}

.edit-tab-pane.active {
    display: block;
}

/* Hero Title Section */
.hero-section-card {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-top: 3px solid #0284c7;
}

.package-name-hero-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.v3-hero-input {
    width: 100%;
    height: 52px;
    padding: 12px 18px;
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    transition: all 0.2s ease;
    outline: none;
    box-sizing: border-box;
}

.v3-hero-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
}

.v3-input-hint {
    font-size: 12px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* 2-Column Dual Cards */
.v3-form-dual-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 900px) {
    .v3-form-dual-cards {
        grid-template-columns: 1fr;
    }
}

.v3-stack-fields {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* Duration Mini Callout */
.v3-duration-info-box {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.v3-duration-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.v3-duration-text {
    flex: 1;
}

.v3-duration-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.v3-duration-val {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

/* Helper Box in Front Row */
.v3-helper-box {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    font-size: 12px;
    color: #166534;
    line-height: 1.4;
}

/* Standard Cards */
.v3-edit-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.v3-edit-card:hover {
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
}

.v3-edit-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    background: #fafbfc;
    border-bottom: 1px solid #f1f5f9;
}

.v3-card-title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
}

.v3-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.v3-card-icon.indigo {
    background: #eef2ff;
    color: #6366f1;
}

.v3-card-icon.sky {
    background: #e0f2fe;
    color: #0284c7;
}

.v3-card-icon.emerald {
    background: #ecfdf5;
    color: #10b981;
}

.v3-card-icon.amber {
    background: #fffbeb;
    color: #f59e0b;
}

.v3-card-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}

.v3-card-subtitle {
    font-size: 13px;
    color: #64748b;
    margin: 2px 0 0 0;
}

.v3-edit-card-body {
    padding: 24px;
}

.v3-form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.v3-form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
}

.v3-form-label-sm {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.v3-form-label .req {
    color: #ef4444;
    font-weight: 700;
}

.v3-input {
    width: 100%;
    height: 44px;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 500;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    transition: all 0.2s ease;
    outline: none;
    box-sizing: border-box;
}

.v3-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}

.v3-select {
    width: 100% !important;
    height: 44px !important;
    border-radius: 10px !important;
}

/* Tab 2: Sticky Days Quick Bar & Toolbar */
.v3-days-quick-bar {
    position: -webkit-sticky;
    position: sticky;
    top: 55px;
    z-index: 95;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 18px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.days-bar-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.days-bar-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}

.days-pill-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.day-jump-pill {
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    background: #f8fafc;
    color: #0284c7;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.day-jump-pill:hover, .day-jump-pill.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);
    text-decoration: none;
}

.btn-days-toggle-all {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-days-toggle-all:hover {
    background: #f1f5f9;
    color: #1e293b;
}

/* Day Cards */
.v3-days-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.v3-day-card {
    scroll-margin-top: 135px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    transition: all 0.2s ease;
}

.v3-day-card:hover {
    border-color: #94a3b8;
}

.v3-day-card.day-card-highlight {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.25) !important;
}

.v3-day-card.is-collapsed {
    background: #fafbfc;
}

.v3-day-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 4px;
}

.v3-day-card:not(.is-collapsed) .v3-day-card-header {
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}

.v3-day-badge {
    padding: 8px 14px;
    background: #0284c7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}

.v3-day-title-input-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.v3-day-input-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}

.v3-day-title-input {
    height: 44px !important;
    font-weight: 600;
    font-size: 15px !important;
}

.v3-day-header-actions {
    display: flex;
    align-items: center;
}

.v3-day-accordion-toggle {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.v3-day-accordion-toggle:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Inclusions & Exclusions Split Grid */
.v3-split-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 768px) {
    .v3-split-grid {
        grid-template-columns: 1fr;
    }
}

.v3-column-header {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f1f5f9;
}

.v3-column-header.inclusions {
    color: #059669;
    border-bottom-color: #a7f3d0;
}

.v3-column-header.exclusions {
    color: #dc2626;
    border-bottom-color: #fecaca;
}

/* Quill Editor Container */
.v3-editor-container .ql-toolbar {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-color: #cbd5e1;
    background: #f8fafc;
}

.v3-editor-container .ql-container {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    border-color: #cbd5e1;
    background: #ffffff;
    font-size: 14px;
    color: #1e293b;
}

/* Tab Navigation Buttons in Body */
.v3-tab-footer-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.btn-v3-next-tab, .btn-v3-prev-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-v3-next-tab {
    background: #0284c7;
    color: #ffffff;
    border: none;
}

.btn-v3-next-tab:hover {
    background: #0369a1;
}

.btn-v3-prev-tab {
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.btn-v3-prev-tab:hover {
    background: #f1f5f9;
}

/* Sticky Action Toolbar */
.v3-edit-sticky-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 260px);
    right: 0;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
    padding: 14px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 999;
    backdrop-filter: blur(8px);
}

@media (max-width: 991px) {
    .v3-edit-sticky-bar {
        left: 0;
        padding: 12px 18px;
    }
}

.v3-sticky-left {
    display: flex;
    align-items: center;
}

.v3-status-pill {
    font-size: 13px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.v3-sticky-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-v3-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    transition: all 0.2s ease;
}

.btn-v3-primary:hover {
    background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
    transform: translateY(-1px);
}

.btn-v3-secondary {
    display: inline-flex;
    align-items: center;
    background: #f8fafc;
    color: #475569;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-v3-secondary:hover {
    background: #f1f5f9;
    color: #1e293b;
    text-decoration: none;
}
</style>