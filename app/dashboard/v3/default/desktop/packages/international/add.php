<?php
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
<div class="package-page-wrapper package-international-add-wrapper" id="packageInternationalAddApp">
    <!-- V3 Header Section -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="package-header-titles">
                <h2>Add International Tour Package</h2>
                <p>Configure global destinations, flight hubs/logistics, and generate an AI-powered holiday itinerary.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <div class="package-type-toggle">
                <a href="/dashboard/packages/domestic/list/" class="type-tab"><i class="fi fi-rr-home"></i> Domestic</a>
                <a href="/dashboard/packages/international/list/" class="type-tab active"><i class="fi fi-rr-globe"></i> International</a>
            </div>
            <a href="/dashboard/packages/international/list/" class="btn-pkg-back">
                <i class="fi fi-rr-arrow-left"></i> All International Packages
            </a>
        </div>
    </div>

    <!-- 3-Step Visual Progress Stepper -->
    <div class="v3-stepper-container">
        <div class="v3-step-item active">
            <div class="v3-step-number">1</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 1</span>
                <strong class="v3-step-title">Route & Flight Logistics</strong>
            </div>
        </div>
        <div class="v3-step-divider"></div>
        <div class="v3-step-item pending">
            <div class="v3-step-number">2</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 2</span>
                <strong class="v3-step-title">Smart AI Itinerary</strong>
            </div>
        </div>
        <div class="v3-step-divider"></div>
        <div class="v3-step-item pending">
            <div class="v3-step-number">3</div>
            <div class="v3-step-info">
                <span class="v3-step-subtitle">Step 3</span>
                <strong class="v3-step-title">Pricing & Publish</strong>
            </div>
        </div>
    </div>

    <!-- Step 1 View: Route & Logistics Setup -->
    <div class="v3-step-view active" id="step1ViewContainer">
        <div class="v3-form-split-layout">
            <!-- Main Form Column -->
            <div class="v3-form-main-col">
                <form action="#" id="addInternationalPackageForm" method="POST" autocomplete="off" novalidate>
                    <div class="v3-form-container">

                        <!-- Card 1: Basic Information -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon indigo">
                                    <i class="fi fi-rr-info"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Package Information</h3>
                                    <p>Provide package title, destination country and primary travel theme</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <div class="v3-form-grid">
                                    <div class="v3-form-group span-2">
                                        <div class="v3-form-label-with-action">
                                            <label class="v3-form-label" for="packageName">Package Title / Name <sup class="required">*</sup></label>
                                            <button type="button" class="btn-quick-suggest-title" id="btnSuggestTitle" title="Auto-suggest catchy title based on route and theme">
                                                <i class="fi fi-rr-magic-wand"></i> Auto Suggest Title
                                            </button>
                                        </div>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-briefcase input-icon"></i>
                                            <input type="text" class="v3-form-control" id="packageName" name="name" placeholder="e.g. 7 Days Scenic Switzerland & Paris Explorer" />
                                        </div>
                                        <span class="v3-field-hint">A catchy title highlighting main countries, cities and signature experiences (auto-suggested on submit if blank).</span>
                                    </div>

<?php
// Fetch all countries
$allCountries = [];
$cRes = mysqli_query($connection, "SELECT country_name FROM " . TABLE_PREFIX . "country ORDER BY country_name ASC");
if ($cRes) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        $cName = trim(stripslashes($cRow['country_name']));
        if (!empty($cName) && !in_array($cName, $allCountries)) {
            $allCountries[] = $cName;
        }
    }
}
?>
                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="destinationCountry">Primary Destination Country <sup class="required">*</sup></label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-globe input-icon"></i>
                                            <select class="v3-standard-select v3-form-control" name="country" id="destinationCountry" data-placeholder="Search or select country...">
                                                <?php foreach ($allCountries as $countryOption): 
                                                    $isSelected = (strtolower($countryOption) === 'united arab emirates' || strtolower($countryOption) === 'switzerland') ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($countryOption); ?>" <?php echo $isSelected; ?>>
                                                        <?php echo htmlspecialchars($countryOption); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="travelStyle">Travel Style / Theme <sup class="required">*</sup></label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-navigation input-icon"></i>
                                            <select class="v3-standard-select v3-form-control" name="travelStyle" id="travelStyle">
                                                <option value="cultural" selected>Cultural & Sightseeing</option>
                                                <option value="honeymoon">Honeymoon & Romantic</option>
                                                <option value="family">Family Vacation</option>
                                                <option value="adventure">Adventure & Skiing</option>
                                                <option value="luxury">Luxury & Premium</option>
                                                <option value="solo">Solo Explorer</option>
                                                <option value="group">Group & Friends</option>
                                                <option value="cruise">International Cruise</option>
                                                <option value="shopping">Shopping & City Life</option>
                                                <option value="wellness">Spa & Wellness</option>
                                                <option value="backpacking">Backpacking</option>
                                                <option value="beach">Beach & Island Gateway</option>
                                            </select>
                                        </div>
                                        <div class="v3-travel-style-pills" id="v3TravelStylePills">
                                            <button type="button" class="v3-style-pill active" data-style="cultural"><i class="fi fi-rr-landmark-alt"></i> Cultural</button>
                                            <button type="button" class="v3-style-pill" data-style="honeymoon"><i class="fi fi-rr-heart"></i> Honeymoon</button>
                                            <button type="button" class="v3-style-pill" data-style="family"><i class="fi fi-rr-users-alt"></i> Family</button>
                                            <button type="button" class="v3-style-pill" data-style="adventure"><i class="fi fi-rr-mountain"></i> Adventure</button>
                                            <button type="button" class="v3-style-pill" data-style="luxury"><i class="fi fi-rr-gem"></i> Luxury</button>
                                            <button type="button" class="v3-style-pill" data-style="beach"><i class="fi fi-rr-sun"></i> Beach</button>
                                            <button type="button" class="v3-style-pill" data-style="shopping"><i class="fi fi-rr-shopping-bag"></i> Shopping</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Destination & Route Planner -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon emerald">
                                    <i class="fi fi-rr-map-marker"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>International Destination & Route Planner</h3>
                                    <p>Define global cities and nights spent at each stopover</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <!-- Dynamic Destination Rows -->
                                <div class="v3-destinations-wrapper">
                                    <div class="v3-dest-rows-header">
                                        <span>Destinations & Stays</span>
                                        <span class="v3-dest-total-badge">Total Duration: <strong id="statTotalNights">5</strong> Nights / <strong id="statTotalDays">6</strong> Days</span>
                                    </div>

                                    <div class="v3-route-journey-flow" id="v3RouteJourneyFlow">
                                        <span class="v3-rjf-title"><i class="fi fi-rr-route"></i> Route Flow:</span>
                                        <span class="v3-rjf-node pickup"><i class="fi fi-rr-plane-departure"></i> Origin</span>
                                        <span class="v3-rjf-arrow"><i class="fi fi-rr-angle-right"></i></span>
                                        <span class="v3-rjf-node stop"><i class="fi fi-rr-marker"></i> Stop 1</span>
                                        <span class="v3-rjf-arrow"><i class="fi fi-rr-angle-right"></i></span>
                                        <span class="v3-rjf-node drop"><i class="fi fi-rr-plane-arrival"></i> Drop-off</span>
                                    </div>

                                    <div id="destinationRowsContainer" class="v3-dest-rows-container">
                                        <div class="v3-destination-row" data-row="1">
                                            <div class="v3-dest-handle">
                                                <span class="v3-dest-badge"><i class="fi fi-rr-marker"></i> Stop 1</span>
                                            </div>
                                            <div class="v3-form-group v3-dest-city-group">
                                                <label class="v3-form-label">Destination City <sup class="required">*</sup></label>
                                                <div class="v3-input-icon-wrap">
                                                    <i class="fi fi-rr-marker input-icon"></i>
                                                    <select class="v3-city-select v3-form-control" name="destination[]" data-placeholder="Select or type international city (e.g. Zurich, Dubai, Paris)...">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="v3-form-group v3-dest-duration-group">
                                                <label class="v3-form-label">Stay Duration <sup class="required">*</sup></label>
                                                <div class="v3-duration-input-stepper">
                                                    <button type="button" class="v3-duration-btn v3-duration-btn-minus" title="Decrease nights">−</button>
                                                    <input type="number" class="v3-form-control v3-duration-field" name="duration[]" value="5" min="1" max="60" />
                                                    <span class="v3-duration-suffix">Nights</span>
                                                    <button type="button" class="v3-duration-btn v3-duration-btn-plus" title="Increase nights">+</button>
                                                </div>
                                            </div>
                                            <div class="v3-dest-action">
                                                <button type="button" class="btn-remove-dest-row" title="Remove this stop">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="v3-add-dest-bar">
                                        <button type="button" class="btn-add-dest-v3" id="btnAddDestination">
                                            <i class="fi fi-rr-plus"></i> Add Another Destination Stop
                                        </button>
                                    </div>

                                    <!-- Day-by-Day Route & Night Stay Planner Section -->
                                    <div class="v3-day-schedule-wrapper" id="v3DaySchedulePlannerWrapper">
                                        <div class="v3-day-schedule-header">
                                            <div class="v3-dsh-left">
                                                <div class="v3-dsh-icon">
                                                    <i class="fi fi-rr-bed"></i>
                                                </div>
                                                <div>
                                                    <h4 class="v3-dsh-title">Day-by-Day Route & Night Stay Planner</h4>
                                                    <p class="v3-dsh-subtitle">Specify where travelers stay each night and add day highlights / focus for the itinerary</p>
                                                </div>
                                            </div>
                                            <span class="v3-dsh-badge" id="dshDaysCountBadge">6 Days Scheduled</span>
                                        </div>

                                        <div class="v3-day-schedule-list" id="v3DaySchedulePlannerList">
                                            <!-- Dynamically generated Day 1..N rows via JS -->
                                        </div>
                                        <datalist id="v3PlannerCitiesDatalist"></datalist>
                                    </div>
                                </div>

                                <hr class="v3-divider" />

                                <!-- Pickup and Drop Logistics -->
                                <div class="v3-logistics-grid">
                                    <div class="v3-form-group">
                                        <div class="v3-form-label-row">
                                            <label class="v3-form-label" for="startingCity">Arrival / Origin Hub <sup class="required">*</sup></label>
                                        </div>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-plane-departure input-icon"></i>
                                            <select class="v3-city-select v3-form-control" name="startingCity" id="startingCity" data-placeholder="Select arrival city / airport...">
                                            </select>
                                        </div>
                                        <span class="v3-field-hint">Airport or international port where travelers arrive.</span>
                                    </div>

                                    <div class="v3-form-group">
                                        <div class="v3-form-label-row">
                                            <label class="v3-form-label" for="endCity">Departure / Return Hub <sup class="required">*</sup></label>
                                            <label class="v3-same-pickup-toggle" title="Auto-match return hub with arrival hub">
                                                <input type="checkbox" id="sameAsPickupToggle" checked />
                                                <span>Same as arrival</span>
                                            </label>
                                        </div>
                                        <div class="v3-input-icon-wrap" id="endCityWrap">
                                            <i class="fi fi-rr-plane-arrival input-icon"></i>
                                            <select class="v3-city-select v3-form-control" name="endCity" id="endCity" data-placeholder="Select departure city / airport...">
                                            </select>
                                        </div>
                                        <span class="v3-field-hint">Where travelers conclude their journey and board return flights.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Transportation & Vehicle Assignment -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon sky">
                                    <i class="fi fi-rr-plane"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Transit Logistics & Touring Transport</h3>
                                    <p>Select international transit modes and ground transfer vehicles</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <div class="v3-form-grid">
                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="outstationVehicle">International / Intercity Transit <sup class="required">*</sup></label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-plane-alt input-icon"></i>
                                            <select class="v3-standard-select v3-form-control" name="outstationVehicle" id="outstationVehicle">
                                                <option value="Flight Included" selected>Flight / Air Transfer Included</option>
                                                <option value="Private Coach">Private AC Coach / Bus (30-45 Seats)</option>
                                                <option value="Luxury MPV">Luxury MPV / Mercedes V-Class (6-7 Seats)</option>
                                                <option value="Private Sedan">Private Executive Sedan (4 Seats)</option>
                                                <option value="Scenic Express Train">Scenic Train / Eurail Pass</option>
                                                <option value="Self Drive Car">Self Drive Rental Car</option>
                                                <option value="Flight Excluded">Flight Excluded (Land Package Only)</option>
                                            </select>
                                        </div>
                                        <span class="v3-field-hint">Mode for major airport transfers and intercity travel.</span>
                                    </div>

                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="localSighseeignVehicle">Local Sightseeing Vehicle <sup class="required">*</sup></label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-taxi input-icon"></i>
                                            <select class="v3-standard-select v3-form-control" name="localSighseeignVehicle" id="localSighseeignVehicle">
                                                <option value="Private AC Cab" selected>Private AC Cab / Sedan</option>
                                                <option value="Private SUV">Private Luxury SUV</option>
                                                <option value="Hop-On Hop-Off Bus">Hop-On Hop-Off Sightseeing Bus</option>
                                                <option value="Shared AC Coach">Shared AC Tourist Coach</option>
                                                <option value="Walking Tour & Public Transit">Walking Tour & Public Metro Pass</option>
                                            </select>
                                        </div>
                                        <span class="v3-field-hint">Vehicle for city tours, monument admissions and daily excursions.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Included Meals (Optional) -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon amber">
                                    <i class="fi fi-rr-restaurant"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Included Meals (Optional)</h3>
                                    <p>Select daily complimentary meals included throughout this international tour</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <div class="v3-meals-selection-wrap">
                                    <div class="v3-meals-preset-bar">
                                        <span class="v3-meals-preset-label"><i class="fi fi-rr-sparkles"></i> Quick Plans:</span>
                                        <button type="button" class="v3-meal-preset-btn active" data-preset="none">No Meals (EP)</button>
                                        <button type="button" class="v3-meal-preset-btn" data-preset="cp">Breakfast Only (CP)</button>
                                        <button type="button" class="v3-meal-preset-btn" data-preset="map">Breakfast &amp; Dinner (MAP)</button>
                                        <button type="button" class="v3-meal-preset-btn" data-preset="ap">All Meals (AP)</button>
                                    </div>
                                    <div class="v3-meals-grid">
                                        <label class="v3-meal-card" data-meal="Breakfast">
                                            <input type="checkbox" name="meals[]" value="Breakfast" class="v3-meal-checkbox" />
                                            <div class="v3-meal-card-content">
                                                <div class="v3-meal-icon"><i class="fi fi-rr-coffee"></i></div>
                                                <div class="v3-meal-info">
                                                    <span class="v3-meal-name">Breakfast</span>
                                                    <span class="v3-meal-desc">Morning buffet or complimentary hotel breakfast</span>
                                                </div>
                                                <div class="v3-meal-check-indicator"><i class="fi fi-rr-check"></i></div>
                                            </div>
                                        </label>

                                        <label class="v3-meal-card" data-meal="Lunch">
                                            <input type="checkbox" name="meals[]" value="Lunch" class="v3-meal-checkbox" />
                                            <div class="v3-meal-card-content">
                                                <div class="v3-meal-icon"><i class="fi fi-rr-utensils"></i></div>
                                                <div class="v3-meal-info">
                                                    <span class="v3-meal-name">Lunch</span>
                                                    <span class="v3-meal-desc">Afternoon lunch at hotel or international restaurant</span>
                                                </div>
                                                <div class="v3-meal-check-indicator"><i class="fi fi-rr-check"></i></div>
                                            </div>
                                        </label>

                                        <label class="v3-meal-card" data-meal="Evening Snacks">
                                            <input type="checkbox" name="meals[]" value="Evening Snacks" class="v3-meal-checkbox" />
                                            <div class="v3-meal-card-content">
                                                <div class="v3-meal-icon"><i class="fi fi-rr-mug-hot-alt"></i></div>
                                                <div class="v3-meal-info">
                                                    <span class="v3-meal-name">Evening Snacks</span>
                                                    <span class="v3-meal-desc">Afternoon high tea, coffee &amp; refreshments</span>
                                                </div>
                                                <div class="v3-meal-check-indicator"><i class="fi fi-rr-check"></i></div>
                                            </div>
                                        </label>

                                        <label class="v3-meal-card" data-meal="Dinner">
                                            <input type="checkbox" name="meals[]" value="Dinner" class="v3-meal-checkbox" />
                                            <div class="v3-meal-card-content">
                                                <div class="v3-meal-icon"><i class="fi fi-rr-soup"></i></div>
                                                <div class="v3-meal-info">
                                                    <span class="v3-meal-name">Dinner</span>
                                                    <span class="v3-meal-desc">Evening buffet or fine dining dinner at hotel</span>
                                                </div>
                                                <div class="v3-meal-check-indicator"><i class="fi fi-rr-check"></i></div>
                                            </div>
                                        </label>
                                    </div>
                                    <span class="v3-field-hint">Selected meals are automatically integrated into package inclusions and daily tour schedules.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 5: Action Footer Card -->
                        <div class="v3-card-section v3-actions-card">
                            <div class="v3-actions-wrap">
                                <button type="button" class="btn-v3-reset" id="btnResetPackageForm">
                                    <i class="fi fi-rr-refresh"></i> Reset Form
                                </button>
                                <button type="button" class="btn-v3-generate" id="btnGeneratePackage">
                                    <i class="fi fi-rr-sparkles"></i> Generate AI Itinerary Preview
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Right Live Preview Summary Sticky Sidebar -->
            <div class="v3-form-sidebar-col">
                <div class="v3-preview-sticky-card">
                    <div class="v3-card-section v3-preview-card">
                        <div class="v3-preview-top-bar">
                            <div class="v3-preview-badge sky">
                                <span class="pulse-dot sky"></span> Live Preview
                            </div>
                            <span class="v3-preview-type">International Itinerary</span>
                        </div>

                        <div class="v3-preview-content">
                            <h4 class="v3-preview-title" id="summaryPackageTitle">Untitled International Package</h4>
                            <div class="v3-preview-badges-row">
                                <span class="v3-chip-badge theme-badge" id="summaryTravelStyleBadge">
                                    <i class="fi fi-rr-compass"></i> Cultural
                                </span>
                                <span class="v3-chip-badge duration-badge" id="summaryDurationBadge">
                                    <i class="fi fi-rr-moon"></i> 5N / <i class="fi fi-rr-sun"></i> 6D
                                </span>
                            </div>

                            <!-- Route Visualizer Timeline -->
                            <div class="v3-route-timeline-box">
                                <div class="v3-timeline-title">
                                    <i class="fi fi-rr-map"></i> Global Route Overview
                                </div>
                                <div class="v3-route-timeline" id="summaryRouteTimeline">
                                    <!-- Dynamic timeline nodes injected via JS -->
                                </div>
                            </div>

                            <!-- Vehicle Summary -->
                            <div class="v3-summary-vehicles-box">
                                <div class="v3-summary-vehicle-item">
                                    <i class="fi fi-rr-plane"></i>
                                    <div>
                                        <span class="label">Transit</span>
                                        <strong id="summaryOutstationVehicle">Flight Included</strong>
                                    </div>
                                </div>
                                <div class="v3-summary-vehicle-item">
                                    <i class="fi fi-rr-taxi"></i>
                                    <div>
                                        <span class="label">Sightseeing</span>
                                        <strong id="summaryLocalVehicle">Private AC Cab</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Included Meals Summary -->
                            <div class="v3-summary-meals-box" id="summaryMealsBox">
                                <div class="v3-summary-meals-header">
                                    <i class="fi fi-rr-restaurant"></i>
                                    <span>Included Meals</span>
                                </div>
                                <div class="v3-summary-meals-tags" id="summaryMealsTags">
                                    <span class="v3-meal-tag-pill none">None (Room Only)</span>
                                </div>
                            </div>

                            <!-- AI Notice Box -->
                            <div class="v3-ai-notice-box">
                                <div class="v3-ai-icon sky">
                                    <i class="fi fi-rr-sparkles"></i>
                                </div>
                                <div class="v3-ai-text">
                                    <strong>Global AI Itinerary Engine</strong>
                                    <p>Generating this package produces comprehensive international schedules, landmark touring, visa details, and smart flight logistics.</p>
                                </div>
                            </div>

                            <!-- Instant Generate CTA in Sticky Sidebar -->
                            <div class="v3-sidebar-action-wrap">
                                <button type="button" class="btn-sidebar-generate" id="btnSidebarGenerate">
                                    <i class="fi fi-rr-sparkles"></i> Generate AI Itinerary
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2 View: Smart AI Itinerary Preview & Customization (SPA In-Page) -->
    <div class="v3-step-view is-hidden" id="step2ItineraryContainer">
        <!-- Loading State Indicator -->
        <div class="v3-ai-loading-container is-hidden" id="itineraryLoadingState">
            <div class="v3-ai-loading-card">
                <div class="v3-ai-spinner-wrap">
                    <div class="v3-ai-spinner"></div>
                    <i class="fi fi-rr-sparkles v3-ai-spinner-icon"></i>
                </div>
                <h3 class="v3-ai-loading-title">Generating International AI Itinerary...</h3>
                <p class="v3-ai-loading-desc" id="aiLoadingDesc">Analyzing global destinations, flight routes, and crafting bespoke day-by-day touring...</p>
                <div class="v3-ai-progress-track">
                    <div class="v3-ai-progress-bar"></div>
                </div>
                <div class="v3-ai-loading-steps">
                    <span class="step-tag active"><i class="fi fi-rr-check"></i> Flight & Logistics</span>
                    <span class="step-tag active"><i class="fi fi-rr-loading spin"></i> AI Day Planner</span>
                    <span class="step-tag"><i class="fi fi-rr-circle"></i> Inclusions & Stays</span>
                </div>
            </div>
        </div>

        <!-- Error State Indicator -->
        <div class="v3-ai-error-container is-hidden" id="itineraryErrorState">
            <div class="v3-ai-error-card">
                <div class="v3-ai-error-icon">
                    <i class="fi fi-rr-triangle-warning"></i>
                </div>
                <h3>Unable to Generate Itinerary</h3>
                <p id="aiErrorMessage">An unexpected issue occurred while generating the AI itinerary. Please try again or return to route setup.</p>
                <div class="v3-ai-error-actions">
                    <button type="button" class="btn-v3-outline" id="btnErrorBackToStep1">
                        <i class="fi fi-rr-arrow-left"></i> Back to Route Setup
                    </button>
                    <button type="button" class="btn-v3-primary" id="btnErrorRetry">
                        <i class="fi fi-rr-refresh"></i> Retry Generation
                    </button>
                </div>
            </div>
        </div>

        <!-- Itinerary Preview & Publish Form -->
        <div class="v3-itinerary-preview-wrap is-hidden" id="itineraryPreviewContent">
            <!-- Hero Summary Bar -->
            <div class="v3-preview-hero-card">
                <div class="v3-preview-hero-left">
                    <span class="v3-preview-kicker"><i class="fi fi-rr-sparkles"></i> AI Generated International Proposal</span>
                    <h2 class="v3-preview-hero-title" id="previewHeroTitle">International Tour Itinerary</h2>
                    <div class="v3-preview-hero-pills" id="previewHeroPills">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div class="v3-preview-hero-right">
                    <button type="button" class="btn-v3-outline-sm" id="btnTopBackToStep1">
                        <i class="fi fi-rr-arrow-left"></i> Edit Route
                    </button>
                </div>
            </div>

            <!-- Form Submitting Itinerary to Backend API -->
            <form action="#" id="createTourPackageForm" method="POST">
                <!-- Hidden inputs for backend package creation API -->
                <input type="hidden" name="packageName" id="itineraryPackageName" value="" />
                <input type="hidden" name="travelStyle" id="itineraryTravelStyle" value="" />
                <input type="hidden" name="duration" id="itineraryDuration" value="" />
                <input type="hidden" name="packageType" id="packageType" value="international" />
                <input type="hidden" name="outstationVehicle" id="itineraryOutstationVehicle" value="" />
                <input type="hidden" name="localSighseeignVehicle" id="itineraryLocalVehicle" value="" />
                <input type="hidden" name="location" id="itineraryLocation" value="" />
                <input type="hidden" name="country" id="itineraryCountry" value="" />
                <input type="hidden" name="startCity" id="itineraryStartCity" value="" />
                <input type="hidden" name="endCity" id="itineraryEndCity" value="" />
                <input type="hidden" name="mealPlan" id="itineraryMealPlan" value="" />

                <div class="v3-form-container">
                    <!-- Card 1: Overview -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon indigo">
                                <i class="fi fi-rr-document"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Tour Overview & Highlights</h3>
                                <p>Comprehensive summary describing the international holiday experience</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="itineraryOverviewField">Package Introduction / Overview <sup class="required">*</sup></label>
                                <textarea class="v3-form-control v3-form-textarea" name="overview" id="itineraryOverviewField" rows="5" required placeholder="Write or edit package overview..."></textarea>
                                <span class="v3-field-hint">Engaging overview highlighting iconic sights, cultural spots, and tour flavor.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Day by Day Itinerary -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon emerald">
                                <i class="fi fi-rr-calendar"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Day-by-Day Daily Itinerary</h3>
                                <p>Review and edit titles and activities planned for each day</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-days-toolbar">
                                <span class="v3-days-count-label" id="previewDaysCountLabel">Daily Itinerary Schedule</span>
                                <div class="v3-days-toolbar-actions">
                                    <button type="button" class="btn-toggle-days-collapse" id="btnToggleAllDaysCollapse">
                                        <i class="fi fi-rr-compress-alt"></i> <span>Collapse All</span>
                                    </button>
                                </div>
                            </div>
                            <div class="v3-days-list-container" id="itineraryDaysCardsContainer">
                                <!-- Daily cards rendered dynamically by JS -->
                            </div>
                            <div class="v3-add-day-btn-bar">
                                <button type="button" class="btn-add-preview-day" id="btnAddPreviewDay">
                                    <i class="fi fi-rr-plus"></i> Add Another Day to Itinerary
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Inclusions and Exclusions -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon violet">
                                <i class="fi fi-rr-list-check"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Inclusions & Exclusions</h3>
                                <p>Specify what is covered in this international package and what is excluded</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-inc-exc-grid">
                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="itineraryInclusionField">
                                        <i class="fi fi-rr-check-circle color-emerald"></i> Inclusions <sup class="required">*</sup>
                                    </label>
                                    <textarea class="v3-form-control v3-form-textarea" name="inclusion" id="itineraryInclusionField" rows="6" required placeholder="List of inclusions..."></textarea>
                                    <span class="v3-field-hint">e.g. Flight tickets, 4-star hotel stays, daily breakfast, visa assistance, private airport transfers.</span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="itineraryExclusionField">
                                        <i class="fi fi-rr-cross-circle color-rose"></i> Exclusions <sup class="required">*</sup>
                                    </label>
                                    <textarea class="v3-form-control v3-form-textarea" name="exclusion" id="itineraryExclusionField" rows="6" required placeholder="List of exclusions..."></textarea>
                                    <span class="v3-field-hint">e.g. Visa fee, personal expenses, monument entry tickets, travel insurance, city tax.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Hotel Type & Accommodation -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon sky">
                                <i class="fi fi-rr-bed"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Accommodation Category</h3>
                                <p>Select default hotel standard for this package</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-group span-2">
                                <label class="v3-form-label" for="itineraryHotelSelect">Hotel / Stay Category <sup class="required">*</sup></label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-bed input-icon"></i>
                                    <select class="v3-standard-select v3-form-control" name="hotel" id="itineraryHotelSelect">
                                        <option value="4_star_hotel" selected>4 Star Hotel (First Class)</option>
                                        <option value="5_star_hotel">5 Star Hotel (Luxury)</option>
                                        <option value="3_star_hotel">3 Star Hotel (Comfort)</option>
                                        <option value="2_star_hotel">2 Star Hotel (Standard)</option>
                                        <option value="resorts">Luxury Resorts</option>
                                        <option value="apartments">Serviced Apartments</option>
                                        <option value="house">Private Villa</option>
                                        <option value="capsule_hotel">Capsule Hotel</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Itinerary & Proposal Template Layout -->
                    <div class="v3-card-section template-selection-card">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon fuchsia">
                                <i class="fi fi-rr-layout-fluid"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Itinerary & Proposal Template Layout</h3>
                                <p>Choose the presentation layout for customer quotations, web previews, and PDF downloads</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
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

                    <!-- Card 6: Actions Bar -->
                    <div class="v3-card-section v3-actions-card">
                        <div class="v3-actions-wrap">
                            <button type="button" class="btn-v3-reset" id="btnBottomBackToStep1">
                                <i class="fi fi-rr-arrow-left"></i> Edit Route & Logistics
                            </button>
                            <div class="v3-action-buttons-group">
                                <button type="button" class="btn-v3-outline" id="btnRegenerateItinerary">
                                    <i class="fi fi-rr-sparkles"></i> Regenerate AI Content
                                </button>
                                <button type="button" class="btn-v3-generate" id="btnPublishPackage">
                                    <i class="fi fi-rr-check"></i> Save Itinerary & Continue to Pricing
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
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