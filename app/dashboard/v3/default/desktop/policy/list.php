<?php
/**
 * V3 Policies & Legal Terms Directory View
 * File: app/dashboard/v3/default/desktop/policy/list.php
 * Fully compliant with V3 SPA guidelines, external assets, Select2 dropdowns, and REST API data binding.
 */
?>
<div class="package-page-wrapper policy-list-wrapper" id="policyListApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-shield-check"></i>
            </div>
            <div class="package-header-titles">
                <h2>Policies &amp; Legal Terms</h2>
                <p>Manage customer terms of service, privacy statements, cancellation rules, and legal compliance pages.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/policy/add/" class="btn-add-pkg-v3" data-tooltip="Create new policy page">
                <i class="fi fi-rr-plus"></i> <span>Add Policy</span>
            </a>
        </div>
    </div>

    <!-- KPI Metrics Row -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-document-signed"></i></div>
            <div class="kpi-meta">
                <p>Total Policies</p>
                <h3 id="statTotalPolicies">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-shield-check"></i></div>
            <div class="kpi-meta">
                <p>Active Policies</p>
                <h3 id="statActivePolicies">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-globe"></i></div>
            <div class="kpi-meta">
                <p>Published on Web</p>
                <h3 id="statPublishedPolicies">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber">
            <div class="kpi-icon"><i class="fi fi-rr-edit"></i></div>
            <div class="kpi-meta">
                <p>Draft / Inactive</p>
                <h3 id="statDraftPolicies">-</h3>
            </div>
        </div>
    </div>

    <!-- Policies Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="policySearchInput" class="filter-search-input" placeholder="Search by policy title, url slug, or content..." autocomplete="off">
                <button type="button" id="clearPolicySearchBtn" class="clear-search-btn is-hidden" title="Clear search">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Status Filter -->
                <select id="filterPolicyStatus" class="filter-select selectJS2">
                    <option value="" selected>All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Draft / Inactive</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetPolicyFilters" class="btn-v3-action btn-v3-sm" data-tooltip="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- Responsive DataTable View -->
        <div class="list-box">
            <table class="package-v3-datatable" id="policyDataTable">
                <thead>
                    <tr>
                        <th class="th-policy-title">Policy Title &amp; URL Slug</th>
                        <th class="th-policy-banner">Banner Image</th>
                        <th class="th-policy-published">Published On</th>
                        <th class="th-policy-status">Status</th>
                        <th class="th-policy-actions text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="policyTableBody">
                    <tr>
                        <td colspan="5" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading policies...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="package-pagination-bar is-hidden" id="policyPaginationBar">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="policyPerPage">Show:</label>
                    <select id="policyPerPage" class="pagination-size-select selectJS2">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="policyPaginationInfo" class="pagination-info-text">Showing 0 of 0 policies</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="policyPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="policyPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="policyNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>