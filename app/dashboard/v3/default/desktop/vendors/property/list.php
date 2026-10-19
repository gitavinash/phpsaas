<div class="package-page-wrapper property-list-wrapper" id="propertiesListApp">
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon teal"><i class="fi fi-rr-building"></i></div>
            <div class="package-header-titles">
                <h2>Properties Directory</h2>
                <p>Manage property details, contacts, rooms, and publishing status.</p>
            </div>
        </div>
        <div class="package-header-actions"><a href="/dashboard/vendors/property/add/" class="btn-add-pkg-v3"><i class="fi fi-rr-plus"></i> Add Property</a></div>
    </div>
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-building"></i></div>
            <div class="kpi-meta">
                <p>Total Properties</p>
                <h3 id="statTotalProperties">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Active Properties</p>
                <h3 id="statActiveProperties">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-bed-empty"></i></div>
            <div class="kpi-meta">
                <p>Room Inventory</p>
                <h3 id="statPropertyRooms">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-marker"></i></div>
            <div class="kpi-meta">
                <p>Locations</p>
                <h3 id="statPropertyLocations">-</h3>
            </div>
        </div>
    </div>
    <div class="package-table-card">
        <div class="package-filter-toolbar">
            <div class="filter-search-box"><i class="fi fi-rr-search search-svg-icon"></i><input type="search" id="propertySearchInput" class="filter-search-input" placeholder="Search by name, city, email, or mobile..." autocomplete="off"><button type="button" id="clearPropertySearchBtn" class="clear-search-btn is-hidden" title="Clear search">&times;</button></div>
            <div class="filter-dropdowns-wrap"><select id="filterPropertyType" class="filter-select selectJS2" data-placeholder="All Property Types">
                    <option value="">All Property Types</option>
                    <option value="apartments">Apartments</option>
                    <option value="capsule_hotel">Capsule hotel</option>
                    <option value="guest_house">Guest house</option>
                    <option value="inn">Inn</option>
                    <option value="hostel">Hostel</option>
                    <option value="house">House</option>
                    <option value="home_stay">Home stay</option>
                    <option value="1_star_hotel">1 Star Hotel ( Tourist )</option>
                    <option value="2_star_hotel">2 Star Hotel ( Standard )</option>
                    <option value="3_star_hotel">3 Star Hotel ( Comfort )</option>
                    <option value="4_star_hotel">4 Star Hotel ( First Class )</option>
                    <option value="5_star_hotel">5 Star Hotel ( Luxury )</option>
                    <option value="motel">Motel</option>
                    <option value="lodge">Lodge</option>
                    <option value="tent">Tent</option>
                    <option value="farm_stay">Farm stay</option>
                    <option value="resorts">Resorts</option>
                </select><select id="filterPropertyLocation" class="filter-select selectJS2" data-placeholder="All Locations">
                    <option value="">All Locations</option>
                </select><select id="filterPropertyRoomType" class="filter-select selectJS2" data-placeholder="All Room Types">
                    <option value="">All Room Types</option>
                </select><select id="filterPropertyStatus" class="filter-select selectJS2" data-placeholder="All Status">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select><button type="button" id="btnResetPropertyFilters" class="btn-v3-action"><i class="fi fi-rr-refresh"></i> Reset</button></div>
        </div>
        <div class="list-box">
            <table class="package-v3-datatable" id="propertiesTable">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Contact Details</th>
                        <th>Location</th>
                        <th>Property Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="propertiesTableBody">
                    <tr>
                        <td colspan="6" class="table-loading-state">
                            <div class="table-loading-spinner"></div><span>Loading properties directory...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="package-pagination-bar" id="propertiesPaginationBar">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="propertiesPerPage">Show</label>
                    <select id="propertiesPerPage" class="pagination-size-select selectJS2">
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span class="pagination-info-text" id="propertiesPaginationInfo">Loading…</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn prev" id="propertiesPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Prev
                </button>
                <div class="pagination-page-numbers" id="propertiesPaginationPages"></div>
                <button type="button" class="pagination-nav-btn next" id="propertiesNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>