<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>


<div class="modern-user-list">
    <h2 id="userListHeading">All <?php echo ucwords($postType . 's'); ?></h2>
    <a href="user/?type=add" class="floating-add-btn" title="Add New User">
        <i class="fas fa-plus"></i>
        <span>Add User</span>
    </a>
    <div class="panel" role="region" tabindex="0" aria-labelledby="userListHeading">
        <table id="table" data-default-order-column="0" data-default-order-dir="asc" data-page-length="10" data-empty-message="No users found.">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 150px" data-orderable="false" data-searchable="false">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $applyUserFilter = '';
                if (SITE_APP == 'nms') {
                    $applyUserFilter = "site_access_type NOT IN ('subscriber', 'customer') AND ";
                }

                if ($dataList = mysqli_query($connection, "SELECT user_id, user_username, user_email, user_status, site_access_type, profile_display_name, profile_image FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE {$applyUserFilter} site_access_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            extract($data);

                            $nameParts = preg_split('/\s+/', trim((string)$profile_display_name));
                            $initials = 'US';
                            if (!empty($nameParts[0])) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1));
                                if (!empty($nameParts[1])) {
                                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                                }
                            }

                            $userIdEncoded = base64_encode((string)$user_id);
                            $safeUsername = htmlspecialchars((string)$user_username, ENT_QUOTES, 'UTF-8');
                            $safeDisplayName = htmlspecialchars((string)$profile_display_name, ENT_QUOTES, 'UTF-8');
                            $safeEmail = htmlspecialchars((string)$user_email, ENT_QUOTES, 'UTF-8');
                            $safeRole = htmlspecialchars((string)$site_access_type, ENT_QUOTES, 'UTF-8');
                            $safeInitials = htmlspecialchars($initials, ENT_QUOTES, 'UTF-8');
                            $trimmedImage = trim((string)$profile_image);
                ?>
                <tr>
                    <td data-order="<?php echo $safeUsername; ?>">
                        <div class="user-cell">
                            <span class="user-avatar" data-initials="<?php echo $safeInitials; ?>"><?php
                                echo ($trimmedImage !== '')
                                    ? '<img src="' . htmlspecialchars($trimmedImage, ENT_QUOTES, 'UTF-8') . '" alt="">'
                                    : $safeInitials;
                            ?></span>
                            <div>
                                <a class="user-name"
                                    href="user/?type=edit&editor=true&id=<?php echo $userIdEncoded; ?>"><?php echo $safeUsername; ?></a>
                                <div class="user-sub">Manage account access</div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo $safeDisplayName; ?></td>
                    <td><?php echo $safeEmail; ?></td>
                    <td><span class="role-chip"><?php echo $safeRole; ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a class="action-btn btn-edit"
                                href="user/?type=edit&editor=true&id=<?php echo $userIdEncoded; ?>" title="Edit"><i
                                    class="fas fa-pencil-alt"></i> Edit</a>
                            <?php if ($site_access_type != 'administrator') { ?>
                            <a href="#" id="<?php echo $userIdEncoded; ?>_post"
                                class="deleteBtn action-btn btn-delete" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                            <?php } ?>
                        </div>
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
</div>