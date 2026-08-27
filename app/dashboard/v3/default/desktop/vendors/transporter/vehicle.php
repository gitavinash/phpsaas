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
                <p>Manage fleet categories, vehicle models, seating capacities, and transport assignments.</p>
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
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="vehicleSearchInput" class="filter-search-input" placeholder="Search by vehicle model, category or features...">
                <button type="button" id="btnVehicleSearchClear" class="clear-search-btn" title="Clear search" style="display: none;">✕</button>
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
                <button type="button" id="btnResetVehicleFilters" class="btn-v3-action" style="padding: 7px 12px; font-size: 12px;" data-tooltip="Reset all filters">
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
                        <th class="col-vehicle-specs">Specifications &amp; Features</th>
                        <th class="col-vehicle-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="vehiclesTableBody">
                    <tr>
                        <td colspan="5" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading fleet vehicles...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add / Edit Vehicle Modal Dialog -->
    <div class="v3-modal-overlay" id="vehicleModalOverlay">
        <div class="v3-modal-dialog">
            <div class="v3-modal-header">
                <div class="modal-header-title">
                    <div class="modal-icon-wrap">
                        <i class="fi fi-rr-car" id="modalHeaderIcon"></i>
                    </div>
                    <div>
                        <h3 id="vehicleModalTitle">Add New Vehicle</h3>
                        <p id="vehicleModalSubtitle">Configure model specifications and seating capacity</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseVehicleModal" title="Close">✕</button>
            </div>

            <form id="vehicleForm" novalidate>
                <input type="hidden" id="modalVehicleId" name="vehicle_id" value="">

                <div class="v3-modal-body">
                    <?php if (empty($fleetTransporterScoped)) { ?>
                        <div class="v3-form-group">
                            <label class="v3-form-label" for="modalVehicleTitle">Vehicle Model Name <sup>*</sup></label>
                            <div class="v3-input-icon-wrap">
                                <i class="fi fi-rr-car-alt"></i>
                                <input type="text" id="modalVehicleTitle" class="v3-form-control" placeholder="e.g. Toyota Innova Crysta, Volvo Multi-Axle" required>
                            </div>
                            <span class="v3-field-error" id="errModalTitle"></span>
                        </div>

                        <div class="v3-form-row">
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
                                <label class="v3-form-label" for="modalVehicleSeating">Seating Capacity (Pax)</label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-users-alt"></i>
                                    <input type="number" id="modalVehicleSeating" class="v3-form-control" placeholder="e.g. 7" min="0" max="100">
                                </div>
                                <span class="v3-field-error" id="errModalSeating"></span>
                            </div>
                        </div>

                        <div class="v3-form-group">
                            <label class="v3-form-label" for="modalVehicleDescription">Description / Features</label>
                            <div class="v3-input-icon-wrap textarea-wrap">
                                <i class="fi fi-rr-document-signed"></i>
                                <textarea id="modalVehicleDescription" class="v3-form-control" rows="3" placeholder="AC / Non-AC, luggage space, carrier, amenities..."></textarea>
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

                    <?php if (empty($fleetTransporterScoped)) { ?>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vehicle Photo / Icon</label>
                            <div class="v3-upload-zone" id="vehicleUploadZone">
                                <div class="v3-upload-preview" id="modalImagePreviewWrap">
                                    <img src="" alt="Vehicle preview" id="modalImagePreview">
                                    <button type="button" class="v3-remove-preview-btn" id="btnRemoveImagePreview" title="Remove photo">✕</button>
                                </div>
                                <div class="v3-upload-placeholder" id="modalUploadPlaceholder">
                                    <i class="fi fi-rr-cloud-upload"></i>
                                    <h4>Choose vehicle image or drag &amp; drop</h4>
                                    <p>PNG, JPG, WebP up to 5MB (Recommended: 300x200px)</p>
                                    <input type="file" id="modalVehicleFileInput" accept="image/*" class="v3-hidden-file-input">
                                    <input type="hidden" id="modalVehicleThumbnail" value="">
                                </div>
                            </div>
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
</div>