<h2>All <?php echo ucwords($postType . 's') ?></h2>
<div class="options-bar">
    <div class="left-box">
        <!-- <form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?> >All <?php echo $postType ?></option>
				<option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?> >Drafts <?php echo $postType ?></option>
				<option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>  >Published <?php echo $postType ?></option>
			</select>
			<input type="submit" class="btn" value="Filter" >
		</form> -->
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT user_id, user_username, user_email, user_status, site_access_type, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td>
                <p><?php echo $user_username; ?></p>
                <!-- <p><a
                        href="user.php?type=edit&editor=true&id=<?php echo base64_encode($user_id); ?>"><?php echo $user_username; ?></a>
                </p> -->
                <!-- <div class="hover">
                    <a href="user.php?type=edit&editor=true&id=<?php echo base64_encode($user_id); ?>"><i
                            class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($user_id) ?>_post" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                </div> -->
            </td>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo $user_email; ?></td>
            <td><?php echo $site_access_type; ?></td>
            <td></td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>