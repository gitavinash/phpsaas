<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period ( <?php echo ((isset($daterange)) ? $daterange : 'Current month') ?> )</label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker"
                value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>
<div class="widget box-4">
    <?php

        require_once 'dashboard/widgets/enquiry.php';

    ?>
</div>
<div class="widget box-4">
    <?php

        require_once 'dashboard/widgets/confirmed-enquiry.php';

    ?>
</div>
<div class="widget box-4">
    <?php

        require_once 'dashboard/widgets/pending-enquiry.php';

    ?>
</div>
<div class="widget box-4">
    <?php

        require_once 'dashboard/widgets/follow-up-enquiry.php';

    ?>
</div>

<?php

    if($site_access_type=='administrator') { ?>
<div class="widget box-5">
    <?php

            require_once 'dashboard/widgets/top-enquiry-source.php';

        ?>
</div>

<div class="widget box-5">
    <?php

            require_once 'dashboard/widgets/enquiry-performance.php';

        ?>
</div>
<?php
    }

?>

<div class="widget box-1">
    <?php

    require_once 'dashboard/widgets/recent-followup-enquiry.php';

    ?>
</div>

<div class="widget box-1">
    <?php

    require_once 'dashboard/widgets/recent-enquiry.php';

    ?>
</div>