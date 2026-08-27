<div class="package-page-wrapper transporter-list-wrapper" id="transportersListApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <i class="fi fi-rr-truck-side" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2>Transporters Directory</h2>
                <p>Manage transport vendors, fleet operators, service areas, and package assignments.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/transporter/fleet/" class="btn-v3-action" data-tooltip="Manage Fleet & Vehicle Models">
                <i class="fi fi-rr-car-alt"></i> Fleet &amp; Vehicles
            </a>
            <a href="/dashboard/vendors/transporter/add/" class="btn-add-pkg-v3" data-tooltip="Add new transporter to directory">
                <i class="fi fi-rr-plus"></i> Add Transporter
            </a>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-truck-side"></i></div>
            <div class="kpi-meta">
                <p>Total Transporters</p>
                <h3 id="statTotalTransporters">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Active Vendors</p>
                <h3 id="statActiveTransporters">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-map"></i></div>
            <div class="kpi-meta">
                <p>Service States</p>
                <h3 id="statServiceStates">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-link-alt"></i></div>
            <div class="kpi-meta">
                <p>Assigned to Packages</p>
                <h3 id="statAssignedTransporters">-</h3>
            </div>
        </div>
    </div>

    <!-- Transporters Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="transporterSearchInput" class="filter-search-input" placeholder="Search by name, city, phone, email or state...">
                <button type="button" id="clearTransporterSearchBtn" class="clear-search-btn" title="Clear search" style="display: none;">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Status Filter -->
                <select id="filterTransporterStatus" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <!-- State / City Filter -->
                <select id="filterTransporterState" class="filter-select">
                    <option value="">All Service States / Cities</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetTransporterFilters" class="btn-v3-action" style="padding: 7px 12px; font-size: 12px;" data-tooltip="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Transporters DataTable View -->
        <div class="list-box">
            <table class="package-v3-datatable" id="transportersTable">
                <thead>
                    <tr>
                        <th class="col-tpt-name">Transporter / Vendor</th>
                        <th class="col-tpt-contact">Contact Details</th>
                        <th class="col-tpt-location">Base Location</th>
                        <th class="col-tpt-service">Service Area / States</th>
                        <th class="col-tpt-status">Status</th>
                        <th class="col-tpt-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="transportersTableBody">
                    <tr>
                        <td colspan="6" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading transporters directory...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Rows Per Page Bar -->
        <div class="package-pagination-bar" id="transporterPaginationBar" style="display: none;">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="transporterPerPage">Rows per page:</label>
                    <select id="transporterPerPage" class="pagination-size-select">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="transporterPaginationInfo" class="pagination-info-text">Showing 0 of 0 transporters</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="transporterPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="transporterPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="transporterNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>