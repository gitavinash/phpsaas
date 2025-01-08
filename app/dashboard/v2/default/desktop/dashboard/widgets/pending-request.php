<?php

if ($site_access_type == 'administrator') {
    $userFilter = '?';
} else {
    $userFilter = "?agentId={$user_id}&";
}

$url = "/dashboard/request/list/{$userFilter}status=0&daterange={$startDate} - {$endDate}";

?>
<a href="<?php echo $url ?>">
    <div class="widget-info" style="background: #F2F3F4" id="totalPendingRequest">
        <div class="info-box">
            <p>Pending request
                <span id="totalPendingRequestValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </div>
</a>