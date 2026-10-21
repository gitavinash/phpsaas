<?php

/**
 * Master Data Copy Modal Component
 * Path: app/dashboard/v3/default/desktop/templates/master-data-modal.php
 * Interactive dialog allowing users to copy master tour packages, destinations, vehicles, hotels, and transporters.
 */

?>
<div id="masterDataModal" class="master-modal-backdrop" aria-hidden="true" role="dialog" aria-labelledby="masterModalTitle">
    <div class="master-modal-card">
        <!-- Header -->
        <div class="master-modal-header">
            <div class="master-modal-title-group">
                <div class="master-modal-icon-badge">
                    <i class="fi fi-rr-cloud-download"></i>
                </div>
                <div class="master-modal-titles">
                    <h3 id="masterModalTitle">Copy Master Catalog to Dashboard</h3>
                    <p>Select ready-to-sell master data to instantly clone into your workspace with full day-wise itineraries, rates, and fleet details.</p>
                </div>
            </div>
            <button type="button" class="master-modal-close-btn" id="btnCloseMasterModal" aria-label="Close dialog">
                <i class="fi fi-rr-cross"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="master-modal-body">
            <!-- Datasets Grid -->
            <div class="master-dataset-grid">
                <!-- 1. Tour Packages -->
                <label class="master-dataset-item selected" for="masterDatasetTourPkg">
                    <input type="checkbox" id="masterDatasetTourPkg" name="master_types[]" value="tour_package" checked class="master-dataset-checkbox">
                    <div class="master-dataset-icon blue">
                        <i class="fi fi-rr-suitcase-alt"></i>
                    </div>
                    <div class="master-dataset-info">
                        <div class="master-dataset-title-row">
                            <h4 class="master-dataset-name">Tour Packages</h4>
                            <span class="master-dataset-count-badge" id="badgeMasterPackages">155 Ready</span>
                        </div>
                        <p class="master-dataset-desc">Day-by-day itineraries, inclusions, exclusions, pricing slabs, and hotel mappings.</p>
                        <span class="master-dataset-current-stat" id="statWorkspacePackages">
                            <i class="fi fi-rr-check"></i> <span id="textWorkspacePackages">Checking workspace...</span>
                        </span>
                    </div>
                </label>

                <!-- 2. Hotels & Properties -->
                <label class="master-dataset-item selected" for="masterDatasetHotels">
                    <input type="checkbox" id="masterDatasetHotels" name="master_types[]" value="hotels" checked class="master-dataset-checkbox">
                    <div class="master-dataset-icon teal">
                        <i class="fi fi-rr-building"></i>
                    </div>
                    <div class="master-dataset-info">
                        <div class="master-dataset-title-row">
                            <h4 class="master-dataset-name">Classified Hotels</h4>
                            <span class="master-dataset-count-badge" id="badgeMasterHotels">315 Ready</span>
                        </div>
                        <p class="master-dataset-desc">3-Star, 4-Star &amp; 5-Star verified properties with room inventory across popular travel hubs.</p>
                        <span class="master-dataset-current-stat" id="statWorkspaceHotels">
                            <i class="fi fi-rr-check"></i> <span id="textWorkspaceHotels">Checking workspace...</span>
                        </span>
                    </div>
                </label>

                <!-- 3. Fleet & Vehicles -->
                <label class="master-dataset-item selected" for="masterDatasetVehicles">
                    <input type="checkbox" id="masterDatasetVehicles" name="master_types[]" value="vehicles" checked class="master-dataset-checkbox">
                    <div class="master-dataset-icon purple">
                        <i class="fi fi-rr-car-alt"></i>
                    </div>
                    <div class="master-dataset-info">
                        <div class="master-dataset-title-row">
                            <h4 class="master-dataset-name">Fleet &amp; Vehicles</h4>
                            <span class="master-dataset-count-badge" id="badgeMasterVehicles">11 Models</span>
                        </div>
                        <p class="master-dataset-desc">Innova Crysta, Force Urbania, Tempo Travellers, Dzire, and Volvo coaches with per-km rates.</p>
                        <span class="master-dataset-current-stat" id="statWorkspaceVehicles">
                            <i class="fi fi-rr-check"></i> Standard cab models
                        </span>
                    </div>
                </label>

                <!-- 4. Transporters -->
                <label class="master-dataset-item selected" for="masterDatasetTransporters">
                    <input type="checkbox" id="masterDatasetTransporters" name="master_types[]" value="transporter" checked class="master-dataset-checkbox">
                    <div class="master-dataset-icon amber">
                        <i class="fi fi-rr-truck-side"></i>
                    </div>
                    <div class="master-dataset-info">
                        <div class="master-dataset-title-row">
                            <h4 class="master-dataset-name">Transporters &amp; Vendors</h4>
                            <span class="master-dataset-count-badge" id="badgeMasterTransporters">Ready</span>
                        </div>
                        <p class="master-dataset-desc">Transport vendor profiles with service states, fleet types, and contact details.</p>
                        <span class="master-dataset-current-stat" id="statWorkspaceTransporters">
                            <i class="fi fi-rr-check"></i> <span id="textWorkspaceTransporters">Checking workspace...</span>
                        </span>
                    </div>
                </label>

                <!-- 5. Travel Destinations -->
                <label class="master-dataset-item selected" for="masterDatasetDestinations">
                    <input type="checkbox" id="masterDatasetDestinations" name="master_types[]" value="travel_destinations" checked class="master-dataset-checkbox">
                    <div class="master-dataset-icon rose">
                        <i class="fi fi-rr-marker"></i>
                    </div>
                    <div class="master-dataset-info">
                        <div class="master-dataset-title-row">
                            <h4 class="master-dataset-name">Travel Destinations</h4>
                            <span class="master-dataset-count-badge" id="badgeMasterDestinations">17 Hubs</span>
                        </div>
                        <p class="master-dataset-desc">Manali, Shimla, Kerala, Goa, Kashmir, Dubai, Bali, Vietnam with best visit months.</p>
                        <span class="master-dataset-current-stat" id="statWorkspaceDestinations">
                            <i class="fi fi-rr-check"></i> Top travel destinations
                        </span>
                    </div>
                </label>
            </div>

            <!-- Optional Destination Filters for Packages -->
            <div class="master-dest-section" id="masterDestFilterSection">
                <div class="master-dest-header">
                    <h4><i class="fi fi-rr-filter"></i> Filter Tour Packages by Destination (Optional)</h4>
                    <span id="masterDestFilterHint">All destinations selected (155 packages)</span>
                </div>
                <div class="master-dest-pills-wrap" id="masterDestPillsContainer">
                    <button type="button" class="master-dest-pill active" data-dest="all">
                        <i class="fi fi-rr-check"></i> All Destinations
                    </button>
                    <!-- Populated dynamically via master-data.js -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="master-modal-footer">
            <div class="master-modal-footer-info">
                <i class="fi fi-rr-info"></i>
                <span id="masterFooterSummary">Ready to clone selected master datasets into your dashboard.</span>
            </div>
            <div class="master-modal-footer-actions">
                <button type="button" class="btn-master-cancel" id="btnCancelMasterModal">Cancel</button>
                <button type="button" class="btn-master-confirm" id="btnExecuteMasterCopy">
                    <i class="fi fi-rr-cloud-download"></i>
                    <span id="btnExecuteMasterText">Copy to My Dashboard</span>
                </button>
            </div>
        </div>
    </div>
</div>
