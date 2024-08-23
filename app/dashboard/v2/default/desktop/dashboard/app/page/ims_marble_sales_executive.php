<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period ( <?php echo ((isset($daterange)) ? $daterange : 'Last 30 days') ?> )</label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker"
                value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>

<div class="widget box-2 box-mb-5">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php';

    ?>
</div>
<div class="widget box-2 box-mb-5">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php';

    ?>
</div>

<?php
if (SITE_VERSION == 'mobile') { ?>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard/leads/add/" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-plus"></i>
                Add new lead
            </div>
        </a>
    </div>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard/sales/add/" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-plus"></i>
                Add new sale
            </div>
        </a>
    </div>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard/leads/list/?leadStatus=2" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-pending"></i>
                View all Pending leads
            </div>
        </a>
    </div>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard/leads/list/" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-lead-management"></i>
                View all leads
            </div>
        </a>
    </div>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard/sales/list/" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-point-of-sale-bill"></i>
                All Sales
            </div>
        </a>
    </div>
    <div class="widget box-2 box-mb-5">
        <a href="/dashboard//sales_request/list/" class="actionBtn new-item-btn">
            <div class="widget-info">
                <i class="fi fi-rr-email-pending"></i>
                All Sales request
            </div>
        </a>
    </div>
<?php
}

?>