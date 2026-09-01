<?php

$rawId = $_GET['id'] ?? 0;
if (empty($rawId) && isset($urlStructure) && is_array($urlStructure)) {
    $lastSeg = end($urlStructure);
    if (!empty($lastSeg) && $lastSeg !== 'edit' && $lastSeg !== 'domestic' && $lastSeg !== 'international') {
        $rawId = $lastSeg;
    }
}
if (empty($rawId) && isset($id)) {
    $rawId = $id;
}
if (is_numeric($rawId) && (int)$rawId > 0) {
    $id = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $id = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;
}
$encodedId = is_numeric($id) && (int)$id > 0 ? base64_encode($id) : $rawId;

// Discover Available Itinerary Proposal Templates dynamically from templates/itinerary/
$itineraryTemplatesDir = realpath(__DIR__ . '/../../templates/itinerary');
$availableTemplates = [];
if ($itineraryTemplatesDir && is_dir($itineraryTemplatesDir)) {
    foreach (scandir($itineraryTemplatesDir) as $folder) {
        if ($folder === '.' || $folder === '..') continue;
        $folderPath = $itineraryTemplatesDir . '/' . $folder;
        if (is_dir($folderPath) && (file_exists($folderPath . '/theme.json') || file_exists($folderPath . '/index.php'))) {
            $tData = [];
            if (file_exists($folderPath . '/theme.json')) {
                $jsonStr = file_get_contents($folderPath . '/theme.json');
                $tData = json_decode($jsonStr, true) ?: [];
            }
            $tData['id'] = $tData['id'] ?? $folder;
            $tData['name'] = $tData['name'] ?? ucfirst($folder);
            $tData['title'] = $tData['title'] ?? ($tData['name'] . ' Proposal');
            $tData['description'] = $tData['description'] ?? 'Custom proposal template.';
            $tData['badge'] = $tData['badge'] ?? ($tData['tag'] ?? '');
            $tData['icon'] = $tData['icon'] ?? 'fi fi-rr-document';
            $tData['order'] = isset($tData['order']) ? (int)$tData['order'] : 99;
            $availableTemplates[] = $tData;
        }
    }
    usort($availableTemplates, function($a, $b) { 
        return ($a['order'] ?? 99) <=> ($b['order'] ?? 99); 
    });
}

// Fallback if empty
if (empty($availableTemplates)) {
    $availableTemplates = [
        ['id' => 'mit', 'name' => 'MIT Proposal', 'title' => 'MIT Multi-Page Proposal', 'badge' => 'Default', 'description' => 'Multi-page proposal with dynamic waves & PDF export.', 'icon' => 'fi fi-rr-document-signed', 'tags' => [['icon' => 'fi fi-rr-document', 'label' => 'Multi-Page A4'], ['icon' => 'fi fi-rr-file-pdf', 'label' => 'PDF Ready']]],
        ['id' => 'standard', 'name' => 'Standard Timeline', 'title' => 'Standard Timeline Proposal', 'badge' => 'Clean', 'description' => 'Day-by-day vertical timeline with compact summary.', 'icon' => 'fi fi-rr-list-timeline', 'tags' => [['icon' => 'fi fi-rr-list-timeline', 'label' => 'Timeline'], ['icon' => 'fi fi-rr-apps', 'label' => 'Compact']]],
        ['id' => 'modern', 'name' => 'Modern Cards', 'title' => 'Modern Card Proposal', 'badge' => 'Visual', 'description' => 'Modular cards with visual hotel badges & meal highlights.', 'icon' => 'fi fi-rr-grid', 'tags' => [['icon' => 'fi fi-rr-grid', 'label' => 'Card Blocks'], ['icon' => 'fi fi-rr-sparkles', 'label' => 'Highlights']]],
        ['id' => 'aleya', 'name' => 'Aleya Luxury', 'title' => 'Aleya Luxury Proposal', 'badge' => 'Luxury', 'description' => 'Gold luxury theme with gallery lightbox, stays & A4 PDF export.', 'icon' => 'fi fi-rr-crown', 'tags' => [['icon' => 'fi fi-rr-crown', 'label' => 'Gold Theme'], ['icon' => 'fi fi-rr-picture', 'label' => 'Gallery']]]
    ];
}
?>
<script>
    window.V3_AVAILABLE_TEMPLATES = <?php echo json_encode($availableTemplates); ?>;
</script>

<div class="package-page-wrapper package-edit-wrapper" id="packageEditApp" data-package-id="<?php echo $id; ?>" data-encoded-id="<?php echo $encodedId; ?>" data-package-type="international">

    <!-- Loading State -->
    <div id="packageEditLoading" class="package-edit-state-loading">
        <div class="package-edit-loading-spinner"></div>
        <h4 class="package-edit-loading-title">Loading Package Details...</h4>
        <p class="package-edit-loading-desc">Please wait while we retrieve the international itinerary.</p>
    </div>

    <!-- Error State -->
    <div id="packageEditError" class="package-edit-state-error">
        <i class="fi fi-rr-exclamation error-icon"></i>
        <h3 class="error-title">Tour Package Not Found</h3>
        <p id="packageErrorText" class="error-desc">The requested international package could not be retrieved or has been removed.</p>
        <a href="/dashboard/packages/international/list/" class="btn-package-return">
            <i class="fi fi-rr-arrow-left"></i> Return to Packages List
        </a>
    </div>

    <!-- Edit Package Content Body -->
    <div id="packageEditContent" class="package-edit-content">
        <!-- Header Banner -->
        <div class="package-header-bar">
            <div class="package-header-left">
                <div class="package-header-icon">
                    <i class="fi fi-rr-edit"></i>
                </div>
                <div class="package-header-titles">
                    <h2 id="pkgHeaderTitle">Tour Package</h2>
                    <div class="pkg-meta-row">
                        <span class="pkg-theme-pill pkg-theme-standard" id="pkgMetaTheme">Standard</span>
                        <span class="pkg-duration-badge" id="pkgMetaDuration">
                            <i class="fi-rr-moon icon-moon"></i> 0N / <i class="fi-rr-sun icon-sun"></i> 1D
                        </span>
                        <span class="pkg-duration-badge pkg-location-badge" id="pkgMetaLocation">
                            <i class="fi-rr-marker icon-marker"></i> International
                        </span>
                    </div>
                </div>
            </div>
            <div class="package-header-actions">
                <a href="#" target="_blank" class="btn-gallery-action btn-view-live" id="btnViewLivePackage" title="View Live Package" style="display: none;">
                    <i class="fi fi-rr-eye"></i> View Live
                </a>
                <a href="/dashboard/packages/international/gallery/?id=<?php echo $encodedId; ?>" class="btn-gallery-action btn-manage-gallery" id="btnManageGallery" title="Manage Gallery Photos">
                    <i class="fi fi-rr-gallery"></i> Manage Gallery
                </a>
                <a href="/dashboard/packages/international/list/" class="btn-gallery-action btn-back-list" id="btnBackToList" title="Back to International List">
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
            <input type="hidden" name="packageType" id="packageType" value="international">
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
                            <input id="name" name="packageName" class="v3-hero-input" type="text" value="" required placeholder="e.g. Dubai & Abu Dhabi Highlights 4N5D" />
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
                                    <p class="v3-card-subtitle">Travel style, template, and accommodation standard.</p>
                                </div>
                            </div>
                        </div>
                        <div class="v3-edit-card-body">
                            <div class="v3-stack-fields">
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
                                <!-- Outstation Vehicle -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="outstationVehicle">
                                        <i class="fi fi-rr-road icon-road"></i> Outstation Vehicle <span class="req">*</span>
                                    </label>
                                    <select class="selectJS form-control v3-select" name="outstationVehicle" id="outstationVehicle">
                                        <option value="select">Select vehicle</option>
                                    </select>
                                </div>

                                <!-- Local Sightseeing Vehicle -->
                                <div class="v3-form-field">
                                    <label class="v3-form-label" for="localSighseeignVehicle">
                                        <i class="fi fi-rr-camera icon-camera"></i> Local Sightseeing Vehicle <span class="req">*</span>
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
                            <div class="editor" id="overviewEditor"></div>
                            <textarea name="overview" id="editor-0" class="d-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Template Selection Cards -->
                <div class="v3-edit-card template-selection-card">
                    <div class="v3-edit-card-header">
                        <div class="v3-card-title-wrap">
                            <div class="v3-card-icon fuchsia"><i class="fi fi-rr-layout-fluid"></i></div>
                            <div>
                                <h3 class="v3-card-title">Itinerary & Proposal Template Layout</h3>
                                <p class="v3-card-subtitle">Choose the presentation layout for customer quotations, web previews, and PDF downloads.</p>
                            </div>
                        </div>
                    </div>
                    <div class="v3-edit-card-body">
                        <input type="hidden" name="tourPackageTemplate" id="tourPackageTemplate" value="mit">

                        <div class="v3-template-cards-grid" id="v3TemplateCardsGrid">
                            <?php foreach ($availableTemplates as $idx => $tmpl): 
                                $tId = htmlspecialchars($tmpl['id'] ?? 'mit');
                                $tName = htmlspecialchars($tmpl['name'] ?? ucfirst($tId));
                                $tDesc = htmlspecialchars($tmpl['description'] ?? '');
                                $tBadge = htmlspecialchars($tmpl['badge'] ?? ($tmpl['tag'] ?? ''));
                                $tBadgeStyle = !empty($tmpl['badge_style']) ? 'style="' . htmlspecialchars($tmpl['badge_style']) . '"' : '';
                                $tIcon = htmlspecialchars($tmpl['icon'] ?? 'fi fi-rr-document');
                                $tIconStyle = !empty($tmpl['icon_style']) ? 'style="' . htmlspecialchars($tmpl['icon_style']) . '"' : '';
                                $isActive = ($tId === 'mit' || $idx === 0) ? ' active' : '';
                            ?>
                                <div class="v3-template-card<?php echo $isActive; ?>" data-template-id="<?php echo $tId; ?>">
                                    <div class="v3-template-card-top">
                                        <div class="v3-template-icon-box" <?php echo $tIconStyle; ?>>
                                            <i class="<?php echo $tIcon; ?>"></i>
                                        </div>
                                        <div class="v3-template-header-info">
                                            <div class="v3-template-title-row">
                                                <h4 class="v3-template-name"><?php echo $tName; ?></h4>
                                                <?php if (!empty($tBadge)): ?>
                                                    <span class="v3-template-pill-badge" <?php echo $tBadgeStyle; ?>><?php echo $tBadge; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="v3-template-desc"><?php echo $tDesc; ?></p>
                                        </div>
                                        <div class="v3-template-radio">
                                            <i class="fi fi-rr-check"></i>
                                        </div>
                                    </div>
                                    <div class="v3-template-card-bottom">
                                        <div class="v3-template-mini-tags">
                                            <?php if (!empty($tmpl['tags']) && is_array($tmpl['tags'])): ?>
                                                <?php foreach ($tmpl['tags'] as $tag): ?>
                                                    <span class="v3-temp-tag">
                                                        <i class="<?php echo htmlspecialchars($tag['icon'] ?? 'fi fi-rr-tag'); ?>"></i> 
                                                        <?php echo htmlspecialchars($tag['label'] ?? ''); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="v3-temp-tag"><i class="fi fi-rr-document"></i> Proposal</span>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="btn-template-preview" data-template-id="<?php echo $tId; ?>" title="Preview <?php echo $tName; ?>">
                                            <i class="fi fi-rr-eye"></i> <span>Preview</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Website Publishing Option Card -->
                <div class="v3-edit-card" id="cardWebsitePublish">
                    <div class="v3-edit-card-body" style="padding: 16px 20px;">
                        <label class="v3-publish-checkbox-label" for="chkPublishToWebsite">
                            <div class="v3-publish-checkbox-left">
                                <div class="v3-publish-icon-box">
                                    <i class="fi fi-rr-globe"></i>
                                </div>
                                <div class="v3-publish-text-wrap">
                                    <h4 class="v3-publish-title">Publish to Website</h4>
                                    <p class="v3-publish-desc">Show and make this tour package bookable on your live website.</p>
                                </div>
                            </div>
                            <div class="v3-publish-checkbox-control">
                                <input type="checkbox" id="chkPublishToWebsite" name="publishToWebsite" value="1" checked class="v3-custom-checkbox">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="v3-tab-footer-actions">
                    <button type="button" class="btn-v3-primary" id="btnUpdateGeneral">
                        <i class="fi fi-rr-disk"></i> Update Itinerary
                    </button>
                </div>
            </div>

            <!-- TAB 2: Day-Wise Itinerary Breakdown -->
            <div class="edit-tab-pane" id="tabItinerary">
                <!-- Sticky Day Quick Switcher & Toolbar -->
                <div class="v3-days-quick-bar">
                    <div class="days-bar-left">
                        <!-- Accordion Jumper Dropdown -->
                        <div class="v3-jumper-dropdown-wrap">
                            <button type="button" class="btn-jumper-accordion" id="btnJumperDropdown" aria-expanded="false">
                                <i class="fi fi-rr-apps"></i>
                                <span id="currentActiveDayLabel">Jump to: <strong id="activeDayText">Day 1</strong></span>
                                <i class="fi fi-rr-angle-small-down jumper-arrow"></i>
                            </button>
                            <div class="v3-jumper-accordion-menu" id="jumperAccordionMenu">
                                <div class="jumper-menu-header">
                                    <span>Select Itinerary Day</span>
                                    <span class="badge-day-count" id="jumperMenuDayCount">0 Days</span>
                                </div>
                                <div class="jumper-menu-items" id="jumperMenuItems"></div>
                            </div>
                        </div>

                        <!-- Horizontal Quick Day Pills -->
                        <div class="days-pill-list" id="daysPillList"></div>
                    </div>
                    <div class="days-bar-right">
                        <button type="button" class="btn-add-day-top" id="btnAddDayTop">
                            <i class="fi fi-rr-plus"></i> Add Day
                        </button>
                        <button type="button" class="btn-days-toggle-all" id="btnExpandAllDays">
                            <i class="fi fi-rr-compress-alt"></i> Collapse All
                        </button>
                    </div>
                </div>

                <!-- Day Cards Stack -->
                <div class="v3-days-container" id="daysContainer"></div>

                <!-- Add Day Bottom CTA -->
                <button type="button" class="btn-add-day-bottom" id="btnAddDayBottom">
                    <i class="fi fi-rr-plus"></i> Add Another Day to Itinerary
                </button>

                <!-- Update All Days Footer Actions -->
                <div class="v3-tab-footer-actions v3-itinerary-footer-actions">
                    <button type="button" class="btn-v3-primary btn-update-all-days" id="btnUpdateAllDays">
                        <i class="fi fi-rr-disk"></i> Update All Days Details
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
                                    <span><i class="fi fi-rr-check-circle"></i> What's Included</span>
                                </div>
                                <div class="v3-editor-container">
                                    <div class="editor" id="inclusionsEditor"></div>
                                    <textarea name="inclusion" id="editor-inclusion" class="d-none"></textarea>
                                </div>
                            </div>

                            <!-- Exclusions -->
                            <div class="v3-split-column">
                                <div class="v3-column-header exclusions">
                                    <span><i class="fi fi-rr-cross-circle"></i> What's Excluded</span>
                                </div>
                                <div class="v3-editor-container">
                                    <div class="editor" id="exclusionsEditor"></div>
                                    <textarea name="exclusion" id="editor-exclusion" class="d-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="v3-tab-footer-actions">
                    <button type="button" class="btn-v3-primary" id="btnUpdatePolicies">
                        <i class="fi fi-rr-disk"></i> Update Inclusions & Exclusions
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Template Live Preview Modal -->
    <div id="modalPackageTemplatePreview" class="v3-modal-overlay">
        <div class="v3-modal-dialog v3-modal-dialog-preview">
            <div class="v3-preview-modal-header">
                <div class="v3-preview-header-left">
                    <span class="v3-preview-badge"><i class="fi fi-rr-layout-fluid"></i> Template Preview</span>
                    <h3 class="v3-preview-header-title" id="previewModalTitle">Template Preview</h3>
                    <span class="v3-preview-counter-pill" id="previewTemplateCounter">1 / 4</span>
                </div>
                <div class="v3-preview-header-actions">
                    <div class="v3-preview-nav-btns">
                        <button type="button" class="btn-v3-action btn-outline btn-prev-template" id="btnPrevPreviewTemplate" title="Previous Template (Left Arrow)">
                            <i class="fi fi-rr-angle-left"></i> Previous
                        </button>
                        <button type="button" class="btn-v3-action btn-outline btn-next-template" id="btnNextPreviewTemplate" title="Next Template (Right Arrow)">
                            Next <i class="fi fi-rr-angle-right"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-v3-action btn-green" id="btnApplyPreviewedTemplate">
                        <i class="fi fi-rr-check"></i> Use This Template
                    </button>
                    <button type="button" class="btn-v3-action btn-outline" id="btnCloseTemplatePreviewModal" title="Close Preview (Esc)">
                        <i class="fi fi-rr-cross"></i> Close
                    </button>
                </div>
            </div>
            <div class="v3-preview-modal-body">
                <div id="templatePreviewCanvas" class="itinerary-mit-preview-container">
                    <!-- Live preview pages dynamically rendered here -->
                </div>
            </div>
        </div>
    </div>

</div>