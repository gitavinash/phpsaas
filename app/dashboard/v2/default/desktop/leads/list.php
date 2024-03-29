<?php
    $statusCondition = '';
    if(isset($leadStatus)){
        $statusCondition = "e.enquiry_status={$leadStatus} AND ";
    }

    if(isset($daterange)) {
        $dates = explode(" - ", $daterange);
        $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
    } else {
        $currentMonth=date('m');
        $startDate = date("Y-{$currentMonth}-01 00:00:00");
        $endDate = date("Y-{$currentMonth}-31 23:59:59");
    }

    $user_filter='';

    if($site_access_type=='agent') {
        $user_filter=" e.enquiry_agent='".USER_ID."' AND ";
    }

    if(isset($agentId)) {
        $user_filter=" e.enquiry_agent='{$agentId}' AND ";
    }

    $userList=array();

        if ($userInfoList = mysqli_query($connection, "SELECT user_id, user_username, user_mobile, user_email, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($userInfoList) != 0) {
                while ($data = mysqli_fetch_assoc($userInfoList)) {
                    extract($data);
                    $userList[]=array(
                        'id' => $user_id,
                        'name' => $profile_display_name,
                        'email' => $user_email
                    );
                }
            }
        }

?>
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed her</span>
</h2>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value=""><?php echo ucwords($urlStructure[1]) ?> Status</option>
                    <option value="0">Fresh</option>
                    <option value="1">Confirm</option>
                    <option value="2">Pending</option>
                    <option value="3">Follow up</option>
                    <option value="4">Cancel</option>
                    <option value="5">Duplicate</option>
                </select>
            </div>
            <div class="form-group right">
                <label>Date</label>
                <input type="text" class="dateRangePicker" placeholder="Select date range">
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <!-- <th>Email</th> -->
                        <th>Mobile</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <?php
                        
                        if($site_access_type!='agent') {
                            echo "<th>Source</th><th>Assign to</th>";
                        }

                        ?>
                        <th>Recent note</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

        if ($dataList = mysqli_query($connection, "SELECT 
    e.enquiry_id, 
    e.enquiry_time, 
    CASE 
        WHEN e.enquiry_agent = 0 THEN NULL
        ELSE p.profile_display_name 
    END AS agent_display_name, 
    e.enquiry_first_name, 
    e.enquiry_email, 
    e.enquiry_mobile, 
    e.enquiry_subject, 
    e.enquiry_source, 
    e.enquiry_status,
    n.enquiry_notes_meta_type,
    n.enquiry_notes_meta_value
FROM 
    ".TABLE_PREFIX."enquiry e
LEFT JOIN 
    ".TABLE_PREFIX."profile p ON e.enquiry_agent = p.profile_user_id
LEFT JOIN
    (SELECT 
         enquiry_notes_ref_id,
         MAX(enquiry_notes_update) AS latest_notes_time
     FROM 
         ".TABLE_PREFIX."enquiry_notes
     GROUP BY 
         enquiry_notes_ref_id) latest_notes ON enquiry_id = latest_notes.enquiry_notes_ref_id
LEFT JOIN
    ".TABLE_PREFIX."enquiry_notes n ON enquiry_id = n.enquiry_notes_ref_id AND latest_notes.latest_notes_time = n.enquiry_notes_update
WHERE 
    {$user_filter} 
    {$statusCondition} 
    e.enquiry_site_id = ".SITE_ID." 
    AND enquiry_time BETWEEN '{$startDate}' AND '{$endDate}' 
ORDER BY 
    e.enquiry_time DESC 
LIMIT 
    0, 500")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data);
                    
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
                        case 5:
                            $backgroundColor='#E6E6FA';
                            break;
                        default:
                        $backgroundColor= '#FFFFFF';
                    }

                    ?>
                    <tr style="background: <?php echo $backgroundColor; ?>">
                        <td><?php echo $enquiry_first_name; ?></td>
                        <!-- <td><?php echo $enquiry_email; ?></td> -->
                        <td><?php echo $enquiry_mobile; ?></td>
                        <td><?php echo $enquiry_subject; ?></td>
                        <td><?php echo date('d M Y, H:i A', strtotime($enquiry_time)); ?></td>
                        <?php
                            
                            if($site_access_type!='agent') { ?>
                        <td><?php echo $enquiry_source ?></td>
                        <td>
                            <form method="POST">
                                <?php
                                            if($site_access_type!='agent' && $enquiry_status==0 && $agent_display_name==null) { ?>
                                <select class="selectJS form-control" name="userId" required>
                                    <option value="">Select user</option>
                                    <?php
                                                
                                                    for($i=0; $i<sizeof($userList); $i++) {
                                                        echo "<option value='".base64_encode($userList[$i]['id'].'_'.$userList[$i]['name'])."'>{$userList[$i]['name']} {$userList[$i]['email']}</option>";
                                                    }

                                                ?>
                                </select>
                                <input type="hidden" name="leadId" value="<?php echo base64_encode($enquiry_id) ?>">
                                <input type="hidden" name="action" value="assign">
                                <input type="hidden" name="type" value="leads">
                                <input type="submit" value="Assign lead to user"
                                    class="btn dataTableSubmitBtn dataTableBtn">
                                <?php
                                            } else {
                                                echo $agent_display_name;
                                            }
                                        ?>
                            </form>
                        </td>
                        <?php
                                }

                            ?>
                        <td>
                            <strong><?php echo $enquiry_notes_meta_type?></strong><br /><?php echo $enquiry_notes_meta_value ?>
                        </td>
                        </td>
                        <td>
                            <a href="leads/view/?id=<?php echo base64_encode($enquiry_id); ?>"
                                class="btn dataTableBtn"><i class="fi-rr-eye"></i> View
                                lead details</a>
                            <?php
                        
                        if($enquiry_status==0 && $site_access_type!='agent') { ?>


                            <a href="#" class="btn dataTableBtn duplicateLead"
                                id="<?php echo base64_encode($enquiry_id) ?>"><i class="fi-rr-ban"></i> Mark
                                as duplicate lead</a>

                            <?php
                        }
                        
                        ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        }

        ?>
                </tbody>
            </table>
        </div>
    </section>
</div>