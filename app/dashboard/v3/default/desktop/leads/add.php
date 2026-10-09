<?php
/**
 * V3 Add New Lead & Inquiry View
 * File: app/dashboard/v3/default/desktop/leads/add.php
 * Workspace Compliant: External CSS/JS, REST API communication via leads-add.js, Select2 dropdowns, full SPA support.
 */

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? intval(SITE_ID) : 0;
$userRole = isset($site_access_type) ? strtolower($site_access_type) : 'agent';
$currentUserId = defined('USER_ID') ? intval(USER_ID) : 0;

if ($userRole === 'digital_marketing') {
    header("Location: /dashboard/integration/facebook/");
    exit;
}

// 1. Fetch Agents for assignment dropdown if admin or operations
$agentsList = [];
if ($userRole !== 'agent' && isset($connection) && $connection) {
    $siteCondition = ($siteId > 0) ? " AND site_access_site_id = {$siteId}" : "";
    $agentQuery = "SELECT user_id, user_email, profile_display_name, site_access_type
                   FROM {$tblPrefix}user
                   INNER JOIN {$tblPrefix}profile ON profile_user_id = user_id
                   INNER JOIN {$tblPrefix}site_access ON site_access_user_id = user_id
                   WHERE site_access_status = 1{$siteCondition}
                   ORDER BY profile_display_name ASC";
    if ($res = mysqli_query($connection, $agentQuery)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $name = !empty($row['profile_display_name']) ? trim($row['profile_display_name']) : $row['user_email'];
            $agentsList[] = [
                'id'    => (int)$row['user_id'],
                'name'  => $name,
                'role'  => ucwords(str_replace('_', ' ', $row['site_access_type'] ?? 'Staff'))
            ];
        }
    }
}

// 2. Fetch Tour Packages for Tour Package / Destination dropdown
$domesticPackages = [];
$internationalPackages = [];
if (isset($connection) && $connection) {
    $pkgQuery = "SELECT tour_package_id, tour_package_title, tour_package_city, tour_package_country, tour_package_duration, tour_package_type, tour_package_theme
                 FROM {$tblPrefix}tour_package
                 WHERE tour_package_site_id = {$siteId} AND tour_package_status != 3
                 ORDER BY tour_package_title ASC";
    if ($pkgRes = mysqli_query($connection, $pkgQuery)) {
        while ($pRow = mysqli_fetch_assoc($pkgRes)) {
            $pTitle = trim(stripslashes($pRow['tour_package_title']));
            $pCity = trim(stripslashes($pRow['tour_package_city'] ?? ''));
            $pCountry = trim(stripslashes($pRow['tour_package_country'] ?? ''));
            $pDuration = (int)($pRow['tour_package_duration'] ?? 0);
            $pType = strtolower($pRow['tour_package_type'] ?? 'domestic');
            $pTheme = ucfirst(str_replace('_', ' ', $pRow['tour_package_theme'] ?? 'Standard'));
            
            $loc = $pCity ?: $pCountry;
            $pkgItem = [
                'id'       => $pTitle,
                'pkg_id'   => (int)$pRow['tour_package_id'],
                'title'    => $pTitle,
                'duration' => $pDuration,
                'days'     => $pDuration > 0 ? ($pDuration + 1) : 1,
                'city'     => $loc,
                'type'     => $pType,
                'theme'    => $pTheme
            ];

            if ($pType === 'international') {
                $internationalPackages[] = $pkgItem;
            } else {
                $domesticPackages[] = $pkgItem;
            }
        }
    }
}
?>

<div class="package-page-wrapper leads-page-wrapper lead-add-page-wrapper" id="leadAddApp" data-user-role="<?php echo htmlspecialchars($userRole, ENT_QUOTES); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <i class="fi fi-rr-user-add"></i>
            </div>
            <div class="package-header-titles">
                <h2>Add New Lead &amp; Inquiry</h2>
                <p>Register a new sales prospect, configure itinerary requirements, and assign representatives.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/leads/list/" class="btn-pkg-back" title="Back to Pipeline">
                <i class="fi fi-rr-arrow-left"></i> <span>All Leads</span>
            </a>
            <a href="/dashboard/leads/kanban/" class="leads-view-tab" title="Kanban Board View">
                <i class="fi fi-rr-apps"></i> <span>Kanban</span>
            </a>
        </div>
    </div>

    <!-- Main Add Lead Form -->
    <form id="leadAddForm" class="v3-form-container" autocomplete="off">
        <div class="v3-form-grid-layout">

            <!-- Section 1: Customer Contact Information -->
            <div class="v3-card-section">
                <div class="v3-card-header">
                    <div class="v3-card-header-left">
                        <div class="v3-card-header-icon sky">
                            <i class="fi fi-rr-user"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Customer &amp; Contact Details</h3>
                            <p>Primary client contact and communication info</p>
                        </div>
                    </div>
                </div>
                <div class="v3-card-body">
                    <div class="v3-form-row">
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadFirstName">First Name <span class="required">*</span></label>
                                <input type="text" id="leadFirstName" name="first_name" class="v3-form-input" placeholder="e.g. Arvind" required>
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadMiddleName">Middle Name</label>
                                <input type="text" id="leadMiddleName" name="middle_name" class="v3-form-input" placeholder="e.g. Kumar">
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadLastName">Last Name</label>
                                <input type="text" id="leadLastName" name="last_name" class="v3-form-input" placeholder="e.g. Sharma">
                            </div>
                        </div>
                    </div>

                    <div class="v3-form-row">
                        <div class="v3-form-col-6">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadMobile">Mobile Number <span class="required">*</span></label>
                                <div class="v3-input-with-icon">
                                    <span class="v3-input-prefix"><i class="fi fi-rr-phone-call"></i></span>
                                    <input type="tel" id="leadMobile" name="mobile" class="v3-form-input has-prefix" placeholder="e.g. 9876543210" required>
                                </div>
                            </div>
                        </div>
                        <div class="v3-form-col-6">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadEmail">Email Address</label>
                                <div class="v3-input-with-icon">
                                    <span class="v3-input-prefix"><i class="fi fi-rr-envelope"></i></span>
                                    <input type="email" id="leadEmail" name="email" class="v3-form-input has-prefix" placeholder="e.g. client@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="v3-form-row">
                        <div class="v3-form-col-12">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadCurrentLocation">Customer's Current City / Location</label>
                                <select id="leadCurrentLocation" name="current_location" class="v3-city-select" data-placeholder="Search or enter customer current city / location...">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tour Requirements & Itinerary Logistics -->
            <div class="v3-card-section">
                <div class="v3-card-header">
                    <div class="v3-card-header-left">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-plane-alt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Travel Requirements &amp; Logistics</h3>
                            <p>Destination, travel dates, passenger count, and duration</p>
                        </div>
                    </div>
                </div>
                <div class="v3-card-body">
                    <div class="v3-form-row">
                        <div class="v3-form-col-6">
                            <div class="v3-form-group">
                                <div class="v3-form-label-row">
                                    <label class="v3-form-label" for="leadPackage">Tour Package / Destination <span class="required">*</span></label>
                                    <span class="v3-label-hint">Select itinerary or type custom destination</span>
                                </div>
                                <select id="leadPackage" name="package" class="v3-package-select" data-placeholder="Choose a tour package or type custom destination..." required>
                                    <option value=""></option>
                                    <?php if (!empty($domesticPackages)) { ?>
                                        <optgroup label="🏔️ Domestic Tour Packages (<?php echo count($domesticPackages); ?>)">
                                            <?php foreach ($domesticPackages as $pkg) { ?>
                                                <option value="<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>" 
                                                        data-id="<?php echo $pkg['pkg_id']; ?>"
                                                        data-type="domestic"
                                                        data-duration="<?php echo $pkg['duration']; ?>" 
                                                        data-days="<?php echo $pkg['days']; ?>" 
                                                        data-city="<?php echo htmlspecialchars($pkg['city'], ENT_QUOTES); ?>"
                                                        data-theme="<?php echo htmlspecialchars($pkg['theme'], ENT_QUOTES); ?>">
                                                    <?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>
                                                </option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                    <?php if (!empty($internationalPackages)) { ?>
                                        <optgroup label="✈️ International Packages (<?php echo count($internationalPackages); ?>)">
                                            <?php foreach ($internationalPackages as $pkg) { ?>
                                                <option value="<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>" 
                                                        data-id="<?php echo $pkg['pkg_id']; ?>"
                                                        data-type="international"
                                                        data-duration="<?php echo $pkg['duration']; ?>" 
                                                        data-days="<?php echo $pkg['days']; ?>" 
                                                        data-city="<?php echo htmlspecialchars($pkg['city'], ENT_QUOTES); ?>"
                                                        data-theme="<?php echo htmlspecialchars($pkg['theme'], ENT_QUOTES); ?>">
                                                    <?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>
                                                </option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </select>

                                <!-- Interactive Selected Package Preview Badge -->
                                <div id="leadSelectedPkgCard" class="v3-selected-pkg-card" style="display: none;">
                                    <div class="v3-pkg-card-left">
                                        <div class="v3-pkg-card-icon" id="cardPkgIcon">
                                            <i class="fi fi-rr-sparkles"></i>
                                        </div>
                                        <div class="v3-pkg-card-info">
                                            <div class="v3-pkg-card-title-row">
                                                <span class="v3-pkg-card-name" id="cardPkgTitle">Package Title</span>
                                                <span class="v3-pkg-type-pill" id="cardPkgTypeBadge">Domestic</span>
                                            </div>
                                            <div class="v3-pkg-card-tags">
                                                <span class="v3-pkg-tag" id="cardPkgDuration"><i class="fi fi-rr-moon"></i> <strong>4N / 5D</strong></span>
                                                <span class="v3-pkg-tag" id="cardPkgLocation"><i class="fi fi-rr-marker"></i> Location</span>
                                                <span class="v3-pkg-tag theme" id="cardPkgTheme"><i class="fi fi-rr-star"></i> Standard</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-clear-pkg-card" id="btnClearPkgCard" title="Clear selection">✕</button>
                                </div>
                            </div>
                        </div>
                        <div class="v3-form-col-3">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadTravelDate">Date of Travel <span class="required">*</span></label>
                                <div class="v3-input-with-icon">
                                    <span class="v3-input-prefix"><i class="fi fi-rr-calendar"></i></span>
                                    <input type="text" id="leadTravelDate" name="date_of_travel" class="v3-form-input has-prefix" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                        </div>
                        <div class="v3-form-col-3">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadNumberOfDays">Duration (Nights / Days) <span class="required">*</span></label>
                                <div class="v3-input-with-icon">
                                    <span class="v3-input-prefix"><i class="fi fi-rr-moon"></i></span>
                                    <input type="number" id="leadNumberOfDays" name="number_of_days" class="v3-form-input has-prefix" min="1" max="90" value="4" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Counts -->
                    <div class="v3-form-row">
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadAdults">Adults (12+ yrs) <span class="required">*</span></label>
                                <div class="v3-counter-input-wrap">
                                    <button type="button" class="v3-counter-btn btn-dec" data-target="leadAdults" title="Decrease">−</button>
                                    <input type="number" id="leadAdults" name="number_of_adults" class="v3-form-input v3-counter-field" min="1" max="100" value="2" required>
                                    <button type="button" class="v3-counter-btn btn-inc" data-target="leadAdults" title="Increase">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadChildren">Children (5-11 yrs)</label>
                                <div class="v3-counter-input-wrap">
                                    <button type="button" class="v3-counter-btn btn-dec" data-target="leadChildren" title="Decrease">−</button>
                                    <input type="number" id="leadChildren" name="number_of_childrens" class="v3-form-input v3-counter-field" min="0" max="50" value="0">
                                    <button type="button" class="v3-counter-btn btn-inc" data-target="leadChildren" title="Increase">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadInfants">Infants (Below 5 yrs)</label>
                                <div class="v3-counter-input-wrap">
                                    <button type="button" class="v3-counter-btn btn-dec" data-target="leadInfants" title="Decrease">−</button>
                                    <input type="number" id="leadInfants" name="number_of_infants" class="v3-form-input v3-counter-field" min="0" max="20" value="0">
                                    <button type="button" class="v3-counter-btn btn-inc" data-target="leadInfants" title="Increase">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="v3-form-row">
                        <div class="v3-form-col-6">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadPickupLocation">Pickup Location / Airport</label>
                                <select id="leadPickupLocation" name="pickup_location" class="v3-city-select" data-placeholder="Search or enter pickup location / airport...">
                                    <option value="Delhi" selected>Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="v3-form-col-6">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadDropLocation">Drop Location (Leave blank if same)</label>
                                <select id="leadDropLocation" name="drop_location" class="v3-city-select" data-placeholder="Search or enter drop location (optional)...">
                                    <option value="Delhi" selected>Delhi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: CRM Assignment & Lead Management -->
            <div class="v3-card-section">
                <div class="v3-card-header">
                    <div class="v3-card-header-left">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-chart-network"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>CRM Assignment &amp; Notes</h3>
                            <p>Channel source, pipeline status, and representative assignment</p>
                        </div>
                    </div>
                </div>
                <div class="v3-card-body">
                    <div class="v3-form-row">
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadSourceSelect">Lead Source</label>
                                <select id="leadSourceSelect" name="source" class="v3-lead-select">
                                    <option value="direct" selected>Direct / Walk-in</option>
                                    <option value="website">Website Inquiry</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="phone">Phone Call</option>
                                    <option value="facebook">Facebook Ads</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="google">Google Search / Ads</option>
                                    <option value="referral">Client Referral</option>
                                    <option value="partner">DMC / Agent Partner</option>
                                </select>
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadStatusSelect">Initial Stage</label>
                                <select id="leadStatusSelect" name="status" class="v3-lead-select">
                                    <option value="0" selected>New Leads</option>
                                    <option value="2">Assigned</option>
                                    <option value="3">Follow Up</option>
                                    <option value="1">Confirmed</option>
                                </select>
                            </div>
                        </div>
                        <div class="v3-form-col-4">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadAgentSelect">Assigned Representative</label>
                                <?php if ($userRole === 'agent') { ?>
                                    <input type="text" class="v3-form-input" value="<?php echo htmlspecialchars(PROFILE_DISPLAY_NAME, ENT_QUOTES); ?> (You)" readonly disabled>
                                    <input type="hidden" name="agent_id" value="<?php echo $currentUserId; ?>">
                                <?php } else { ?>
                                    <select id="leadAgentSelect" name="agent_id" class="v3-lead-select">
                                        <option value="<?php echo $currentUserId; ?>">Self (<?php echo htmlspecialchars(PROFILE_DISPLAY_NAME, ENT_QUOTES); ?>)</option>
                                        <?php foreach ($agentsList as $ag) { 
                                            if ($ag['id'] == $currentUserId) continue;
                                        ?>
                                            <option value="<?php echo $ag['id']; ?>">
                                                <?php echo htmlspecialchars($ag['name'], ENT_QUOTES); ?> (<?php echo htmlspecialchars($ag['role'], ENT_QUOTES); ?>)
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="v3-form-row">
                        <div class="v3-form-col-12">
                            <div class="v3-form-group">
                                <label class="v3-form-label" for="leadRequirements">Special Requirements / Client Remarks</label>
                                <textarea id="leadRequirements" name="requirements" class="v3-form-textarea" rows="3" placeholder="Enter special hotel category, meal plan preferences, flight notes, budget range, or customer specific requests..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions Card -->
            <div class="v3-form-actions-card">
                <a href="/dashboard/leads/list/" class="btn-v3-cancel" title="Cancel and return to leads pipeline">
                    <i class="fi fi-rr-cross-small"></i> <span>Cancel</span>
                </a>
                <button type="submit" id="btnCreateLeadSubmit" class="btn-v3-primary">
                    <i class="fi fi-rr-check"></i> <span>Create Lead &amp; Save</span>
                </button>
            </div>

        </div>
    </form>
</div>