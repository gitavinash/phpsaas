<h2>Active User Sessions</h2>

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
                $name = htmlspecialchars($row['profile_display_name']);
                $email = htmlspecialchars($row['profile_email']);
                $deviceCount = intval($row['device_count']);
                $activeFrom = date('d M Y, H:i:s', strtotime($row['first_active_time']));
                $user_id = $row['us_user_id'];
        ?>
        <tr>
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $deviceCount; ?> Device<?php echo $deviceCount > 1 ? 's' : ''; ?></td>
            <td><?php echo $activeFrom; ?></td>
            <td><a href="#" id="<?php echo base64_encode($user_id) ?>_usersession"
                    class="btn btn-red btn-sm deleteBtn"><i class="fas fa-trash"></i> Destroy Session</a></td>
        </tr>
        <?php endwhile;
        endif; ?>
    </tbody>
</table>