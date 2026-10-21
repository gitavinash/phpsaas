<?php
$fleetTransporterId = $_GET['id'] ?? ($_GET['transporter_id'] ?? '');
?>
<div class="package-page-wrapper vehicle-master-wrapper transporter-fleet-wrapper" id="vehiclesMasterApp"
    data-transporter-id="<?php echo htmlspecialchars((string)$fleetTransporterId, ENT_QUOTES, 'UTF-8'); ?>"
    data-fleet-scope="transporter">
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon vehicle-header-badge">
                <i class="fi fi-rr-car-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2>Transporter Fleet</h2>
                <p>Choose the vehicle models available to this transporter.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/transporter/list/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Transporters
            </a>
            <button type="button" class="btn-add-pkg-v3 btn-add-vehicle-v3" id="btnOpenAddVehicleModal">
                <i class="fi fi-rr-plus"></i> Assign Vehicle
            </button>
        </div>
    </div>

    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-car-alt"></i></div>
            <div class="kpi-meta">
                <p>Assigned Fleet</p>
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

    <div class="package-table-card" id="vehicleTableCard">
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <input type="text" id="vehicleSearchInput" class="filter-search-input" placeholder="Search assigned fleet...">
                <button type="button" id="btnVehicleSearchClear" class="clear-search-btn" title="Clear search">✕</button>
            </div>
            <div class="filter-dropdowns-wrap">
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
                <button type="button" id="btnResetVehicleFilters" class="btn-v3-action">Reset</button>
            </div>
        </div>
        <div class="list-box">
            <table class="package-v3-datatable" id="vehiclesTable">
                <thead>
                    <tr>
                        <th>Vehicle Model</th>
                        <th>Category / Type</th>
                        <th>Capacity</th>
                        <th>Specifications &amp; Features</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vehiclesTableBody">
                    <tr>
                        <td colspan="5" class="table-loading-state">
                            <div class="table-loading-spinner"></div><span>Loading assigned fleet...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="vehiclesEmptyState" class="transporter-fleet-empty-state is-hidden">
        <div class="transporter-fleet-empty-icon"><i class="fi fi-rr-car-alt"></i></div>
        <h3>No fleet vehicles assigned</h3>
        <p id="vehicleEmptyStateMsg">Choose the vehicle models this transporter can operate.</p>
        <button type="button" class="btn-add-pkg-v3 btn-add-vehicle-v3">
            <i class="fi fi-rr-plus"></i> Assign Vehicle
        </button>
    </div>

    <div class="v3-modal-overlay" id="vehicleModalOverlay">
        <div class="v3-modal-dialog">
            <div class="v3-modal-header">
                <div class="modal-header-title">
                    <div class="modal-icon-wrap"><i class="fi fi-rr-car" id="modalHeaderIcon"></i></div>
                    <div>
                        <h3 id="vehicleModalTitle">Choose Fleet Vehicles</h3>
                        <p id="vehicleModalSubtitle">Select the vehicles available to this transporter</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseVehicleModal" title="Close">✕</button>
            </div>
            <form id="vehicleForm" novalidate>
                <div class="v3-modal-body">
                    <div class="v3-form-group">
                        <label class="v3-form-label">Fleet Models</label>
                        <div class="transporter-fleet-selector-toolbar">
                            <input type="search" id="transporterFleetSearch" class="filter-search-input" placeholder="Search vehicles...">
                            <button type="button" class="v3-btn-secondary" id="btnSelectAllFleet">Select All</button>
                            <button type="button" class="v3-btn-secondary" id="btnClearAllFleet">Clear</button>
                            <span id="transporterFleetSelectedCount" class="transporter-fleet-selected-count">0 selected</span>
                        </div>
                        <div id="transporterFleetChecklist" class="transporter-fleet-checklist"><span class="v3-field-hint">Loading fleet options...</span></div>
                    </div>
                </div>
                <div class="v3-modal-footer">
                    <button type="button" class="v3-btn-secondary" id="btnCancelVehicleModal">Cancel</button>
                    <button type="submit" class="v3-btn-primary" id="btnSaveVehicleModal"><i class="fi fi-rr-check"></i> <span id="btnSaveVehicleText">Save Fleet Selection</span></button>
                </div>
            </form>
        </div>
    </div>
</div>