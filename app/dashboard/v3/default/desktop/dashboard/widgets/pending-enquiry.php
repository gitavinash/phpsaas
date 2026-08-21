<?php
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=2&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-sky" id="totalPendingLeads">
        <div class="kpi-icon-box"><i class="fi-rr-time-fast"></i></div>
        <div class="info-box">
            <p class="kpi-label">Pending Enquiries</p>
            <span class="kpi-metric" id="totalPendingLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>