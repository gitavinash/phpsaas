<div class="package-page-wrapper transporter-form-wrapper" id="editTransporterApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-edit"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="headerTransporterTitle">Edit Transporter</h2>
                <p>Update vendor profile, contact numbers, base garage location, fleet types, and service coverage.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/transporter/list/" class="btn-v3-action" data-tooltip="Back to Transporters Directory">
                <i class="fi fi-rr-arrow-left"></i> Transporters Directory
            </a>
        </div>
    </div>

    <!-- Loading State -->
    <div class="v3-form-loading" id="transporterEditLoading">
        <div class="v3-loading-spinner-wrap">
            <i class="fi fi-rr-spinner spinner"></i>
            <p>Loading transporter profile details...</p>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout" id="transporterEditLayout" style="display: none;">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="transporterAddForm" class="v3-form-container" autocomplete="off" novalidate>
                <input type="hidden" id="transporterId" name="transporter_id" value="">
                <input type="hidden" id="editTransporterId" name="id" value="">

                <!-- Card 1: Vendor Profile & Contact -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-building"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Vendor Profile & Contact Information</h3>
                            <p>Enter the primary business name and contact channels of the transport operator.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Transporter Name -->
                            <div class="v3-form-group span-2">
                                <label for="transporterName" class="v3-form-label">
                                    Transporter / Agency Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="transporterName" name="name" class="v3-form-input" placeholder="e.g. Himalayan Fleet & Travels" required>
                                </div>
                                <span class="v3-field-error" id="errTransporterName"></span>
                            </div>

                            <!-- Contact Person -->
                            <div class="v3-form-group">
                                <label for="transporterContactPerson" class="v3-form-label">
                                    Contact Person / Fleet Manager
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="transporterContactPerson" name="contact_person" class="v3-form-input" placeholder="e.g. Rajesh Sharma">
                                </div>
                            </div>

                            <!-- Primary Contact Number -->
                            <div class="v3-form-group">
                                <label for="transporterMobile" class="v3-form-label">
                                    Primary Contact Mobile <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="transporterMobile" name="mobile" class="v3-form-input" placeholder="e.g. 9876543210" required>
                                </div>
                                <span class="v3-field-error" id="errTransporterMobile"></span>
                            </div>

                            <!-- WhatsApp / Alt Mobile -->
                            <div class="v3-form-group">
                                <label for="transporterAltMobile" class="v3-form-label">
                                    WhatsApp Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <input type="tel" id="transporterAltMobile" name="alt_mobile" class="v3-form-input" placeholder="e.g. 9812345678">
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="v3-form-group">
                                <label for="transporterEmail" class="v3-form-label">
                                    Official Email Address
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="transporterEmail" name="email" class="v3-form-input" placeholder="e.g. contact@himalayantravels.com">
                                </div>
                                <span class="v3-field-error" id="errTransporterEmail"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Base Location & Operating Address -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-marker"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Base Location & Garage Address</h3>
                            <p>Specify the base city and registered garage/office location.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Base City -->
                            <div class="v3-form-group">
                                <label for="transporterCity" class="v3-form-label">
                                    Base Operational City <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-city input-icon"></i>
                                    <select id="transporterCity" name="city" class="v3-form-select v3-city-select2" required>
                                        <option value="">Select or search operational city...</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="errTransporterCity"></span>
                            </div>

                            <!-- PIN Code -->
                            <div class="v3-form-group">
                                <label for="transporterPincode" class="v3-form-label">
                                    Postal / PIN Code <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-map-pin input-icon"></i>
                                    <input type="text" id="transporterPincode" name="pinCode" class="v3-form-input" placeholder="e.g. 175131" maxlength="10" required>
                                </div>
                                <span class="v3-field-error" id="errTransporterPincode"></span>
                            </div>

                            <!-- Full Address -->
                            <div class="v3-form-group span-2">
                                <label for="transporterAddress" class="v3-form-label">
                                    Operating Address / Office Location <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-map-marker input-icon"></i>
                                    <textarea id="transporterAddress" name="address" class="v3-form-textarea" rows="2" placeholder="e.g. Shop No. 12, Mall Road, Near Private Bus Stand" required></textarea>
                                </div>
                                <span class="v3-field-error" id="errTransporterAddress"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Service Area & Operational States (Interactive Tag Cloud) -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon sky">
                            <i class="fi fi-rr-map"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Service Coverage & Operational States</h3>
                            <p>Specify the states and destinations where this transporter provides cab and tour services.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-group">
                            <label for="transporterServiceStates" class="v3-form-label">
                                Selected Service States / Regions <span class="required">*</span>
                            </label>

                            <!-- Searchable Cities & States Select2 Multi-Select -->
                            <div class="v3-input-icon-wrap select2-wrap">
                                <select id="transporterServiceStates" name="serviceStates[]" class="v3-form-select v3-service-states-select" multiple="multiple">
                                </select>
                            </div>
                            <input type="hidden" id="transporterServiceArea" name="serviceArea" value="">
                            <span class="v3-field-error" id="errTransporterServiceArea"></span>
                            <span class="v3-field-hint">Search &amp; select operational states or cities (e.g. Himachal Pradesh, Manali, Goa, Rajasthan).</span>
                        </div>

                        <!-- Quick Region Groups & Chips -->
                        <div class="v3-chips-section">
                            <div class="v3-chips-group-title">Popular Operational Regions:</div>
                            <div class="v3-chips-cloud" id="quickStateChips">
                                <button type="button" class="v3-chip-pill" data-state="Himachal Pradesh">Himachal Pradesh</button>
                                <button type="button" class="v3-chip-pill" data-state="Uttarakhand">Uttarakhand</button>
                                <button type="button" class="v3-chip-pill" data-state="Punjab">Punjab</button>
                                <button type="button" class="v3-chip-pill" data-state="Haryana">Haryana</button>
                                <button type="button" class="v3-chip-pill" data-state="Delhi NCR">Delhi NCR</button>
                                <button type="button" class="v3-chip-pill" data-state="Jammu & Kashmir">Jammu & Kashmir</button>
                                <button type="button" class="v3-chip-pill" data-state="Ladakh">Ladakh</button>
                                <button type="button" class="v3-chip-pill" data-state="Rajasthan">Rajasthan</button>
                                <button type="button" class="v3-chip-pill" data-state="Goa">Goa</button>
                                <button type="button" class="v3-chip-pill" data-state="Kerala">Kerala</button>
                                <button type="button" class="v3-chip-pill" data-state="Karnataka">Karnataka</button>
                                <button type="button" class="v3-chip-pill" data-state="Maharashtra">Maharashtra</button>
                                <button type="button" class="v3-chip-pill" data-state="Uttar Pradesh">Uttar Pradesh</button>
                                <button type="button" class="v3-chip-pill" data-state="Gujarat">Gujarat</button>
                                <button type="button" class="v3-chip-pill" data-state="West Bengal">West Bengal</button>
                                <button type="button" class="v3-chip-pill" data-state="Sikkim">Sikkim</button>
                                <button type="button" class="v3-chip-pill" data-state="North East">North East</button>
                                <button type="button" class="v3-chip-pill" data-state="All India">All India</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Operating Notes & Special Terms -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon amber">
                            <i class="fi fi-rr-document"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Fleet Notes & Special Terms</h3>
                            <p>Any additional operating notes, driver languages, or special permits.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-group">
                            <div class="v3-input-icon-wrap textarea-wrap">
                                <i class="fi fi-rr-edit input-icon"></i>
                                <textarea id="transporterDescription" name="description" class="v3-form-textarea" rows="3" placeholder="e.g. Well-maintained fleet, local hill-trained drivers, all permits for Rohtang & Ladakh, GPS enabled cabs."></textarea>
                            </div>
                            <span class="v3-field-hint">Optional remarks visible to tour planners when assigning transport to packages.</span>
                        </div>
                    </div>
                </div>

                <!-- Form Sticky Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/vendors/transporter/list/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="v3-btn-primary" id="btnSubmitTransporter">
                        <i class="fi fi-rr-check"></i> <span id="btnSubmitText">Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Sticky Live Transporter Preview Card -->
        <div class="v3-form-sidebar-col">
            <div class="v3-preview-sticky-card">
                <div class="v3-preview-header">
                    <div class="v3-preview-badge">
                        <span class="pulse-dot"></span> Live Directory Preview
                    </div>
                    <div class="v3-preview-progress" id="formProgressBadge">
                        <span id="formProgressPct">0%</span> Complete
                    </div>
                </div>

                <div class="v3-progress-bar-wrap">
                    <div class="v3-progress-bar-fill" id="formProgressBar" style="width: 0%;"></div>
                </div>

                <!-- Preview Card Layout -->
                <div class="v3-transporter-live-card">
                    <div class="live-card-top">
                        <div class="live-avatar-box" id="previewAvatar">
                            <span id="previewInitials">TR</span>
                        </div>
                        <div class="live-card-titles">
                            <h3 id="previewTitle">Transporter Name</h3>
                            <p id="previewContactPerson"><i class="fi fi-rr-user"></i> <span>Contact Person</span></p>
                        </div>
                    </div>

                    <div class="live-card-details">
                        <div class="live-detail-item">
                            <i class="fi fi-rr-phone-call"></i>
                            <span id="previewMobile">Contact Number</span>
                        </div>
                        <div class="live-detail-item">
                            <i class="fi fi-rr-envelope"></i>
                            <span id="previewEmail">Email Address</span>
                        </div>
                        <div class="live-detail-item">
                            <i class="fi fi-rr-marker"></i>
                            <span id="previewLocation">Base City, PIN</span>
                        </div>
                    </div>

                    <div class="live-card-section-divider"></div>

                    <!-- Service Coverage Preview -->
                    <div class="live-coverage-section">
                        <div class="live-section-label"><i class="fi fi-rr-map"></i> Service Coverage:</div>
                        <div class="live-coverage-pills" id="previewCoverageList">
                            <span class="live-empty-pill">No service regions added</span>
                        </div>
                    </div>

                    <!-- Fleet Master Quick Link -->
                    <div class="live-fleet-link-section">
                        <a href="/dashboard/vendors/transporter/vehicle/" class="live-fleet-link-btn" data-tooltip="Open Fleet & Vehicle Models Manager">
                            <i class="fi fi-rr-car-alt"></i> Manage Fleet &amp; Vehicles <i class="fi fi-rr-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Live Notes Preview -->
                    <div class="live-notes-preview" id="previewNotesBox" style="display: none;">
                        <p id="previewNotesText"></p>
                    </div>
                </div>

                <!-- Quick Help / Tips Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-info"></i></div>
                    <div class="tips-content">
                        <strong>Pro-Tip:</strong> Updating service states dynamically updates priority transporter recommendations across all connected tour packages.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>