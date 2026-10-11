<div class="package-page-wrapper user-list-wrapper" id="usersListApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon user-header-badge">
                <i class="fi fi-rr-users"></i>
            </div>
            <div class="package-header-titles">
                <h2>Team &amp; Users Management</h2>
                <p>Manage employee accounts, role permissions, and system access credentials.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <button type="button" class="btn-wa-buy-license btn-contact-aleya btn-upgrade d-none" id="btnBuyUserLicense" title="Upgrade team member licenses" data-modal-title="Team Member Limit Reached" data-modal-notice="You have reached your team member license limit. Please contact Aleya to upgrade your plan and add more team members.">
                <i class="fi fi-rr-sparkles"></i> <span>Buy More User Licenses</span>
            </button>
            <a href="/dashboard/user/add/" class="btn-add-pkg-v3" id="btnAddUser" data-tooltip="Create new user account">
                <i class="fi fi-rr-user-add"></i> <span>Add User</span>
            </a>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-users"></i></div>
            <div class="kpi-meta">
                <p>Total Team Members</p>
                <h3 id="statTotalUsers">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-shield-check"></i></div>
            <div class="kpi-meta">
                <p>Administrators</p>
                <h3 id="statTotalAdmins">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-headset"></i></div>
            <div class="kpi-meta">
                <p>Sales Agents</p>
                <h3 id="statTotalAgents">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-settings-sliders"></i></div>
            <div class="kpi-meta">
                <p>Operations &amp; Staff</p>
                <h3 id="statTotalOperations">-</h3>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="userSearchInput" class="filter-search-input" placeholder="Search by name, email, mobile, or role..." autocomplete="off">
                <button type="button" id="clearUserSearchBtn" class="clear-search-btn" title="Clear search" style="display: none;">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Role Filter -->
                <select id="filterUserRole" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="administrator">Administrator</option>
                    <option value="agent">Sales Agent</option>
                    <option value="operations">Operations</option>
                    <option value="accountant">Accounts</option>
                    <option value="support">Support</option>
                </select>

                <!-- Status Filter -->
                <select id="filterUserStatus" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetUserFilters" class="btn-v3-action" style="padding: 7px 12px; font-size: 12px;" data-tooltip="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Users DataTable View -->
        <div class="list-box">
            <table class="package-v3-datatable" id="usersTable">
                <thead>
                    <tr>
                        <th class="col-user-info">Team Member / Name</th>
                        <th class="col-user-contact">Contact Details</th>
                        <th class="col-user-role">Role &amp; Permissions</th>
                        <th class="col-user-status">Status</th>
                        <th class="col-user-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="5" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading team members...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Rows Per Page Bar -->
        <div class="package-pagination-bar" id="userPaginationBar" style="display: none;">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="userPerPage">Rows per page:</label>
                    <select id="userPerPage" class="pagination-size-select">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="userPaginationInfo" class="pagination-info-text">Showing 0 of 0 users</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="userPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="userPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="userNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>