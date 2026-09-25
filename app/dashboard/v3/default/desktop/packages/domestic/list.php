<div class="package-page-wrapper" id="packageListApp" data-package-type="domestic">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi-rr-map-marker-home"></i>
            </div>
            <div class="package-header-titles">
                <h2>Domestic Tour Packages</h2>
                <p>Manage, customize, and publish domestic itineraries and travel offerings.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <div class="package-type-toggle">
                <a href="/dashboard/packages/domestic/list/" class="type-tab active"><i class="fi-rr-home"></i> Domestic</a>
                <a href="/dashboard/packages/international/list/" class="type-tab"><i class="fi-rr-globe"></i> International</a>
            </div>
            <a href="/dashboard/packages/domestic/add/" class="btn-add-pkg-v3">
                <i class="fi-rr-plus"></i> Add Package
            </a>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi-rr-box-alt"></i></div>
            <div class="kpi-meta">
                <p>Total Packages</p>
                <h3 id="statTotalPackages">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Active & Live</p>
                <h3 id="statActivePackages">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber">
            <div class="kpi-icon"><i class="fi-rr-edit"></i></div>
            <div class="kpi-meta">
                <p>Drafts</p>
                <h3 id="statDraftPackages">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi-rr-marker"></i></div>
            <div class="kpi-meta">
                <p>Destinations</p>
                <h3 id="statDestinations">-</h3>
            </div>
        </div>
    </div>

    <!-- Package Table Card -->
    <div class="package-table-card">
        <!-- Modern Interactive Filter & Search Toolbar (Single Line) -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="pkgTableSearch" class="filter-search-input" placeholder="Search packages, destinations...">
                <button type="button" id="clearSearchBtn" class="clear-search-btn" title="Clear search">✕</button>
            </div>
            <div class="filter-dropdowns-wrap">
                <!-- Status Filter -->
                <select id="filterStatus" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                </select>

                <!-- Theme Filter -->
                <select id="filterTheme" class="filter-select">
                    <option value="">All Themes</option>
                    <option value="religious">Religious</option>
                    <option value="honeymoon">Honeymoon</option>
                    <option value="family">Family</option>
                    <option value="adventure">Adventure</option>
                    <option value="wildlife">Wildlife</option>
                    <option value="beach">Beach</option>
                    <option value="hill station">Hill Station</option>
                    <option value="heritage">Heritage</option>
                    <option value="standard">Standard</option>
                </select>

                <!-- Duration Filter -->
                <select id="filterDuration" class="filter-select">
                    <option value="">All Durations</option>
                    <option value="short">1 - 3 Nights (Short)</option>
                    <option value="medium">4 - 6 Nights (Medium)</option>
                    <option value="long">7+ Nights (Long)</option>
                </select>

                <!-- Hotel Rating Filter -->
                <select id="filterHotel" class="filter-select">
                    <option value="">All Hotels</option>
                    <option value="5 star">5 Star</option>
                    <option value="4 star">4 Star</option>
                    <option value="3 star">3 Star</option>
                    <option value="standard">Standard</option>
                </select>

                <!-- Destination Filter -->
                <select id="filterDestination" class="filter-select">
                    <option value="">All Destinations</option>
                </select>

                <!-- Reset Button -->
                <button type="button" id="resetPkgFilters" class="btn-filter-reset" title="Reset All Filters">
                    <i class="fi fi-rr-refresh"></i> <span>Reset</span>
                </button>
            </div>
        </div>

        <div class="list-box">
            <table class="package-v3-datatable">
                <thead>
                    <tr>
                        <th class="col-pkg-main">Package & Details</th>
                        <th class="col-pkg-location">Destination & Lodging</th>
                        <th class="col-pkg-pricing">Pricing & Plans</th>
                        <th class="col-pkg-status">Status</th>
                        <th class="col-pkg-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="packageTableBody">
                    <tr>
                        <td colspan="5" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading tour packages...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Rows Per Page Bar -->
        <div class="package-pagination-bar" id="packagePaginationBar">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="pkgPerPage">Rows per page:</label>
                    <select id="pkgPerPage" class="pagination-size-select">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="pkgPaginationInfo" class="pagination-info-text">Showing 0 of 0 packages</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="pkgPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="pkgPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="pkgNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>