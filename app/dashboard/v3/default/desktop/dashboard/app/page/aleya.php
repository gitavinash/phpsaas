<?php
$displayDateRange = 'This Month';
$displayDuration = '1 Month';

if (isset($daterange) && !empty($daterange)) {
    $dates = explode(' - ', trim($daterange));
    if (count($dates) == 2) {
        $t1 = strtotime($dates[0]);
        $t2 = strtotime($dates[1]);
        if ($t1 && $t2) {
            $days = round(abs($t2 - $t1) / 86400) + 1;
            $todayT = strtotime('today');
            $yesterdayT = strtotime('yesterday');

            if ($t1 == $todayT && $t2 == $todayT) {
                $displayDateRange = 'Today';
            } elseif ($t1 == $yesterdayT && $t2 == $yesterdayT) {
                $displayDateRange = 'Yesterday';
            } elseif ($days == 7) {
                $displayDateRange = 'Last 7 Days';
            } elseif ($days == 30) {
                $displayDateRange = 'Last 30 Days';
            } elseif (date('m-Y', $t1) == date('m-Y', $t2) && date('d', $t1) == '01') {
                if (date('m-Y', $t1) == date('m-Y')) {
                    $displayDateRange = 'This Month';
                } else {
                    $displayDateRange = 'Last Month';
                }
            } else {
                $displayDateRange = 'Custom Range';
            }

            if ($days == 1) {
                $displayDuration = '1 Day';
            } elseif ($days >= 28 && $days <= 31) {
                $displayDuration = '1 Month';
            } elseif ($days > 31) {
                $m = round($days / 30);
                $displayDuration = ($m == 1) ? '1 Month' : $m . ' Months';
            } else {
                $displayDuration = $days . ' Days';
            }
        }
    }
}
?>
<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2>Dashboard Overview</h2>
        <p>Real-time enquiry metrics, pipeline distribution & team performance</p>
    </div>
    <div class="dashboard-datepicker">
        <div class="date-range-badge-pill" id="dateRangeDurationPill">
            <i class="fi-rr-calendar"></i>
            <span class="selected-range-text" id="selectedRangeText"><?php echo $displayDateRange ?></span>
            <span class="duration-tag" id="dateDurationTag"><?php echo $displayDuration ?></span>
        </div>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker" value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker" placeholder="Select date range" autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="kpi-grid">
    <?php
    if ($site_access_type == 'administrator') {
        require_once ADMIN_PATH . 'dashboard/widgets/enquiry.php';
    }
    ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/confirmed-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/pending-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/follow-up-enquiry.php'; ?>
    <?php require_once ADMIN_PATH . 'dashboard/widgets/conversion-rate.php'; ?>
    <?php
    if ($site_access_type == 'administrator') {
        require_once ADMIN_PATH . 'dashboard/widgets/revenue-summary.php';
        require_once ADMIN_PATH . 'dashboard/widgets/duplicate-enquiry.php';
        require_once ADMIN_PATH . 'dashboard/widgets/cancle-enquiry.php';
    } else {
        require_once ADMIN_PATH . 'dashboard/widgets/cancle-enquiry.php';
    }
    ?>
</div>

<?php if ($site_access_type == 'administrator') { ?>

    <!-- Executive Volume Flow Trend Chart -->
    <div class="charts-grid-full">
        <div class="widget-info chart-card">
            <div class="chart-card-header">
                <p class="kpi-label">Enquiry Volume Flow (Trend Timeline)</p>
            </div>
            <div class="chart-card-body" id="enquiryFlowContainer" style="height: 220px; position: relative;">
                <canvas id="enquiryFlowCanvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Analytics & Distribution Charts -->
    <div class="charts-grid">
        <?php require_once ADMIN_PATH . 'dashboard/widgets/top-enquiry-source.php'; ?>
        <?php require_once ADMIN_PATH . 'dashboard/widgets/enquiry-performance.php'; ?>
    </div>

<?php } ?>

<!-- Operational Data Tables Stack -->
<div class="tables-stack">
    <?php
    // if ($site_access_type == 'agent') {
    //     require_once ADMIN_PATH . 'dashboard/widgets/recent-followup-enquiry.php';
    // }
    require_once ADMIN_PATH . 'dashboard/widgets/recent-enquiry.php';
    ?>
</div>