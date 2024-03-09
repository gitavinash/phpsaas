<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=3&daterange={$startDate} - {$endDate}";

?>
<div class="widget-info" style="background: #FFD699" id="totalFollowupLeads">
    <a href="<?php echo $url ?>">
        <div class="info-box">
            <p>Follow-up enquiries
                <span id="totalFollowupLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </a>
</div>