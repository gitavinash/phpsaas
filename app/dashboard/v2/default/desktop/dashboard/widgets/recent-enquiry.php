<div class="widget-info">
    <div class="info-box">
        <p>Recent enquiry</p>
    </div>
    <table class="no-margin showTableListWithoutFilter">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Enquiry for</th>
                <th>Time</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php

        if ($dataList = mysqli_query($connection, "SELECT enquiry_id, enquiry_first_name as name, enquiry_mobile, enquiry_subject, enquiry_time, enquiry_status FROM ".TABLE_PREFIX."enquiry WHERE enquiry_site_id=".SITE_ID." ORDER BY enquiry_time DESC LIMIT 0, 5")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
            <tr>
                <td><?php echo $name; ?></td>
                <td><?php echo $enquiry_mobile; ?></td>
                <td><?php echo $enquiry_subject; ?></td>
                <td><?php echo date('d-m-Y H:i:s', strtotime($enquiry_time)); ?></td>
                <td>
                    <a href="leads/view/?id=<?php echo base64_encode($enquiry_id); ?>" class="btn">View
                        enquiry</a>
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