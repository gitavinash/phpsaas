<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period ( <?php echo ((isset($daterange)) ? $daterange : 'Last 30 days') ?> )</label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker" value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker" placeholder="Select date range" autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>
<div class="widget box-2">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php';

    ?>
</div>
<div class="widget box-2">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php';

    ?>
</div>
<div class="widget box-2">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/pending-enquiry.php';

    ?>
</div>
<div class="widget box-2">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/follow-up-enquiry.php';

    ?>
</div>
<div class="widget box-2">
    <?php

    if ($site_access_type == 'administrator') {
        require_once ADMIN_PATH . 'dashboard/widgets/duplicate-enquiry.php';
    } else {
        require_once ADMIN_PATH . 'dashboard/widgets/cancle-enquiry.php';
    }

    ?>
</div>
<?php

if ($site_access_type == 'administrator') { ?>
    <div class="widget box-5">
        <?php

        require_once ADMIN_PATH . 'dashboard/widgets/top-enquiry-source.php';

        ?>
    </div>

    <div class="widget box-5">
        <?php

        require_once ADMIN_PATH . 'dashboard/widgets/enquiry-performance.php';

        ?>
    </div>
<?php
}

?>

<div class="widget box-1">
    <?php

    if ($site_access_type == 'agent') {
        require_once ADMIN_PATH . 'dashboard/widgets/recent-followup-enquiry.php';
    }

    ?>
</div>

<div class="widget box-1">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/recent-enquiry.php';

    ?>
</div>