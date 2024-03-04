<?php
    $statusCondition = '';
    if(isset($leadStatus)){
        $statusCondition = "enquiry_status={$leadStatus} AND ";
    }

    $user_filter='';

    if($site_access_type=='agent') {
        $user_filter=" enquiry_agent='".USER_ID."' AND ";
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
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed here.</span>
</h2>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value=""><?php echo ucwords($urlStructure[1]) ?> Status</option>
                    <option value="1">Confirm</option>
                    <option value="2">Pending</option>
                    <option value="3">Cancel</option>
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
                        <th>Name.</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <th>Source</th>
                        <th>Assign to</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

        if ($dataList = mysqli_query($connection, "SELECT enquiry_id, enquiry_time, enquiry_agent, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_source, enquiry_status FROM ".TABLE_PREFIX."enquiry WHERE {$user_filter} {$statusCondition} enquiry_site_id = ".SITE_ID." ORDER BY enquiry_time DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data);
                    
                    switch ($enquiry_status) {
                        case 1:
                            $backgroundColor='#ABEBC6';
                            break;
                        case 2:
                            $backgroundColor='#F2F3F4';
                            break;
                        case 3:
                            $backgroundColor='#FCF3CF';
                            break;
                        case 4:
                            $backgroundColor='#FADBD8';
                            break;
                        default:
                        $backgroundColor= '#FFFFFF';
                    }

                    ?>
                    <tr style="background: <?php echo $backgroundColor; ?>">
                        <td><?php echo $enquiry_first_name; ?></td>
                        <td><?php echo $enquiry_email; ?></td>
                        <td><?php echo $enquiry_mobile; ?></td>
                        <td><?php echo $enquiry_subject; ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($enquiry_time)); ?></td>
                        <td><?php echo $enquiry_source ?></td>
                        <td>
                            <form method="POST" class="">
                                <?php
                                    if($site_access_type!='agent' && $enquiry_status==0 && $enquiry_agent==0) { ?>
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
                                    }
                                ?>
                                <a href="leads/view/?id=<?php echo base64_encode($enquiry_id); ?>"
                                    class="btn dataTableBtn">View
                                    lead details</a>
                            </form>
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