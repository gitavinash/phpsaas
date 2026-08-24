<?php
// Workspace Rule Compliant: No inline SQL queries, styles, or scripts.
// All data is dynamically loaded via /api/v3/leads/list.php by leads.js
?>

<div class="package-page-wrapper leads-page-wrapper" id="leadsListApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-user-time"></i>
            </div>
            <div class="package-header-titles">
                <h2>Leads & Enquiries Pipeline</h2>
                <p>Manage, track, and convert traveler inquiries and sales prospects.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <div class="leads-pipeline-toggle">
                <a href="/dashboard/leads/list/" class="leads-view-tab active" title="Table List View">
                    <i class="fi fi-rr-list"></i> <span>List</span>
                </a>
                <a href="/dashboard/leads/kanban/" class="leads-view-tab" title="Kanban Board View">
                    <i class="fi fi-rr-apps"></i> <span>Kanban</span>
                </a>
            </div>
            <a href="/dashboard/leads/add/" class="btn-add-pkg-v3" title="Add New Enquiry">
                <i class="fi fi-rr-plus"></i> Add Lead
            </a>
        </div>
    </div>

    <!-- Quick Metric KPI Row -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo lead-kpi-clickable lead-kpi-filter-btn" data-status="all" title="Click to view all leads">
            <div class="kpi-icon"><i class="fi fi-rr-users-alt"></i></div>
            <div class="kpi-meta">
                <p>Total Leads</p>
                <h3 id="statTotalLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple lead-kpi-clickable lead-kpi-filter-btn" data-status="0" title="Click to filter fresh leads">
            <div class="kpi-icon"><i class="fi fi-rr-inbox"></i></div>
            <div class="kpi-meta">
                <p>Fresh Leads</p>
                <h3 id="statFreshLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber lead-kpi-clickable lead-kpi-filter-btn" data-status="2" title="Click to filter pending / follow up">
            <div class="kpi-icon"><i class="fi fi-rr-phone-call"></i></div>
            <div class="kpi-meta">
                <p>Pending / Follow Up</p>
                <h3 id="statPendingLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald lead-kpi-clickable lead-kpi-filter-btn" data-status="1" title="Click to filter confirmed conversions">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Confirmed Sales</p>
                <h3 id="statConfirmedLeads">-</h3>
            </div>
        </div>
    </div>

    <!-- Leads Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar leads-filter-toolbar">
            <!-- Search Input: Name, Mobile, Email, Subject -->
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="leadTableSearch" class="filter-search-input" placeholder="Search by name, mobile, email, subject...">
                <button type="button" id="clearLeadSearchBtn" class="clear-search-btn" title="Clear search">✕</button>
            </div>

            <!-- Date Range Filter -->
            <div class="filter-date-box" id="leadDateRangeWrap" title="Filter by date range">
                <i class="fi fi-rr-calendar"></i>
                <input type="text" id="filterLeadDateRange" class="filter-date-input" placeholder="Select date range" readonly>
                <button type="button" id="clearLeadDateRangeBtn" class="clear-date-btn" title="Clear date range">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Status Filter -->
                <select id="filterLeadStatus" class="filter-select leads-filter-select">
                    <option value="all">All Statuses</option>
                    <option value="0">Fresh Leads</option>
                    <option value="2">Pending</option>
                    <option value="3">Follow-Up</option>
                    <option value="1">Confirmed</option>
                    <option value="6">Payment Initiated</option>
                    <option value="4">Cancelled</option>
                    <option value="5">Duplicate</option>
                    <option value="8">Closed</option>
                </select>

                <!-- Assigned Agent Filter -->
                <select id="filterLeadAgent" class="filter-select leads-filter-select">
                    <option value="all">All Assignees</option>
                    <option value="0">Unassigned Only</option>
                </select>

                <!-- Source Filter -->
                <select id="filterLeadSource" class="filter-select leads-filter-select">
                    <option value="all">All Sources</option>
                    <option value="website">Website</option>
                    <option value="direct">Direct</option>
                    <option value="facebook">Facebook</option>
                    <option value="instagram">Instagram</option>
                    <option value="google">Google</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="referral">Referral</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetLeadFilters" class="btn-reset-filters-v3" title="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- Bulk Action Floating Bar -->
        <div class="leads-bulk-toolbar is-hidden" id="leadsBulkToolbar">
            <div class="bulk-toolbar-left">
                <span class="bulk-count-badge" id="selectedLeadsCount">0</span>
                <span>leads selected</span>
            </div>
            <div class="bulk-toolbar-right">
                <select id="bulkActionSelect" class="v3-select-compact">
                    <option value="">Choose bulk action...</option>
                    <option value="convert_to_sales">Convert to Confirmed Sales</option>
                    <option value="mark_duplicate">Mark as Duplicate</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="button" class="btn-v3-primary" id="btnApplyBulkAction">Apply</button>
            </div>
        </div>

        <!-- Data Table Wrap -->
        <div class="table-responsive">
            <table class="package-data-table leads-data-table">
                <thead>
                    <tr>
                        <th class="col-checkbox">
                            <input type="checkbox" id="selectAllLeads" title="Select all leads on this page">
                        </th>
                        <th class="col-customer">Customer & Details</th>
                        <th class="col-contact">Contact Info</th>
                        <th class="col-subject">Requirement / Notes</th>
                        <th class="col-agent">Assigned Agent</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="leadsTableBody">
                    <tr>
                        <td colspan="6">
                            <div class="v3-empty-table-state">
                                <i class="fi fi-rr-spinner icon-spin"></i>
                                <h4>Loading Leads Pipeline...</h4>
                                <p>Fetching inquiries and customer records.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div id="leadsPaginationWrap"></div>
    </div>
</div>