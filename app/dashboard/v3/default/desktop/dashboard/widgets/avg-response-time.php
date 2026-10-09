<?php
    $formattedStart = !empty($startDate) ? date('d-M-Y', strtotime($startDate)) : date('d-M-Y', strtotime('-29 days'));
    $formattedEnd   = !empty($endDate) ? date('d-M-Y', strtotime($endDate)) : date('d-M-Y');
    $url = "/dashboard/report/employees/?daterange=" . urlencode("{$formattedStart} - {$formattedEnd}");
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-sky" id="kpiAvgResponse">
        <div class="kpi-icon-box"><i class="fi-rr-stopwatch"></i></div>
        <div class="info-box">
            <p class="kpi-label">Avg. Response Time</p>
            <span class="kpi-metric" id="kpiAvgResponseValue">--</span>
        </div>
    </div>
</a>
