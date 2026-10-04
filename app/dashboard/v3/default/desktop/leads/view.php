<?php

if (isset($site_access_type) && $site_access_type === 'digital_marketing') {
    echo "Invalid Access";
    exit();
}

$packageId = isset($site_package_id) ? (int)$site_package_id : (defined('SITE_PACKAGE_ID') ? (int)SITE_PACKAGE_ID : 7);
if (!defined('SITE_PACKAGE_ID')) {
    define('SITE_PACKAGE_ID', $packageId);
}
$isPackageSix = ($packageId === 6 || (defined('SITE_PACKAGE_ID') && (int)SITE_PACKAGE_ID === 6));

$rawLeadId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
if (empty($rawLeadId) && isset($urlStructure) && is_array($urlStructure)) {
    $lastSeg = end($urlStructure);
    if (!empty($lastSeg) && $lastSeg !== 'view') {
        $rawLeadId = $lastSeg;
    }
}
?>
<div id="leadViewApp" class="lead-view-wrapper<?php echo $isPackageSix ? ' is-package-six' : ''; ?>"
    data-lead-id="<?php echo htmlspecialchars($rawLeadId); ?>"
    data-user-id="<?php echo htmlspecialchars(defined('USER_ID') ? USER_ID : 0); ?>"
    data-user-role="<?php echo htmlspecialchars(isset($site_access_type) ? $site_access_type : 'administrator'); ?>"
    data-package-id="<?php echo $packageId; ?>"
    data-is-package-six="<?php echo $isPackageSix ? '1' : '0'; ?>"
    data-site-name="<?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : ''); ?>"
    data-site-logo="<?php echo htmlspecialchars(defined('SITE_LOGO') ? SITE_LOGO : ''); ?>"
    data-site-domain="<?php echo htmlspecialchars(defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : '')); ?>"
    data-site-theme="<?php echo htmlspecialchars(defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#e76317'); ?>"
    data-site-bg="<?php echo htmlspecialchars(defined('SITE_BACKGROUND_COLOUR') ? SITE_BACKGROUND_COLOUR : '#fb923c'); ?>">
    <!-- Dynamic Content Container Populated via /api/v3/leads/view.php -->
    <div id="leadViewMainContainer">
        <div class="v3-leads-table-loader">
            <div class="v3-table-spinner">
                <div class="v3-spinner-ring"></div>
                <div class="v3-spinner-inner-dot"></div>
            </div>
            <h4 class="v3-loader-title">Loading Lead Details...</h4>
            <p class="v3-loader-subtitle">Fetching customer profile, timeline, and booking records.</p>
        </div>
    </div>
</div>

<!-- Modal: Edit Lead Profile & Travel Details -->
<div class="v3-modal-overlay" id="modalEditLead">
    <div class="v3-modal-dialog v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-edit">
                    <i class="fi fi-rr-edit"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Edit Lead & Travel Details</h4>
                    <span class="v3-modal-subtitle">Update customer information and tour requirements</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body v3-modal-body-scrollable">
            <input type="hidden" id="editLeadId" value="<?php echo htmlspecialchars($rawLeadId); ?>">
            <h5 class="v3-modal-subheading">Customer Information</h5>
            <div class="v3-form-grid-2">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">First Name *</label>
                    <input type="text" id="editLeadFirstName" class="v3-modal-input" placeholder="e.g. Rohit">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Last Name</label>
                    <input type="text" id="editLeadLastName" class="v3-modal-input" placeholder="e.g. Sharma">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Email Address</label>
                    <input type="email" id="editLeadEmail" class="v3-modal-input" placeholder="e.g. rohit@gmail.com">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Primary Mobile *</label>
                    <input type="text" id="editLeadMobile" class="v3-modal-input" placeholder="e.g. 9876543210">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Alternate Mobile</label>
                    <input type="text" id="editLeadAltMobile" class="v3-modal-input" placeholder="e.g. 9876543211">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Current City / Location</label>
                    <select id="editLeadCurrentLocation" class="v3-modal-select"></select>
                </div>
            </div>

            <h5 class="v3-modal-subheading">Travel Logistics</h5>
            <div class="v3-form-grid-2">
                <div class="v3-modal-form-group">
                    <div class="v3-form-label-row">
                        <label class="v3-modal-label">Tour Package / Destination *</label>
                        <span class="v3-label-hint">Select package or type custom destination</span>
                    </div>
                    <select id="editLeadPackage" class="v3-modal-select v3-package-select" data-placeholder="Choose a tour package or type custom destination..."></select>
                    
                    <!-- Selected Package Preview Badge in Modal -->
                    <div id="editLeadSelectedPkgCard" class="v3-selected-pkg-card" style="display: none; margin-top: 8px;">
                        <div class="v3-pkg-card-left">
                            <div class="v3-pkg-card-icon" id="editCardPkgIcon">
                                <i class="fi fi-rr-sparkles"></i>
                            </div>
                            <div class="v3-pkg-card-info">
                                <div class="v3-pkg-card-title-row">
                                    <span class="v3-pkg-card-name" id="editCardPkgTitle">Package Title</span>
                                    <span class="v3-pkg-type-pill" id="editCardPkgTypeBadge">Domestic</span>
                                </div>
                                <div class="v3-pkg-card-tags">
                                    <span class="v3-pkg-tag" id="editCardPkgDuration"><i class="fi fi-rr-moon"></i> <strong>4N / 5D</strong></span>
                                    <span class="v3-pkg-tag" id="editCardPkgLocation"><i class="fi fi-rr-marker"></i> Location</span>
                                    <span class="v3-pkg-tag theme" id="editCardPkgTheme"><i class="fi fi-rr-star"></i> Standard</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-clear-pkg-card" id="editBtnClearPkgCard" title="Clear selection">✕</button>
                    </div>
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Package / Subject</label>
                    <input type="text" id="editLeadSubject" class="v3-modal-input" placeholder="e.g. Dubai Tour Package">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Date of Travel</label>
                    <input type="text" id="editLeadTravelDate" class="v3-modal-input v3-single-datepicker-input" data-future-only="true" placeholder="YYYY-MM-DD" autocomplete="off" readonly>
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Trip Duration (Nights)</label>
                    <input type="number" id="editLeadDuration" class="v3-modal-input" placeholder="e.g. 5" min="0">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Pickup Point</label>
                    <select id="editLeadPickup" class="v3-modal-select"></select>
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Drop Point</label>
                    <select id="editLeadDrop" class="v3-modal-select"></select>
                </div>
            </div>

            <h5 class="v3-modal-subheading">Guests Breakdown</h5>
            <div class="v3-form-grid-3">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Adults</label>
                    <input type="number" id="editLeadAdults" class="v3-modal-input" min="1" value="1">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Children</label>
                    <input type="number" id="editLeadChildren" class="v3-modal-input" min="0" value="0">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Infants</label>
                    <input type="number" id="editLeadInfants" class="v3-modal-input" min="0" value="0">
                </div>
            </div>

            <div class="v3-modal-form-group">
                <div class="v3-form-label-row">
                    <label class="v3-modal-label">
                        <i class="fi fi-rr-list-check"></i> Special Notes &amp; Form Data
                    </label>
                    <div class="v3-req-modal-actions">
                        <button type="button" class="btn-v3-action btn-outline btn-xs" id="btnAddReqRow" title="Add a custom requirement or note">
                            <i class="fi fi-rr-plus"></i> Add Note
                        </button>
                    </div>
                </div>
                <div id="editLeadRequirementsTabularWrap" class="v3-edit-req-tabular-wrap">
                    <!-- Populated dynamically as tabular key mapper -->
                </div>
                <textarea id="editLeadRequirements" class="v3-modal-textarea is-hidden" placeholder="Add specific requirements (e.g. 4-star hotel, vegetarian food, beach view, or JSON object)..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalEditLead">
                <i class="fi fi-rr-check"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Modal: Full Screen Send & Customize Itinerary Cover -->
<div class="v3-modal-overlay" id="modalSendItinerary">
    <div class="v3-modal-dialog v3-modal-dialog-fullscreen">
        <!-- Sticky Fullscreen Header -->
        <div class="v3-modal-fullscreen-header">
            <div class="v3-modal-fullscreen-title-wrap">
                <div class="v3-modal-header-icon-box">
                    <i class="fi fi-rr-route"></i>
                </div>
                <div class="v3-modal-header-text">
                    <h4 class="v3-modal-title">Tour Itinerary & Proposal Builder</h4>
                    <span class="v3-modal-lead-chip" id="itinModalLeadBadge">Lead Itinerary</span>
                </div>
            </div>
            <div class="v3-modal-fullscreen-actions">
                <button type="button" class="btn-v3-action btn-outline v3-hidden" id="btnChangeItinerary">
                    <i class="fi fi-rr-refresh"></i> Change Itinerary
                </button>
                <button type="button" class="btn-v3-action btn-green v3-hidden" id="btnPreviewItinerary">
                    <i class="fi fi-rr-eye"></i> Preview Itinerary
                </button>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close (Press Esc)"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>

        <!-- Scrollable Fullscreen Canvas Body -->
        <div class="v3-modal-fullscreen-body">
            <div class="v3-itin-canvas-container">

                <!-- 1. Search & Template Selector -->
                <div class="v3-itin-section-card" id="itinerarySearchSection">
                    <div class="v3-itin-section-header">
                        <div class="v3-itin-section-title">
                            <i class="fi fi-rr-search"></i>
                            <span>1. Search & Select Itinerary Template</span>
                        </div>
                    </div>
                    <div class="v3-itinerary-search-row">
                        <div class="v3-modal-form-group">
                            <label class="v3-modal-label"><i class="fi fi-rr-marker"></i> Destination / Country / City *</label>
                            <select class="form-control v3-select-full" id="itineraryDestinationInput"></select>
                        </div>
                        <div class="v3-modal-form-group">
                            <label class="v3-modal-label"><i class="fi fi-rr-compass"></i> Travel Style / Theme</label>
                            <select class="leads-filter-select v3-select-full" id="itineraryTravelStyle">
                                <option value="">All Travel Styles</option>
                                <option value="adventure">Adventure Travel</option>
                                <option value="cultural">Cultural Travel</option>
                                <option value="luxury">Luxury Travel</option>
                                <option value="honeymoon">Honeymoon Travel</option>
                                <option value="couple">Couple Travel</option>
                                <option value="family">Family Travel</option>
                                <option value="budget">Budget Travel</option>
                                <option value="religious">Religious Travel</option>
                            </select>
                        </div>
                        <div class="v3-itinerary-search-btn-wrap">
                            <button type="button" class="btn-search-packages-cta" id="btnFindItineraries">
                                <i class="fi fi-rr-search"></i> Search Packages
                            </button>
                        </div>
                    </div>

                    <!-- Inline Search Spinner -->
                    <div id="itinerarySearchLoader" class="v3-itinerary-inline-loader v3-hidden">
                        <div class="v3-table-spinner">
                            <div class="v3-spinner-ring"></div>
                        </div>
                        <span id="itinerarySearchLoaderText">Searching matching tour packages...</span>
                    </div>

                    <!-- Inline No Package Found Box -->
                    <div id="itineraryNotFoundBox" class="v3-itinerary-not-found-box v3-hidden">
                        <div class="v3-not-found-left">
                            <div class="v3-not-found-icon-wrap">
                                <i class="fi fi-rr-info"></i>
                            </div>
                            <div>
                                <h5 class="v3-not-found-title">No Predefined Packages Found</h5>
                                <p class="v3-not-found-desc" id="itineraryNotFoundDesc">No package templates match this destination and travel style.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-v3-action btn-outline" id="btnStartCustomItinFromEmpty">
                            <i class="fi fi-rr-plus"></i> Create Custom Itinerary
                        </button>
                    </div>

                    <!-- Package Select Row -->
                    <div id="itineraryResultContainer" class="v3-hidden v3-mt-14">
                        <div class="v3-itinerary-package-select-card">
                            <div class="v3-package-select-header">
                                <label class="v3-modal-label"><i class="fi fi-rr-box"></i> Choose Package Template to Customize *</label>
                                <span class="v3-packages-count-badge" id="itineraryPackageCountBadge">0 Packages</span>
                            </div>
                            <select id="itineraryPackageSelect" class="leads-filter-select v3-select-full"></select>
                        </div>
                    </div>
                </div>

                <!-- 2. Main Customization Form -->
                <div id="itineraryCustomSection" class="v3-hidden v3-itin-custom-canvas">

                    <!-- Section A: Overview & Pricing Configuration -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-settings-sliders"></i>
                                <span>2. General Package & Quotation Pricing</span>
                            </div>
                        </div>

                        <div class="v3-itin-pricing-config-wrap">
                            <!-- Itinerary Title & Travel Date Row -->
                            <div class="v3-form-grid-2 v3-mb-20">
                                <div class="v3-modal-form-group">
                                    <label class="v3-modal-label"><i class="fi fi-rr-edit"></i> Itinerary Title *</label>
                                    <input type="text" id="itineraryCustomTitle" class="v3-modal-input v3-modal-input-lg" placeholder="e.g. 5 Nights 6 Days Spectacular Dubai Holiday">
                                </div>
                                <div class="v3-modal-form-group">
                                    <label class="v3-modal-label"><i class="fi fi-rr-calendar"></i> Travel Date / Departure *</label>
                                    <input type="text" id="itineraryCustomTravelDate" class="v3-modal-input v3-modal-input-lg v3-single-datepicker-input" data-future-only="true" placeholder="Select departure date" readonly style="cursor:pointer;background:#fff;">
                                </div>
                            </div>

                            <!-- Pickup, Drop & Travel Theme Row -->
                            <div class="v3-form-grid-3 v3-mb-20">
                                <div class="v3-modal-form-group">
                                    <label class="v3-modal-label"><i class="fi fi-rr-map-marker"></i> Pickup Location</label>
                                    <select id="itineraryCustomPickup" class="leads-filter-select v3-select-full"></select>
                                </div>
                                <div class="v3-modal-form-group">
                                    <label class="v3-modal-label"><i class="fi fi-rr-map-marker-home"></i> Drop Location</label>
                                    <select id="itineraryCustomDrop" class="leads-filter-select v3-select-full"></select>
                                </div>
                                <div class="v3-modal-form-group">
                                    <label class="v3-modal-label"><i class="fi fi-rr-compass"></i> Travel Theme / Style</label>
                                    <select id="itineraryCustomTheme" class="leads-filter-select v3-select-full">
                                        <option value="standard">Standard / General</option>
                                        <option value="family">Family Vacation</option>
                                        <option value="honeymoon">Honeymoon & Romantic</option>
                                        <option value="adventure">Adventure & Trekking</option>
                                        <option value="cultural">Cultural & Heritage</option>
                                        <option value="luxury">Luxury & Premium</option>
                                        <option value="spiritual">Spiritual & Pilgrimage</option>
                                        <option value="wildlife">Wildlife & Nature</option>
                                        <option value="beach">Beach & Relaxation</option>
                                        <option value="budget">Budget Friendly</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Commercials & Pricing Breakdown Card -->
                            <div class="v3-itin-pricing-panel">
                                <div class="v3-itin-pricing-panel-header">
                                    <div class="v3-itin-pricing-panel-title">
                                        <i class="fi fi-rr-receipt"></i> Commercials & Quotation Breakdown
                                    </div>
                                    <span class="v3-itin-pricing-panel-hint" id="itinTravelerCountBadge">Auto-calculates per traveler</span>
                                </div>

                                <div class="v3-itin-commercials-grid">
                                    <!-- Total Quotation Price Hero Field -->
                                    <div class="v3-itin-price-hero-card">
                                        <div class="v3-price-hero-top">
                                            <span class="v3-price-hero-label">Total Quotation Value (₹) *</span>
                                            <span class="v3-calc-badge"><i class="fi fi-rr-calculator"></i> Auto</span>
                                        </div>
                                        <div class="v3-input-currency-group hero-currency-group">
                                            <span class="v3-currency-addon">₹</span>
                                            <input type="number" id="itineraryCustomTotalPrice" class="v3-modal-input v3-currency-input bold-price" placeholder="0">
                                        </div>
                                        <span class="v3-hero-subtext">Includes all travelers & taxes</span>
                                    </div>

                                    <!-- Adults Card -->
                                    <div class="v3-itin-traveler-price-card">
                                        <div class="v3-price-label-wrap">
                                            <div class="v3-traveler-type-title">
                                                <i class="fi fi-rr-user"></i> Adults
                                            </div>
                                            <span class="v3-age-tag">12+ yrs</span>
                                        </div>
                                        <div class="v3-traveler-fields-row">
                                            <div class="v3-field-col count-col">
                                                <label class="v3-sub-field-label">Pax</label>
                                                <input type="number" id="itineraryCustomAdultCount" class="v3-modal-input v3-count-input" min="1" value="2" placeholder="2">
                                            </div>
                                            <div class="v3-field-col rate-col">
                                                <label class="v3-sub-field-label">Rate / Adult (₹)</label>
                                                <div class="v3-input-currency-group">
                                                    <span class="v3-currency-addon">₹</span>
                                                    <input type="number" id="itineraryCustomAdultPrice" class="v3-modal-input v3-currency-input" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Children Card -->
                                    <div class="v3-itin-traveler-price-card">
                                        <div class="v3-price-label-wrap">
                                            <div class="v3-traveler-type-title">
                                                <i class="fi fi-rr-child"></i> Children
                                            </div>
                                            <span class="v3-age-tag">2-12 yrs</span>
                                        </div>
                                        <div class="v3-traveler-fields-row">
                                            <div class="v3-field-col count-col">
                                                <label class="v3-sub-field-label">Pax</label>
                                                <input type="number" id="itineraryCustomChildCount" class="v3-modal-input v3-count-input" min="0" value="0" placeholder="0">
                                            </div>
                                            <div class="v3-field-col rate-col">
                                                <label class="v3-sub-field-label">Rate / Child (₹)</label>
                                                <div class="v3-input-currency-group">
                                                    <span class="v3-currency-addon">₹</span>
                                                    <input type="number" id="itineraryCustomChildPrice" class="v3-modal-input v3-currency-input" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Infants Card -->
                                    <div class="v3-itin-traveler-price-card">
                                        <div class="v3-price-label-wrap">
                                            <div class="v3-traveler-type-title">
                                                <i class="fi fi-rr-baby"></i> Infants
                                            </div>
                                            <span class="v3-age-tag">&lt;2 yrs</span>
                                        </div>
                                        <div class="v3-traveler-fields-row">
                                            <div class="v3-field-col count-col">
                                                <label class="v3-sub-field-label">Pax</label>
                                                <input type="number" id="itineraryCustomInfantCount" class="v3-modal-input v3-count-input" min="0" value="0" placeholder="0">
                                            </div>
                                            <div class="v3-field-col rate-col">
                                                <label class="v3-sub-field-label">Rate / Infant (₹)</label>
                                                <div class="v3-input-currency-group">
                                                    <span class="v3-currency-addon">₹</span>
                                                    <input type="number" id="itineraryCustomInfantPrice" class="v3-modal-input v3-currency-input" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Overview & Highlights -->
                            <div class="v3-modal-form-group v3-mt-16">
                                <label class="v3-modal-label"><i class="fi fi-rr-document"></i> Tour Overview & Highlights</label>
                                <textarea id="itineraryCustomOverview" class="v3-modal-textarea" rows="3" placeholder="Brief introduction and trip summary for the traveler..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section B: Interactive Day-Wise Breakdown -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-calendar"></i>
                                <span>3. Detailed Day-Wise Itinerary, Accommodation & Transport</span>
                            </div>
                            <div class="v3-flex-gap-8">
                                <div class="v3-itin-bulk-hotel-toggle" title="Quickly show or hide hotel details across all days">
                                    <span class="v3-bulk-hotel-label">Hotels:</span>
                                    <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnBulkShowHotels"><i class="fi fi-rr-eye"></i> Show All</button>
                                    <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnBulkHideHotels"><i class="fi fi-rr-eye-crossed"></i> Hide All</button>
                                </div>
                                <button type="button" class="btn-v3-action btn-outline" id="btnAddItinDayTop">
                                    <i class="fi fi-rr-plus"></i> Add Day
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Days Stack -->
                        <div class="v3-itin-days-stack" id="itinDaysStack">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <div class="v3-mt-16">
                            <button type="button" class="v3-btn-add-day-full" id="btnAddItinDayBottom">
                                <i class="fi fi-rr-plus"></i> Add Another Day to Itinerary
                            </button>
                        </div>
                    </div>

                    <!-- Section C: Inclusions, Exclusions & Notes -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-list-check"></i>
                                <span>4. Inclusions, Exclusions & Special Terms</span>
                            </div>
                        </div>

                        <div class="v3-form-grid-2">
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label"><i class="fi fi-rr-check-circle v3-text-green"></i> What's Included <span class="v3-modal-hint">(One item per line)</span></label>
                                <textarea id="itineraryCustomInclusions" class="v3-modal-textarea" rows="5" placeholder="• Hotel accommodation with breakfast&#10;• Airport & sightseeing transfers&#10;• All toll taxes and parking charges"></textarea>
                            </div>
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label"><i class="fi fi-rr-cross-circle v3-text-red"></i> What's Excluded <span class="v3-modal-hint">(One item per line)</span></label>
                                <textarea id="itineraryCustomExclusions" class="v3-modal-textarea" rows="5" placeholder="• Airfare / Train tickets&#10;• Personal expenses & tips&#10;• Entry fees and camera tickets"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Footer Preview CTA Bar -->
                    <div class="v3-itin-footer-actions">
                        <button type="button" class="btn-v3-action btn-green btn-v3-preview-lg" id="btnPreviewItineraryBottom">
                            <i class="fi fi-rr-eye"></i> Preview Itinerary Proposal
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Real Itinerary Proposal Live Preview -->
<div class="v3-modal-overlay" id="modalItineraryLivePreview">
    <div class="v3-modal-dialog v3-modal-dialog-preview">
        <!-- Preview Header -->
        <div class="v3-preview-modal-header">
            <div class="v3-preview-header-left">
                <div class="v3-preview-header-icon">
                    <i class="fi fi-rr-eye"></i>
                </div>
                <div class="v3-preview-header-meta">
                    <div class="v3-preview-header-title-wrap">
                        <h4 class="v3-preview-header-title" id="livePreviewHeaderTitle">Custom Tour Proposal</h4>
                        <span class="v3-preview-badge"><i class="fi fi-rr-sparkles"></i> Live Proposal Preview</span>
                    </div>
                    <span class="v3-preview-subtitle">Client-ready presentation & Multi-Page A4 PDF document</span>
                </div>
            </div>
            <div class="v3-preview-header-actions">
                <button type="button" class="btn-v3-action btn-outline" id="btnLivePreviewDownloadPdf">
                    <i class="fi fi-rr-file-pdf"></i> Download PDF
                </button>
                <button type="button" class="btn-v3-action btn-green" id="btnLivePreviewSendItinerary">
                    <i class="fi fi-rr-paper-plane"></i> Send Itinerary to Customer
                </button>
                <button type="button" class="v3-modal-close-btn v3-preview-close-trigger" title="Close Preview (Esc)"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>
        <!-- Scrollable Document Preview Body -->
        <div class="v3-preview-modal-body">
            <div id="livePreviewLoader" class="v3-itinerary-preview-loader v3-hidden">
                <div class="v3-table-spinner">
                    <div class="v3-spinner-ring"></div>
                </div>
                <span>Rendering template proposal...</span>
            </div>
            <iframe id="livePreviewIframe" class="v3-live-preview-iframe" title="Itinerary Live Proposal Preview"></iframe>
        </div>
    </div>
</div>

<!-- Modal: Set Follow-Up Reminder -->
<div class="v3-modal-overlay" id="modalSetFollowUp">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-followup">
                    <i class="fi fi-rr-alarm-clock"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Schedule Follow-Up</h4>
                    <span class="v3-modal-subtitle">Set callback reminder and call objective</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Reason / Objective *</label>
                <select id="modalFollowUpReason" class="leads-filter-select v3-select-full">
                    <option value="Interested - Price Discussion">Interested - Price Discussion</option>
                    <option value="Itinerary Customization Request">Itinerary Customization Request</option>
                    <option value="Client Asked to Call Later">Client Asked to Call Later</option>
                    <option value="Mobile Not Reachable / Busy">Mobile Not Reachable / Busy</option>
                    <option value="Payment Confirmation Follow-up">Payment Confirmation Follow-up</option>
                    <option value="General Follow-up" selected>General Follow-up</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Follow-Up Schedule *</label>
                <div class="v3-followup-schedule-card">
                    <div class="v3-followup-presets-wrap">
                        <span class="v3-preset-label"><i class="fi fi-rr-bolt"></i> Quick Presets:</span>
                        <div class="v3-preset-chips" data-target="#modalFollowUpTime">
                            <button type="button" class="v3-preset-chip" data-preset="in_2_hours"><i class="fi fi-rr-bolt"></i> In 2 Hours</button>
                            <button type="button" class="v3-preset-chip active" data-preset="tomorrow_morning"><i class="fi fi-rr-sun"></i> Tomorrow 10:30 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_afternoon"><i class="fi fi-rr-sun"></i> Tomorrow 3:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_evening"><i class="fi fi-rr-sunset"></i> Tomorrow 6:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="in_2_days"><i class="fi fi-rr-calendar"></i> In 2 Days 11:00 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="next_monday"><i class="fi fi-rr-calendar-clock"></i> Next Mon 10:30 AM</button>
                        </div>
                    </div>
                    <div class="v3-datetime-split-grid">
                        <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="modalFollowUpDate"><i class="fi fi-rr-calendar"></i> Date</label>
                            <div class="v3-native-input-wrap">
                                <input type="date" id="modalFollowUpDate" class="v3-native-datetime-input">
                            </div>
                        </div>
                        <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="modalFollowUpTimeInput"><i class="fi fi-rr-clock"></i> Time</label>
                            <div class="v3-native-input-wrap">
                                <input type="time" id="modalFollowUpTimeInput" class="v3-native-datetime-input">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="modalFollowUpTime" value="">
                    <div class="v3-time-quick-pills" data-target="#modalFollowUpTimeInput">
                        <span class="v3-time-pills-label">Popular slots:</span>
                        <button type="button" class="v3-time-pill" data-time="10:00">10:00 AM</button>
                        <button type="button" class="v3-time-pill active" data-time="10:30">10:30 AM</button>
                        <button type="button" class="v3-time-pill" data-time="14:30">02:30 PM</button>
                        <button type="button" class="v3-time-pill" data-time="16:00">04:00 PM</button>
                        <button type="button" class="v3-time-pill" data-time="18:00">06:00 PM</button>
                        <button type="button" class="v3-time-pill" data-time="19:30">07:30 PM</button>
                    </div>
                    <div class="v3-slot-status-box" id="modalSlotStatus"></div>
                </div>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Notes / Instructions</label>
                <textarea id="modalFollowUpComment" class="v3-modal-textarea" placeholder="Add specific follow-up points..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalFollowUp">Set Reminder</button>
        </div>
    </div>
</div>

<!-- Modal: Send Payment Link -->
<div class="v3-modal-overlay" id="modalSendPaymentLink">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-link">
                    <i class="fi fi-rr-link"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Send Payment Link</h4>
                    <span class="v3-modal-subtitle">Generate deposit link for instant online collection</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Quotation Price</label>
                <div id="modalQuotationPrice" class="v3-modal-quote-price">₹ 0</div>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Amount to Collect (₹) *</label>
                <input type="number" id="modalPaymentAmountInput" class="v3-modal-input" placeholder="e.g. 15000">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Link Notes</label>
                <textarea id="modalPaymentLinkNote" class="v3-modal-textarea" placeholder="e.g. Advance deposit for booking confirmation"></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-purple" id="btnSubmitModalSendPaymentLink">Send Payment Link</button>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div class="v3-modal-overlay" id="modalUpdatePayment">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-payment">
                    <i class="fi fi-rr-indian-rupee-sign"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Record Payment Received</h4>
                    <span class="v3-modal-subtitle">Log cash, UPI, or bank transfer confirmation</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Amount (₹) *</label>
                <input type="number" id="modalPayAmount" class="v3-modal-input" placeholder="e.g. 25000">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Method *</label>
                <select id="modalPayMethod" class="leads-filter-select v3-select-full">
                    <option value="UPI / QR">UPI / QR Code</option>
                    <option value="Net Banking">Net Banking (NEFT/IMPS)</option>
                    <option value="Credit / Debit Card">Credit / Debit Card</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque / DD">Cheque / Demand Draft</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Date *</label>
                <input type="text" id="modalPayDate" class="v3-modal-input v3-single-datepicker-input" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" readonly>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transaction / UTR Reference Number</label>
                <input type="text" id="modalPayRef" class="v3-modal-input" placeholder="e.g. UTR192837465">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Remarks / Description</label>
                <textarea id="modalPayNotes" class="v3-modal-textarea" placeholder="Payment remarks..."></textarea>
            </div>
            <div class="v3-modal-form-group">
                <div class="v3-form-label-row">
                    <label class="v3-modal-label">Payment Receipt / Screenshot</label>
                    <span class="v3-label-hint">Optional (PNG, JPG, PDF)</span>
                </div>
                <div class="v3-file-upload-box" id="payReceiptUploadBox">
                    <input type="file" id="modalPayFile" name="files" class="v3-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, application/pdf">
                    <label for="modalPayFile" class="v3-file-upload-label" id="modalPayFileLabel">
                        <i class="fi fi-rr-cloud-upload"></i>
                        <span class="v3-file-upload-text" id="modalPayFileText">Click to upload payment receipt or screenshot</span>
                    </label>
                    <button type="button" class="btn-clear-uploaded-file v3-hidden" id="btnClearPayFile" title="Remove file">✕</button>
                </div>
                <div id="modalPayFilePreview" class="v3-file-preview-row v3-hidden"></div>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalPayment">Record Payment</button>
        </div>
    </div>
</div>

<!-- Modal: Close Lead -->
<div class="v3-modal-overlay" id="modalCloseLead">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-close">
                    <i class="fi fi-rr-cross-circle"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Close, Cancel, or Drop Lead</h4>
                    <span class="v3-modal-subtitle">Specify outcome status and reason for archiving lead</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Outcome / Target Status *</label>
                <select id="modalCloseStatus" class="leads-filter-select v3-select-full">
                    <option value="15" selected>Closed (15)</option>
                    <option value="16">Cancelled (16)</option>
                    <option value="17">Lost (17)</option>
                    <option value="4">Failed (4)</option>
                    <option value="18">Payment Refund (18)</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Reason *</label>
                <select id="modalCloseReason" class="leads-filter-select v3-select-full">
                    <option value="">Select reason</option>
                    <option value="Not interested">Not Interested</option>
                    <option value="Purchased Elsewhere">Purchased Elsewhere / Competitor</option>
                    <option value="Budget Constraint">Budget Constraint</option>
                    <option value="Trip Cancelled / Postponed">Trip Cancelled / Postponed</option>
                    <option value="Unqualified Lead">Unqualified Lead</option>
                    <option value="Fake / Invalid Enquiry">Fake / Invalid Enquiry</option>
                    <option value="Client Not Responding">Client Not Responding</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Final Conclusion Notes</label>
                <textarea id="modalCloseMsg" class="v3-modal-textarea" placeholder="Provide additional details on why this lead is being closed or archived..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-red" id="btnSubmitModalCloseLead">Confirm &amp; Archive</button>
        </div>
    </div>
</div>

<!-- Modal: View Payment Receipt & Breakdown -->
<div class="v3-modal-overlay" id="modalViewPaymentReceipt">
    <div class="v3-modal-dialog v3-modal-dialog-receipt v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-payment">
                    <i class="fi fi-rr-receipt"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Payment Confirmation & Receipt</h4>
                    <span class="v3-modal-subtitle" id="receiptModalSubtitle">Transaction Details</span>
                </div>
            </div>
            <div class="v3-modal-header-right-group">
                <div class="v3-receipt-nav-bar" id="receiptNavBar">
                    <button type="button" class="btn-receipt-nav" id="btnReceiptNavPrev" title="Previous Payment (Left Arrow)">
                        <i class="fi fi-rr-angle-left"></i>
                    </button>
                    <span class="v3-receipt-nav-counter" id="receiptNavCounter">1 of 1</span>
                    <button type="button" class="btn-receipt-nav" id="btnReceiptNavNext" title="Next Payment (Right Arrow)">
                        <i class="fi fi-rr-angle-right"></i>
                    </button>
                </div>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close (Press Esc)"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>
        <div class="v3-receipt-modal-content-wrap">
            <!-- Left Side: Image / Receipt Preview -->
            <div class="v3-receipt-col-left" id="receiptPreviewPane">
                <div class="v3-receipt-img-container" id="receiptImgContainer">
                    <!-- Dynamic preview image or PDF placeholder -->
                </div>
            </div>
            <!-- Right Side: Structured Payment Information -->
            <div class="v3-receipt-col-right" id="receiptInfoPane">
                <div class="v3-receipt-amount-card">
                    <span class="v3-receipt-amount-label">Amount Received</span>
                    <h2 class="v3-receipt-amount-val" id="receiptModalAmount">₹ 0</h2>
                    <span class="v3-lead-badge badge-confirmed" id="receiptModalStatusBadge"><i class="fi fi-rr-check"></i> Recorded</span>
                </div>

                <div class="v3-receipt-details-list">
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-calendar"></i> Payment Date</span>
                        <span class="v3-receipt-detail-val" id="receiptModalDate">-</span>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-credit-card"></i> Payment Method</span>
                        <span class="v3-receipt-detail-val" id="receiptModalMethod">-</span>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-hashtag"></i> Transaction / UTR Ref</span>
                        <div class="v3-receipt-ref-row">
                            <span class="v3-receipt-detail-val monospace" id="receiptModalRef">-</span>
                            <button type="button" class="btn-copy-receipt-ref" id="btnCopyReceiptRef" title="Copy Reference"><i class="fi fi-rr-copy"></i></button>
                        </div>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-user"></i> Recorded By</span>
                        <span class="v3-receipt-detail-val" id="receiptModalRecordedBy">-</span>
                    </div>
                    <div class="v3-receipt-detail-item full-width" id="receiptModalNotesWrap">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-comment-alt"></i> Remarks / Description</span>
                        <p class="v3-receipt-notes-text" id="receiptModalNotes">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="v3-modal-footer">
            <a href="#" target="_blank" class="btn-v3-action btn-outline" id="receiptModalDownloadBtn" download>
                <i class="fi fi-rr-download"></i> Download Image
            </a>
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
        </div>
    </div>
</div>

<script src="<?php echo ADMIN_JS ?>lead-view.js?v=<?php echo time(); ?>"></script>