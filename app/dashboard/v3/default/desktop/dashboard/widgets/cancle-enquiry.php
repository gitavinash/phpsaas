<?php
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=4&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-rose" id="totalCancelLeads">
        <div class="kpi-icon-box"><i class="fi-rr-cross-circle"></i></div>
        <div class="info-box">
            <p class="kpi-label">Cancelled Enquiries</p>
            <span class="kpi-metric" id="totalCancelLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>