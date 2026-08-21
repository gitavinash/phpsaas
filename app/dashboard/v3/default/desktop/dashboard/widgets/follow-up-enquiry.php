<?php
    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=3&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-purple" id="totalFollowupLeads">
        <div class="kpi-icon-box"><i class="fi-rr-phone-call"></i></div>
        <div class="info-box">
            <p class="kpi-label">Follow-up Enquiries</p>
            <span class="kpi-metric" id="totalFollowupLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>