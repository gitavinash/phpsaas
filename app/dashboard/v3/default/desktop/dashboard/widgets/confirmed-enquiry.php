<?php
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=1&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-green" id="totalConfirmLeads">
        <div class="kpi-icon-box"><i class="fi-rr-check-circle"></i></div>
        <div class="info-box">
            <p class="kpi-label">Confirmed Enquiries</p>
            <span class="kpi-metric" id="totalConfirmLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>