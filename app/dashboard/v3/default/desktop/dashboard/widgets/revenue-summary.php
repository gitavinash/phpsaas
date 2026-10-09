<?php
    $formattedStart = !empty($startDate) ? date('d-M-Y', strtotime($startDate)) : date('d-M-Y', strtotime('-29 days'));
    $formattedEnd   = !empty($endDate) ? date('d-M-Y', strtotime($endDate)) : date('d-M-Y');
    $url = "/dashboard/accounts/?status=verified&daterange=" . urlencode("{$formattedStart} - {$formattedEnd}");
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-purple" id="kpiRevenueSummary">
        <div class="kpi-icon-box"><i class="fi-rr-usd-circle"></i></div>
        <div class="info-box">
            <p class="kpi-label">Total Revenue</p>
            <span class="kpi-metric" id="kpiRevenueSummaryValue">--</span>
        </div>
    </div>
</a>
