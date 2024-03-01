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

    if(isset($daterange)) {
        $dates = explode(" - ", $daterange);
        $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
    } else {
        $currentMonth=date('m');
        $startDate = date("Y-{$currentMonth}-01 00:00:00");
        $endDate = date("Y-{$currentMonth}-31 23:59:59");
    }

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
    statuses.enquiry_status,
    COUNT(enquiry.enquiry_id) AS totalLeads
FROM 
    (
        SELECT 1 AS enquiry_status
        UNION ALL
        SELECT 2
        UNION ALL
        SELECT 3
        UNION ALL
        SELECT 4
    ) AS statuses
LEFT JOIN 
    isw_enquiry AS enquiry ON statuses.enquiry_status = enquiry.enquiry_status AND enquiry.enquiry_agent='{$user_id}' AND enquiry.enquiry_site_id = ".SITE_ID." AND enquiry_time BETWEEN '".date('Y-m-d 00:00:00', strtotime($startDate))."' AND '".date('Y-m-d 00:00:00', strtotime($endDate))."'
GROUP BY 
    statuses.enquiry_status;
"));

    if(mysqli_num_rows($checkPerformace)!= 0) {
        echo "<div class='box-1'><h3 class='report-heading' >{$profile_display_name}".(($site_access_type=='administrator') ? ' ( Administrator )' : '')."</h4>";
        while($checkPerformaceData = mysqli_fetch_assoc($checkPerformace)) {
            extract($checkPerformaceData);
            echo "
                <div class='widget box-4'>
                    <div class='widget-info'>
                        <div class='info-box'>
                            <p>".(($enquiry_status==1) ? 'Confirmed': (($enquiry_status==2) ? 'Pending': (($enquiry_status==3) ? 'Follow-ups': (($enquiry_status==4) ? 'Rejected': 'U/A Status'))))." enquiries
                                <span>{$totalLeads}</span>
                            </p>
                        </div>
                    </div>
            </div>";
        }
        echo "</div>";
    }

            }
        }
    }

?>