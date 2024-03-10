<?php

    if($site_access_type=='administrator') {
        $userFilter='?';
    } else {
        $userFilter="?agentId={$user_id}&";
    }

    $url="/dashboard/leads/list/{$userFilter}leadStatus=1&daterange={$startDate} - {$endDate}";

?>
<a href="<?php echo $url ?>">
    <div class="widget-info" style="background: #C7EAE4" id="totalConfirmLeads">
        <div class="info-box">
            <p>Confirmed enquiries
                <span id="totalConfirmLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </div>
</a>