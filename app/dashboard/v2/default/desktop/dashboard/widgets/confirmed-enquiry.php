<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=1&daterange={$startDate} - {$endDate}";

?>
<div class="widget-info" style="background: #C7EAE4" id="totalConfirmLeads">
    <a href="<?php echo $url ?>">
        <div class="info-box">
            <p>Total confirmed enquiries
                <span id="totalConfirmLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </a>
</div>