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

$loggedInUsername = '';
if (!empty($_COOKIE['user_name'])) {
    $loggedInUsername = trim((string)base64_decode($_COOKIE['user_name']));
}
if (empty($loggedInUsername) && !empty($_SESSION['user_name'])) {
    $loggedInUsername = trim((string)$_SESSION['user_name']);
}
if (empty($loggedInUsername) && !empty($_SESSION['user_username'])) {
    $loggedInUsername = trim((string)$_SESSION['user_username']);
}
if (empty($loggedInUsername) && defined('USER_ID') && (int)USER_ID > 0 && isset($connection)) {
    $uRes = mysqli_query($connection, "SELECT p.profile_display_name, u.user_username FROM " . TABLE_PREFIX . "user u LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id WHERE u.user_id = " . (int)USER_ID . " LIMIT 1");
    if ($uRes && $uRow = mysqli_fetch_assoc($uRes)) {
        $loggedInUsername = !empty($uRow['profile_display_name']) ? trim($uRow['profile_display_name']) : trim($uRow['user_username']);
    }
}

$companyNature = defined('COMPANY_NATURE') ? strtolower(COMPANY_NATURE) : 'travel_agency';
$leadQuotationAction = defined('LEAD_QUOTATION_ACTION') ? strtolower(LEAD_QUOTATION_ACTION) : 'auto';
$leadViewOpts = [
    'show_payment_link' => true,
    'show_record_payment' => true,
    'show_convert_sale' => true,
    'show_call_button' => true,
    'show_whatsapp_button' => true,
    'show_send_itinerary' => true,
    'show_send_quotation' => true
];
$leadViewSiteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
if ($leadViewSiteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $leadViewSiteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($leadViewSiteId <= 0) {
    $leadViewSiteId = 1;
}
$leadViewTbl = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
if ($leadViewSiteId > 0 && isset($connection)) {
    $lvRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$leadViewTbl}site_configuration WHERE site_configuration_site_id = {$leadViewSiteId} AND site_configuration_status = 1 AND site_configuration_key IN ('company_nature', 'lead_view_show_payment_link', 'lead_view_show_record_payment', 'lead_view_show_convert_sale', 'lead_view_show_call_button', 'lead_view_show_whatsapp_button', 'lead_view_show_send_itinerary', 'lead_view_show_send_quotation')");
    if ($lvRes && mysqli_num_rows($lvRes) > 0) {
        while ($lvRow = mysqli_fetch_assoc($lvRes)) {
            $k = $lvRow['site_configuration_key'];
            $v = $lvRow['site_configuration_value'];
            if ($k === 'company_nature' && !empty($v)) $companyNature = strtolower($v);
            if ($k === 'lead_view_show_payment_link') $leadViewOpts['show_payment_link'] = ($v !== '0');
            if ($k === 'lead_view_show_record_payment') $leadViewOpts['show_record_payment'] = ($v !== '0');
            if ($k === 'lead_view_show_convert_sale') $leadViewOpts['show_convert_sale'] = ($v !== '0');
            if ($k === 'lead_view_show_call_button') $leadViewOpts['show_call_button'] = ($v !== '0');
            if ($k === 'lead_view_show_whatsapp_button') $leadViewOpts['show_whatsapp_button'] = ($v !== '0');
            if ($k === 'lead_view_show_send_itinerary') $leadViewOpts['show_send_itinerary'] = ($v !== '0');
            if ($k === 'lead_view_show_send_quotation') $leadViewOpts['show_send_quotation'] = ($v !== '0');
        }
    }
}
?>
<div id="leadViewApp" class="lead-view-wrapper<?php echo $isPackageSix ? ' is-package-six' : ''; ?>"
    data-lead-id="<?php echo htmlspecialchars($rawLeadId); ?>"
    data-user-id="<?php echo htmlspecialchars(defined('USER_ID') ? USER_ID : 0); ?>"
    data-user-name="<?php echo htmlspecialchars($loggedInUsername); ?>"
    data-user-role="<?php echo htmlspecialchars(isset($site_access_type) ? $site_access_type : 'administrator'); ?>"
    data-package-id="<?php echo $packageId; ?>"
    data-is-package-six="<?php echo $isPackageSix ? '1' : '0'; ?>"
    data-company-nature="<?php echo htmlspecialchars($companyNature); ?>"
    data-lead-action="<?php echo htmlspecialchars($leadQuotationAction); ?>"
    data-lead-view-options='<?php echo json_encode($leadViewOpts); ?>'
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
                    <h4 class="v3-modal-title" id="editLeadModalTitle">Edit Lead & Travel Details</h4>
                    <span class="v3-modal-subtitle" id="editLeadModalSubtitle">Update customer information and requirements</span>
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

            <!-- Logistics Section (Dynamically tailored for Transporter vs Travel Agency) -->
            <h5 class="v3-modal-subheading" id="editLeadLogisticsHeading">Travel Logistics</h5>
            <div class="v3-form-grid-2" id="editLeadLogisticsGrid">
                <!-- Route: Pickup Point -->
                <div class="v3-modal-form-group" id="editLeadPickupWrap">
                    <label class="v3-modal-label" id="editLeadPickupLabel">Pickup Point / City *</label>
                    <select id="editLeadPickup" class="v3-modal-select" data-placeholder="Search or enter pickup location..."></select>
                </div>
                <!-- Route: Drop Point -->
                <div class="v3-modal-form-group" id="editLeadDropWrap">
                    <label class="v3-modal-label" id="editLeadDropLabel">Drop Point / City *</label>
                    <select id="editLeadDrop" class="v3-modal-select" data-placeholder="Search or enter drop location..."></select>
                </div>
                <!-- Transporter Only: Destination City -->
                <div class="v3-modal-form-group only-transporter" id="editLeadDestinationWrap">
                    <label class="v3-modal-label">Destination City</label>
                    <select id="editLeadDestination" class="v3-modal-select" data-placeholder="Search or enter destination city..."></select>
                </div>
                <!-- Transporter Only: Trip Type -->
                <div class="v3-modal-form-group only-transporter" id="editLeadTripTypeWrap">
                    <label class="v3-modal-label">Trip Type</label>
                    <select id="editLeadTripType" class="v3-modal-select">
                        <option value="outstation_round">Outstation (Round Trip)</option>
                        <option value="outstation_oneway">Outstation (One Way)</option>
                        <option value="local_rental">Local City Rental</option>
                        <option value="airport_transfer">Airport Transfer</option>
                    </select>
                </div>
                <!-- Travel Agency Only: Tour Package / Destination -->
                <div class="v3-modal-form-group only-travel-agency" id="editLeadPackageWrap">
                    <div class="v3-form-label-row">
                        <label class="v3-modal-label">Tour Package / Destination *</label>
                        <span class="v3-label-hint">Select package or type custom destination</span>
                    </div>
                    <select id="editLeadPackage" class="v3-modal-select v3-package-select" data-placeholder="Choose a tour package or type custom destination..."></select>
                    
                    <!-- Selected Package Preview Badge in Modal -->
                    <div id="editLeadSelectedPkgCard" class="v3-selected-pkg-card v3-hidden v3-mt-8">
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
                <!-- Travel Agency Only: Package / Subject -->
                <div class="v3-modal-form-group only-travel-agency" id="editLeadSubjectWrap">
                    <label class="v3-modal-label">Package / Subject</label>
                    <input type="text" id="editLeadSubject" class="v3-modal-input" placeholder="e.g. Dubai Tour Package">
                </div>
                <!-- Common: Date of Travel (Calendar Datepicker, past dates disabled) -->
                <div class="v3-modal-form-group" id="editLeadTravelDateWrap">
                    <label class="v3-modal-label">Date of Travel</label>
                    <input type="text" id="editLeadTravelDate" class="v3-modal-input v3-single-datepicker-input" data-future-only="true" placeholder="YYYY-MM-DD" autocomplete="off" readonly>
                </div>
                <!-- Common: Trip Duration -->
                <div class="v3-modal-form-group" id="editLeadDurationWrap">
                    <label class="v3-modal-label" id="editLeadDurationLabel">Trip Duration (Days)</label>
                    <input type="number" id="editLeadDuration" class="v3-modal-input" placeholder="e.g. 3" min="1" value="1">
                </div>
                <!-- Transporter Only: Vehicle Category -->
                <div class="v3-modal-form-group only-transporter" id="editLeadVehicleTypeWrap">
                    <label class="v3-modal-label">Vehicle Category</label>
                    <select id="editLeadVehicleType" class="v3-modal-select">
                        <option value="all">All Fleet Categories</option>
                        <option value="sedan">Sedan (Dzire, Etios, Aura)</option>
                        <option value="suv">SUV (Innova, Crysta, Ertiga)</option>
                        <option value="hatchback">Hatchback (WagonR, Swift)</option>
                        <option value="tempo">Tempo Traveller (12/17/20 Seater)</option>
                        <option value="luxury">Luxury Car (BMW, Mercedes, Audi)</option>
                        <option value="bus">Mini Bus / Coach</option>
                    </select>
                </div>
                <!-- Transporter Only: Preferred Vehicle Model -->
                <div class="v3-modal-form-group only-transporter" id="editLeadVehicleModelWrap">
                    <label class="v3-modal-label">Preferred Vehicle Model</label>
                    <select id="editLeadVehicleModel" class="v3-modal-select" data-placeholder="Choose vehicle model...">
                        <option value="">-- Choose Vehicle Model --</option>
                    </select>
                </div>
            </div>

            <!-- Passengers & Capacity Breakdown -->
            <h5 class="v3-modal-subheading" id="editLeadGuestsHeading">Passengers &amp; Capacity</h5>
            <div class="v3-form-grid-3">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label" id="editLeadAdultsLabel">Passengers / Adults</label>
                    <input type="number" id="editLeadAdults" class="v3-modal-input" min="1" value="1">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Children</label>
                    <input type="number" id="editLeadChildren" class="v3-modal-input" min="0" value="0">
                </div>
                <div class="v3-modal-form-group only-transporter" id="editLeadLuggageBagsWrap">
                    <label class="v3-modal-label">Luggage Bags</label>
                    <input type="number" id="editLeadLuggageBags" class="v3-modal-input" min="0" value="0">
                </div>
                <div class="v3-modal-form-group only-travel-agency" id="editLeadInfantsWrap">
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
                <div class="v3-save-pkg-toggle-wrapper v3-hidden" id="wrapSaveAsNewPackage" title="Toggle to save this itinerary as a new tour package template in your library">
                    <label class="v3-save-pkg-toggle-label" for="toggleSaveAsNewPackage">
                        <span class="v3-save-pkg-text"><i class="fi fi-rr-box-alt"></i> Save as New Package</span>
                        <span class="v3-switch-slider-wrap">
                            <input type="checkbox" id="toggleSaveAsNewPackage" name="save_as_new_package" value="1">
                            <span class="v3-switch-slider"></span>
                        </span>
                        <span class="v3-save-pkg-badge" id="savePkgBadge">OFF</span>
                    </label>
                </div>
                <button type="button" class="btn-v3-action btn-outline v3-hidden" id="btnChangeItinerary">
                    <i class="fi fi-rr-refresh"></i> Change Itinerary
                </button>
                <button type="button" class="btn-v3-action btn-green v3-hidden" id="btnPreviewItinerary">
                    <i class="fi fi-rr-eye"></i> Preview Itinerary
                </button>
                <button type="button" class="v3-modal-close-btn" id="btnCloseProposalBuilder" title="Close (Press Esc)"><i class="fi fi-rr-cross"></i></button>
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
                            <select class="form-control v3-select-full" id="itineraryDestinationInput"><option></option></select>
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
                                    <input type="text" id="itineraryCustomTravelDate" class="v3-modal-input v3-modal-input-lg v3-single-datepicker-input" data-future-only="true" placeholder="Select departure date" readonly>
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
                        <h4 class="v3-preview-header-title" id="livePreviewHeaderTitle" title="Custom Tour Proposal">Custom Tour Proposal</h4>
                        <span class="v3-preview-badge"><i class="fi fi-rr-sparkles"></i> Preview</span>
                    </div>
                </div>
            </div>
            <div class="v3-preview-header-actions">
                <div class="v3-save-pkg-toggle-wrapper v3-preview-pkg-toggle v3-hidden" id="wrapSaveAsNewPackagePreview" title="Toggle to save this customized itinerary as a new tour package template in your library">
                    <label class="v3-save-pkg-toggle-label" for="toggleSaveAsNewPackagePreview">
                        <span class="v3-save-pkg-text"><i class="fi fi-rr-box-alt"></i> Save as Package</span>
                        <span class="v3-switch-slider-wrap">
                            <input type="checkbox" id="toggleSaveAsNewPackagePreview" name="save_as_new_package_preview" value="1">
                            <span class="v3-switch-slider"></span>
                        </span>
                        <span class="v3-save-pkg-badge" id="savePkgBadgePreview">OFF</span>
                    </label>
                </div>
                <button type="button" class="btn-v3-action btn-outline" id="btnLivePreviewDownloadPdf" title="Download PDF document">
                    <i class="fi fi-rr-file-pdf"></i> Download PDF
                </button>
                <button type="button" class="btn-v3-action btn-green" id="btnLivePreviewSendItinerary" title="Send proposal to customer">
                    <i class="fi fi-rr-paper-plane"></i> Send Itinerary
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
                <label class="v3-modal-label">Task Type *</label>
                <select id="modalFollowUpType" class="selectJS leads-filter-select v3-select-full">
                    <option value="Call" selected>📞 Phone Call</option>
                    <option value="WhatsApp">💬 WhatsApp Message</option>
                    <option value="Email">✉️ Email</option>
                    <option value="Meeting">🤝 In-Person Meeting</option>
                    <option value="Other">📌 Other Action</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Reason / Objective *</label>
                <select id="modalFollowUpReason" class="selectJS leads-filter-select v3-select-full">
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
                <label class="v3-modal-label">Next Action / Goal</label>
                <input type="text" id="modalFollowUpNextAction" class="v3-modal-input" placeholder="e.g. Discuss revised quote, take deposit confirmation...">
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

<!-- Modal: Log Customer Interaction -->
<div class="v3-modal-overlay" id="modalLogInteraction">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-interaction">
                    <i class="fi fi-rr-headset"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Log Customer Interaction</h4>
                    <span class="v3-modal-subtitle">Record calls, WhatsApp messages, or customer responses</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Interaction Direction &amp; Flow *</label>
                <select id="logInteractionDirection" class="selectJS leads-filter-select v3-select-full">
                    <option value="outbound" selected>📤 Outbound (Agent contacted customer)</option>
                    <option value="inbound">📥 Inbound (Customer replied / called agent)</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Channel / Type *</label>
                <select id="logInteractionType" class="selectJS leads-filter-select v3-select-full">
                    <option value="call" selected>📞 Phone Call</option>
                    <option value="whatsapp">💬 WhatsApp</option>
                    <option value="email">✉️ Email</option>
                    <option value="meeting">🤝 In-Person Meeting</option>
                    <option value="other">📌 Other</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Discussion Notes / Outcome *</label>
                <textarea id="logInteractionNotes" class="v3-modal-textarea" placeholder="Summarize the conversation or customer reply..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitLogInteraction">Log Interaction</button>
        </div>
    </div>
</div>

<!-- Modal: Complete Follow-Up Task -->
<div class="v3-modal-overlay" id="modalCompleteTask">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-complete-task">
                    <i class="fi fi-rr-check-circle"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Complete Follow-Up Task</h4>
                    <span class="v3-modal-subtitle">Record task outcome and schedule next steps</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <input type="hidden" id="completeTaskId" value="">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Follow-Up Outcome / Notes *</label>
                <textarea id="completeTaskOutcome" class="v3-modal-textarea" placeholder="Describe the result of this follow-up call/meeting..."></textarea>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-checkbox-label">
                    <input type="checkbox" id="completeTaskScheduleNext" value="1">
                    <span>Schedule next follow-up task now</span>
                </label>
            </div>
            <div id="completeTaskNextSection" class="is-hidden">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Next Task Type</label>
                    <select id="completeTaskNextType" class="leads-filter-select v3-select-full">
                        <option value="Call" selected>📞 Phone Call</option>
                        <option value="WhatsApp">💬 WhatsApp Message</option>
                        <option value="Email">✉️ Email</option>
                        <option value="Meeting">🤝 Meeting</option>
                        <option value="Other">📌 Other</option>
                    </select>
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Next Due Date &amp; Time</label>
                    <div class="v3-datetime-split-grid">
                        <div class="v3-datetime-col">
                            <input type="date" id="completeTaskNextDate" class="v3-native-datetime-input">
                        </div>
                        <div class="v3-datetime-col">
                            <input type="time" id="completeTaskNextTime" class="v3-native-datetime-input" value="11:00">
                        </div>
                    </div>
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Next Action / Goal</label>
                    <input type="text" id="completeTaskNextAction" class="v3-modal-input" placeholder="e.g. Send updated pricing...">
                </div>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitCompleteTask">Complete Task</button>
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

<!-- Modal: Transporter & Fleet Quotation Builder -->
<div class="v3-modal-overlay" id="modalSendTransporterQuotation">
    <div class="v3-modal-dialog v3-modal-dialog-xl v3-modal-dialog-tq">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-transporter">
                    <i class="fi fi-rr-bus"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Transporter Quotation &amp; Fleet Proposal</h4>
                    <span class="v3-modal-subtitle">Generate vehicle pricing, route estimates, and share with customer</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body tq-modal-body">
            <!-- 1. Customer Section -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon blue"><i class="fi fi-rr-user"></i></div>
                        <div>
                            <h4 class="tq-section-title">Customer Information</h4>
                            <p class="tq-section-subtitle">Customer identity details pre-filled from lead record</p>
                        </div>
                    </div>
                </div>
                <div class="tq-grid-2">
                    <div class="tq-form-group">
                        <label for="tqCustomerName" class="tq-form-label">Customer Name <span class="req">*</span></label>
                        <input type="text" id="tqCustomerName" class="tq-form-control" placeholder="Customer Full Name" required>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqCustomerMobile" class="tq-form-label">Mobile Number <span class="req">*</span></label>
                        <input type="tel" id="tqCustomerMobile" class="tq-form-control" placeholder="Mobile / WhatsApp Number" required>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqCustomerEmail" class="tq-form-label">Email Address</label>
                        <input type="email" id="tqCustomerEmail" class="tq-form-control" placeholder="client@example.com">
                    </div>
                    <div class="tq-form-group">
                        <label for="tqCustomerAddress" class="tq-form-label">Address / Location</label>
                        <input type="text" id="tqCustomerAddress" class="tq-form-control" placeholder="City, State, or Street Address">
                    </div>
                </div>
            </div>

            <!-- 2. Travel & Transport Section -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon teal"><i class="fi fi-rr-route"></i></div>
                        <div>
                            <h4 class="tq-section-title">Travel &amp; Transport Logistics</h4>
                            <p class="tq-section-subtitle">Origin, destination, route points, and schedule</p>
                        </div>
                    </div>
                </div>
                <div class="tq-grid-2">
                    <div class="tq-form-group">
                        <label for="tqDestination" class="tq-form-label">Destination <span class="req">*</span></label>
                        <input type="text" id="tqDestination" class="tq-form-control" placeholder="Tour destination or city" required>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqPickupLocation" class="tq-form-label">Pickup Location <span class="req">*</span></label>
                        <input type="text" id="tqPickupLocation" class="tq-form-control" placeholder="Airport, Railway Station, Hotel..." required>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqDropLocation" class="tq-form-label">Drop Location <span class="req">*</span></label>
                        <input type="text" id="tqDropLocation" class="tq-form-control" placeholder="Destination point or return city" required>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqTravelDate" class="tq-form-label">Travel Date <span class="req">*</span></label>
                        <div class="tq-input-with-icon">
                            <i class="fi fi-rr-calendar tq-input-icon"></i>
                            <input type="text" id="tqTravelDate" class="tq-form-control v3-single-datepicker-input" data-future-only="true" placeholder="YYYY-MM-DD" readonly required>
                        </div>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqTravelDuration" class="tq-form-label">Travel Duration (Days)</label>
                        <input type="number" id="tqTravelDuration" class="tq-form-control" min="1" step="1" value="1" placeholder="1">
                    </div>
                </div>
            </div>

            <!-- 3. Vehicle Section -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon amber"><i class="fi fi-rr-car-alt"></i></div>
                        <div>
                            <h4 class="tq-section-title">Vehicle &amp; Fleet Selection</h4>
                            <p class="tq-section-subtitle">Select category and vehicle model from master fleet catalog</p>
                        </div>
                    </div>
                </div>
                <div class="tq-grid-2">
                    <div class="tq-form-group">
                        <label for="tqVehicleType" class="tq-form-label">Vehicle Type / Category <span class="req">*</span></label>
                        <select id="tqVehicleType" class="tq-form-control selectJS" data-placeholder="All Categories">
                            <option value="">All Categories</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="mpv">MPV</option>
                            <option value="traveller">Tempo Traveller</option>
                            <option value="bus">Bus / Coach</option>
                            <option value="hatchback">Hatchback</option>
                            <option value="light_commercial_vehicle">Light Commercial / Van</option>
                        </select>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqVehicleModel" class="tq-form-label">Vehicle Model <span class="req">*</span></label>
                        <select id="tqVehicleModel" class="tq-form-control selectJS" data-placeholder="Choose vehicle model...">
                            <option value="">Loading fleet models...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4. Billing Section -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon emerald"><i class="fi fi-rr-receipt"></i></div>
                        <div>
                            <h4 class="tq-section-title">Billing &amp; Tariff Structure</h4>
                            <p class="tq-section-subtitle">Trip total cost, KM threshold, extra rate, and advance payment</p>
                        </div>
                    </div>
                </div>
                <div class="tq-grid-2">
                    <div class="tq-form-group">
                        <label for="tqTotalCost" class="tq-form-label">Total Cost <span class="req">*</span></label>
                        <div class="tq-input-wrap">
                            <span class="tq-input-prefix tq-currency-symbol">₹</span>
                            <input type="number" id="tqTotalCost" class="tq-form-control has-prefix" min="0" step="any" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqKmRunning" class="tq-form-label">KM Running (Daily Limit)</label>
                        <div class="tq-input-wrap">
                            <input type="number" id="tqKmRunning" class="tq-form-control has-suffix" min="1" step="1" value="250" placeholder="250">
                            <span class="tq-input-suffix">KM</span>
                        </div>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqExtraKmCharge" class="tq-form-label">Extra KM Charges</label>
                        <div class="tq-input-wrap">
                            <span class="tq-input-prefix tq-currency-symbol">₹</span>
                            <input type="number" id="tqExtraKmCharge" class="tq-form-control has-prefix has-suffix" min="0" step="any" placeholder="14.00">
                            <span class="tq-input-suffix">/ KM</span>
                        </div>
                    </div>
                    <div class="tq-form-group">
                        <label for="tqAdvancePercent" class="tq-form-label">Advance Deposit (%)</label>
                        <div class="tq-input-wrap">
                            <input type="number" id="tqAdvancePercent" class="tq-form-control has-suffix" min="0" max="100" step="1" value="25" placeholder="25">
                            <span class="tq-input-suffix">%</span>
                        </div>
                    </div>
                </div>

                <div class="tq-balance-card">
                    <div>
                        <span class="tq-balance-label">Advance Booking Deposit:</span>
                        <div class="tq-balance-val positive">
                            <span class="tq-currency-symbol">₹</span> <span id="tqAdvanceAmountDisplay">0.00</span>
                        </div>
                    </div>
                    <div>
                        <span class="tq-balance-label">Balance Payable on Trip:</span>
                        <div class="tq-balance-val">
                            <span class="tq-currency-symbol">₹</span> <span id="tqBalanceAmountDisplay">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Billing Terms Section -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon purple"><i class="fi fi-rr-shield-check"></i></div>
                        <div>
                            <h4 class="tq-section-title">Billing Terms &amp; Conditions</h4>
                            <p class="tq-section-subtitle">Toggle standard transporter policy inclusions and exclusions</p>
                        </div>
                    </div>
                </div>
                <div class="tq-terms-list">
                    <!-- Term 1: Parking Charges (Default: No) -->
                    <div class="tq-term-row">
                        <div class="tq-term-meta">
                            <h5 class="tq-term-title">1. Parking Charges</h5>
                            <p class="tq-term-desc">Airport, railway station, hotel, or monument parking fees.</p>
                        </div>
                        <div class="tq-segmented-toggle">
                            <input type="radio" name="tq_term_parking" id="tqParkingYes" value="yes">
                            <label for="tqParkingYes">Yes</label>
                            <input type="radio" name="tq_term_parking" id="tqParkingNo" value="no" checked>
                            <label for="tqParkingNo">No</label>
                        </div>
                    </div>

                    <!-- Term 2: Garage to Garage (Default: Yes) -->
                    <div class="tq-term-row">
                        <div class="tq-term-meta">
                            <h5 class="tq-term-title">2. Garage to Garage Calculation</h5>
                            <p class="tq-term-desc">Timing and distance to be calculated from garage to garage.</p>
                        </div>
                        <div class="tq-segmented-toggle">
                            <input type="radio" name="tq_term_garage" id="tqGarageYes" value="yes" checked>
                            <label for="tqGarageYes">Yes</label>
                            <input type="radio" name="tq_term_garage" id="tqGarageNo" value="no">
                            <label for="tqGarageNo">No</label>
                        </div>
                    </div>

                    <!-- Term 3: Toll Tax (Default: Yes) -->
                    <div class="tq-term-row">
                        <div class="tq-term-meta">
                            <h5 class="tq-term-title">3. Toll Tax</h5>
                            <p class="tq-term-desc">National and state expressways / highway toll plazas.</p>
                        </div>
                        <div class="tq-segmented-toggle">
                            <input type="radio" name="tq_term_toll" id="tqTollYes" value="yes" checked>
                            <label for="tqTollYes">Yes</label>
                            <input type="radio" name="tq_term_toll" id="tqTollNo" value="no">
                            <label for="tqTollNo">No</label>
                        </div>
                    </div>

                    <!-- Term 4: State Tax (Default: Yes) -->
                    <div class="tq-term-row">
                        <div class="tq-term-meta">
                            <h5 class="tq-term-title">4. State Tax</h5>
                            <p class="tq-term-desc">Commercial vehicle inter-state border entry taxes &amp; permits.</p>
                        </div>
                        <div class="tq-segmented-toggle">
                            <input type="radio" name="tq_term_state_tax" id="tqStateTaxYes" value="yes" checked>
                            <label for="tqStateTaxYes">Yes</label>
                            <input type="radio" name="tq_term_state_tax" id="tqStateTaxNo" value="no">
                            <label for="tqStateTaxNo">No</label>
                        </div>
                    </div>

                    <!-- Term 5: Driver Allowance (Default: Yes) -->
                    <div class="tq-term-row">
                        <div class="tq-term-meta">
                            <h5 class="tq-term-title">5. Driver Allowance</h5>
                            <p class="tq-term-desc">Driver daytime duty and night halt allowances.</p>
                        </div>
                        <div class="tq-segmented-toggle">
                            <input type="radio" name="tq_term_driver" id="tqDriverYes" value="yes" checked>
                            <label for="tqDriverYes">Yes</label>
                            <input type="radio" name="tq_term_driver" id="tqDriverNo" value="no">
                            <label for="tqDriverNo">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Additional Billing Terms & Rules Extension -->
            <div class="tq-section-card">
                <div class="tq-section-header">
                    <div class="tq-section-title-wrap">
                        <div class="tq-section-icon blue"><i class="fi fi-rr-settings-sliders"></i></div>
                        <div>
                            <h4 class="tq-section-title">Additional Business Terms &amp; Rules</h4>
                            <p class="tq-section-subtitle">Add custom policies (daily KM limit, payment schedules, night rules)</p>
                        </div>
                    </div>
                </div>
                <div class="tq-rules-repeater-list" id="tqCustomRulesList">
                    <!-- Dynamic rules inserted via JS -->
                </div>
                <div class="tq-rules-actions-row">
                    <button type="button" class="btn-v3-action btn-outline" id="tqBtnAddRule">
                        <i class="fi fi-rr-plus"></i> Add More Rule
                    </button>
                    <button type="button" class="btn-v3-action btn-outline" id="tqBtnSaveDefaultTerms" title="Save these rules as default for future quotes">
                        <i class="fi fi-rr-disk"></i> Save Terms as Business Default
                    </button>
                </div>
            </div>
        </div>
        <div class="tq-modal-footer">
            <div class="tq-footer-summary">
                <span>Total Quote:</span>
                <strong><span class="tq-currency-symbol">₹</span> <span id="tqFooterTotalDisplay">0.00</span></strong>
            </div>
            <div class="tq-footer-actions">
                <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger" id="tqBtnCancelQuote">Cancel</button>
                <button type="button" class="btn-add-pkg-v3" id="tqBtnSubmitQuote">
                    <i class="fi fi-rr-eye"></i> Preview &amp; Generate Quotation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Transporter Quotation Live Proposal Preview -->
<div class="v3-modal-overlay" id="modalTransporterQuotePreview">
    <div class="v3-modal-dialog v3-modal-dialog-preview v3-modal-dialog-tq-preview">
        <!-- Preview Header -->
        <div class="v3-preview-modal-header">
            <div class="v3-preview-header-left">
                <div class="v3-preview-header-icon icon-tq-preview">
                    <i class="fi fi-rr-document-signed"></i>
                </div>
                <div class="v3-preview-header-meta">
                    <div class="v3-preview-header-title-wrap">
                        <h4 class="v3-preview-header-title" id="tqPreviewHeaderTitle">Transporter Quotation &amp; Fleet Proposal</h4>
                        <span class="v3-preview-badge"><i class="fi fi-rr-sparkles"></i> Live Preview</span>
                    </div>
                </div>
            </div>
            <div class="v3-preview-header-actions">
                <button type="button" class="btn-v3-action btn-outline" id="tqBtnPreviewBack" title="Edit inputs in quote builder">
                    <i class="fi fi-rr-edit"></i> Edit Details
                </button>
                <button type="button" class="btn-v3-action btn-outline" id="tqBtnPreviewDownloadPdf" title="Download quotation PDF document">
                    <i class="fi fi-rr-file-pdf"></i> Download PDF
                </button>
                <button type="button" class="btn-v3-action btn-green" id="tqBtnPreviewSendQuote" title="Send quotation to customer">
                    <i class="fi fi-brands-whatsapp"></i> Send Quotation
                </button>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close Preview"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>
        <!-- Scrollable Document Preview Body -->
        <div class="v3-preview-modal-body tq-preview-modal-body">
            <div id="tqPreviewLoader" class="v3-itinerary-preview-loader v3-hidden">
                <div class="v3-table-spinner">
                    <div class="v3-spinner-ring"></div>
                </div>
                <span>Rendering transporter proposal...</span>
            </div>
            <iframe id="tqPreviewIframe" class="v3-live-preview-iframe tq-live-preview-iframe" title="Transporter Quotation Proposal Preview"></iframe>
        </div>
    </div>
</div>

<!-- Modal: WhatsApp Quote Share / Success -->
<div class="v3-modal-overlay" id="modalTransporterQuoteShare">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box tq-wa-icon-box">
                    <i class="fi fi-brands-whatsapp"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Quotation Ready to Share</h4>
                    <span class="v3-modal-subtitle">Transporter quotation has been recorded in the lead timeline</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <p class="tq-wa-intro-text">
                Review the quotation text below before sending to the customer via WhatsApp:
            </p>
            <div class="tq-wa-preview-box" id="tqWaPreviewBox"></div>
        </div>
        <div class="v3-modal-footer tq-wa-modal-footer">
            <button type="button" class="btn-v3-action btn-outline" id="tqBtnCopyWaText">
                <i class="fi fi-rr-copy"></i> Copy Text
            </button>
            <div class="tq-wa-modal-actions">
                <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
                <a href="#" target="_blank" class="btn-add-pkg-v3 tq-wa-send-btn" id="tqBtnSendWaDirect">
                    <i class="fi fi-brands-whatsapp"></i> Send on WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>