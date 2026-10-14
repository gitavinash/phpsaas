<?php
$fleetTransporterId = $_GET['id'] ?? ($_GET['transporter_id'] ?? '');
?>
<div class="package-page-wrapper vehicle-master-wrapper" id="vehiclesMasterApp" data-transporter-id="<?php echo htmlspecialchars((string)$fleetTransporterId, ENT_QUOTES, 'UTF-8'); ?>" data-fleet-scope="<?php echo !empty($fleetTransporterScoped) ? 'transporter' : 'master'; ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon vehicle-header-badge">
                <i class="fi fi-rr-car-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2>Fleet &amp; Vehicles Master</h2>
                <p>Manage fleet categories, vehicle models, photo galleries, per KM tariff rates, and seating capacities.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/transporter/list/" class="btn-v3-action" data-tooltip="Back to Transporters directory">
                <i class="fi fi-rr-arrow-left"></i> Transporters
            </a>
            <button type="button" class="btn-add-pkg-v3 btn-add-vehicle-v3" id="btnOpenAddVehicleModal" data-tooltip="Add new vehicle model to fleet">
                <i class="fi fi-rr-plus"></i> <span>Add Vehicle</span>
            </button>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-car-alt"></i></div>
            <div class="kpi-meta">
                <p>Total Fleet Models</p>
                <h3 id="statTotalVehicles">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-car"></i></div>
            <div class="kpi-meta">
                <p>MPVs &amp; SUVs</p>
                <h3 id="statMpvSuv">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-bus"></i></div>
            <div class="kpi-meta">
                <p>Tempos &amp; Coaches</p>
                <h3 id="statTempoBus">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-taxi"></i></div>
            <div class="kpi-meta">
                <p>Sedans &amp; Hatchbacks</p>
                <h3 id="statSedanHatch">-</h3>
            </div>
        </div>
    </div>

    <!-- Vehicles Table Card -->
    <div class="package-table-card" id="vehicleTableCard">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="vehicleSearchInput" class="filter-search-input" placeholder="Search by vehicle model, category, rate or features...">
                <button type="button" id="btnVehicleSearchClear" class="clear-search-btn" title="Clear search">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Category Filter -->
                <select id="vehicleCategoryDropdown" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="sedan">Sedans</option>
                    <option value="mpv">MPV</option>
                    <option value="suv">SUV</option>
                    <option value="bus">Bus / Coach</option>
                    <option value="traveller">Tempo Traveller</option>
                    <option value="light_commercial_vehicle">Light Commercial / Van</option>
                    <option value="hatchback">Hatchback</option>
                    <option value="micro">Small Car / Micro</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetVehicleFilters" class="btn-v3-action btn-reset-filters" data-tooltip="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Vehicles DataTable View -->
        <div class="list-box">
            <table class="package-v3-datatable" id="vehiclesTable">
                <thead>
                    <tr>
                        <th class="col-vehicle-model">Vehicle Model</th>
                        <th class="col-vehicle-type">Category / Type</th>
                        <th class="col-vehicle-capacity">Capacity</th>
                        <th class="col-vehicle-rates">Per KM Tariff Rates</th>
                        <th class="col-vehicle-specs">Specifications &amp; Features</th>
                        <th class="col-vehicle-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="vehiclesTableBody">
                    <tr>
                        <td colspan="6" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading fleet vehicles...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State View -->
    <div id="vehiclesEmptyState" class="transporter-fleet-empty-state is-hidden">
        <div class="transporter-fleet-empty-icon"><i class="fi fi-rr-car-alt"></i></div>
        <h3>No vehicles found</h3>
        <p id="vehicleEmptyStateMsg">Add your first vehicle model to configure fleet tariffs and galleries.</p>
        <button type="button" class="btn-add-pkg-v3 btn-add-vehicle-v3">
            <i class="fi fi-rr-plus"></i> Add Vehicle
        </button>
    </div>

    <!-- Add / Edit Vehicle Modal Dialog -->
    <div class="v3-modal-overlay" id="vehicleModalOverlay">
        <div class="v3-modal-dialog v3-modal-dialog-fleet">
            <div class="v3-modal-header">
                <div class="modal-header-title">
                    <div class="modal-icon-wrap">
                        <i class="fi fi-rr-car" id="modalHeaderIcon"></i>
                    </div>
                    <div>
                        <h3 id="vehicleModalTitle">Add New Fleet Vehicle</h3>
                        <p id="vehicleModalSubtitle">Configure vehicle model, image gallery, per KM rates, and seating capacity</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseVehicleModal" title="Close">✕</button>
            </div>

            <form id="vehicleForm" novalidate>
                <input type="hidden" id="modalVehicleId" name="vehicle_id" value="">
                <input type="hidden" id="modalVehicleThumbnail" name="thumbnail" value="">
                <input type="hidden" id="modalVehicleExistingGallery" name="existing_gallery" value="[]">

                <div class="v3-modal-body">
                    <?php if (empty($fleetTransporterScoped)) { ?>
                        <!-- Section 1: Basic Model Information -->
                        <div class="fleet-form-section">
                            <div class="section-legend">
                                <div class="section-legend-icon"><i class="fi fi-rr-car-alt"></i></div>
                                <div>
                                    <h4>Vehicle Model &amp; Type</h4>
                                    <p>Specify the brand model name, vehicle category, and fuel type</p>
                                </div>
                            </div>
                            <div class="fleet-grid-3">
                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleTitle">Vehicle Model Name <sup>*</sup></label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-car-alt"></i>
                                        <input type="text" id="modalVehicleTitle" class="v3-form-control" placeholder="e.g. Toyota Innova Crysta, Volvo Multi-Axle" required>
                                    </div>
                                    <span class="v3-field-error" id="errModalTitle"></span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleType">Category / Type <sup>*</sup></label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-apps"></i>
                                        <select id="modalVehicleType" class="v3-form-control" required>
                                            <option value="">Select Category</option>
                                            <option value="mpv">MPV</option>
                                            <option value="suv">SUV</option>
                                            <option value="bus">Bus / Coach</option>
                                            <option value="traveller">Tempo Traveller</option>
                                            <option value="light_commercial_vehicle">Light Commercial / Van</option>
                                            <option value="sedan">Sedan</option>
                                            <option value="hatchback">Hatchback</option>
                                            <option value="micro">Micro / Small Car</option>
                                            <option value="flight">Flight</option>
                                            <option value="rail">Rail</option>
                                        </select>
                                    </div>
                                    <span class="v3-field-error" id="errModalType"></span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleFuel">Fuel Type</label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-gas-pump"></i>
                                        <select id="modalVehicleFuel" class="v3-form-control">
                                            <option value="">Select Fuel (Optional)</option>
                                            <option value="diesel">Diesel</option>
                                            <option value="petrol">Petrol</option>
                                            <option value="cng">CNG</option>
                                            <option value="electric">Electric (EV)</option>
                                            <option value="hybrid">Hybrid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Capacities -->
                        <div class="fleet-form-section">
                            <div class="section-legend">
                                <div class="section-legend-icon blue"><i class="fi fi-rr-users-alt"></i></div>
                                <div>
                                    <h4>Capacities</h4>
                                    <p>Configure passenger seating and luggage capacity</p>
                                </div>
                            </div>
                            <div class="fleet-grid-2">
                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleSeating">Seating Capacity (Pax)</label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-users-alt"></i>
                                        <input type="number" id="modalVehicleSeating" class="v3-form-control" placeholder="e.g. 7" min="1" max="100">
                                    </div>
                                    <span class="fleet-field-helper">Maximum passenger seating capacity (excluding driver)</span>
                                    <span class="v3-field-error" id="errModalSeating"></span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleLuggage">Luggage Capacity (Bags)</label>
                                    <div class="v3-input-icon-wrap">
                                        <i class="fi fi-rr-briefcase"></i>
                                        <input type="number" id="modalVehicleLuggage" class="v3-form-control" placeholder="e.g. 4" min="0" max="50">
                                    </div>
                                    <span class="fleet-field-helper">Standard boot space / luggage capacity in medium bags</span>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Per KM Tariff Rates -->
                        <div class="fleet-form-section tint-pricing">
                            <div class="section-legend">
                                <div class="section-legend-icon green"><i class="fi fi-rr-badge-percent"></i></div>
                                <div>
                                    <h4>Per KM Tariff Rates</h4>
                                    <p>Specify standard running charges with and without toll/tax inclusions</p>
                                </div>
                            </div>
                            <div class="fleet-grid-2">
                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleRateWithTax">Rate / KM (Incl. Toll &amp; State Tax)</label>
                                    <div class="fleet-currency-input-wrap">
                                        <span class="fleet-currency-prefix">₹</span>
                                        <input type="number" id="modalVehicleRateWithTax" class="v3-form-control" placeholder="0.00" step="0.5" min="0">
                                    </div>
                                    <span class="fleet-field-helper positive"><i class="fi fi-rr-check-circle"></i> Includes all toll booths, state border permits &amp; road taxes</span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleRateWithoutTax">Rate / KM (Without State &amp; Toll Tax)</label>
                                    <div class="fleet-currency-input-wrap">
                                        <span class="fleet-currency-prefix">₹</span>
                                        <input type="number" id="modalVehicleRateWithoutTax" class="v3-form-control" placeholder="0.00" step="0.5" min="0">
                                    </div>
                                    <span class="fleet-field-helper">Base running rate only (Tolls, parking, and state permit taxes charged extra)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Vehicle Media (Feature Image + Multi-Gallery) -->
                        <div class="fleet-form-section tint-media">
                            <div class="section-legend">
                                <div class="section-legend-icon"><i class="fi fi-rr-picture"></i></div>
                                <div>
                                    <h4>Vehicle Photos &amp; Gallery</h4>
                                    <p>Upload a primary feature cover photo and multiple vehicle gallery angles</p>
                                </div>
                            </div>

                            <div class="fleet-media-layout">
                                <!-- Feature Cover Photo -->
                                <div class="fleet-feature-image-card">
                                    <label class="v3-form-label">Feature Cover Photo</label>
                                    <div class="fleet-feature-preview-zone" id="fleetFeaturePreviewZone" title="Click to upload feature image">
                                        <div id="fleetFeatureEmptyState">
                                            <i class="fi fi-rr-cloud-upload fleet-feature-empty-icon"></i>
                                            <h5 class="fleet-feature-empty-title">Upload Cover Photo</h5>
                                            <p class="fleet-feature-empty-hint">PNG, JPG, WebP up to 5MB</p>
                                        </div>
                                        <div id="fleetFeatureActiveState" class="is-hidden">
                                            <span class="fleet-feature-badge-tag"><i class="fi fi-rr-star"></i> Featured Cover</span>
                                            <button type="button" class="fleet-feature-remove-btn" id="btnRemoveFeaturePhoto" title="Remove cover photo">✕</button>
                                            <img src="" alt="Feature preview" id="fleetFeatureImg" class="fleet-feature-img">
                                        </div>
                                        <input type="file" id="modalVehicleFeatureInput" accept="image/*" class="is-hidden">
                                    </div>
                                </div>

                                <!-- Multi-Image Gallery -->
                                <div class="fleet-gallery-zone-card">
                                    <div class="fleet-gallery-header-row">
                                        <label class="v3-form-label">Vehicle Photo Gallery</label>
                                        <span id="fleetGalleryCountBadge" class="fleet-gallery-count-badge">0 photos</span>
                                    </div>

                                    <div class="fleet-gallery-drop-bar" id="fleetGalleryDropBar">
                                        <div class="drop-bar-left">
                                            <div class="drop-bar-icon"><i class="fi fi-rr-images"></i></div>
                                            <div class="drop-bar-text">
                                                <h5>Add gallery photos</h5>
                                                <p>Drop images here or click browse (Interior, boot space, seats, exterior)</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-browse-gallery" id="btnBrowseGalleryFiles">
                                            <i class="fi fi-rr-plus"></i> Select Images
                                        </button>
                                        <input type="file" id="modalVehicleGalleryInput" accept="image/*" multiple class="is-hidden">
                                    </div>

                                    <!-- Visual Gallery Thumbnails Grid -->
                                    <div id="fleetGalleryGrid" class="fleet-gallery-grid">
                                        <div class="fleet-gallery-empty-hint" id="fleetGalleryEmptyHint">
                                            No gallery photos uploaded yet. Click above to select multiple photos.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Amenities & Features Description -->
                        <div class="fleet-form-section">
                            <div class="section-legend">
                                <div class="section-legend-icon"><i class="fi fi-rr-document-signed"></i></div>
                                <div>
                                    <h4>Specifications, Amenities &amp; Notes</h4>
                                    <p>Select key amenities or add free-text description for client proposals</p>
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label class="v3-form-label">Quick Amenity Tags</label>
                                <div class="fleet-amenity-tags-wrap" id="fleetAmenityTagsWrap">
                                    <span class="fleet-amenity-pill" data-tag="Air Conditioned (AC)">AC</span>
                                    <span class="fleet-amenity-pill" data-tag="Pushback Reclining Seats">Pushback Seats</span>
                                    <span class="fleet-amenity-pill" data-tag="Luggage Roof Carrier">Luggage Carrier</span>
                                    <span class="fleet-amenity-pill" data-tag="Music &amp; Audio System">Music System</span>
                                    <span class="fleet-amenity-pill" data-tag="USB Mobile Charging Points">USB Chargers</span>
                                    <span class="fleet-amenity-pill" data-tag="GPS Real-Time Tracking">GPS Tracking</span>
                                    <span class="fleet-amenity-pill" data-tag="First Aid Kit">First Aid Box</span>
                                    <span class="fleet-amenity-pill" data-tag="Clean &amp; Sanitized">Clean &amp; Sanitized</span>
                                    <span class="fleet-amenity-pill" data-tag="Uniformed Driver">Uniformed Driver</span>
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label class="v3-form-label" for="modalVehicleDescription">Detailed Amenities &amp; Vehicle Notes</label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-document-signed"></i>
                                    <textarea id="modalVehicleDescription" class="v3-form-control" rows="3" placeholder="AC / Non-AC, luggage space, carrier, amenities, driver allowance terms..."></textarea>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($fleetTransporterScoped)) { ?>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Assign Master Fleet Models</label>
                            <div id="transporterFleetChecklist" class="transporter-fleet-checklist">
                                <span class="v3-field-hint">Loading master fleet options...</span>
                            </div>
                            <span class="v3-field-hint">Select every master vehicle this transporter can operate.</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="v3-modal-footer">
                    <button type="button" class="v3-btn-secondary" id="btnCancelVehicleModal">Cancel</button>
                    <?php if (!empty($fleetTransporterScoped)) { ?>
                        <button type="button" class="v3-btn-secondary" id="btnCreateMasterVehicle">
                            <i class="fi fi-rr-plus"></i> Create New Master Vehicle
                        </button>
                    <?php } ?>
                    <button type="submit" class="v3-btn-primary" id="btnSaveVehicleModal">
                        <i class="fi fi-rr-check"></i> <span id="btnSaveVehicleText">Save Vehicle</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Gallery Lightbox Viewer Modal -->
    <div class="v3-modal-overlay" id="vehicleGalleryViewerModal">
        <div class="v3-modal-dialog fleet-lightbox-dialog">
            <div class="v3-modal-header">
                <div class="modal-header-title">
                    <div class="modal-icon-wrap"><i class="fi fi-rr-images"></i></div>
                    <div>
                        <h3 id="lightboxVehicleTitle">Vehicle Gallery</h3>
                        <p id="lightboxVehicleSubtitle">Browse all photos of this vehicle model</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseLightboxModal" title="Close">✕</button>
            </div>
            <div class="fleet-lightbox-body">
                <div class="lightbox-main-stage">
                    <button type="button" class="lightbox-nav-btn prev" id="btnLightboxPrev" title="Previous photo">❮</button>
                    <img src="" alt="Vehicle photo preview" id="lightboxMainImg" class="lightbox-main-img">
                    <button type="button" class="lightbox-nav-btn next" id="btnLightboxNext" title="Next photo">❯</button>
                </div>
                <div class="lightbox-thumb-strip" id="lightboxThumbStrip"></div>
            </div>
        </div>
    </div>
</div>