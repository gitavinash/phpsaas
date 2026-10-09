<?php
    if ($site_access_type == 'administrator') {
        $userFilter = '?';
    } else {
        $userFilter = "?agentId={$user_id}&";
    }

    $formattedStart = !empty($startDate) ? date('d-M-Y', strtotime($startDate)) : date('d-M-Y', strtotime('-29 days'));
    $formattedEnd   = !empty($endDate) ? date('d-M-Y', strtotime($endDate)) : date('d-M-Y');
    $url = "/dashboard/leads/list/{$userFilter}leadStatus=9&daterange=" . urlencode("{$formattedStart} - {$formattedEnd}");
?>
<a href="<?php echo $url ?>">
    <div class="widget-info kpi-card kpi-indigo" id="kpiQuoteSentValueCard">
        <div class="kpi-icon-box"><i class="fi fi-rr-document-signed"></i></div>
        <div class="info-box">
            <p class="kpi-label">Quote Sent Value</p>
            <span class="kpi-metric" id="kpiQuoteSentValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>
