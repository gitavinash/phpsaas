<style>
.modern-user-list {
    --ul-bg: #ffffff;
    --ul-border: #e5e7eb;
    --ul-soft: #f8fafc;
    --ul-text: #111827;
    --ul-subtext: #6b7280;
    --ul-accent: #1d4ed8;
    margin-top: 8px;
}

.modern-user-list h2 {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: var(--ul-text);
    letter-spacing: -0.02em;
}

.modern-user-list .panel {
    background: var(--ul-bg);
    border: 1px solid var(--ul-border);
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    padding: 16px;
}

.modern-user-list #table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--ul-border);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}

.modern-user-list #table thead th {
    background: #f9fafb;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    font-weight: 700;
    border-bottom: 1px solid var(--ul-border);
    padding: 12px 14px;
}

.modern-user-list #table tbody td {
    padding: 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1f2937;
    vertical-align: middle;
}

.modern-user-list #table tbody tr:last-child td {
    border-bottom: 0;
}

.modern-user-list #table tbody tr:hover td {
    background: #fbfdff;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: 1px solid #dbe5f0;
    background: #eef4ff;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 999px;
}

.user-name {
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.user-name:hover {
    color: var(--ul-accent);
}

.user-sub {
    margin-top: 2px;
    color: var(--ul-subtext);
    font-size: 12px;
}

.role-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 8px;
    border: 1px solid #d1d5db;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    text-transform: capitalize;
}

.hover {
    margin-top: 6px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    padding: 6px 10px;
    text-decoration: none;
    border: 1px solid transparent;
}

.action-btn-edit {
    color: #1e40af;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.action-btn-edit:hover {
    background: #dbeafe;
    color: #1e3a8a;
}

.action-btn-delete {
    color: #991b1b;
    background: #fef2f2;
    border-color: #fecaca;
}

.action-btn-delete:hover {
    background: #fee2e2;
    color: #7f1d1d;
}

.empty-user-state {
    text-align: center;
    color: var(--ul-subtext);
    font-size: 13px;
    padding: 28px 16px;
}
</style>

<div class="modern-user-list">
    <h2>All <?php echo ucwords($postType . 's'); ?></h2>
    <div class="panel">
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
                        <div class="hover">
                            <a class="action-btn action-btn-edit"
                                href="user/?type=edit&editor=true&id=<?php echo base64_encode($user_id); ?>"><i
                                    class="fas fa-pencil-alt"></i> Edit</a>
                            <?php

                                        if ($site_access_type != 'administrator') { ?>

                            <a href="#" id="<?php echo base64_encode($user_id) ?>_post"
                                class="deleteBtn action-btn action-btn-delete"><i class="fas fa-trash"></i> Delete</a>
                            <?php
                                        }

                                        ?>
                        </div>

                    </td>
                    <td><?php echo htmlspecialchars($profile_display_name); ?></td>
                    <td><?php echo htmlspecialchars($user_email); ?></td>
                    <td><span class="role-chip"><?php echo htmlspecialchars($site_access_type); ?></span></td>
                    <td></td>
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