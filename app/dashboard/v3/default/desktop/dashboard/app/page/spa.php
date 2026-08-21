<?php if (SITE_VERSION == 'mobile' && $site_access_type === 'administrator') { ?>
<div class="quick-action-grid">
    <a href="membership/sell/" class="quick-action-card spa-link" data-route="/membership/sell/">
        <i class="fi fi-rr-user-add"></i> Create membership
    </a>
    <a href="membership/check-in/" class="quick-action-card spa-link" data-route="/membership/check-in/">
        <i class="fi fi-rr-entrance"></i> Check-in
    </a>
    <a href="membership/reports/" class="quick-action-card spa-link" data-route="/membership/reports/">
        <i class="fi fi-rr-chart-histogram"></i> Reports
    </a>
    <a href="membership/unpaid/" class="quick-action-card spa-link" data-route="/membership/unpaid/">
        <i class="fi fi-rr-indian-rupee-sign"></i> Unpaid Memberships
    </a>
</div>
<?php } ?>

<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2>Spa & Wellness Overview</h2>
        <p>Membership visits, revenue & client reporting</p>
    </div>
    <div class="dashboard-datepicker">
        <div class="form-group right">
            <form action="" class="widgetForm">
                <input type="text" name="daterange" class="dateRangePicker" value="" id="dashboardReportingDateRangePicker" placeholder="Select date range" readonly autocomplete="off">
                <input type="submit" value="Update report" class="btn btn-green date-btn">
            </form>
        </div>
    </div>
</div>

<div class="kpi-grid">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/pending-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/revenue-summary.php'; ?>
</div>

<div class="charts-grid">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/sales-chart.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/visits-chart.php'; ?>
</div>

<div class="tables-stack">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/recent-enquiry.php'; ?>
</div>