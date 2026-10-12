<?php

/**
 * V3 Leads & Inquiries Pipeline List View
 * File: app/dashboard/v3/default/desktop/leads/list.php
 * Workspace Compliant: External CSS/JS, REST API communication via leads.js, Select2 dropdowns, full SPA support.
 */
?>

<?php
$initialLeadStatus   = isset($_GET['leadStatus']) ? (string)$_GET['leadStatus'] : (isset($_GET['status']) ? (string)$_GET['status'] : 'all');
$initialAgentId      = isset($_GET['agentId']) ? (string)$_GET['agentId'] : (isset($_GET['agent_id']) ? (string)$_GET['agent_id'] : (isset($_GET['agent']) ? (string)$_GET['agent'] : 'all'));
$isDigitalMarketing  = (isset($site_access_type) && $site_access_type === 'digital_marketing');
$userRole            = strtolower(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : ''));
$isCallingAgent      = in_array($userRole, ['agent', 'calling_agent', 'sales_executive'], true) || (isset($site_access_type) && $site_access_type !== 'administrator' && !$isDigitalMarketing);

if ($isCallingAgent && $initialLeadStatus === '0') {
    $initialLeadStatus = '2';
}

if ($isDigitalMarketing) {
    header("Location: /dashboard/integration/facebook/");
    exit;
}
?>
<div class="package-page-wrapper leads-page-wrapper<?php echo $isDigitalMarketing ? ' leads-digital-marketing-view' : ''; ?>" id="leadsListApp" data-user-role="<?php echo htmlspecialchars($site_access_type ?? '', ENT_QUOTES); ?>" data-initial-status="<?php echo htmlspecialchars($initialLeadStatus, ENT_QUOTES); ?>" data-initial-agent="<?php echo htmlspecialchars($initialAgentId, ENT_QUOTES); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <i class="fi fi-rr-user-time"></i>
            </div>
            <div class="package-header-titles">
                <h2>Leads &amp; Enquiries Pipeline</h2>
                <p><?php echo $isDigitalMarketing ? 'Track campaign inquiries, lead generation sources, and conversion milestones.' : 'Manage, track, and convert traveler inquiries and sales prospects.'; ?></p>
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
            <?php if (!$isDigitalMarketing): ?>
            <a href="/dashboard/leads/add/" class="btn-add-pkg-v3" title="Add New Enquiry">
                <i class="fi fi-rr-plus"></i> <span>Add New Lead</span>
            </a>
            <?php endif; ?>
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
        <?php if (!$isCallingAgent): ?>
        <div class="pkg-mini-kpi sky lead-kpi-clickable lead-kpi-filter-btn" data-status="0" title="Click to filter new inbound leads">
            <div class="kpi-icon"><i class="fi fi-rr-sparkles"></i></div>
            <div class="kpi-meta">
                <p>New Enquiries</p>
                <h3 id="statFreshLeads">-</h3>
            </div>
        </div>
        <?php endif; ?>
        <div class="pkg-mini-kpi amber lead-kpi-clickable lead-kpi-filter-btn" data-status="2" title="Click to filter assigned leads">
            <div class="kpi-icon"><i class="fi fi-rr-user-time"></i></div>
            <div class="kpi-meta">
                <p>Assigned</p>
                <h3 id="statPendingLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi rose lead-kpi-clickable lead-kpi-filter-btn" data-interest="Hot" title="Click to filter Hot leads">
            <div class="kpi-icon"><i class="fi fi-rr-flame"></i></div>
            <div class="kpi-meta">
                <p>Hot Leads</p>
                <h3 id="statHotLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple lead-kpi-clickable lead-kpi-filter-btn" data-followup="due_today" title="Click to filter follow-ups due today">
            <div class="kpi-icon"><i class="fi fi-rr-alarm-clock"></i></div>
            <div class="kpi-meta">
                <p>Due Today</p>
                <h3 id="statDueTodayLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi red lead-kpi-clickable lead-kpi-filter-btn" data-followup="overdue" title="Click to filter overdue follow-ups">
            <div class="kpi-icon"><i class="fi fi-rr-exclamation"></i></div>
            <div class="kpi-meta">
                <p>Overdue</p>
                <h3 id="statOverdueLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi orange lead-kpi-clickable lead-kpi-filter-btn" data-response="no_response_24h" title="Click to filter leads with no response 24h+">
            <div class="kpi-icon"><i class="fi fi-rr-time-forward"></i></div>
            <div class="kpi-meta">
                <p>No Resp 24h+</p>
                <h3 id="statNoResp24hLeads">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald lead-kpi-clickable lead-kpi-filter-btn" data-status="1" title="Click to filter confirmed bookings">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Converted</p>
                <h3 id="statConfirmedLeads">-</h3>
            </div>
        </div>
    </div>

    <!-- Leads Table Card -->
    <div class="package-table-card">
        <!-- Interactive Filter & Search Toolbar -->
        <div class="package-filter-toolbar leads-filter-toolbar">
            <!-- Search Input -->
            <div class="filter-search-box">
                <svg class="search-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="leadTableSearch" class="filter-search-input" placeholder="<?php echo $isDigitalMarketing ? 'Search by name, subject, source...' : 'Search by name, mobile, email, destination...'; ?>" autocomplete="off">
                <button type="button" id="clearLeadSearchBtn" class="clear-search-btn" title="Clear search">✕</button>
            </div>

            <!-- Date Range Filter Trigger -->
            <div class="filter-date-box" id="leadDateRangeWrap" title="Filter by enquiry date range">
                <i class="fi fi-rr-calendar"></i>
                <input type="text" id="filterLeadDateRange" class="filter-date-input" placeholder="Select date range" readonly>
                <button type="button" id="clearLeadDateRangeBtn" class="clear-date-btn" title="Clear date range">✕</button>
            </div>

            <div class="filter-dropdowns-wrap">
                <!-- Status Filter (Select2) -->
                <div class="leads-select-holder">
                    <select id="filterLeadStatus" class="leads-filter-select">
                        <option value="all" <?php echo ($initialLeadStatus === 'all' ? 'selected' : ''); ?>>All Statuses</option>
                        <optgroup label="Sales Pipeline Stages">
                            <?php if (!$isCallingAgent): ?>
                            <option value="0" <?php echo ($initialLeadStatus === '0' ? 'selected' : ''); ?>>New Enquiry</option>
                            <?php endif; ?>
                            <option value="2" <?php echo ($initialLeadStatus === '2' ? 'selected' : ''); ?>>Assigned</option>
                            <option value="19" <?php echo ($initialLeadStatus === '19' ? 'selected' : ''); ?>>Contacted</option>
                            <option value="20" <?php echo ($initialLeadStatus === '20' ? 'selected' : ''); ?>>Requirement Collected</option>
                            <option value="21" <?php echo ($initialLeadStatus === '21' ? 'selected' : ''); ?>>Itinerary In Progress</option>
                            <option value="9" <?php echo ($initialLeadStatus === '9' ? 'selected' : ''); ?>>Itinerary Sent</option>
                            <option value="22" <?php echo ($initialLeadStatus === '22' ? 'selected' : ''); ?>>Quotation In Progress</option>
                            <option value="23" <?php echo ($initialLeadStatus === '23' ? 'selected' : ''); ?>>Quotation Sent</option>
                            <option value="24" <?php echo ($initialLeadStatus === '24' ? 'selected' : ''); ?>>Awaiting Customer Decision</option>
                            <option value="25" <?php echo ($initialLeadStatus === '25' ? 'selected' : ''); ?>>Negotiation</option>
                            <option value="10" <?php echo ($initialLeadStatus === '10' ? 'selected' : ''); ?>>Quotation Approved</option>
                            <option value="6" <?php echo ($initialLeadStatus === '6' ? 'selected' : ''); ?>>Payment Pending</option>
                            <option value="1" <?php echo ($initialLeadStatus === '1' ? 'selected' : ''); ?>>Converted to Booking</option>
                        </optgroup>
                        <optgroup label="Nurture & Hold">
                            <option value="26" <?php echo ($initialLeadStatus === '26' ? 'selected' : ''); ?>>On Hold</option>
                            <option value="27" <?php echo ($initialLeadStatus === '27' ? 'selected' : ''); ?>>Nurture</option>
                            <option value="3" <?php echo ($initialLeadStatus === '3' ? 'selected' : ''); ?>>Follow Up (Legacy)</option>
                        </optgroup>
                        <optgroup label="Closed & Exception">
                            <option value="28" <?php echo ($initialLeadStatus === '28' ? 'selected' : ''); ?>>Not Interested</option>
                            <option value="17" <?php echo ($initialLeadStatus === '17' ? 'selected' : ''); ?>>Lost</option>
                            <option value="5" <?php echo ($initialLeadStatus === '5' ? 'selected' : ''); ?>>Duplicate</option>
                            <option value="29" <?php echo ($initialLeadStatus === '29' ? 'selected' : ''); ?>>Unqualified</option>
                            <option value="16" <?php echo ($initialLeadStatus === '16' || $initialLeadStatus === 'cancel' || $initialLeadStatus === '4,16' ? 'selected' : ''); ?>>Cancelled</option>
                            <option value="4" <?php echo ($initialLeadStatus === '4' ? 'selected' : ''); ?>>Closed / Cancelled (Legacy)</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Customer Interest Filter -->
                <div class="leads-select-holder">
                    <select id="filterLeadInterest" class="leads-filter-select">
                        <option value="all" selected>All Interests</option>
                        <option value="Hot">🔥 Hot</option>
                        <option value="Warm">✨ Warm</option>
                        <option value="Cold">❄️ Cold</option>
                        <option value="Not Interested">🚫 Not Interested</option>
                        <option value="Unknown">❓ Unknown</option>
                    </select>
                </div>

                <!-- Response & Follow-up Operational Filter -->
                <div class="leads-select-holder">
                    <select id="filterLeadResponse" class="leads-filter-select">
                        <option value="all" selected>All Response States</option>
                        <optgroup label="Customer Response">
                            <option value="responded">✅ Responded</option>
                            <option value="awaiting_response">⏳ Awaiting Response (< 24h)</option>
                            <option value="no_response_24h">⚠️ No Response (24h+)</option>
                            <option value="no_response_48h">🚨 No Response (48h+)</option>
                            <option value="no_response_7d">⛔ No Response (7d+)</option>
                            <option value="no_activity">⚪ No Activity</option>
                        </optgroup>
                        <optgroup label="Follow-Up Schedule">
                            <option value="due_today">⏰ Follow-Up Due Today</option>
                            <option value="overdue">🚨 Follow-Up Overdue</option>
                        </optgroup>
                    </select>
                </div>

                <?php if (!$isDigitalMarketing): ?>
                <!-- Assigned Agent Filter (Select2) -->
                <div class="leads-select-holder">
                    <select id="filterLeadAgent" class="leads-filter-select">
                        <option value="all" <?php echo ($initialAgentId === 'all' ? 'selected' : ''); ?>>All Assignees</option>
                        <option value="0" <?php echo ($initialAgentId === '0' ? 'selected' : ''); ?>>Unassigned Only</option>
                        <?php if ($initialAgentId !== 'all' && $initialAgentId !== '0' && $initialAgentId !== ''): ?>
                            <option value="<?php echo htmlspecialchars($initialAgentId, ENT_QUOTES); ?>" selected>Agent #<?php echo htmlspecialchars($initialAgentId, ENT_QUOTES); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Source Filter (Select2) -->
                <div class="leads-select-holder">
                    <select id="filterLeadSource" class="leads-filter-select">
                        <option value="all" selected>All Sources</option>
                        <option value="website">Website</option>
                        <option value="direct">Direct</option>
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="google">Google</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="referral">Referral</option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <button type="button" id="btnResetLeadFilters" class="btn-reset-filters-v3" title="Reset all filters">
                    <i class="fi fi-rr-refresh"></i> <span>Reset</span>
                </button>
            </div>
        </div>

        <?php if (!$isDigitalMarketing): ?>
        <!-- Bulk Action Floating Bar -->
        <div class="leads-bulk-toolbar is-hidden" id="leadsBulkToolbar">
            <div class="bulk-toolbar-left">
                <span class="bulk-count-badge" id="selectedLeadsCount">0</span>
                <span>leads selected</span>
            </div>
            <div class="bulk-toolbar-right">
                <div class="leads-bulk-select-holder">
                    <select id="bulkActionSelect" class="leads-filter-select">
                        <option value="">Choose bulk action...</option>
                        <option value="convert_to_sales">Convert to Confirmed Sales</option>
                        <option value="mark_duplicate">Mark as Duplicate</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </div>
                <button type="button" class="btn-v3-primary" id="btnApplyBulkAction">Apply</button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Data Table Wrap -->
        <div class="list-box">
            <table class="package-v3-datatable leads-data-table">
                <thead>
                    <tr>
                        <th class="th-checkbox">
                            <input type="checkbox" id="selectAllLeads" title="Select all leads on this page">
                        </th>
                        <th class="th-customer">Customer &amp; Interest</th>
                        <?php if (!$isDigitalMarketing): ?>
                        <th class="th-contact">Contact Info</th>
                        <?php endif; ?>
                        <th class="th-subject">Requirement &amp; Valuation</th>
                        <th class="th-response">Response &amp; Follow-Up</th>
                        <?php if (!$isDigitalMarketing): ?>
                        <th class="th-agent">Assigned Agent</th>
                        <th class="th-actions text-right">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="leadsTableBody">
                    <tr>
                        <td colspan="<?php echo $isDigitalMarketing ? '4' : '7'; ?>" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <span>Loading leads pipeline...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination Bar -->
        <div class="package-pagination-bar" id="leadsPaginationBar">
            <div class="pagination-info-wrap">
                <div class="pagination-size-select-wrap">
                    <label for="leadsPerPage">Show:</label>
                    <select id="leadsPerPage" class="pagination-size-select leads-per-page-select">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span id="leadsPaginationInfo" class="pagination-info-text">Showing 0 of 0 leads</span>
            </div>
            <div class="pagination-controls">
                <button type="button" class="pagination-nav-btn" id="leadsPrevPage" disabled>
                    <i class="fi fi-rr-angle-left"></i> Previous
                </button>
                <div class="pagination-page-numbers" id="leadsPaginationPages"></div>
                <button type="button" class="pagination-nav-btn" id="leadsNextPage" disabled>
                    Next <i class="fi fi-rr-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>