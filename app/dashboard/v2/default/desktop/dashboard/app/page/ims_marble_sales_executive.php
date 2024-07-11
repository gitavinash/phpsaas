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