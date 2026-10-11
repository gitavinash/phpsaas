<?php
    
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=1&daterange={$startDate} - {$endDate}";
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
