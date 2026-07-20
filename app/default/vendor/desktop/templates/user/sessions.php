<style>
    .modern-user-sessions {
        --us-bg: #ffffff;
        --us-border: #e5e7eb;
        --us-soft: #f8fafc;
        --us-text: #111827;
        --us-sub: #6b7280;
        margin-top: 8px;
    }

    .modern-user-sessions h2 {
        margin: 0 0 12px;
        font-size: 24px;
        font-weight: 700;
        color: var(--us-text);
        letter-spacing: -0.02em;
    }

    .modern-user-sessions .panel {
        border: 1px solid var(--us-border);
        border-radius: 14px;
        background: var(--us-bg);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        padding: 16px;
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

    .user-name {
        color: #0f172a;
        font-size: 13px;
        font-weight: 600;
    }

    .user-sub {
        margin-top: 2px;
        color: var(--us-sub);
        font-size: 12px;
    }

    .modern-user-sessions #table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--us-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .modern-user-sessions #table thead th {
        background: #f9fafb;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 11px;
        font-weight: 700;
        border-bottom: 1px solid var(--us-border);
        padding: 12px 14px;
    }

    .modern-user-sessions #table tbody td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1f2937;
        vertical-align: middle;
    }

    .modern-user-sessions #table tbody tr:last-child td {
        border-bottom: 0;
    }

    .modern-user-sessions #table tbody tr:hover td {
        background: #fbfdff;
    }

    .session-destroy-btn {
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        padding: 7px 10px;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        text-decoration: none;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .session-destroy-btn:hover {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .session-empty {
        text-align: center;
        color: var(--us-sub);
        font-size: 13px;
        padding: 28px 16px;
    }
</style>

<div class="modern-user-sessions">
    <h2>Active User Sessions</h2>
    <div class="panel">
        <table id="table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Devices</th>
                    <th>Session Active From</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasSessions = false;
                $query = "
            SELECT 
                p.profile_display_name, 
                p.profile_email, 
                COUNT(us.us_active_device) AS device_count,
                MIN(us.us_active_time) AS first_active_time,
                us.us_user_id
            FROM isw_user_sessions us
            INNER JOIN isw_profile p ON p.profile_user_id = us.us_user_id
            INNER JOIN isw_site_access sa ON sa.site_access_user_id = us.us_user_id
            WHERE sa.site_access_type = 'subscriber' 
              AND sa.site_access_site_id = " . SITE_ID . "
            GROUP BY us.us_user_id, p.profile_display_name, p.profile_email
            ORDER BY first_active_time ASC
        ";

                $result = mysqli_query($connection, $query);

                if ($result && mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                        $hasSessions = true;
                        $name = htmlspecialchars($row['profile_display_name']);
                        $email = htmlspecialchars($row['profile_email']);
                        $deviceCount = intval($row['device_count']);
                        $activeFrom = date('d M Y, H:i:s', strtotime($row['first_active_time']));
                        $user_id = $row['us_user_id'];
                        $nameParts = preg_split('/\s+/', trim((string)$row['profile_display_name']));
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
                                    <span class="user-avatar"><?php echo htmlspecialchars($initials); ?></span>
                                    <div>
                                        <div class="user-name"><?php echo $name; ?></div>
                                        <div class="user-sub">Subscriber Session</div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $deviceCount; ?> Device<?php echo $deviceCount > 1 ? 's' : ''; ?></td>
                            <td><?php echo $activeFrom; ?></td>
                            <td><a href="#" id="<?php echo base64_encode($user_id) ?>_usersession"
                                    class="deleteBtn session-destroy-btn"><i class="fas fa-trash"></i> Destroy Session</a></td>
                        </tr>
                <?php endwhile;
                endif;

                if (!$hasSessions) {
                    echo '<tr><td colspan="5"><div class="session-empty">No active subscriber sessions found.</div></td></tr>';
                }

                ?>
            </tbody>
        </table>
    </div>
</div>