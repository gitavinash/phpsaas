<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2>Inventory & Sales Dashboard</h2>
        <p>Real-time stock, enquiry, and sales performance</p>
    </div>
    <div class="dashboard-datepicker">
        <div class="form-group right">
            <form action="" class="widgetForm">
                <input type="text" name="daterange" class="dateRangePicker" value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker" placeholder="Select date range" autocomplete="off">
                <input type="button" value="Update report" class="btn btn-green date-btn">
            </form>
        </div>
    </div>
</div>

<div class="kpi-grid">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/pending-request.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/revenue-summary.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/conversion-rate.php'; ?>
</div>