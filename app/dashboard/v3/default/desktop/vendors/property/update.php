<?php

/**
 * V3 Hotel Property Update Page
 * Path: app/dashboard/v3/default/desktop/vendors/property/update.php
 * SPA-compliant hotel editor:
 * 1. Basic Profile & Hotel Photos
 * 2. Hotel Amenities (from master data)
 * 3. Rooms & Rate Plans (from master room types with room photo gallery)
 */

$rawId = $_GET['id'] ?? ($_GET['property_id'] ?? 0);
$id = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $id = (int)$rawId;
} elseif (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    $id = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}
$encodedId = $id > 0 ? base64_encode((string)$id) : (string)$rawId;
?>

<div class="property-update-wrapper" id="propertyUpdateApp" data-id="<?php echo htmlspecialchars($encodedId, ENT_QUOTES, 'UTF-8'); ?>" data-raw-id="<?php echo (int)$id; ?>">

    <!-- ── Page Header Bar ────────────────────────────────────── -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" id="propHeaderIcon">
                <i class="fi fi-rr-building"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="propHeaderTitle">Edit Property</h2>
                <p id="propHeaderSubtitle">Update hotel profile, amenities, rooms &amp; rate plans, and media gallery.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="#" target="_blank" class="btn-v3-action btn-view-live" id="btnViewLiveHotel" style="display: none;" title="View Live on Website">
                <i class="fi fi-rr-eye"></i> View Live
            </a>
            <a href="/dashboard/vendors/property/list/" class="btn-v3-action" data-tooltip="Back to Properties Directory">
                <i class="fi fi-rr-arrow-left"></i> Properties Directory
            </a>
        </div>
    </div>

    <!-- ── Loading State ──────────────────────────────────────── -->
    <div class="v3-form-loading" id="propertyEditLoading">
        <div class="v3-loading-spinner-wrap">
            <i class="fi fi-rr-spinner spinner"></i>
            <p>Loading property details...</p>
        </div>
    </div>

    <!-- ── Full-Width Layout ──────────────────────────────────────── -->
    <div class="prop-form-full-layout d-none" id="propertyEditLayout" style="display:none;">

        <!-- ── Left: Form Cards ───────────────────────────────── -->
        <div class="v3-form-main-col">

            <!-- ── Navigation Segment Tabs ────────────────────────── -->
            <div class="prop-nav-tabs-wrapper">
                <div class="prop-nav-tabs">
                    <button type="button" class="prop-tab-btn active" data-target="#tabProfile">
                        <i class="fi fi-rr-building"></i>
                        <span>1. Basic Profile &amp; Hotel Photos</span>
                    </button>
                    <button type="button" class="prop-tab-btn" data-target="#tabAmenities">
                        <i class="fi fi-rr-swimmer"></i>
                        <span>2. Hotel Amenities</span>
                        <span class="prop-tab-badge" id="tabAmenitiesCountBadge">0 Selected</span>
                    </button>
                    <button type="button" class="prop-tab-btn" data-target="#tabRooms">
                        <i class="fi fi-rr-bed-empty"></i>
                        <span>3. Rooms &amp; Rate Plans</span>
                        <span class="prop-tab-badge" id="tabRoomsCountBadge">0 Rooms</span>
                    </button>
                </div>
            </div>

            <form id="propertyUpdateForm" class="v3-form-container" autocomplete="off" novalidate enctype="multipart/form-data">
                <input type="hidden" id="propId" name="property_id" value="">
                <input type="hidden" id="propAmenitiesInput" name="property_amenities" value="[]">

                <!-- ══════════════════════════════════════════════════
                     TAB 1: Basic Profile & Hotel Photos
                ══════════════════════════════════════════════════ -->
                <div class="prop-tab-pane active" id="tabProfile">

                    <!-- Card 1 · Hotel Identity ────────────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon teal">
                                <i class="fi fi-rr-building"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Hotel Identity</h3>
                                <p>Name, category type and public description of the property.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-grid">

                                <!-- Property Name -->
                                <div class="v3-form-group span-2">
                                    <label for="propName" class="v3-form-label">
                                        Property / Hotel Name <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-building input-icon"></i>
                                        <input type="text" id="propName" name="name" class="v3-form-input"
                                            placeholder="e.g. The Grand Himalayan Resort" required>
                                    </div>
                                    <span class="v3-field-error" id="errPropName"></span>
                                </div>

                                <!-- Property Type -->
                                <div class="v3-form-group span-2">
                                    <label for="propType" class="v3-form-label">
                                        Property Type <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-category input-icon"></i>
                                        <select id="propType" name="type" class="v3-form-select selectjs2" required>
                                            <option value="">— Select property type —</option>
                                            <option value="apartments">Apartments</option>
                                            <option value="capsule_hotel">Capsule Hotel</option>
                                            <option value="guest_house">Guest House</option>
                                            <option value="inn">Inn</option>
                                            <option value="hostel">Hostel</option>
                                            <option value="house">House</option>
                                            <option value="home_stay">Home Stay</option>
                                            <option value="1_star_hotel">1 Star Hotel</option>
                                            <option value="2_star_hotel">2 Star Hotel</option>
                                            <option value="3_star_hotel">3 Star Hotel</option>
                                            <option value="4_star_hotel">4 Star Hotel</option>
                                            <option value="5_star_hotel">5 Star Hotel</option>
                                            <option value="motel">Motel</option>
                                            <option value="lodge">Lodge</option>
                                            <option value="tent">Tent / Camp</option>
                                            <option value="farm_stay">Farm Stay</option>
                                            <option value="resorts">Resort</option>
                                        </select>
                                    </div>
                                    <span class="v3-field-error" id="errPropType"></span>
                                </div>

                                <!-- Description -->
                                <div class="v3-form-group span-2">
                                    <label for="propDescription" class="v3-form-label">
                                        About Property
                                    </label>
                                    <div class="v3-input-icon-wrap textarea-wrap">
                                        <i class="fi fi-rr-document input-icon"></i>
                                        <textarea id="propDescription" name="description" class="v3-form-textarea" rows="4"
                                            placeholder="Describe the hotel ambience, location highlights, guest experience and key features..."></textarea>
                                    </div>
                                    <span class="v3-field-hint">Shown on property details page, quote itineraries, and vouchers.</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Card 2 · Location & Address ────────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon sky">
                                <i class="fi fi-rr-marker"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Location &amp; Address</h3>
                                <p>Geographic details and Google Maps location for guest navigation.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-grid">

                                <!-- Address -->
                                <div class="v3-form-group span-2">
                                    <label for="propAddress" class="v3-form-label">
                                        Street / Area Address <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-map-marker input-icon"></i>
                                        <input type="text" id="propAddress" name="address" class="v3-form-input"
                                            placeholder="e.g. Mall Road, Near Hadimba Temple" required>
                                    </div>
                                    <span class="v3-field-error" id="errPropAddress"></span>
                                </div>

                                <!-- City -->
                                <div class="v3-form-group">
                                    <label for="propCity" class="v3-form-label">
                                        City / Destination <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-map-marker-home input-icon"></i>
                                        <select id="propCity" name="city" class="v3-form-select selectjs2" required>
                                            <option value="">— Search city —</option>
                                        </select>
                                    </div>
                                    <span class="v3-field-error" id="errPropCity"></span>
                                </div>

                                <!-- Pin Code -->
                                <div class="v3-form-group">
                                    <label for="propPincode" class="v3-form-label">
                                        Postal / PIN Code <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-hashtag input-icon"></i>
                                        <input type="text" id="propPincode" name="pinCode" class="v3-form-input"
                                            placeholder="e.g. 175131" maxlength="10" required>
                                    </div>
                                    <span class="v3-field-error" id="errPropPincode"></span>
                                </div>

                                <!-- Map Embed URL -->
                                <div class="v3-form-group span-2">
                                    <label for="propMapUrl" class="v3-form-label">
                                        Google Map Link / Embed URL
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-map input-icon"></i>
                                        <input type="url" id="propMapUrl" name="mapUrl" class="v3-form-input"
                                            placeholder="https://maps.google.com/...">
                                    </div>
                                    <span class="v3-field-hint">Paste Google Maps share link or embed URL to show a directions map to guests.</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Card 3 · Contact Details ───────────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon indigo">
                                <i class="fi fi-rr-phone-call"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Contact &amp; Support Info</h3>
                                <p>Front desk, reservations and escalation contact numbers.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-grid">

                                <!-- Reservation Email -->
                                <div class="v3-form-group">
                                    <label for="propEmail" class="v3-form-label">
                                        Reservation Email <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-envelope input-icon"></i>
                                        <input type="email" id="propEmail" name="email" class="v3-form-input"
                                            placeholder="reservations@hotel.com" required>
                                    </div>
                                    <span class="v3-field-error" id="errPropEmail"></span>
                                </div>

                                <!-- Reservation Phone -->
                                <div class="v3-form-group">
                                    <label for="propMobile" class="v3-form-label">
                                        Reservation Contact <span class="required">*</span>
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-phone-call input-icon"></i>
                                        <input type="tel" id="propMobile" name="mobile" class="v3-form-input"
                                            placeholder="+91 98765 43210" required>
                                    </div>
                                    <span class="v3-field-error" id="errPropMobile"></span>
                                </div>

                                <!-- Support Email -->
                                <div class="v3-form-group">
                                    <label for="propSupportEmail" class="v3-form-label">
                                        Support / Manager Email
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-envelope-plus input-icon"></i>
                                        <input type="email" id="propSupportEmail" name="supportEmail" class="v3-form-input"
                                            placeholder="manager@hotel.com">
                                    </div>
                                </div>

                                <!-- Support Phone -->
                                <div class="v3-form-group">
                                    <label for="propSupportMobile" class="v3-form-label">
                                        Support / Duty Manager Phone
                                    </label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-headset input-icon"></i>
                                        <input type="tel" id="propSupportMobile" name="supportMobile" class="v3-form-input"
                                            placeholder="+91 98765 43211">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Card 4 · Feature Image ──────────────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon amber">
                                <i class="fi fi-rr-picture"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Feature Image</h3>
                                <p>Primary listing photo shown on search results and the booking card.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-group">
                                <!-- Current Thumbnail Preview -->
                                <div class="prop-thumb-preview-wrap" id="propThumbPreviewWrap">
                                    <div class="prop-thumb-current" id="propThumbCurrent">
                                        <i class="fi fi-rr-building"></i>
                                    </div>
                                    <div class="prop-thumb-meta">
                                        <p class="prop-thumb-label" id="propThumbLabel">No feature image uploaded</p>
                                        <span class="prop-thumb-hint">Recommended: 1200×800 px, JPG/PNG/WebP, max 2 MB</span>
                                    </div>
                                </div>
                                <!-- Styled Upload Zone -->
                                <label class="v3-form-label">Upload New Feature Image</label>
                                <div class="prop-file-upload-zone" id="propFileUploadZone">
                                    <input type="file" id="propFeatureImage" name="featureImage" class="prop-file-input-hidden"
                                        accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="prop-file-upload-inner">
                                        <div class="prop-file-upload-icon">
                                            <i class="fi fi-rr-cloud-upload"></i>
                                        </div>
                                        <div class="prop-file-upload-text">
                                            <span class="prop-file-upload-title" id="propFileTitle">Choose a new image or drag &amp; drop</span>
                                            <span class="prop-file-selected-name" id="propFileSelectedName">JPG, PNG, GIF or WebP (max 2 MB)</span>
                                        </div>
                                        <button type="button" class="v3-btn-secondary prop-file-browse-btn" tabindex="-1">
                                            <i class="fi fi-rr-folder"></i> Browse
                                        </button>
                                    </div>
                                </div>
                                <span class="v3-field-hint">Leave empty to keep the current image.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5 · Hotel Gallery & Photos ──────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon violet">
                                <i class="fi fi-rr-picture"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Hotel Gallery &amp; Photos</h3>
                                <p>Upload and manage multiple property photos for dining, lobby, views and exterior.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="prop-gallery-section-wrap">
                                <input type="hidden" id="propKeptGallery" name="kept_gallery" value="[]">

                                <!-- Existing Photos Block -->
                                <div class="prop-gallery-block">
                                    <div class="prop-gallery-block-title">
                                        <i class="fi fi-rr-images"></i> Current Hotel Photos
                                        <span class="prop-gallery-count-badge" id="propGalleryCountBadge">0 Photos</span>
                                    </div>
                                    <div class="prop-gallery-grid" id="propExistingGalleryGrid">
                                        <!-- Rendered dynamically via JS -->
                                    </div>
                                    <div class="prop-gallery-empty-state" id="propGalleryEmptyState" style="display:none;">
                                        <i class="fi fi-rr-picture"></i>
                                        <span>No hotel photos uploaded yet.</span>
                                    </div>
                                </div>

                                <!-- Upload New Photos Block -->
                                <div class="prop-gallery-block">
                                    <label class="v3-form-label">Upload New Hotel Photos</label>
                                    <div class="prop-file-upload-zone" id="propGalleryUploadZone">
                                        <input type="file" id="propGalleryImages" name="gallery[]" class="prop-file-input-hidden"
                                            accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                                        <div class="prop-file-upload-inner">
                                            <div class="prop-file-upload-icon">
                                                <i class="fi fi-rr-add-image"></i>
                                            </div>
                                            <div class="prop-file-upload-text">
                                                <span class="prop-file-upload-title" id="propGalleryFileTitle">Choose images or drag &amp; drop multiple files</span>
                                                <span class="prop-file-selected-name" id="propGalleryFileSubtitle">Select multiple JPG, PNG, GIF or WebP photos</span>
                                            </div>
                                            <button type="button" class="v3-btn-secondary prop-file-browse-btn" tabindex="-1">
                                                <i class="fi fi-rr-folder"></i> Browse Photos
                                            </button>
                                        </div>
                                    </div>
                                    <span class="v3-field-hint">You can select and upload multiple images at once. New uploads will be added when you save.</span>

                                    <!-- Staged New Photos Preview Grid -->
                                    <div class="prop-gallery-grid" id="propStagedGalleryGrid" style="margin-top: 12px; display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6 · Listing Status ─────────────────── -->
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon rose">
                                <i class="fi fi-rr-settings"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Listing Status</h3>
                                <p>Control whether this property is active and available in directory searches &amp; package builder.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="v3-form-grid">
                                <div class="v3-form-group span-2">
                                    <label class="v3-form-label">Property Status</label>
                                    <div class="v3-radio-pill-group" id="propStatusGroup">
                                        <label class="v3-radio-pill active-pill">
                                            <input type="radio" name="status" value="1" id="statusActive" checked>
                                            <i class="fi fi-rr-check-circle"></i> Active
                                        </label>
                                        <label class="v3-radio-pill inactive-pill">
                                            <input type="radio" name="status" value="0" id="statusInactive">
                                            <i class="fi fi-rr-cross-circle"></i> Inactive
                                        </label>
                                    </div>
                                    <span class="v3-field-hint">Inactive properties are hidden from booking and package searches.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Website Publishing Option Card (Matching Tour Package Style) -->
                    <div class="v3-card-section" id="cardWebsitePublish">
                        <div class="v3-card-body" style="padding: 16px 20px;">
                            <label class="v3-publish-checkbox-label" for="chkPublishToWebsite">
                                <div class="v3-publish-checkbox-left">
                                    <div class="v3-publish-icon-box">
                                        <i class="fi fi-rr-globe"></i>
                                    </div>
                                    <div class="v3-publish-text-wrap">
                                        <h4 class="v3-publish-title">Publish to Website</h4>
                                        <p class="v3-publish-desc">Show and make this property bookable on your live website.</p>
                                    </div>
                                </div>
                                <div class="v3-publish-checkbox-control">
                                    <input type="checkbox" id="chkPublishToWebsite" name="publishToWebsite" value="1" checked class="v3-custom-checkbox">
                                </div>
                            </label>

                            <!-- Live Website Link Box (shown when published & URL exists) -->
                            <!-- <div class="prop-live-website-box" id="propLiveWebsiteBox" style="display: none; margin-top: 14px;">
                                <div class="prop-live-website-info">
                                    <i class="fi fi-rr-link-alt"></i>
                                    <div>
                                        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #047857; letter-spacing: 0.05em;">Live Web Page</div>
                                        <div class="prop-live-website-url" id="propLiveWebsiteUrl">https://...</div>
                                    </div>
                                </div>
                                <a href="#" target="_blank" class="prop-live-website-btn" id="propLiveWebsiteBtn">
                                    <i class="fi fi-rr-arrow-up-right"></i> Open Page
                                </a>
                            </div> -->
                        </div>
                    </div>

                </div><!-- /tabProfile -->

                <!-- ══════════════════════════════════════════════════
                     TAB 2: Hotel Amenities (From Master Data)
                ══════════════════════════════════════════════════ -->
                <div class="prop-tab-pane" id="tabAmenities">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon emerald">
                                <i class="fi fi-rr-swimmer"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Hotel Amenities &amp; Facilities</h3>
                                <p>Select all features and facilities available at this property from the master library.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="prop-amenities-wrapper">
                                <!-- Toolbar (All in same row) -->
                                <div class="prop-amenities-toolbar">
                                    <div class="v3-input-icon-wrap" style="flex: 1; min-width: 180px;">
                                        <i class="fi fi-rr-search input-icon"></i>
                                        <input type="text" id="propAmenitySearch" class="v3-form-input" placeholder="Search facilities & amenities..." style="padding-left: 42px !important;">
                                    </div>
                                    <div class="v3-input-icon-wrap" style="width: 220px; flex-shrink: 0;">
                                        <i class="fi fi-rr-apps input-icon"></i>
                                        <select id="propAmenityCatSelect" class="v3-form-select selectjs2" style="width: 100%;">
                                            <option value="all">All Categories</option>
                                        </select>
                                    </div>
                                    <div class="prop-amenities-counter" style="flex-shrink: 0;">
                                        <span id="propAmenitySelectedCount">0</span> Selected
                                    </div>
                                </div>

                                <!-- Dynamic Amenities Category Blocks Container -->
                                <div id="propAmenitiesCategoriesContainer">
                                    <div style="text-align: center; padding: 30px; color: #94a3b8;">
                                        <i class="fi fi-rr-spinner spinner" style="font-size: 24px;"></i>
                                        <p style="margin-top: 8px;">Loading master amenities...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /tabAmenities -->

                <!-- ══════════════════════════════════════════════════
                     TAB 3: Rooms & Rate Plans
                ══════════════════════════════════════════════════ -->
                <div class="prop-tab-pane" id="tabRooms">
                    <div class="v3-card-section">
                        <div class="v3-card-header">
                            <div class="v3-card-header-icon violet">
                                <i class="fi fi-rr-bed"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Rooms, Pricing &amp; Photo Galleries</h3>
                                <p>Configure room categories, EP/CP/MAP/AP meal plan rates, and individual room photos.</p>
                            </div>
                        </div>
                        <div class="v3-card-body">
                            <div class="prop-rooms-header-bar">
                                <div class="prop-rooms-count-info">
                                    <h3>Configured Rooms &amp; Rates</h3>
                                    <span class="prop-tab-badge" id="propRoomsHeaderBadge">0 Price Slabs</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <button type="button" class="v3-btn-secondary" id="btnOpenRoomGalleryModal" title="Manage master photos for each room type">
                                        <i class="fi fi-rr-picture"></i> Manage Room Galleries
                                    </button>
                                    <button type="button" class="v3-btn-primary" id="btnOpenAddRoomModal" title="Add new seasonal pricing slab">
                                        <i class="fi fi-rr-plus"></i> Add Price Slab
                                    </button>
                                </div>
                            </div>

                            <!-- Rooms List Container -->
                            <div class="prop-rooms-list" id="propRoomsListContainer">
                                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                                    <i class="fi fi-rr-spinner spinner" style="font-size: 24px;"></i>
                                    <p style="margin-top: 8px;">Loading hotel rooms...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /tabRooms -->

                <!-- ── Sticky Action Footer ──────────────────────── -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/vendors/property/list/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="v3-btn-primary" id="btnSaveProperty">
                        <i class="fi fi-rr-check"></i> <span id="btnSaveText">Save Property Details</span>
                    </button>
                </div>

            </form>
        </div><!-- /v3-form-main-col -->

    </div><!-- /prop-form-full-layout -->

</div><!-- /property-update-wrapper -->

<!-- ══════════════════════════════════════════════════════════════
     MODAL: Add / Edit Room Pricing Slab (Pure Business Rates)
══════════════════════════════════════════════════════════════ -->
<div class="prop-room-modal-backdrop" id="propRoomModal">
    <div class="prop-room-modal" style="max-width: 740px;">
        <div class="prop-room-modal-head">
            <h3 id="roomModalTitle">Add Room Pricing Slab</h3>
            <button type="button" class="prop-room-modal-close" id="btnCloseRoomModal">
                <i class="fi fi-rr-cross"></i>
            </button>
        </div>
        <form id="propRoomForm">
            <input type="hidden" id="roomModalId" name="room_id" value="">
            <input type="hidden" id="roomModalPropertyId" name="property_id" value="">

            <div class="prop-room-modal-body">

                <!-- Info Notice about Master Gallery -->
                <div style="background:#f0fdfa; border:1px solid #99f6e4; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#0f766e; display:flex; align-items:center; gap:8px;">
                    <i class="fi fi-rr-picture" style="font-size:16px; color:#0d9488; flex-shrink:0;"></i>
                    <span>Photo galleries are managed centrally per room category in <strong>Manage Room Galleries</strong>.</span>
                </div>

                <!-- Room Type -->
                <div class="v3-form-group">
                    <label class="v3-form-label">Room Type / Category <span class="required">*</span></label>
                    <div class="v3-input-icon-wrap">
                        <i class="fi fi-rr-bed input-icon"></i>
                        <select id="roomModalType" name="room_type" class="v3-form-select selectjs2" required style="width: 100%;">
                            <option value="">— Select Room Type from Master Library —</option>
                        </select>
                    </div>
                </div>

                <!-- Price Duration & Season Validity (Day & Month Calendar - No Year) -->
                <div class="prop-room-season-box">
                    <div class="prop-season-box-head">
                        <i class="fi fi-rr-calendar-clock"></i>
                        <div>
                            <h4 class="prop-season-title">Seasonal Validity &amp; Price Duration</h4>
                            <p class="prop-season-sub">Set seasonal rate validity (Day &amp; Month) for this room tariff.</p>
                        </div>
                    </div>
                    <div class="prop-season-inputs-grid">
                        <div class="v3-form-group" style="margin-bottom: 0;">
                            <label class="v3-form-label">Season / Rate Title</label>
                            <div class="v3-input-icon-wrap">
                                <i class="fi fi-rr-label input-icon"></i>
                                <input type="text" id="roomSeasonName" name="season_name" class="v3-form-input" placeholder="e.g. Peak Season / Summer / Regular">
                            </div>
                        </div>
                        <div class="v3-form-group" style="margin-bottom: 0;">
                            <label class="v3-form-label">Valid From Date</label>
                            <div class="v3-input-icon-wrap prop-dm-picker-input-wrap">
                                <i class="fi fi-rr-calendar input-icon"></i>
                                <input type="text" id="roomPriceFromDisplay" class="v3-form-input prop-dm-picker-input" data-target="#roomPriceFrom" placeholder="Select Day &amp; Month" readonly style="padding-left: 42px !important; cursor: pointer !important;">
                                <input type="hidden" id="roomPriceFrom" name="price_from">
                            </div>
                        </div>
                        <div class="v3-form-group" style="margin-bottom: 0;">
                            <label class="v3-form-label">Valid To Date</label>
                            <div class="v3-input-icon-wrap prop-dm-picker-input-wrap">
                                <i class="fi fi-rr-calendar input-icon"></i>
                                <input type="text" id="roomPriceToDisplay" class="v3-form-input prop-dm-picker-input" data-target="#roomPriceTo" placeholder="Select Day &amp; Month" readonly style="padding-left: 42px !important; cursor: pointer !important;">
                                <input type="hidden" id="roomPriceTo" name="price_to">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Plan Rates Matrix -->
                <div>
                    <label class="v3-form-label" style="margin-bottom: 8px;">Meal Plan Tariffs &amp; Pricing (₹)</label>
                    <div class="prop-meal-plans-matrix">

                        <!-- EP -->
                        <div class="prop-meal-plan-card">
                            <div class="prop-meal-plan-card-title">
                                <i class="fi fi-rr-bed"></i> EP (Room Only)
                            </div>
                            <div class="prop-meal-plan-inputs">
                                <div>
                                    <label>MRP</label>
                                    <input type="number" id="roomEpMrp" name="priceMRP" placeholder="0">
                                </div>
                                <div>
                                    <label>Selling Price</label>
                                    <input type="number" id="roomEpB2c" name="priceB2C" placeholder="0">
                                </div>
                                <div>
                                    <label>Agent / B2B</label>
                                    <input type="number" id="roomEpB2b" name="priceB2B" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- CP -->
                        <div class="prop-meal-plan-card">
                            <div class="prop-meal-plan-card-title">
                                <i class="fi fi-rr-coffee"></i> CP (Breakfast)
                            </div>
                            <div class="prop-meal-plan-inputs">
                                <div>
                                    <label>MRP</label>
                                    <input type="number" id="roomCpMrp" name="priceCP" placeholder="0">
                                </div>
                                <div>
                                    <label>Selling Price</label>
                                    <input type="number" id="roomCpB2c" name="priceCPB2C" placeholder="0">
                                </div>
                                <div>
                                    <label>Agent / B2B</label>
                                    <input type="number" id="roomCpB2b" name="priceCPB2B" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- MAP -->
                        <div class="prop-meal-plan-card">
                            <div class="prop-meal-plan-card-title">
                                <i class="fi fi-rr-restaurant"></i> MAP (Breakfast + Dinner)
                            </div>
                            <div class="prop-meal-plan-inputs">
                                <div>
                                    <label>MRP</label>
                                    <input type="number" id="roomMapMrp" name="priceMAP" placeholder="0">
                                </div>
                                <div>
                                    <label>Selling Price</label>
                                    <input type="number" id="roomMapB2c" name="priceMAPB2C" placeholder="0">
                                </div>
                                <div>
                                    <label>Agent / B2B</label>
                                    <input type="number" id="roomMapB2b" name="priceMAPB2B" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- AP -->
                        <div class="prop-meal-plan-card">
                            <div class="prop-meal-plan-card-title">
                                <i class="fi fi-rr-utensils"></i> AP (All Meals)
                            </div>
                            <div class="prop-meal-plan-inputs">
                                <div>
                                    <label>MRP</label>
                                    <input type="number" id="roomApMrp" name="priceAP" placeholder="0">
                                </div>
                                <div>
                                    <label>Selling Price</label>
                                    <input type="number" id="roomApB2c" name="priceAPB2C" placeholder="0">
                                </div>
                                <div>
                                    <label>Agent / B2B</label>
                                    <input type="number" id="roomApB2b" name="priceAPB2B" placeholder="0">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- About Room / Notes -->
                <div class="v3-form-group" style="margin-bottom: 0;">
                    <label class="v3-form-label">Special Notes / Description</label>
                    <div class="v3-input-icon-wrap textarea-wrap">
                        <i class="fi fi-rr-document input-icon"></i>
                        <textarea id="roomModalDesc" name="description" class="v3-form-textarea" rows="2" placeholder="e.g. Mountain facing room with private balcony..."></textarea>
                    </div>
                </div>

            </div>
            <div class="prop-room-modal-foot">
                <button type="button" class="v3-btn-secondary" id="btnCancelRoomModal">Cancel</button>
                <button type="submit" class="v3-btn-primary" id="btnSaveRoom">
                    <i class="fi fi-rr-check"></i> <span id="btnSaveRoomText">Save Pricing Slab</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: Room Types Master Photo Gallery
══════════════════════════════════════════════════════════════ -->
<div class="prop-room-modal-backdrop" id="propRoomGalleryModal" style="display:none;">
    <div class="prop-room-modal" style="max-width: 680px;">
        <div class="prop-room-modal-head">
            <div>
                <h3><i class="fi fi-rr-picture" style="color: #0d9488; margin-right: 6px;"></i> Room Type Master Gallery</h3>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">Manage cover photo and photo galleries per room category.</p>
            </div>
            <button type="button" class="prop-room-modal-close" id="btnCloseRoomGalleryModal">
                <i class="fi fi-rr-cross"></i>
            </button>
        </div>
        <form id="propRoomGalleryForm" enctype="multipart/form-data">
            <div class="prop-room-modal-body">
                <!-- Select Room Type -->
                <div class="v3-form-group">
                    <label class="v3-form-label">Select Room Category <span class="required">*</span></label>
                    <div class="v3-input-icon-wrap">
                        <i class="fi fi-rr-bed input-icon"></i>
                        <select id="roomGalleryTypeSelect" name="room_type" class="v3-form-select selectjs2" style="width: 100%;" required>
                            <!-- Dynamically populated -->
                        </select>
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="v3-form-group">
                    <label class="v3-form-label">Cover / Featured Photo</label>
                    <div class="prop-thumb-preview-wrap" id="masterCoverPreviewWrap" style="display: none;">
                        <img src="" alt="Room Cover" id="masterCoverImgPreview" class="prop-thumb-img" style="width: 64px; height: 64px; border-radius: 8px; object-fit: cover;">
                        <div class="prop-thumb-meta" style="flex: 1;">
                            <span class="prop-thumb-title" id="masterCoverName" style="font-size: 13px; font-weight: 600;">cover.jpg</span>
                            <div class="prop-thumb-actions" style="margin-top: 6px; display: flex; gap: 8px;">
                                <button type="button" class="v3-btn-secondary" id="btnChangeMasterCover" style="font-size: 11.5px; padding: 4px 10px;"><i class="fi fi-rr-refresh"></i> Change</button>
                                <button type="button" class="v3-btn-secondary danger" id="btnRemoveMasterCover" style="font-size: 11.5px; padding: 4px 10px; color:#ef4444; border-color:#fecaca;"><i class="fi fi-rr-trash"></i> Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="prop-file-upload-zone" id="masterCoverDropzone">
                        <input type="file" id="masterCoverFileInput" name="feature_image" class="prop-file-input-hidden" accept="image/*">
                        <div class="prop-file-upload-inner">
                            <div class="prop-file-upload-icon"><i class="fi fi-rr-picture"></i></div>
                            <div class="prop-file-upload-text">
                                <span class="prop-file-upload-title" id="masterCoverUploadTitle">Choose room cover photo</span>
                                <span class="prop-file-selected-name">JPG, PNG or WebP</span>
                            </div>
                            <button type="button" class="v3-btn-secondary prop-file-browse-btn" tabindex="-1">Browse</button>
                        </div>
                    </div>
                </div>

                <!-- Photo Gallery -->
                <div class="v3-form-group" style="margin-bottom: 0;">
                    <label class="v3-form-label">Room Photo Gallery (Upload Multiple)</label>
                    <div class="prop-file-upload-zone" id="masterGalleryDropzone">
                        <input type="file" id="masterGalleryFileInput" name="gallery_files[]" class="prop-file-input-hidden" accept="image/*" multiple>
                        <div class="prop-file-upload-inner">
                            <div class="prop-file-upload-icon"><i class="fi fi-rr-add-image"></i></div>
                            <div class="prop-file-upload-text">
                                <span class="prop-file-upload-title">Upload room gallery photos</span>
                                <span class="prop-file-selected-name">Bathroom, bed view, balcony, etc.</span>
                            </div>
                            <button type="button" class="v3-btn-secondary prop-file-browse-btn" tabindex="-1">Browse Photos</button>
                        </div>
                    </div>
                    <!-- Master Gallery Grid Preview -->
                    <div class="prop-gallery-grid" id="masterGalleryGrid" style="margin-top: 10px; display: none;"></div>
                </div>
            </div>
            <div class="prop-room-modal-foot">
                <button type="button" class="v3-btn-secondary" id="btnCancelRoomGalleryModal">Cancel</button>
                <button type="submit" class="v3-btn-primary" id="btnSaveRoomGallery">
                    <i class="fi fi-rr-check"></i> <span id="btnSaveRoomGalleryText">Save Room Gallery</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     Month-Day Calendar Popover (Recurring - No Year)
══════════════════════════════════════════════════════════════ -->
<div class="prop-daymonth-popover" id="propDayMonthPopover" style="display: none;">
    <div class="prop-dm-head">
        <button type="button" class="prop-dm-nav-btn" id="btnPropDmPrevMonth" title="Previous Month">
            <i class="fi fi-rr-angle-small-left"></i>
        </button>
        <div class="prop-dm-month-title" id="btnPropDmToggleMonth" title="Click to pick month">
            <span id="propDmMonthLabel">April</span>
            <i class="fi fi-rr-angle-small-down" style="font-size: 11px; margin-left: 3px;"></i>
        </div>
        <button type="button" class="prop-dm-nav-btn" id="btnPropDmNextMonth" title="Next Month">
            <i class="fi fi-rr-angle-small-right"></i>
        </button>
    </div>
    <!-- Months Quick Selector (hidden by default) -->
    <div class="prop-dm-months-grid" id="propDmMonthsGrid" style="display: none;"></div>
    <!-- Calendar Day Grid View -->
    <div class="prop-dm-calendar-view" id="propDmCalendarView">
        <div class="prop-dm-weekdays">
            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
        </div>
        <div class="prop-dm-days-grid" id="propDmDaysGrid"></div>
    </div>
    <div class="prop-dm-foot">
        <button type="button" class="prop-dm-clear-btn" id="btnPropDmClear">Clear</button>
        <button type="button" class="prop-dm-close-btn" id="btnPropDmClose">Done</button>
    </div>
</div>