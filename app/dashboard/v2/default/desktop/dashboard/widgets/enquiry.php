<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}&daterange={$startDate} - {$endDate}";

?>
<div class="widget-info" id="totalLeads">
    <a href="<?php echo $url ?>">
        <div class="info-box">
            <p>Total enquiry received
                <span id="totalLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt=""></span>
            </p>
        </div>
    </a>
</div>