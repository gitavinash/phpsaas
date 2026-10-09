<?php
    $userRole       = strtolower(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : ''));
    $isCallingAgent = in_array($userRole, ['agent', 'calling_agent', 'sales_executive'], true) || ($site_access_type !== 'administrator');

    if ($site_access_type == 'administrator') {
        $userFilter = '?';
        $kpiStatus  = 0;
        $kpiLabel   = 'New Leads';
        $kpiIcon    = 'fi-rr-sparkles';
        $kpiCardCls = 'kpi-blue';
        $kpiCardId  = 'totalLeads';
    } else {
        $userFilter = "?agentId={$user_id}&";
        $kpiStatus  = 2;
        $kpiLabel   = 'Assigned';
        $kpiIcon    = 'fi-rr-user-time';
        $kpiCardCls = 'kpi-amber';
        $kpiCardId  = 'totalAssignedLeads';
    }

    $url = "/dashboard/leads/list/{$userFilter}leadStatus={$kpiStatus}&daterange={$startDate} - {$endDate}";
?>
<a href="<?php echo $url; ?>">
    <div class="widget-info kpi-card <?php echo $kpiCardCls; ?>" id="<?php echo $kpiCardId; ?>">
        <div class="kpi-icon-box"><i class="fi <?php echo $kpiIcon; ?>"></i></div>
        <div class="info-box">
            <p class="kpi-label"><?php echo $kpiLabel; ?></p>
            <span class="kpi-metric" id="totalLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></span>
        </div>
    </div>
</a>