<?php
/**
 * V3 Add Property View
 * Path: app/dashboard/v3/default/desktop/vendors/property/add.php
 */
?>
<div class="property-update-page property-add-page">

    <!-- ── Header Bar ───────────────────────────────────────────── -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-building"></i>
            </div>
            <div class="package-header-titles">
                <h2>Add New Property</h2>
                <p>Create a new hotel, resort, guest house, or homestay profile.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/property/list/" class="btn-v3-action" data-tooltip="Back to Properties Directory">
                <i class="fi fi-rr-arrow-left"></i> Properties Directory
            </a>
        </div>
    </div>

    <!-- ── 2-Column Form Layout ──────────────────────────────────── -->
    <div class="prop-form-full-layout">
        <div class="v3-form-main-col">

            <form id="propertyAddForm" class="v3-form-container" autocomplete="off" novalidate enctype="multipart/form-data">

                <div class="prop-add-grid-layout">

                    <!-- ── Left Column ────────────────────────────── -->
                    <div class="prop-add-col">

                        <!-- Card 1 · Hotel Identity ────────────────── -->
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
                                            <i class="fi fi-rr-apps input-icon"></i>
                                            <select id="propType" name="type" class="v3-form-select selectjs2" required>
                                                <option value="">— Select property type —</option>
                                                <option value="3_star_hotel" selected>3 Star Hotel (Comfort)</option>
                                                <option value="4_star_hotel">4 Star Hotel (First Class)</option>
                                                <option value="5_star_hotel">5 Star Hotel (Luxury)</option>
                                                <option value="2_star_hotel">2 Star Hotel (Standard)</option>
                                                <option value="1_star_hotel">1 Star Hotel (Tourist)</option>
                                                <option value="resorts">Resort</option>
                                                <option value="home_stay">Home Stay</option>
                                                <option value="guest_house">Guest House</option>
                                                <option value="apartments">Apartments</option>
                                                <option value="lodge">Lodge</option>
                                                <option value="motel">Motel</option>
                                                <option value="inn">Inn</option>
                                                <option value="hostel">Hostel</option>
                                                <option value="house">House</option>
                                                <option value="tent">Tent / Camp</option>
                                                <option value="farm_stay">Farm Stay</option>
                                                <option value="capsule_hotel">Capsule Hotel</option>
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

                        <!-- Card 2 · Location & Address ────────────── -->
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
                                        <label for="propPinCode" class="v3-form-label">
                                            Postal / PIN Code <span class="required">*</span>
                                        </label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-marker input-icon"></i>
                                            <input type="text" id="propPinCode" name="pinCode" class="v3-form-input"
                                                placeholder="e.g. 175131" maxlength="10" required>
                                        </div>
                                        <span class="v3-field-error" id="errPropPinCode"></span>
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
                                        <span class="v3-field-hint">Paste Google Maps share link or embed URL to show directions to guests.</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Card 3 · Status & Publishing ───────────── -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon rose">
                                    <i class="fi fi-rr-settings"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Listing Status</h3>
                                    <p>Control whether this property is active and published to the website.</p>
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

                                <div style="margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
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
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ── Right Column ───────────────────────────── -->
                    <div class="prop-add-col">

                        <!-- Card 4 · Contact Details ───────────────── -->
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
                                            Support / Frontdesk Email
                                        </label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-envelope-plus input-icon"></i>
                                            <input type="email" id="propSupportEmail" name="supportEmail" class="v3-form-input"
                                                placeholder="support@hotel.com">
                                        </div>
                                    </div>

                                    <!-- Support Phone -->
                                    <div class="v3-form-group">
                                        <label for="propSupportMobile" class="v3-form-label">
                                            Support / Frontdesk Phone
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

                        <!-- Card 5 · Feature Image ─────────────────── -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon amber">
                                    <i class="fi fi-rr-picture"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Feature Cover Image</h3>
                                    <p>Primary listing photo shown on search results and booking cards.</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <div class="v3-form-group">
                                    <div class="prop-thumb-preview-wrap" id="propThumbPreviewWrap">
                                        <div class="prop-thumb-current" id="propThumbCurrent">
                                            <i class="fi fi-rr-building"></i>
                                        </div>
                                        <div class="prop-thumb-meta">
                                            <p class="prop-thumb-label" id="propThumbLabel">No feature image selected</p>
                                            <span class="prop-thumb-hint">Recommended: 1200×800 px, JPG/PNG/WebP, max 5 MB</span>
                                        </div>
                                    </div>
                                    <label class="v3-form-label">Select Feature Image</label>
                                    <div class="prop-file-upload-zone" id="propFileUploadZone">
                                        <input type="file" id="propFeatureImage" name="featureImage" class="prop-file-input-hidden"
                                            accept="image/jpeg,image/png,image/gif,image/webp">
                                        <div class="prop-file-upload-inner">
                                            <div class="prop-file-upload-icon">
                                                <i class="fi fi-rr-cloud-upload"></i>
                                            </div>
                                            <div class="prop-file-upload-text">
                                                <span class="prop-file-upload-title" id="propFileTitle">Choose a cover image or drag &amp; drop</span>
                                                <span class="prop-file-selected-name" id="propFileSelectedName">JPG, PNG, GIF or WebP (max 5 MB)</span>
                                            </div>
                                            <button type="button" class="v3-btn-secondary prop-file-browse-btn" tabindex="-1">
                                                <i class="fi fi-rr-folder"></i> Browse
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 6 · Hotel Gallery & Photos ───────── -->
                        <div class="v3-card-section">
                            <div class="v3-card-header">
                                <div class="v3-card-header-icon violet">
                                    <i class="fi fi-rr-gallery"></i>
                                </div>
                                <div class="v3-card-header-text">
                                    <h3>Hotel Gallery &amp; Photos</h3>
                                    <p>Upload multiple property photos for dining, lobby, views and exterior.</p>
                                </div>
                            </div>
                            <div class="v3-card-body">
                                <div class="prop-gallery-section-wrap">
                                    <div class="prop-gallery-block">
                                        <label class="v3-form-label">Upload Hotel Photos (Multiple)</label>
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
                                        <span class="v3-field-hint">You can select and upload multiple images at once.</span>

                                        <!-- Staged New Photos Preview Grid -->
                                        <div class="prop-gallery-grid" id="propStagedGalleryGrid" style="margin-top: 12px; display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- ── Sticky Action Footer ──────────────────────── -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/vendors/property/list/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="v3-btn-primary" id="btnCreateProperty">
                        <i class="fi fi-rr-check"></i> <span id="btnCreateText">Create Property &amp; Configure Rooms</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>