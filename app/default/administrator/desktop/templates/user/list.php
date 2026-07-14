<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>


<div class="modern-user-list">
    <h2>All <?php echo ucwords($postType . 's'); ?></h2>
    <a href="user/?type=add" class="floating-add-btn" title="Add New User">
        <i class="fas fa-plus"></i>
        <span>Add User</span>
    </a>
    <div class="panel">
        <table id="table" data-default-order-column="0" data-page-length="10">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 150px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasUsers = false;

                $applyUserFilter = '';
                if (SITE_APP == 'nms') {
                    $applyUserFilter = "site_access_type NOT IN ('subscriber', 'customer') AND ";
                }

                if ($dataList = mysqli_query($connection, "SELECT user_id, user_username, user_email, user_status, site_access_type, profile_display_name, profile_image FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE {$applyUserFilter} site_access_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            $hasUsers = true;
                            extract($data);

                            $nameParts = preg_split('/\s+/', trim((string)$profile_display_name));
                            $initials = 'US';
                            if (!empty($nameParts[0])) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1));
                                if (!empty($nameParts[1])) {
                                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                                }
                            }
                ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <span
                                class="user-avatar"><?php echo (($profile_image != '') ? '<img src="' . $profile_image . '" alt="Profile Picture">' : htmlspecialchars($initials)); ?></span>
                            <div>
                                <a class="user-name"
                                    href="user/?type=edit&editor=true&id=<?php echo base64_encode($user_id); ?>"><?php echo htmlspecialchars($user_username); ?></a>
                                <div class="user-sub">Manage account access</div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($profile_display_name); ?></td>
                    <td><?php echo htmlspecialchars($user_email); ?></td>
                    <td><span class="role-chip"><?php echo htmlspecialchars($site_access_type); ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a class="action-btn btn-edit"
                                href="user/?type=edit&editor=true&id=<?php echo base64_encode($user_id); ?>" title="Edit"><i
                                    class="fas fa-pencil-alt"></i> Edit</a>
                            <?php if ($site_access_type != 'administrator') { ?>
                            <a href="#" id="<?php echo base64_encode($user_id) ?>_post"
                                class="deleteBtn action-btn btn-delete" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php
                        }
                    }
                }

                if (!$hasUsers) {
                    echo '<tr><td colspan="5"><div class="empty-user-state">No users found.</div></td></tr>';
                }

                ?>
            </tbody>
        </table>
    </div>
</div>