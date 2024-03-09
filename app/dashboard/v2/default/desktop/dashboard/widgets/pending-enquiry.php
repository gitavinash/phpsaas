<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=2&daterange={$startDate} - {$endDate}";

?>
<a href="<?php echo $url ?>">
    <div class="widget-info" style="background: #F2F3F4" id="totalPendingLeads">
        <div class="info-box">
            <p>Pending enquiries
                <span id="totalPendingLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </div>
</a>