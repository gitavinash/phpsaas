<div class="package-page-wrapper employees-report-wrapper" id="employeesReportApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <i class="fi fi-rr-users-alt" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2>Employee Performance</h2>
                <p>Sales conversion, pipeline distribution & agent productivity insights · <strong id="reportActivePeriodLabel" style="color: #4f46e5; font-weight: 600;">Last 30 days</strong></p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Quick Date Presets -->
            <div class="report-preset-btn-group">
                <button type="button" class="btn-date-preset active" data-preset="last_30_days">Last 30 Days</button>
                <button type="button" class="btn-date-preset" data-preset="this_month">This Month</button>
                <button type="button" class="btn-date-preset" data-preset="last_month">Last Month</button>
                <button type="button" class="btn-date-preset" data-preset="all_time">All Time</button>
            </div>

            <!-- Custom Date Range Picker -->
            <input type="text" id="reportDateRangePicker" class="report-datepicker-input" placeholder="Custom Range" readonly autocomplete="off">

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshEmployeeReport" class="btn-v3-action" title="Refresh metrics">
                <i class="fi fi-rr-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-users"></i></div>
            <div class="kpi-meta">
                <p>Active Team</p>
                <h3 id="statTotalEmployees">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-inbox-in"></i></div>
            <div class="kpi-meta">
                <p>Total Assigned Leads</p>
                <h3 id="statTotalAssignedLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Confirmed (Won)</p>
                <h3 id="statTotalConfirmedLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber">
            <div class="kpi-icon"><i class="fi fi-rr-time-forward"></i></div>
            <div class="kpi-meta">
                <p>Active In Pipeline</p>
                <h3 id="statTotalPipelineLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-chart-pie-alt"></i></div>
            <div class="kpi-meta">
                <p>Conversion Rate</p>
                <h3 id="statConversionRate">-</h3>
            </div>
        </div>
    </div>

    <!-- Employee Performance Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <!-- Left Tabs -->
            <div class="gallery-filter-tabs">
                <button type="button" class="theme-filter-btn report-tab-btn active" data-tab="all">
                    All Members <span class="filter-badge" id="badgeTabAll">0</span>
                </button>
                <button type="button" class="theme-filter-btn report-tab-btn" data-tab="top">
                    <i class="fi fi-rr-trophy" style="color: #f59e0b; margin-right: 3px;"></i> Top Performers <span class="filter-badge" id="badgeTabTop">0</span>
                </button>
                <button type="button" class="theme-filter-btn report-tab-btn" data-tab="pipeline">
                    <i class="fi fi-rr-time-forward" style="color: #3b82f6; margin-right: 3px;"></i> In Pipeline <span class="filter-badge" id="badgeTabPipeline">0</span>
                </button>
                <button type="button" class="theme-filter-btn report-tab-btn" data-tab="admin">
                    Administrators <span class="filter-badge" id="badgeTabAdmin">0</span>
                </button>
            </div>

            <!-- Right Controls: Search, Role, Sort -->
            <div class="filter-dropdowns-wrap">
                <div class="filter-search-box" style="margin-bottom: 0;">
                    <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="employeeReportSearch" class="filter-search-input" placeholder="Search employee...">
                    <button type="button" id="clearEmployeeSearchBtn" class="clear-search-btn" title="Clear search" style="display: none;">✕</button>
                </div>

                <select id="filterEmployeeRole" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="agent">Agents</option>
                    <option value="operations">Operations</option>
                    <option value="administrator">Administrators</option>
                </select>

                <select id="sortEmployeeReport" class="filter-select">
                    <option value="confirmed">Sort: Confirmed Leads</option>
                    <option value="leads">Sort: Total Leads</option>
                    <option value="conversion">Sort: Conversion Rate</option>
                    <option value="pipeline">Sort: Active Pipeline</option>
                    <option value="name">Sort: Name (A-Z)</option>
                </select>
            </div>
        </div>

        <!-- DataTable View -->
        <div class="list-box" style="overflow-x: auto; width: 100%;">
            <table class="package-v3-datatable" id="employeesReportTable">
                <thead>
                    <tr>
                        <th style="width: 35%;">Team Member</th>
                        <th style="width: 9%; text-align: center;">Total Leads</th>
                        <th style="width: 9%; text-align: center;">Confirmed</th>
                        <th style="width: 16%; text-align: center;">Active Pipeline</th>
                        <th style="width: 9%; text-align: center;">Lost / Closed</th>
                        <th style="width: 12%;">Conversion</th>
                        <th style="width: 10%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody id="employeesReportTableBody">
                    <tr>
                        <td colspan="7" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading team performance KPIs...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>