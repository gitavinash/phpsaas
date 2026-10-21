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
                <h2>Fleet &amp; Vehicles</h2>
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
                <select id="vehicleCategoryDropdown" class="filter-select selectJS">
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
        <div class="v3-modal-dialog v3-modal-dialog-fleet v3-modal-dialog-xl">
            <div class="v3-modal-header">
                <div class="modal-header-title">
                    <div class="modal-icon-wrap">
                        <i class="fi fi-rr-car-alt" id="modalHeaderIcon"></i>
                    </div>
                    <div>
                        <h3 id="vehicleModalTitle">Add New Fleet Vehicle</h3>
                        <p id="vehicleModalSubtitle">Configure vehicle model specifications, photo gallery, tariff rates, and seating capacity</p>
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
                        <!-- Group 1: General Specifications -->
                        <div class="fleet-mature-card">
                            <div class="fleet-card-header">
                                <div class="fleet-card-header-icon indigo"><i class="fi fi-rr-settings-sliders"></i></div>
                                <div>
                                    <h4>General Specifications</h4>
                                    <p>Vehicle brand model, body classification, and powertrain</p>
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
                                    <label class="v3-form-label" for="modalVehicleType">Category / Body Type <sup>*</sup></label>
                                    <select id="modalVehicleType" class="v3-form-control selectjs2" required>
                                        <option value="">Select Category</option>
                                        <option value="mpv" data-icon="fi-rr-cars">MPV (Multi-Purpose Vehicle)</option>
                                        <option value="suv" data-icon="fi-rr-truck-pickup">SUV (Sport Utility Vehicle)</option>
                                        <option value="sedan" data-icon="fi-rr-car-side">Sedan (Executive Car)</option>
                                        <option value="hatchback" data-icon="fi-rr-car-alt">Hatchback (Compact Car)</option>
                                        <option value="traveller" data-icon="fi-rr-bus">Tempo Traveller (Minibus)</option>
                                        <option value="bus" data-icon="fi-rr-bus-alt">Bus / Coach (Heavy Passenger)</option>
                                        <option value="light_commercial_vehicle" data-icon="fi-rr-truck-side">Light Commercial / Van</option>
                                        <option value="micro" data-icon="fi-rr-car">Micro / Small Car</option>
                                        <option value="flight" data-icon="fi-rr-plane">Flight</option>
                                        <option value="rail" data-icon="fi-rr-train">Rail</option>
                                    </select>
                                    <span class="v3-field-error" id="errModalType"></span>
                                </div>

                                <div class="v3-form-group">
                                    <label class="v3-form-label" for="modalVehicleFuel">Fuel / Powertrain</label>
                                    <select id="modalVehicleFuel" class="v3-form-control selectjs2">
                                        <option value="">Select Fuel (Optional)</option>
                                        <option value="diesel" data-icon="fi-rr-gas-pump">Diesel</option>
                                        <option value="petrol" data-icon="fi-rr-oil-can">Petrol</option>
                                        <option value="cng" data-icon="fi-rr-wind">CNG (Natural Gas)</option>
                                        <option value="electric" data-icon="fi-rr-bolt">Electric (EV)</option>
                                        <option value="hybrid" data-icon="fi-rr-leaf">Hybrid</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Group 2: Capacities & Per-KM Tariff Rates -->
                        <div class="fleet-mature-card-row">
                            <!-- Left: Capacities -->
                            <div class="fleet-mature-card flex-1">
                                <div class="fleet-card-header">
                                    <div class="fleet-card-header-icon blue"><i class="fi fi-rr-users-alt"></i></div>
                                    <div>
                                        <h4>Passenger &amp; Luggage Capacity</h4>
                                        <p>Seating arrangement and luggage capacity</p>
                                    </div>
                                </div>
                                <div class="fleet-grid-2">
                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="modalVehicleSeating">Seating Capacity (Pax)</label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-users-alt"></i>
                                            <input type="number" id="modalVehicleSeating" class="v3-form-control" placeholder="e.g. 7" min="1" max="100">
                                        </div>
                                        <span class="fleet-field-helper">Passengers (excl. driver)</span>
                                        <span class="v3-field-error" id="errModalSeating"></span>
                                    </div>

                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="modalVehicleLuggage">Luggage Capacity (Bags)</label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-briefcase"></i>
                                            <input type="number" id="modalVehicleLuggage" class="v3-form-control" placeholder="e.g. 4" min="0" max="50">
                                        </div>
                                        <span class="fleet-field-helper">Standard boot bags</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Tariff Pricing -->
                            <div class="fleet-mature-card flex-1">
                                <div class="fleet-card-header">
                                    <div class="fleet-card-header-icon green"><i class="fi fi-rr-badge-percent"></i></div>
                                    <div>
                                        <h4>Per KM Tariff Rates</h4>
                                        <p>Running tariff with and without toll/tax inclusions</p>
                                    </div>
                                </div>
                                <div class="fleet-grid-2">
                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="modalVehicleRateWithTax">Rate / KM (Incl. Toll &amp; Tax)</label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-indian-rupee-sign"></i>
                                            <input type="number" id="modalVehicleRateWithTax" class="v3-form-control" placeholder="e.g. 18.00" step="0.5" min="0">
                                        </div>
                                        <span class="fleet-field-helper"><i class="fi fi-rr-check-circle"></i> Tolls, permits &amp; state tax included</span>
                                    </div>

                                    <div class="v3-form-group">
                                        <label class="v3-form-label" for="modalVehicleRateWithoutTax">Rate / KM (Without Toll &amp; Tax)</label>
                                        <div class="v3-input-icon-wrap">
                                            <i class="fi fi-rr-indian-rupee-sign"></i>
                                            <input type="number" id="modalVehicleRateWithoutTax" class="v3-form-control" placeholder="e.g. 14.00" step="0.5" min="0">
                                        </div>
                                        <span class="fleet-field-helper"><i class="fi fi-rr-info"></i> Base rate only (Tolls &amp; taxes extra)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group 3: Feature Photo & Visual Gallery Manager -->
                        <div class="fleet-mature-card">
                            <div class="fleet-card-header">
                                <div class="fleet-card-header-icon purple"><i class="fi fi-rr-picture"></i></div>
                                <div>
                                    <h4>Visual Media &amp; Photo Gallery</h4>
                                    <p>Upload a primary feature cover photo and multiple vehicle gallery angles</p>
                                </div>
                            </div>

                            <div class="fleet-media-layout">
                                <!-- Feature Cover Photo (Left 310px) -->
                                <div class="fleet-feature-image-card">
                                    <div class="fleet-media-subhead">
                                        <label class="v3-form-label">Primary Cover Photo</label>
                                        <span class="fleet-feature-sub-badge">Main Display</span>
                                    </div>
                                    <div class="fleet-feature-preview-zone" id="fleetFeaturePreviewZone" title="Click or drop cover photo here">
                                        <div id="fleetFeatureEmptyState">
                                            <i class="fi fi-rr-cloud-upload fleet-feature-empty-icon"></i>
                                            <h5 class="fleet-feature-empty-title">Upload Cover Photo</h5>
                                            <p class="fleet-feature-empty-hint">PNG, JPG, WebP up to 5MB</p>
                                            <span class="btn-browse-feature"><i class="fi fi-rr-plus"></i> Browse Photo</span>
                                        </div>
                                        <div id="fleetFeatureActiveState" class="is-hidden">
                                            <span class="fleet-feature-badge-tag"><i class="fi fi-rr-star"></i> Featured Cover</span>
                                            <button type="button" class="fleet-feature-remove-btn" id="btnRemoveFeaturePhoto" title="Remove cover photo">✕</button>
                                            <img src="" alt="Feature preview" id="fleetFeatureImg" class="fleet-feature-img">
                                        </div>
                                        <input type="file" id="modalVehicleFeatureInput" accept="image/*" class="is-hidden">
                                    </div>
                                </div>

                                <!-- Multi-Image Gallery (Right flex-1) -->
                                <div class="fleet-gallery-zone-card">
                                    <div class="fleet-media-subhead">
                                        <div class="fleet-media-subhead-left">
                                            <label class="v3-form-label">Vehicle Photo Gallery</label>
                                            <span id="fleetGalleryCountBadge" class="fleet-gallery-count-badge">0 photos</span>
                                        </div>
                                        <div class="fleet-gallery-top-actions is-hidden" id="fleetGalleryTopActions">
                                            <button type="button" class="btn-gallery-action-link" id="btnBrowseGalleryFilesTop">
                                                <i class="fi fi-rr-plus"></i> Add Photos
                                            </button>
                                            <button type="button" class="btn-gallery-action-link danger" id="btnClearAllGalleryFiles">
                                                <i class="fi fi-rr-trash"></i> Clear All
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Empty Gallery Dropzone (Matches Left Cover Photo Height & Polish) -->
                                    <div class="fleet-gallery-preview-zone" id="fleetGalleryEmptyDropzone" title="Click or drop gallery photos here">
                                        <i class="fi fi-rr-picture fleet-feature-empty-icon"></i>
                                        <h5 class="fleet-feature-empty-title">Upload Photo Gallery</h5>
                                        <p class="fleet-feature-empty-hint">Drag &amp; drop multiple vehicle photos or browse (Seats, dashboard, boot, exterior)</p>
                                        <span class="btn-browse-feature" id="btnBrowseGalleryEmpty">
                                            <i class="fi fi-rr-plus"></i> Select Gallery Images
                                        </span>
                                    </div>

                                    <!-- Active Gallery Container with Grid and Add-More Tile -->
                                    <div class="fleet-gallery-active-container is-hidden" id="fleetGalleryActiveContainer">
                                        <div id="fleetGalleryGrid" class="fleet-gallery-grid"></div>
                                    </div>

                                    <input type="file" id="modalVehicleGalleryInput" accept="image/*" multiple class="is-hidden">
                                </div>
                            </div>
                        </div>

                        <!-- Group 4: Amenities & Notes -->
                        <div class="fleet-mature-card">
                            <div class="fleet-card-header">
                                <div class="fleet-card-header-icon amber"><i class="fi fi-rr-sparkles"></i></div>
                                <div>
                                    <h4>Amenities, Features &amp; Proposal Notes</h4>
                                    <p>Select key amenities or add free-text description for quotes &amp; itineraries</p>
                                </div>
                            </div>

                            <div class="v3-form-group">
                                <label class="v3-form-label">Quick Amenity Highlights</label>
                                <div class="fleet-amenity-tags-wrap" id="fleetAmenityTagsWrap">
                                    <span class="fleet-amenity-pill" data-tag="Air Conditioned (AC)"><i class="fi fi-rr-snowflake"></i> AC</span>
                                    <span class="fleet-amenity-pill" data-tag="Pushback Reclining Seats"><i class="fi fi-rr-chair"></i> Pushback Seats</span>
                                    <span class="fleet-amenity-pill" data-tag="Luggage Roof Carrier"><i class="fi fi-rr-luggage-cart"></i> Roof Carrier</span>
                                    <span class="fleet-amenity-pill" data-tag="Music &amp; Audio System"><i class="fi fi-rr-music"></i> Music System</span>
                                    <span class="fleet-amenity-pill" data-tag="USB Mobile Charging Points"><i class="fi fi-rr-charging-station"></i> USB Chargers</span>
                                    <span class="fleet-amenity-pill" data-tag="GPS Real-Time Tracking"><i class="fi fi-rr-marker"></i> GPS Tracking</span>
                                    <span class="fleet-amenity-pill" data-tag="Clean &amp; Sanitized"><i class="fi fi-rr-shield-check"></i> Clean &amp; Sanitized</span>
                                    <span class="fleet-amenity-pill" data-tag="Uniformed Driver"><i class="fi fi-rr-user-police-tie"></i> Uniformed Driver</span>
                                    <span class="fleet-amenity-pill" data-tag="First Aid Kit"><i class="fi fi-rr-cross"></i> First Aid Box</span>
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
                            <label class="v3-form-label">Assign Fleet Models</label>
                            <div id="transporterFleetChecklist" class="transporter-fleet-checklist">
                                <span class="v3-field-hint">Loading fleet options...</span>
                            </div>
                            <span class="v3-field-hint">Select every vehicle this transporter can operate.</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="v3-modal-footer">
                    <button type="button" class="v3-btn-secondary" id="btnCancelVehicleModal">Cancel</button>
                    <?php if (!empty($fleetTransporterScoped)) { ?>
                        <button type="button" class="v3-btn-secondary" id="btnCreateMasterVehicle">
                            <i class="fi fi-rr-plus"></i> Create New Vehicle
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