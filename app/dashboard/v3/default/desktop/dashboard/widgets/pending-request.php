<?php
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}&request=pending&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-orange" id="totalPendingRequests">
        <div class="kpi-icon-box"><i class="fi-rr-spinner"></i></div>
        <div class="info-box">
            <p class="kpi-label">Pending Requests</p>
            <span class="kpi-metric" id="totalPendingRequestsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>