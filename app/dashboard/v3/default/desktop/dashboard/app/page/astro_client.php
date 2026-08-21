<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2>Client Dashboard</h2>
        <p>Overview of client consultations & enquiry metrics</p>
    </div>
    <div class="dashboard-datepicker">
        <div class="form-group right">
            <form action="" class="widgetForm">
                <input type="text" name="daterange" class="dateRangePicker" value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker" placeholder="Select date range" autocomplete="off">
                <input type="submit" value="Update report" class="btn btn-green date-btn">
            </form>
        </div>
    </div>
</div>

<div class="kpi-grid">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/pending-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/follow-up-enquiry.php'; ?>
    <?php
    if ($site_access_type == 'administrator') {
        require_once ADMIN_PATH . 'dashboard/widgets/duplicate-enquiry.php';
    } else {
        require_once ADMIN_PATH . 'dashboard/widgets/cancle-enquiry.php';
    }
    ?>
</div>

<?php if ($site_access_type == 'administrator') { ?>
<div class="charts-grid">
    <?php require_once ADMIN_PATH . 'dashboard/widgets/top-enquiry-source.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/enquiry-performance.php'; ?>
</div>
<?php } ?>

<div class="tables-stack">
    <?php
    if ($site_access_type == 'agent') {
        require_once ADMIN_PATH . 'dashboard/widgets/recent-followup-enquiry.php';
    }
    require_once ADMIN_PATH . 'dashboard/widgets/recent-enquiry.php';
    ?>
</div>