<?php
/**
 * V3 Companies & Branches Management Directory View
 * File: app/dashboard/v3/default/desktop/company/list.php
 * Fully compliant with V3 SPA guidelines, external assets, Select2 dropdowns, and REST API data binding.
 */
?>
<div class="package-page-wrapper company-list-wrapper" id="companyListApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon company-header-badge">
                <i class="fi fi-rr-building"></i>
            </div>
            <div class="package-header-titles">
                <h2>Companies &amp; Branches</h2>
                <p>Manage registered legal entities, branch offices, tax GSTIN/PAN compliance, and official addresses.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/company/add/" class="btn-add-pkg-v3" data-tooltip="Register new company or branch office">
                <i class="fi fi-rr-plus"></i> <span>Add Company</span>
            </a>
        </div>
    </div>

    <!-- Quick Metric Cards (KPI Row) -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-building"></i></div>
            <div class="kpi-meta">
                <p>Total Entities</p>
                <h3 id="statTotalCompanies">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-shield-check"></i></div>
            <div class="kpi-meta">
                <p>Active Companies</p>
                <h3 id="statActiveCompanies">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-shop"></i></div>
            <div class="kpi-meta">
                <p>Head Offices</p>
                <h3 id="statHeadOffices">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-marker"></i></div>
            <div class="kpi-meta">
                <p>Branch Offices</p>
                <h3 id="statBranchOffices">-</h3>
            </div>
        </div>
    </div>

    <!-- Companies Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="companySearchInput" class="filter-search-input" placeholder="Search by name, city, GST, email, or code..." autocomplete="off">
                <button type="button" id="clearCompanySearchBtn" class="clear-search-btn" title="Clear search" style="display: none;">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Type Filter -->
                <select id="filterCompanyType" class="filter-select">
                    <option value="">All Entity Types</option>
                    <option value="private">Private Limited</option>
                    <option value="public">Public Limited</option>
                    <option value="partnerships">Partnership Firm</option>
                    <option value="llp">LLP</option>
                    <option value="opc">OPC</option>
                    <option value="proprietorship">Sole Proprietorship</option>
                    <option value="branch">Branch Office</option>
                    <option value="clinic">Clinic / Retail Store</option>
                </select>

                <!-- Status Filter -->
                <select id="filterCompanyStatus" class="filter-select">
                    <option value="" selected>All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetCompanyFilters" class="btn-v3-action" style="padding: 7px 12px; font-size: 12px;" data-tooltip="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- Responsive Table Container -->
        <div class="list-box">
            <table class="package-v3-datatable" id="companyDataTable">
                <thead>
                    <tr>
                        <th class="th-company">Company Entity</th>
                        <th class="th-type">Type &amp; Tax Info</th>
                        <th class="th-contact">Official Contact</th>
                        <th class="th-address">Registered Address</th>
                        <th class="th-status">Status</th>
                        <th class="th-actions text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="companyTableBody">
                    <tr>
                        <td colspan="6" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading registered companies...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="package-pagination-bar" id="companyPaginationBar" style="display: none;">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="companyPerPage">Show:</label>
                    <select id="companyPerPage" class="pagination-size-select">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="companyPaginationInfo" class="pagination-info-text">Showing 0 of 0 companies</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="companyPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="companyPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="companyNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>