<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=4&daterange={$startDate} - {$endDate}";

?>
<a href="<?php echo $url ?>">
    <div class="widget-info" style="background: #FFCCCC" id="totalCancelLeads">
        <div class="info-box">
            <p>Total Cancelled enquiries
                <span id="totalCancelLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </div>
</a>