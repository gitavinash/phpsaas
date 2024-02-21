<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All Users list will be displayed here.</span>
</h2>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label>User Status</label>
                <select id="UserStatus">
                    <option value="">User Status</option>
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
                        <th>User No.</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

        if ($dataList = mysqli_query($connection, "SELECT user_id, user_username, user_mobile, user_email, user_status, site_access_type, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <tr>
                        <td>
                            <p><a
                                    href="users/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>"><?php echo $user_username; ?></a>
                            </p>
                            <!-- <div class="hover">
                                <a href="users/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>"><i
                                        class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="#" id="<?php echo base64_encode($user_id) ?>_user" class="deleteBtn"><i
                                        class="fas fa-trash"></i> Delete</a>
                            </div> -->
                        </td>
                        <td><?php echo $profile_display_name; ?></td>
                        <td><?php echo $user_mobile; ?></td>
                        <td><?php echo $user_email; ?></td>
                        <td><?php echo $site_access_type; ?></td>
                        <td>
                            <a href="users/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>" class="btn">View
                                user</a>
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