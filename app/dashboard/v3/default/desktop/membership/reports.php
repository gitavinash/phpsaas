<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period <?php echo ((isset($daterange)) ? '' : ' ( Last 30 days )') ?></label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker" value="" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" readonly autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>


<div class="widget box-1">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/sales-chart.php';

    ?>
</div>
<div class="widget box-1">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/visits-chart.php';

    ?>
</div>