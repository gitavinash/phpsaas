<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period ( <?php echo ((isset($daterange)) ? $daterange : 'Current month') ?> )</label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker"
                value="<?php echo ((isset($daterange)) ? $daterange : '') ?>" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>

<?php

    if($checkEmployees=mysqli_query($connection, "SELECT 
    user_id, 
    profile_display_name, 
    site_access_type, 
    user_reg_time, 
    user_act_time 
FROM 
    ".TABLE_PREFIX."user 
INNER JOIN 
    ".TABLE_PREFIX."site_access ON site_access_user_id=user_id 
INNER JOIN 
    ".TABLE_PREFIX."profile ON profile_user_id=user_id 
WHERE 
    site_access_site_id = ".SITE_ID."
ORDER BY 
    CASE WHEN site_access_type = 'administrator' THEN 0 ELSE 1 END, 
    user_id")) {
        
        if(mysqli_num_rows($checkEmployees)!=0) {
            while($employeesData = mysqli_fetch_assoc($checkEmployees)) {

                extract($employeesData);

                if($checkPerformace=mysqli_query($connection, "SELECT 
    enquiry_status, 
    totalLeads 
FROM 
    (SELECT 
         CASE 
             WHEN statuses.enquiry_status IS NULL THEN 'total'
             ELSE CAST(statuses.enquiry_status AS CHAR)
         END AS enquiry_status, 
         COUNT(enquiry.enquiry_id) AS totalLeads 
     FROM 
         (SELECT 1 AS enquiry_status 
          UNION ALL 
          SELECT 2 
          UNION ALL 
          SELECT 3 
          UNION ALL 
          SELECT 4) AS statuses 
     LEFT JOIN 
         ".TABLE_PREFIX."enquiry AS enquiry ON statuses.enquiry_status = enquiry.enquiry_status 
                                AND enquiry.enquiry_agent = '{$user_id}' 
                                AND enquiry.enquiry_site_id = ".SITE_ID." 
                                AND enquiry.enquiry_time BETWEEN '{$startDate}' AND '{$endDate}' 
     GROUP BY 
         statuses.enquiry_status WITH ROLLUP) AS results
ORDER BY 
    CASE WHEN enquiry_status = 'total' THEN 0 ELSE 1 END, enquiry_status ASC"));

    if(mysqli_num_rows($checkPerformace)!= 0) {
        echo "<div class='box-1'><h3 class='report-heading' >{$profile_display_name}".(($site_access_type=='administrator') ? ' ( Administrator )' : '')."</h4>";
        while($checkPerformaceData = mysqli_fetch_assoc($checkPerformace)) {
            extract($checkPerformaceData);

            switch ($enquiry_status) {
                case 1:
                    $backgroundColor='#C7EAE4';
                    break;
                case 2:
                    $backgroundColor='#F2F3F4';
                    break;
                case 3:
                    $backgroundColor='#FFD699';
                    break;
                case 4:
                    $backgroundColor='#FFCCCC';
                    break;
                default:
                    $backgroundColor= '#FFFFFF';
            }

            $agentLeadURL="/dashboard/leads/list/?agentId={$user_id}".(($enquiry_status=='total' || $enquiry_status=='') ? '' :"&leadStatus={$enquiry_status}")."&daterange={$startDate} - {$endDate}";

            echo "
                <div class='widget box-2'>
                    <div class='widget-info' style='background: {$backgroundColor}' >
                        <a href='{$agentLeadURL}'>
                            <div class='info-box'>
                                <p>".(($enquiry_status=='total' || $enquiry_status=='') ? 'Total Leads' : (($enquiry_status==1) ? 'Confirmed': (($enquiry_status==2) ? 'Pending': (($enquiry_status==3) ? 'Follow-ups': (($enquiry_status==4) ? 'Rejected': 'U/A Status')))))." enquiries
                                    <span>{$totalLeads}</span>
                                </p>
                            </div>
                        </a>
                    </div>
            </div>";
        }
        echo "</div>";
    }

            }
        }
    }

?>