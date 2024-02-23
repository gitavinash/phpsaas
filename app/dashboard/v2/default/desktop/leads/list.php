<?php
    $statusCondition = '';
    if(isset($status)){
        $statusCondition = "enquiry_status={$status} AND ";
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
            <table id="showTableList">
                <thead>
                    <tr>
                        <th>Name.</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Subject</th>
                        <th>Assign to</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

        if ($dataList = mysqli_query($connection, "SELECT enquiry_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_status FROM ".TABLE_PREFIX."enquiry WHERE {$statusCondition} enquiry_site_id = ".SITE_ID)) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <tr>
                        <td><?php echo $enquiry_first_name; ?></td>
                        <td><?php echo $enquiry_email; ?></td>
                        <td><?php echo $enquiry_mobile; ?></td>
                        <td><?php echo $enquiry_subject; ?></td>
                        <td>
                            <?php
                                
                                    if($site_access_type!='agent') { ?>
                            <form action="">
                                <select class="selectJS form-control" name="user">
                                    <option value="#">Select user</option>
                                    <?php
                                        
                                            for($i=0; $i<sizeof($userList); $i++) {
                                                echo "<option value='{$userList[$i]['id']}'>{$userList[$i]['email']}</option>";
                                            }

                                        ?>
                                </select>
                                <input type="hidden" name="leadId" value="<?php echo base64_encode($enquiry_id) ?>">
                                <input type="submit" value="Assign lead to user"
                                    class="btn dataTableSubmitBtn dataTableBtn">
                                <a href="leads/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>"
                                    class="btn dataTableBtn">View
                                    lead details</a>
                            </form>
                            <?php
                                    } else { ?>
                            <a href="leads/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>"
                                class="btn dataTableBtn">View
                                lead details</a>
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