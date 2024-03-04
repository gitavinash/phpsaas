<?php

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
<div class="widget-info">
    <div class="info-box">
        <p>Upcoming followups</p>
    </div>
    <table class="no-margin showTableListWithoutFilter">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Enquiry for</th>
                <th>Source</th>
                <th>Time</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php

            $userFilter='';
            
            if($site_access_type=='administrator') {
                $enquiryFilter=" AND enquiry_status=3";
            } else {
                $userFilter=" enquiry_agent='".USER_ID."' AND ";
                $enquiryFilter=" AND enquiry_status=3";
            }


        if ($dataList = mysqli_query($connection, "SELECT enquiry_id, enquiry_agent, enquiry_first_name as name, enquiry_mobile, enquiry_subject, enquiry_time, enquiry_status, enquiry_source FROM ".TABLE_PREFIX."enquiry WHERE {$userFilter} enquiry_site_id=".SITE_ID." {$enquiryFilter} ORDER BY enquiry_followup ASC LIMIT 0, 5")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
            <tr>
                <td><?php echo $name; ?></td>
                <td><?php echo $enquiry_mobile; ?></td>
                <td><?php echo $enquiry_subject; ?></td>
                <td><?php echo $enquiry_source; ?></td>
                <td></span><?php echo date('d-m-Y H:i:s', strtotime($enquiry_followup)); ?>
                </td>
                <td>
                    <a href="leads/view/?id=<?php echo base64_encode($enquiry_id); ?>" class="btn dataTableBtn">View
                        lead details</a>
                </td>
            </tr>
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