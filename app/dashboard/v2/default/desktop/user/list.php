<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All Users list will be displayed here.</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/user/add/"><i class="fi fi-rr-plus"></i></a>
</div>
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
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($dataList = mysqli_query($connection, "SELECT user_id, user_username, user_mobile, user_email, user_status, site_access_type, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_status=1 AND site_access_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($dataList) != 0) {
                            while ($data = mysqli_fetch_assoc($dataList)) {
                                extract($data) ?>
                    <tr>
                        <td><?php echo $profile_display_name; ?></td>
                        <td><?php echo $user_mobile; ?></td>
                        <td><?php echo $user_email; ?></td>
                        <td><?php echo $site_access_type; ?></td>
                        <td>
                            <a href="user/edit/?editor=true&id=<?php echo base64_encode($user_id); ?>"
                                class='tableActionIconBtn'><i class="fi fi-rr-edit bgLightGreen"></i></a>
                            <?php

                                        if ($site_access_type != 'administrator' || USER_ID != $user_id) { ?>
                            <a href='#' data-type='user' data-id='<?php echo $user_id ?>'
                                class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
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