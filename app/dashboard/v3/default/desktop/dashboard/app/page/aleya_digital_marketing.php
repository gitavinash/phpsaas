<?php
/**
 * V3 Digital Marketing Dashboard Overview
 * Page Component: app/dashboard/v3/default/desktop/dashboard/app/page/aleya_digital_marketing.php
 * Workspace Compliant: External CSS & JS, REST API integration, SPA asynchronous state, Zero PII contact leakage.
 */

$displayDateRange = 'Last 30 Days';
$displayDuration  = '30 Days';

if (!isset($daterange) || empty($daterange)) {
    $defaultStart = date('d-M-Y', strtotime('-29 days'));
    $defaultEnd   = date('d-M-Y');
    $daterange    = $defaultStart . ' - ' . $defaultEnd;
}

if (!empty($daterange)) {
    $dates = explode(' - ', trim($daterange));
    if (count($dates) === 2) {
        $t1 = strtotime($dates[0]);
        $t2 = strtotime($dates[1]);
        if ($t1 && $t2) {
            $days = round(abs($t2 - $t1) / 86400) + 1;
            if ($days == 1) {
                $displayDateRange = (date('Y-m-d', $t1) === date('Y-m-d')) ? 'Today' : '1 Day';
                $displayDuration  = '1 Day';
            } elseif ($days == 7) {
                $displayDateRange = 'Last 7 Days';
                $displayDuration  = '7 Days';
            } elseif ($days == 30) {
                $displayDateRange = 'Last 30 Days';
                $displayDuration  = '30 Days';
            } else {
                $displayDateRange = 'Custom Range';
                $displayDuration  = "{$days} Days";
            }
        }
    }
}
?>

<div class="mkt-dashboard-wrapper" id="digitalMarketingDashboardApp">
    <!-- Top Bar: Header, Live Badge & Date Range Filter -->
    <div class="mkt-top-bar">
        <div class="mkt-title-group">
            <h2>
                <span>Marketing Overview</span>
                <span class="mkt-live-pill">
                    <span class="mkt-live-dot"></span> Live Ingestion
                </span>
            </h2>
            <p>Campaign lead acquisition, ad source distribution &amp; channel health</p>
        </div>

        <div class="mkt-header-controls">
            <button type="button" class="mkt-refresh-btn" id="mktRefreshBtn" title="Fetch latest campaign data">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>

            <div class="dashboard-datepicker">
                <div class="date-range-badge-pill" id="mktDateRangePill">
                    <i class="fi-rr-calendar"></i>
                    <span class="selected-range-text" id="mktSelectedRangeText"><?php echo htmlspecialchars($displayDateRange); ?></span>
                    <span class="duration-tag" id="mktDateDurationTag"><?php echo htmlspecialchars($displayDuration); ?></span>
                </div>
                <form action="" class="widgetForm" id="mktDateRangeForm">
                    <input type="text" name="daterange" class="dateRangePicker" value="<?php echo htmlspecialchars($daterange); ?>" id="mktDaterangeInput" placeholder="Select date range" autocomplete="off">
                    <input type="submit" value="Update" class="btn btn-green date-btn">
                </form>
            </div>
        </div>
    </div>

    <!-- 1. KPI Cards Grid -->
    <div class="mkt-kpi-grid">
        <!-- KPI 1: Total Leads Captured -->
        <div class="mkt-kpi-card brand-total">
            <div class="mkt-kpi-card-header">
                <div class="mkt-kpi-icon-wrap">
                    <i class="fi fi-rr-chart-network"></i>
                </div>
                <span class="mkt-kpi-badge">Total Ingestion</span>
            </div>
            <div class="mkt-kpi-val" id="mktValTotal">0</div>
            <div class="mkt-kpi-label">Total Leads Received</div>
            <div class="mkt-kpi-sub"><i class="fi fi-rr-check-circle"></i> Across all digital campaigns</div>
        </div>

        <!-- KPI 2: Facebook Lead Ads -->
        <div class="mkt-kpi-card brand-fb">
            <div class="mkt-kpi-card-header">
                <div class="mkt-kpi-icon-wrap">
                    <i class="fi fi-rr-facebook"></i>
                </div>
                <span class="mkt-kpi-badge">Meta Leadgen</span>
            </div>
            <div class="mkt-kpi-val" id="mktValFb">0</div>
            <div class="mkt-kpi-label">Facebook &amp; IG Leads</div>
            <div class="mkt-kpi-sub"><i class="fi fi-rr-refresh"></i> Real-time Webhook Synced</div>
        </div>

        <!-- KPI 3: Google Ads Inquiries -->
        <div class="mkt-kpi-card brand-google">
            <div class="mkt-kpi-card-header">
                <div class="mkt-kpi-icon-wrap">
                    <i class="fi fi-rr-search-alt"></i>
                </div>
                <span class="mkt-kpi-badge">Google Ads</span>
            </div>
            <div class="mkt-kpi-val" id="mktValGoogle">0</div>
            <div class="mkt-kpi-label">Google Ads Inquiries</div>
            <div class="mkt-kpi-sub"><i class="fi fi-rr-bullseye-arrow"></i> Search &amp; Display Assets</div>
        </div>

        <!-- KPI 4: Active Facebook Forms -->
        <div class="mkt-kpi-card brand-forms">
            <div class="mkt-kpi-card-header">
                <div class="mkt-kpi-icon-wrap">
                    <i class="fi fi-rr-form"></i>
                </div>
                <span class="mkt-kpi-badge">Lead Forms</span>
            </div>
            <div class="mkt-kpi-val" id="mktValForms">0</div>
            <div class="mkt-kpi-label">Active Lead Forms</div>
            <div class="mkt-kpi-sub"><i class="fi fi-rr-shield-check"></i> Connected Facebook Forms</div>
        </div>
    </div>

    <!-- 2. Visual Charts Grid (Ingestion Timeline & Channel Distribution) -->
    <div class="mkt-charts-grid">
        <!-- Chart 1: Ingestion Timeline Flow -->
        <div class="mkt-card">
            <div class="mkt-card-header">
                <div class="mkt-card-title">
                    <i class="fi fi-rr-chart-line-up mkt-icon-indigo"></i>
                    <div>
                        <h3>Campaign Ingestion Flow</h3>
                        <p>Daily volume comparison across advertising platforms</p>
                    </div>
                </div>
                <div class="mkt-timeline-legend">
                    <div class="mkt-legend-item">
                        <span class="mkt-legend-dot fb"></span> Facebook &amp; IG
                    </div>
                    <div class="mkt-legend-item">
                        <span class="mkt-legend-dot google"></span> Google Ads
                    </div>
                    <div class="mkt-legend-item">
                        <span class="mkt-legend-dot total"></span> Total
                    </div>
                </div>
            </div>
            <div class="mkt-chart-container">
                <canvas id="mktTimelineCanvas"></canvas>
            </div>
        </div>

        <!-- Chart 2: Channel Acquisition Share -->
        <div class="mkt-card">
            <div class="mkt-card-header">
                <div class="mkt-card-title">
                    <i class="fi fi-rr-pie mkt-icon-sky"></i>
                    <div>
                        <h3>Acquisition Channels</h3>
                        <p>Traffic source &amp; campaign breakdown</p>
                    </div>
                </div>
            </div>
            <div class="mkt-chart-container mkt-chart-donut">
                <canvas id="mktSourceCanvas"></canvas>
            </div>
            <div class="mkt-source-bars-list" id="mktSourceBarsList">
                <!-- Dynamically populated by marketing-dashboard.js -->
            </div>
        </div>
    </div>

    <!-- 3. Lower Section: Live Captured Inquiries Stream -->
    <div class="mkt-lower-grid">
        <!-- Live Captured Inquiries Stream -->
        <div class="mkt-card">
            <div class="mkt-card-header">
                <div class="mkt-card-title">
                    <i class="fi fi-rr-bolt mkt-icon-amber"></i>
                    <div>
                        <h3>Live Captured Inquiries Stream</h3>
                        <p>Recent prospects ingested from active marketing campaigns</p>
                    </div>
                </div>
                <div class="mkt-card-actions">
                    <a href="/dashboard/integration/facebook/" class="v3-btn-table-action" title="View Facebook Forms &amp; Leads">
                        <i class="fi fi-rr-form"></i> Facebook Leads
                    </a>
                    <a href="/dashboard/integration/google_ads/" class="v3-btn-table-action" title="View Google Ads Leads">
                        <i class="fi fi-rr-search-alt"></i> Google Leads
                    </a>
                </div>
            </div>

            <div class="mkt-table-responsive">
                <table class="mkt-inquiries-table">
                    <thead>
                        <tr>
                            <th>Prospect</th>
                            <th>Channel</th>
                            <th>Campaign / Form</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="mktInquiriesTbody">
                        <tr>
                            <td colspan="5" class="mkt-table-loading-cell">
                                <i class="fi fi-rr-spinner mkt-table-loading-spinner"></i>
                                <span class="mkt-table-loading-text">Loading campaign leads...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
